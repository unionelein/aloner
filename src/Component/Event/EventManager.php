<?php declare(strict_types=1);
/**
 * This file is subject to the terms and conditions defined in file 'LICENSE', which is part of this source code
 * package. If the file is missing a copy can be found at:
 * https://gitlab.cybercoder.site/vj/policies-procedures-standards/blob/master/licensing/GHCM-LICENSE.
 */

namespace App\Component\Event;

use App\Entity\Event;
use App\Entity\User;
use App\Repository\EventRepository;

class EventManager
{
    /** @var EventRepository */
    private $eventRepo;

    public function __construct(EventRepository $eventRepo)
    {
        $this->eventRepo     = $eventRepo;
    }

    /**
     * @param User $user
     *
     * @return null|Event
     */
    public function findForUser(User $user): ?Event
    {
        $events = $this->eventRepo->findAppropriateEventsForUser($user);

        if (\count($events) === 0) {
            return null;
        }

        \shuffle($events);

        foreach ($events as $event) {
            if ($user->getSkippedEvents(new \DateTime())->contains($event)) {
                continue;
            }

            if (!$event->findAvailableTimetableForSC($user->getSearchCriteria())) {
                continue;
            }

            return $event;
        }

        return null;
    }
}
