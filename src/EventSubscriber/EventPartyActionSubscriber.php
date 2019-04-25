<?php

namespace App\EventSubscriber;

use App\Component\Events\Events;
use App\Component\Events\EventPartyActionEvent;
use App\Component\Events\MeetingPointOfferedEvent;
use App\Component\Messaging\EventParty\Model\Pusher\JoinData;
use App\Component\Messaging\EventParty\Model\Pusher\MeetingPointOfferData;
use App\Component\Messaging\EventParty\Model\Pusher\SkipData;
use App\Component\Messaging\EventParty\PusherFacade;
use App\Component\User\UserManager;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class EventPartyActionSubscriber implements EventSubscriberInterface
{
    /** @var PusherFacade */
    private $pusherFacade;

    /** @var EventDispatcherInterface */
    private $dispatcher;

    public function __construct(PusherFacade $pusherFacade, EventDispatcherInterface $dispatcher)
    {
        $this->pusherFacade = $pusherFacade;
        $this->dispatcher   = $dispatcher;
    }

    public static function getSubscribedEvents()
    {
        return [
            Events::JOIN_TO_EVENT_PARTY   => 'onEventPartyJoin',
            Events::SKIP_EVENT_PARTY      => 'onEventPartySkip',
            Events::MEETING_POINT_OFFERED => 'onMeetingPointOffered',
        ];
    }
    
    public function onEventPartyJoin(EventPartyActionEvent $event): void
    {
        $this->pusherFacade->send(
            new JoinData($event->getUser(), $event->getEventParty())
        );

        if ($event->getEventParty()->isFilled()) {
            $this->dispatcher->dispatch(Events::EVENT_PARTY_FILLED, $event);
        }
    }

    public function onEventPartySkip(EventPartyActionEvent $event): void
    {
        $this->pusherFacade->send(
            new SkipData($event->getUser(), $event->getEventParty())
        );
    }

    public function onMeetingPointOffered(MeetingPointOfferedEvent $event)
    {
        $this->pusherFacade->send(
            new MeetingPointOfferData(
                $event->getUser(),
                $event->getEventParty(),
                $event->getPlace(),
                $event->getMeetingDateTime()
            )
        );
    }
}
