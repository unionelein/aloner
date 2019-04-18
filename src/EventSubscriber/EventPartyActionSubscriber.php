<?php

namespace App\EventSubscriber;

use App\Component\Events\Events;
use App\Component\Events\EventPartyActionEvent;
use App\Component\Messaging\EventParty\Model\Pusher\JoinData;
use App\Component\Messaging\EventParty\Model\Pusher\SkipData;
use App\Component\Messaging\EventParty\PusherFacade;
use App\Component\User\UserManager;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class EventPartyActionSubscriber implements EventSubscriberInterface
{
    /** @var PusherFacade */
    private $pusherFacade;

    /** @var UserManager */
    private $userManager;

    public function __construct(PusherFacade $pusherFacade, UserManager $userManager)
    {
        $this->pusherFacade = $pusherFacade;
        $this->userManager  = $userManager;
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
        $this->userManager->updateTempHash($event->getUser());
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
