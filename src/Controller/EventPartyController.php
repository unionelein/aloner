<?php declare(strict_types=1);

namespace App\Controller;

use App\Component\EventParty\EventPartyFinder;
use App\Component\EventParty\EventPartyService;
use App\Entity\EventParty;
use App\Entity\User;
use App\Repository\EventPartyMessageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @IsGranted(User::ROLE_FULL_REG)
 * @Route("/event_party")
 */
class EventPartyController extends BaseController
{
    /**
     * @Route("/", name="app_current_event_party")
     */
    public function currentEventParty(EventPartyMessageRepository $epMsgRepo, EntityManagerInterface $em)
    {
        $user       = $this->getUser();
        $eventParty = $user->getActiveEventParty();

        if (!$eventParty) {
            return $this->redirectToRoute('app_main');
        }

        // update hash on each load of page for better security
        $user->updateTempHash();

        $em->persist($user);
        $em->flush();

        return $this->render('eventParty/event_party.html.twig', [
            'eventParty'      => $eventParty,
            'messagesHistory' => $epMsgRepo->getMessageHistoryFor($eventParty, $user),
        ]);
    }

    /**
     * @Route("/join", name="app_join_to_event_party")
     */
    public function join(EventPartyFinder $eventPartyFinder, EventPartyService $eventPartyService)
    {
        $user = $this->getUser();

        if ($user->hasActiveEventParty()) {
            return $this->redirectToRoute('app_current_event_party');
        }

        $eventParty = $eventPartyFinder->findForUser($user) ?? $eventPartyService->createForUser($user);

        if (!$eventParty) {
            $this->redirectToRoute('app_no_events_found');
        }

        $eventPartyService->join($user, $eventParty);

        return $this->redirectToRoute('app_current_event_party');
    }

    /**
     * @Route("/skip/{id}", name="app_skip_event_party")
     */
    public function skip(EventParty $eventParty, EventPartyService $eventPartyService)
    {
        $eventPartyService->skip($this->getUser(), $eventParty);

        return $this->redirectToRoute('app_join_to_event_party');
    }

    /**
     * @Route("/leave/{id}", name="app_leave_event_party")
     */
    public function leave(EventParty $eventParty, EventPartyService $eventPartyService)
    {
        $eventPartyService->skip($this->getUser(), $eventParty);

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
     * @Route("/test_pusher", name="app_test_pusher")
     */
    public function testPusher()
    {
        // This is our new stuff
        $context = new \ZMQContext();
        $socket = $context->getSocket(\ZMQ::SOCKET_PUSH, 'my pusher');
        $socket->connect("tcp://localhost:5555");

        $socket->send(json_encode([
            'id' => 'pusher_test',
            'topic' => 'pusher',
        ]));

        return $this->json(['status' => 'da']);
    }
}
