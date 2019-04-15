<?php declare(strict_types=1);

namespace App\Component\EventParty;

use App\Component\Model\VO\DateTimeInterval;
use App\Entity\Timetable;

class EventTimeChecker
{
    /**
     * @param Timetable[]      $timetables
     * @param DateTimeInterval $searchInterval
     * @param DateTimeInterval $usersInterval
     * @param int              $minsForDayEvent
     * @param int              $allowedMinsOffset
     *
     * @return bool
     */
    public static function check(
        array $timetables,
        DateTimeInterval $searchInterval,
        DateTimeInterval $usersInterval,
        int $minsForDayEvent,
        int $allowedMinsOffset = 0
    ): bool{
        // пользователи могут прийти немного раньше, и уйти немного позже, чем указали
        $searchTimeFrom = DateTimeInterval::time($searchInterval->getFrom())->modify("-{$allowedMinsOffset} min");
        $searchTimeTo   = DateTimeInterval::time($searchInterval->getTo())->modify("+{$allowedMinsOffset} min");

        $usersTimeFrom = DateTimeInterval::time($usersInterval->getFrom())->modify("-{$allowedMinsOffset} min");
        $usersTimeTo   = DateTimeInterval::time($usersInterval->getTo())->modify("+{$allowedMinsOffset} min");

        foreach ($timetables as $timetable) {
            $timeFrom = DateTimeInterval::time($timetable->getTimeFrom());
            $timeTo   = DateTimeInterval::time($timetable->getTimeTo());

            if ($timetable->getType() === Timetable::TYPE_DAY) {
                $maxTimeFrom = \max([$timeFrom, $usersTimeFrom, $searchTimeFrom]);
                $minTimeTo   = \min([$timeTo, $usersTimeTo, $searchTimeTo]);

                $availableMins = ($minTimeTo->getTimestamp() - $maxTimeFrom->getTimestamp()) / 60;

                if ($availableMins >= $minsForDayEvent) {
                    return true;
                }
            }

            if ($timetable->getType() === Timetable::TYPE_VISIT) {
                $availableForUsers   = $usersTimeFrom <= $timeFrom && $usersTimeTo >= $timeTo;
                $availableForNewUser = $searchTimeFrom <= $timeFrom && $searchTimeTo >= $timeTo;

                if ($availableForUsers && $availableForNewUser) {
                    return true;
                }
            }
        }

        return false;
    }
}
