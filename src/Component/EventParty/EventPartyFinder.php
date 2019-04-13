<?php declare(strict_types=1);

namespace App\Component\EventParty;

use App\Component\Model\VO\DateTimeInterval;
use App\Entity\EventParty;
use App\Entity\User;
use App\Repository\EventPartyRepository;

class EventPartyFinder
{
    private const ALLOWED_MINS_OFFSET = 30;

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

        $criteriaDay      = (int) $criteria->getDay()->format('w');
        $criteriaInterval = new DateTimeInterval($criteria->getTimeFrom(), $criteria->getTimeTo());

        $eventParties = $this->eventPartyRepo->findAvailableEventPartiesForUser($user);
        $this->sortByRelevance($eventParties);

        foreach ($eventParties as $eventParty) {
            if ($user->getSkippedEventParties()->contains($eventParty)) {
                continue;
            }

            if ($user->getSkippedTodayEvents()->contains($eventParty->getEvent())) {
                continue;
            }

            if (!$eventParty->canUserJoin($user)) {
                continue;
            }

            $timetables = $eventParty->getEvent()->getTimetables()->getForWeekDay($criteriaDay);

            if (!EventTimeChecker::check($timetables, $criteriaInterval, self::ALLOWED_MINS_OFFSET)) {
                continue;
            }

            return $eventParty;
        }

        return null;
    }

    private function sortByRelevance(array &$eventParties)
    {
        \usort($eventParties, function (EventParty $eventParty1, EventParty $eventParty2) {
            return $eventParty1->getPeopleRemaining() <=> $eventParty2->getPeopleRemaining();
        });
    }
}
