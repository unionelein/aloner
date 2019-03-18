<?php declare(strict_types=1);
/**
 * This file is subject to the terms and conditions defined in file 'LICENSE', which is part of this source code
 * package. If the file is missing a copy can be found at:
 * https://gitlab.cybercoder.site/vj/policies-procedures-standards/blob/master/licensing/GHCM-LICENSE.
 */

namespace App\Component\EventParty;

use App\Entity\EventParty;
use App\Entity\User;
use App\Repository\EventRepository;

class EventPartyService
{
    /**
     * @var EventRepository
     */
    private $eventRepo;

    public function __construct(EventRepository $eventRepo)
    {
        $this->eventRepo = $eventRepo;
    }

    public function createForUser(User $user): EventParty
    {
        $events = $this->eventRepo->findAppropriateEventsForUser($user);

        if (!\count($events)) {
            throw new \LogicException('Нет евентов для юзера');
        }

        \shuffle($events);
        $event = \reset($events);

        $eventParty = new EventParty($event);
        $eventParty->setNumberOfGuys(2);
        $eventParty->setNumberOfGirls(1);

        return $eventParty;
    }
}
