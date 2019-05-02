<?php declare(strict_types=1);

namespace App\Controller;

use App\Component\EventParty\EventPartyFinder;
use App\Component\EventParty\EventPartyManager;
use App\Component\Events\Events;
use App\Component\Events\EventPartyActionEvent;
use App\Component\Model\DTO\Form\MeetingPointData;
use App\Component\User\UserManager;
use App\Entity\EventParty;
use App\Entity\User;
use App\Form\MeetingPointOfferType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
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
    /**
     * @Route("/", name="app_event_party_current")
     */
    public function current(EventDispatcherInterface $dispatcher)
    {
        $user       = $this->getUser();
        $eventParty = $user->findLastActiveEventParty();

        if (!$eventParty) {
            return $this->redirectToRoute('app_main');
        }

        return $this->eventParty($eventParty, $dispatcher);
    }

    /**
     * @Route("/{id}", name="app_event_party", requirements={"id"="\d+"})
     * @IsGranted(EventPartyVoter::DO_ACTIONS, subject="eventParty")
     */
    public function eventParty(EventParty $eventParty, EventDispatcherInterface $dispatcher)
    {
        $dispatcher->dispatch(
            Events::LOAD_EVENT_PARTY,
            new EventPartyActionEvent($this->getUser(), $eventParty)
        );

        return $this->render('eventParty/event_party.html.twig', ['eventParty' => $eventParty]);
    }

    /**
     * @Route("/join", name="app_join_to_event_party")
     */
    public function join(UserManager $userManager, EventPartyFinder $eventPartyFinder, EventPartyManager $epManager)
    {
        $user = $this->getUser();

        if ($user->hasActiveEventParty()) {
            return $this->redirectToRoute('app_event_party_current');
        }

        $eventParty = $eventPartyFinder->findForUser($user) ?? $epManager->createForUser($user);

        if (!$eventParty) {
            return $this->redirectToRoute('app_no_events_found');
        }

        $userManager->join($user, $eventParty);

        return $this->redirectToRoute('app_event_party_current');
    }

    /**
     * @Route("/skip/{id}", name="app_skip_event_party")
     */
    public function skip(EventParty $eventParty, UserManager $userManager)
    {
        $userManager->skip($this->getUser(), $eventParty);

        return $this->redirectToRoute('app_join_to_event_party');
    }

    /**
     * @Route("/leave/{id}", name="app_leave_event_party")
     */
    public function leave(EventParty $eventParty, UserManager $userManager)
    {
        $userManager->skip($this->getUser(), $eventParty);

        return $this->redirectToRoute('app_main');
    }

    /**
     * @Route("/no_events_found", name="app_no_events_found")
     */
    public function noEventsFound()
    {
        return $this->render('eventParty/no_events_found.html.twig');
    }

    /**
     * @Route("/meeting_point_offer/{id}", name="app_meeting_point_offer")
     * @IsGranted(EventPartyVoter::DO_ACTIONS, subject="eventParty")
     */
    public function meetingPointOffer(EventParty $eventParty, Request $request, UserManager $userManager)
    {
        $form = $this->createForm(MeetingPointOfferType::class, null, [
            'eventParty'      => $eventParty,
            'rejectedOfferId' => $request->get('rejected_offer_id'),
        ])->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var MeetingPointData $meetingPointData */
            $meetingPointData = $form->getData();

            $place = $meetingPointData->getPlace();
            $day   = clone $meetingPointData->getDay();
            $time  = clone $meetingPointData->getTime();

            $meetingDateTime  = $day->modify($time->format('H:i:s'));

            if ($rejectedOfferId = $form->get('rejectedOfferId')->getData()) {
                $userManager->answerOnMeetingPointOffer($this->getUser(), (int) $rejectedOfferId, false);
            }

            $userManager->offerMeetingPoint($this->getUser(), $eventParty, $place, $meetingDateTime);

            return new JsonResponse(['status' => 'success']);
        }

        return $this->render('eventParty/meeting_point_offer.html.twig', [
            'eventParty'        => $eventParty,
            'form'              => $form->createView(),
        ]);
    }

    /**
     * @Route("/meeting_point_offer_answer/{id}", name="app_meeting_point_offer_answer")
     * @IsGranted(EventPartyVoter::DO_ACTIONS, subject="eventParty")
     */
    public function meetingPointOfferAnswer(EventParty $eventParty, Request $request, UserManager $userManager)
    {
        $offerId = (int) $request->get('offer_id');
        $answer  = (bool) $request->get('answer');

        $userManager->answerOnMeetingPointOffer($this->getUser(), $offerId, $answer);

        return new Response();
    }
}
