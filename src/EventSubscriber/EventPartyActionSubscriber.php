<?php

namespace App\EventSubscriber;

use App\Component\Events;
use App\Component\Events\EventPartyActionEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class EventPartyActionSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [
            Events::JOIN_TO_EVENT_PARTY => 'onEventPartyJoin',
            Events::SKIP_EVENT_PARTY    => 'onEventPartySkip',
        ];
    }

    public function onEventPartyJoin(EventPartyActionEvent $event)
    {

    }

    public function onEventPartySkip(EventPartyActionEvent $event)
    {

    }
}
