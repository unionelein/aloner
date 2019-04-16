<?php

namespace App\EventSubscriber;

use App\Component\Events;
use App\Component\Events\EventPartyActionEvent;
use App\Component\Messaging\EventParty\PushManager;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class EventPartyActionSubscriber implements EventSubscriberInterface
{
    /** @var PushManager */
    private $pushManager;

    public function __construct(PushManager $pushManager)
    {
        $this->pushManager = $pushManager;
    }

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
