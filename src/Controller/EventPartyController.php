<?php declare(strict_types=1);

namespace App\Controller;

use App\Component\EventParty\EventPartyFinder;
use App\Component\EventParty\EventPartyManager;
use App\Component\Events\Events;
use App\Component\Events\EventPartyActionEvent;
use App\Component\Events\MeetingPointOfferedEvent;
use App\Component\Model\DTO\Form\MeetingPointData;
use App\Component\User\UserManager;
use App\Component\Util\Date;
use App\Entity\EventParty;
use App\Entity\User;
use App\Form\MeetingPointOfferType;
use App\Repository\EventPartyMessageRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\EventDispatcher\EventDispatcher;
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
     * @Route("/", name="app_current_event_party")
     */
    public function currentEventParty(EventPartyMessageRepository $epMsgRepo, EventDispatcherInterface $dispatcher)
    {
        $user       = $this->getUser();
        $eventParty = $user->getActiveEventParty();

        if (!$eventParty) {
            return $this->redirectToRoute('app_main');
        }

        $dispatcher->dispatch(
            Events::LOAD_EVENT_PARTY,
            new EventPartyActionEvent($user, $eventParty)
        );

        return $this->render('eventParty/event_party.html.twig', [
            'eventParty'      => $eventParty,
            'messagesHistory' => $epMsgRepo->getMessageHistoryFor($eventParty, $user),
        ]);
    }

    /**
     * @Route("/join", name="app_join_to_event_party")
     */
    public function join(UserManager $userManager, EventPartyFinder $eventPartyFinder, EventPartyManager $epManager)
    {
        $user = $this->getUser();

        if ($user->hasActiveEventParty()) {
            return $this->redirectToRoute('app_current_event_party');
        }

        $eventParty = $eventPartyFinder->findForUser($user) ?? $epManager->createForUser($user);

        if (!$eventParty) {
            return $this->redirectToRoute('app_no_events_found');
        }

        $userManager->join($user, $eventParty);

        return $this->redirectToRoute('app_current_event_party');
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
        $form = $this->createForm(MeetingPointOfferType::class, null, ['eventParty' => $eventParty])
            ->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var MeetingPointData $meetingPointData */
            $meetingPointData = $form->getData();

            $userManager->offerMeetingPoint($this->getUser(), $eventParty, $meetingPointData);

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
