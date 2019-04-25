<?php declare(strict_types=1);

namespace App\Component\EventParty;

use App\Component\Model\VO\TimeInterval;
use App\Entity\Event;
use App\Entity\SearchCriteria;
use App\Entity\Timetable;
use App\Entity\User;

class EventTimeChecker
{
    public static function findAvailableEventTimetableForUser(
        User $user,
        Event $event,
        TimeInterval $usersTimeInterval = null,
        int $allowedMinsOffset = SearchCriteria::ALLOWED_MINS_OFFSET
    ): ?Timetable {
        $usersTimeInterval = $usersTimeInterval ?? TimeInterval::fullDayTimeInterval();

        $searchTimeFrom = $user->getSearchCriteria()->getTimeFrom()->modify("-{$allowedMinsOffset} min");
        $searchTimeTo   = $user->getSearchCriteria()->getTimeTo()->modify("+{$allowedMinsOffset} min");

        $usersTimeFrom = $usersTimeInterval->getFrom()->modify("-{$allowedMinsOffset} min");
        $usersTimeTo   = $usersTimeInterval->getTo()->modify("+{$allowedMinsOffset} min");

        foreach ($event->getTimetables()->get() as $timetable) {
            $timeFrom = $timetable->getTimeFrom();
            $timeTo   = $timetable->getTimeTo();

            if ($timetable->getType() === Timetable::TYPE_DAY) {
                $maxTimeFrom = \max([$timeFrom, $usersTimeFrom, $searchTimeFrom]);
                $minTimeTo   = \min([$timeTo, $usersTimeTo, $searchTimeTo]);

                $availableMins = ($minTimeTo->getTimestamp() - $maxTimeFrom->getTimestamp()) / 60;

                if ($availableMins >= $timetable->getTimeLength()) {
                    return $timetable;
                }
            }

            if ($timetable->getType() === Timetable::TYPE_VISIT) {
                $availableForUsers   = $usersTimeFrom <= $timeFrom && $usersTimeTo >= $timeTo;
                $availableForNewUser = $searchTimeFrom <= $timeFrom && $searchTimeTo >= $timeTo;

                if ($availableForUsers && $availableForNewUser) {
                    return $timetable;
                }
            }
        }

        return null;
    }
}
