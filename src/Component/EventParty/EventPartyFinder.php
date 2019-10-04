<?php declare(strict_types=1);

namespace App\Component\EventParty;

use App\Component\Util\Date;
use App\Entity\EventParty;
use App\Entity\User;
use App\Repository\EventPartyRepository;

class EventPartyFinder
{
    /**
     * @var EventPartyRepository
     */
    private $eventPartyRepo;

    public function __construct(EventPartyRepository $eventPartyRepo)
    {
        $this->eventPartyRepo = $eventPartyRepo;
    }

    public function findForUser(User $user): ?EventParty
    {
        $eventParties = $this->eventPartyRepo->findAvailableEventPartiesForUser($user);
        $this->sortByRelevance($eventParties);

        foreach ($eventParties as $eventParty) {
            if ($user->getSkippedEventParties()->contains($eventParty)) {
                continue;
            }

            if ($user->getSkippedEvents(new \DateTime())->contains($eventParty->getEvent())) {
                continue;
            }

            if (!$eventParty->canUserJoin($user)) {
                continue;
            }

            return $eventParty;
        }

        return null;
    }

    private function sortByRelevance(array &$eventParties): void
    {
        \usort($eventParties, function (EventParty $ep1, EventParty $ep2) {
            if ($ep1->getPeopleRemaining() === $ep2->getPeopleRemaining()) {
                return $ep2->getUsers()->count() <=> $ep1->getUsers()->count();
            }

            return $ep1->getPeopleRemaining() <=> $ep2->getPeopleRemaining();
        });
    }
}
