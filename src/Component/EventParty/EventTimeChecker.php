<?php declare(strict_types=1);

namespace App\Component\EventParty;

use App\Component\Model\VO\TimeInterval;
use App\Entity\Event;
use App\Entity\SearchCriteria;
use App\Entity\Timetable;
use App\Entity\User;

class EventTimeChecker
{
    public const MIN_EVENT_DURATION = 30;

    public static function findAvailableEventTimetableForUser(
        User $user,
        Event $event,
        TimeInterval $usersTimeInterval = null,
        int $weekDay = null,
        int $allowedMinsOffset = 0
    ): ?Timetable {
        $usersTimeInterval = $usersTimeInterval ?? TimeInterval::fullDayTimeInterval();

        $searchTimeFrom = $user->getSearchCriteria()->getTimeFrom()->modify("-{$allowedMinsOffset} min");
        $searchTimeTo   = $user->getSearchCriteria()->getTimeTo()->modify("+{$allowedMinsOffset} min");

        $usersTimeFrom = $usersTimeInterval->getFrom()->modify("-{$allowedMinsOffset} min");
        $usersTimeTo   = $usersTimeInterval->getTo()->modify("+{$allowedMinsOffset} min");

        $weekDay = $weekDay ?? (int) $user->getSearchCriteria()->getDay()->format('w');

        foreach ($event->getTimetables()->getForWeekDay($weekDay) as $timetable) {
            $timeFrom = $timetable->getTimeFrom();
            $timeTo   = $timetable->getTimeTo();

            if ($event->getTimetableType() === Event::TIMETABLE_TYPE_DAY) {
                $maxTimeFrom = \max([$timeFrom, $usersTimeFrom, $searchTimeFrom]);
                $minTimeTo   = \min([$timeTo, $usersTimeTo, $searchTimeTo]);

                $availableMins = ($minTimeTo->getTimestamp() - $maxTimeFrom->getTimestamp()) / 60;
                $eventDuration = $event->getDuration() ?? self::MIN_EVENT_DURATION;

                if ($availableMins >= $eventDuration) {
                    return $timetable;
                }
            }

            if ($event->getTimetableType() === Event::TIMETABLE_TYPE_VISIT) {
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
