<?php declare(strict_types=1);
/**
 * This file is subject to the terms and conditions defined in file 'LICENSE', which is part of this source code
 * package. If the file is missing a copy can be found at:
 * https://gitlab.cybercoder.site/vj/policies-procedures-standards/blob/master/licensing/GHCM-LICENSE.
 */

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

        $eventParties = $this->eventPartyRepo->findAvailableEventPartiesForUser($user);

        \usort($eventParties, function (EventParty $eventParty1, EventParty $eventParty2) {
            return $eventParty1->getNumberOfPeople() <=> $eventParty2->getNumberOfPeople();
        });

        $resultEventParties = [];
        foreach ($eventParties as $eventParty) {
            if (\in_array($eventParty, $user->getSkippedEventParties()->toArray(), true)) {
                continue;
            }

            if (!$eventParty->canAddUser($user)) {
                continue;
            }

            $resultEventParties[] = $eventParty;
        }

        return $resultEventParties[0] ?? null;
    }
}
