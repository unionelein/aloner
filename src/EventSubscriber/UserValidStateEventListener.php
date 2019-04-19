<?php declare(strict_types=1);
/**
 * This file is subject to the terms and conditions defined in file 'LICENSE', which is part of this source code
 * package. If the file is missing a copy can be found at:
 * https://gitlab.cybercoder.site/vj/policies-procedures-standards/blob/master/licensing/GHCM-LICENSE.
 */

namespace App\EventSubscriber;

use App\Component\Events\EventPartyActionEvent;
use App\Component\Events\Events;
use App\Component\User\UserManager;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class UserValidStateEventListener implements EventSubscriberInterface
{
    /** @var UserManager */
    private $userManager;

    public function __construct(UserManager $userManager)
    {
        $this->userManager = $userManager;
    }

    public static function getSubscribedEvents()
    {
        return [
            Events::LOAD_EVENT_PARTY => 'onLoadEventParty',
        ];
    }

    public function onLoadEventParty(EventPartyActionEvent $event): void
    {
        $this->userManager->updateTempHash($event->getUser());
    }
}
