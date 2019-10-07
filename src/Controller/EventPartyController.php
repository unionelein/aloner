<?php declare(strict_types=1);

namespace App\Controller;

use App\Component\EventParty\EventPartyManager;
use App\Component\EventParty\Exception\NoEventsForUserException;
use App\Component\Events\Events;
use App\Component\Events\EPActionEvent;
use App\Component\Model\DTO\Form\MeetingPointData;
use App\Component\User\UserManager;
use App\Entity\EPOfferMOHistory;
use App\Entity\EventParty;
use App\Entity\User;
use App\Entity\VO\MeetingOptions;
use App\Form\MeetingPointOfferType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Security\Voter\EventPartyVoter;

/**
 * @IsGranted(User::ROLE_FULL_REG)
 * @Route("/event_party")
 */
class EventPartyController extends BaseController
{
    /** @var UserManager */
    private $userManager;

    /**
     * @param UserManager $userManager
     */
    public function __construct(UserManager $userManager)
    {
        $this->userManager = $userManager;
    }

    /**
     * @Route("/", name="app_event_party_current")
     */
    public function current(): Response
    {
        $user       = $this->getUser();
        $eventParty = $user->findLastActiveEventParty();

        if (!$eventParty) {
            return $this->redirectToRoute('app_main');
        }

        return $this->redirectToRoute('app_event_party', ['id' => $eventParty->getId()]);
    }

    /**
     * @Route("/{id}", name="app_event_party", requirements={"id"="\d+"})
     * @IsGranted(EventPartyVoter::DO_ACTIONS, subject="eventParty")
     */
    public function eventParty(EventParty $eventParty, EventDispatcherInterface $dispatcher): Response
    {
        $user = $this->getUser();

        $this->userManager->updateTempHash($user);
        $dispatcher->dispatch(Events::EP_LOAD, new EPActionEvent($user, $eventParty));

        return $this->render('eventParty/event_party.html.twig', ['eventParty' => $eventParty]);
    }

    /**
     * @Route("/find", name="app_event_party_find")
     */
    public function find(EventPartyManager $epManager): Response
    {
        $user = $this->getUser();

        if ($user->hasActiveEventParty()) {
            return $this->redirectToRoute('app_event_party_current');
        }

        try {
            $eventParty = $epManager->findForUser($user) ?? $epManager->createForUser($user);
        } catch (NoEventsForUserException $e) {
            return $this->redirectToRoute('app_no_events_found');
        }

        $this->userManager->join($user, $eventParty);

        return $this->redirectToRoute('app_event_party_current');
    }

    /**
     * @Route("/skip/{id}", name="app_skip_event_party")
     */
    public function skip(EventParty $eventParty): Response
    {
        $this->userManager->skip($this->getUser(), $eventParty);

        return $this->redirectToRoute('app_event_party_find');
    }

    /**
     * @Route("/leave/{id}", name="app_leave_event_party")
     */
    public function leave(EventParty $eventParty): Response
    {
        $this->userManager->skip($this->getUser(), $eventParty);

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
     * @Route("/offer_mo/{id}", name="app_offer_mo")
     * @IsGranted(EventPartyVoter::DO_ACTIONS, subject="eventParty")
     *
     * @ParamConverter("MO", class="App\Entity\VO\MeetingOptions")
     */
    public function offerMO(EventParty $eventParty, MeetingOptions $MO): JsonResponse
    {
        $this->userManager->offerMO($this->getUser(), $eventParty, $MO);

        return new JsonResponse(['success' => true]);
    }

    /**
     * @Route("/answer_mo/{epId}/{offerId}/{answer}", name="app_answer_mo")
     * @IsGranted(EventPartyVoter::DO_ACTIONS, subject="eventParty")
     *
     * @ParamConverter("eventParty", class="App\Entity\EventParty", options={"id": "epId"})
     * @ParamConverter("offer", class="App\Entity\EPOfferMOHistory", options={"id": "offerId"})
     * @ParamConverter("newMO", class="App\Entity\VO\MeetingOptions")
     */
    public function answerMO(
        EventParty $eventParty,
        EPOfferMOHistory $offer,
        bool $answer,
        MeetingOptions $newMO = null
    ): JsonResponse {
        $this->userManager->answerMO($this->getUser(), $eventParty, $offer, $answer, $newMO);

        return new JsonResponse(['success' => true]);
    }
}
