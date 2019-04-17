<?php declare(strict_types=1);

namespace App\Component\EventParty;

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
        $events = $this->eventRepo->findAppropriateEventsForUser($user);

        if (\count($events) === 0) {
            return null;
        }

        \shuffle($events);
        $event = \reset($events);

        $numOfPeople = \random_int($event->getMinNumberOfPeople(), $event->getMaxNumberOfPeople());

        if (($numOfPeople % 2) !== 0) {
            $numOfPeople > $event->getMinNumberOfPeople() ? $numOfPeople-- : $numOfPeople++;
        }

        return new EventParty($event, $numOfPeople/2, $numOfPeople/2);
    }


}
