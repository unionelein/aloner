<?php declare(strict_types=1);

namespace App\Controller;

use App\Component\EventParty\EventPartyManager;
use App\Component\EventParty\Exception\NoEventsForUserException;
use App\Component\User\UserManager;
use App\Entity\EPOfferMOHistory;
use App\Entity\EventParty;
use App\Entity\VO\MeetingOptions;
use App\Event\EPLoadedEvent;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Security\Voter\EventPartyVoter;

/**
 * @Route("/event_party")
 */
class EventPartyController extends BaseController
{
    /** @var UserManager */
    private $userManager;

    /** @var EventDispatcher */
    private $dispatcher;

    /**
     * @param EventDispatcherInterface $dispatcher
     * @param UserManager              $userManager
     */
    public function __construct(EventDispatcherInterface $dispatcher, UserManager $userManager)
    {
        $this->userManager = $userManager;
        $this->dispatcher  = $dispatcher;
    }

    /**
     * @Route("/", name="app_ep_current")
     */
    public function current(): Response
    {
        $user = $this->getUser();
        $ep   = $user->findLastActiveEventParty();

        if (!$ep) {
            return $this->redirectToRoute('app_main');
        }

        return $this->redirectToRoute('app_ep', ['id' => $ep->getId()]);
    }

    /**
     * @IsGranted(EventPartyVoter::SEE, subject="ep")
     * @Route("/{id}", name="app_ep", requirements={"id"="\d+"})
     *
     * @var EventParty $ep
     *
     * @return Response
     */
    public function eventParty(EventParty $ep): Response
    {
        $user = $this->getUser();

        $this->userManager->updateTempHash($user);
        $this->dispatcher->dispatch(new EPLoadedEvent($user, $ep));

        return $this->render('eventParty/event_party.html.twig', ['eventParty' => $ep]);
    }

    /**
     * @Route("/find", name="app_find_ep")
     *
     * @var EventPartyManager $epManager
     *
     * @return Response
     *
     * @throws \Throwable
     */
    public function find(EventPartyManager $epManager): Response
    {
        $user = $this->getUser();

        if ($user->hasActiveEventParty()) {
            return $this->redirectToRoute('app_ep_current');
        }

        try {
            $eventParty = $epManager->findForUser($user) ?? $epManager->createForUser($user);
        } catch (NoEventsForUserException $e) {
            return $this->redirectToRoute('app_no_events_found');
        }

        $this->userManager->join($user, $eventParty);

        return $this->redirectToRoute('app_ep_current');
    }

    /**
     * @IsGranted(EventPartyVoter::PARTICIPANT, subject="ep")
     * @Route("/skip/{id}", name="app_skip_ep")
     */
    public function skip(EventParty $ep): Response
    {
        $this->userManager->skip($this->getUser(), $ep);

        return $this->redirectToRoute('app_find_ep');
    }

    /**
     * @IsGranted(EventPartyVoter::PARTICIPANT, subject="ep")
     * @Route("/leave/{id}", name="app_leave_ep")
     */
    public function leave(EventParty $ep): Response
    {
        $this->userManager->skip($this->getUser(), $ep);

        return $this->redirectToRoute('app_main');
    }

    /**
     * @Route("/no_events_found", name="app_no_events_found")
     */
    public function noEventsFound(): Response
    {
        return $this->render('eventParty/no_events_found.html.twig');
    }

    /**
     * @IsGranted(EventPartyVoter::OFFER_MO, subject="ep")
     * @Route("/offer_mo/{id}", name="app_offer_mo")
     *
     * @ParamConverter("MO", class="App\Entity\VO\MeetingOptions")
     */
    public function offerMO(EventParty $ep, MeetingOptions $MO): JsonResponse
    {
        $this->userManager->offerMO($this->getUser(), $ep, $MO);

        return new JsonResponse(['success' => true]);
    }

    /**
     * @IsGranted(EventPartyVoter::PARTICIPANT, subject="ep")
     * @Route("/answer_mo/{epId}/{offerId}/{answer}", name="app_answer_mo")
     *
     * @ParamConverter("ep", class="App\Entity\EventParty", options={"id": "epId"})
     * @ParamConverter("offer", class="App\Entity\EPOfferMOHistory", options={"id": "offerId"})
     * @ParamConverter("newMO", class="App\Entity\VO\MeetingOptions")
     */
    public function answerMO(EventParty $ep, EPOfferMOHistory $offer, bool $answer, MeetingOptions $newMO = null): JsonResponse
    {
        $this->userManager->answerMO($this->getUser(), $ep, $offer, $answer, $newMO);

        return new JsonResponse(['success' => true]);
    }
}
