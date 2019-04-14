<?php declare(strict_types=1);

namespace App\Component\EventParty;

use App\Component\Model\VO\DateTimeInterval;
use App\Entity\Timetable;

class EventTimeChecker
{
    /**
     * @param Timetable[] $timetables $timetables
     * @param DateTimeInterval $searchInterval
     * @param DateTimeInterval $usersInterval
     * @param int $allowedMinsOffset
     *
     * @return bool
     */
    public static function check(
        array $timetables,
        DateTimeInterval $searchInterval,
        DateTimeInterval $usersInterval,
        int $allowedMinsOffset = 0
    ): bool{
        // TODO: add check time correct (check ep total users time and event time for acceptable)

        foreach ($timetables as $timetable) {
            if ($timetable->getType() === Timetable::TYPE_DAY) {
                $searchTimeFrom = DateTimeInterval::time($searchInterval->getTimeFrom()->modify("+{$allowedMinsOffset} min"));
                $searchTimeTo   = DateTimeInterval::time($searchInterval->getTimeTo()->modify("-{$allowedMinsOffset} min"));

                $timeFrom = DateTimeInterval::time($timetable->getTimeFrom());
                $timeTo   = DateTimeInterval::time($timetable->getTimeTo());

                if ($searchTimeFrom >= $timeFrom && $searchTimeTo <= $timeTo) {
                    return true;
                }
            }

            if ($timetable->getType() === Timetable::TYPE_VISIT) {
                $searchTimeFrom = DateTimeInterval::time($searchInterval->getTimeFrom()->modify("-{$allowedMinsOffset} min"));
                $searchTimeTo   = DateTimeInterval::time($searchInterval->getTimeTo()->modify("+{$allowedMinsOffset} min"));

                $timeFrom = DateTimeInterval::time($timetable->getTimeFrom());
                $timeTo   = DateTimeInterval::time($timetable->getTimeTo());

                if ($searchTimeFrom <= $timeFrom && $searchTimeTo >= $timeTo) {
                    return true;
                }
            }
        }

        return false;
    }
}