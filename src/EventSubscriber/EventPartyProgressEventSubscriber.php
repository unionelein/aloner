<?php declare(strict_types=1);
/**
 * This file is subject to the terms and conditions defined in file 'LICENSE', which is part of this source code
 * package. If the file is missing a copy can be found at:
 * https://gitlab.cybercoder.site/vj/policies-procedures-standards/blob/master/licensing/GHCM-LICENSE.
 */

namespace App\EventSubscriber;

use App\Component\Events\EventPartyActionEvent;
use App\Component\Events\Events;
use App\Component\Messaging\EventParty\PusherFacade;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class EventPartyProgressEventSubscriber implements EventSubscriberInterface
{
    /** @var PusherFacade */
    private $pusherFacade;

    public function __construct(PusherFacade $pusherFacade)
    {
        $this->pusherFacade = $pusherFacade;
    }

    public static function getSubscribedEvents()
    {
        return [
            Events::EVENT_PARTY_FILLED => 'onEventPartyFilled',
        ];
    }

    public function onEventPartyFilled(EventPartyActionEvent $event): void
    {
        //$this->pusherFacade->send();
    }
}
