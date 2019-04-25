<?php declare(strict_types=1);

namespace App\Component\EventParty;

use App\Entity\Event;
use App\Entity\EventParty;
use App\Entity\User;
use App\Repository\EventRepository;

class EventPartyManager
{
    /** @var EventRepository */
    private $eventRepo;

    public function __construct(EventRepository $eventRepo)
    {
        $this->eventRepo = $eventRepo;
    }

    public function createForUser(User $user): ?EventParty
    {
        $event = $this->findEventForUser($user);

        if (!$event) {
            return null;
        }

        $minNumOfEachSex = (int) \ceil($event->getMinNumberOfPeople() / 2);
        $maxNumOfEachSex = (int) \floor($event->getMaxNumberOfPeople() / 2);
        $numOfEachSex    = \random_int($minNumOfEachSex, $maxNumOfEachSex);

        return new EventParty($event, $numOfEachSex, $numOfEachSex);
    }

    private function findEventForUser(User $user): ?Event
    {
        $events = $this->eventRepo->findAppropriateEventsForUser($user);

        if (\count($events) === 0) {
            return null;
        }

        \shuffle($events);

        foreach ($events as $event) {
            if ($user->getSkippedTodayEvents()->contains($event)) {
                continue;
            }

            if (!EventTimeChecker::findAvailableEventTimetableForUser($user, $event)) {
                continue;
            }

            return $event;
        }

        return null;
    }
}
