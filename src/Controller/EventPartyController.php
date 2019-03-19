<?php declare(strict_types=1);
/**
 * This file is subject to the terms and conditions defined in file 'LICENSE', which is part of this source code
 * package. If the file is missing a copy can be found at:
 * https://gitlab.cybercoder.site/vj/policies-procedures-standards/blob/master/licensing/GHCM-LICENSE.
 */

namespace App\Controller;

use App\Component\EventParty\EventPartyFinder;
use App\Component\EventParty\EventPartyService;
use App\Entity\EventParty;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @IsGranted(User::ROLE_PARTIAL_REG)
 * @Route("/event_party")
 */
class EventPartyController extends BaseController
{
    /**
     * @Route("/", name="app_current_event_party")
     */
    public function currentEventParty()
    {
        $user       = $this->getUser();
        $eventParty = $user->getActiveEventParty();

        if (!$eventParty) {
            return $this->redirectToRoute('app_main');
        }

        return $this->render('eventParty/event_party.html.twig', [
            'eventParty' => $eventParty,
        ]);
    }

    /**
     * @Route("/join", name="app_join_to_event_party")
     */
    public function join(EventPartyFinder $eventPartyFinder, EventPartyService $eventPartyService, EntityManagerInterface $em)
    {
        $user = $this->getUser();

        if ($user->hasActiveEventParty()) {
            return $this->redirectToRoute('app_current_event_party');
        }

        $eventParty = $eventPartyFinder->findForUser($user) ?? $eventPartyService->createForUser($user);
        $user->joinToEventParty($eventParty);

        $em->persist($eventParty);
        $em->flush();

        return $this->redirectToRoute('app_current_event_party');
    }

    /**
     * @Route("/skip/{id}", name="app_skip_event_party")
     */
    public function skip(EventParty $eventParty, EntityManagerInterface $em)
    {
        $user = $this->getUser();
        $user->skipEventParty($eventParty);

        $em->persist($eventParty);
        $em->persist($user);
        $em->flush();

        return $this->redirectToRoute('app_join_to_event_party');
    }

    /**
     * @Route("/leave/{id}", name="app_leave_event_party")
     */
    public function leave(EventParty $eventParty, EntityManagerInterface $em)
    {
        $user = $this->getUser();
        $user->skipEventParty($eventParty);

        $em->persist($eventParty);
        $em->persist($user);
        $em->flush();

        return $this->redirectToRoute('app_main');
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
