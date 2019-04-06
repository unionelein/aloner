<?php declare(strict_types=1);

namespace App\Component\EventParty;

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
        $criteria = $user->getSearchCriteria();

        // TODO: чтобы в 1 день не повторялись евенты, в которые закидывает юзера
        $eventParties = $this->eventPartyRepo->findAvailableEventPartiesForUser($user);
        $this->sortByRelevance($eventParties);

        $resultEventParties = [];
        foreach ($eventParties as $eventParty) {
            if ($user->getSkippedEventParties()->contains($eventParty)) {
                continue;
            }

            if (!$eventParty->canUserJoin($user)) {
                continue;
            }

            $resultEventParties[] = $eventParty;
        }

        return $resultEventParties[0] ?? null;
    }

    private function sortByRelevance(array &$eventParties)
    {
        \usort($eventParties, function (EventParty $eventParty1, EventParty $eventParty2) {
            return $eventParty1->getPeopleRemaining() <=> $eventParty2->getPeopleRemaining();
        });
    }
}
