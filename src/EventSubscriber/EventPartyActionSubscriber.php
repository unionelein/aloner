<?php

namespace App\EventSubscriber;

use App\Component\Events;
use App\Component\Events\EventPartyActionEvent;
use App\Component\Messaging\EventParty\Model\Pusher\JoinData;
use App\Component\Messaging\EventParty\Model\Pusher\SkipData;
use App\Component\Messaging\EventParty\PusherFacade;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class EventPartyActionSubscriber implements EventSubscriberInterface
{
    /** @var PusherFacade */
    private $pusherFacade;

    /** @var EntityManagerInterface */
    private $em;

    public function __construct(PusherFacade $pusherFacade, EntityManagerInterface $em)
    {
        $this->pusherFacade = $pusherFacade;
        $this->em = $em;
    }

    public static function getSubscribedEvents()
    {
        return [
            Events::LOAD_EVENT_PARTY    => 'onLoadEventParty',
            Events::JOIN_TO_EVENT_PARTY => 'onEventPartyJoin',
            Events::SKIP_EVENT_PARTY    => 'onEventPartySkip',
        ];
    }

    public function onLoadEventParty(EventPartyActionEvent $event): void
    {
        $user = $event->getUser();

        // update hash on each load of page for better security
        $user->updateTempHash();

        $this->em->persist($user);
        $this->em->flush();
    }
    
    public function onEventPartyJoin(EventPartyActionEvent $event): void
    {
        $this->pusherFacade->send(
            new JoinData($event->getUser(), $event->getEventParty())
        );
    }

    public function onEventPartySkip(EventPartyActionEvent $event): void
    {
        $this->pusherFacade->send(
            new SkipData($event->getUser(), $event->getEventParty())
        );
    }
}
