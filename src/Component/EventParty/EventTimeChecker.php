<?php declare(strict_types=1);

namespace App\Component\EventParty;

use App\Component\Model\VO\DateTimeInterval;
use App\Entity\Timetable;

class EventTimeChecker
{
    /**
     * @param Timetable[] $timetables
     * @param DateTimeInterval $searchInterval
     * @param int $allowedMinsOffset
     *
     * @return bool
     */
    public static function check(
        array $timetables,
        DateTimeInterval $searchInterval,
        int $allowedMinsOffset = 0
    ): bool{
        foreach ($timetables as $timetable) {
            if ($timetable->getType() === Timetable::TYPE_DAY) {
                $searchTimeFrom = self::time($searchInterval->getTimeFrom()->modify("+{$allowedMinsOffset} min"));
                $searchTimeTo   = self::time($searchInterval->getTimeTo()->modify("-{$allowedMinsOffset} min"));

                $timeFrom = self::time($timetable->getTimeFrom());
                $timeTo   = self::time($timetable->getTimeTo());

                if ($searchTimeFrom >= $timeFrom && $searchTimeTo <= $timeTo) {
                    return true;
                }
            }

            if ($timetable->getType() === Timetable::TYPE_VISIT) {
                $searchTimeFrom = self::time($searchInterval->getTimeFrom()->modify("-{$allowedMinsOffset} min"));
                $searchTimeTo   = self::time($searchInterval->getTimeTo()->modify("+{$allowedMinsOffset} min"));

                $timeFrom = self::time($timetable->getTimeFrom());
                $timeTo   = self::time($timetable->getTimeTo());

                if ($searchTimeFrom <= $timeFrom && $searchTimeTo >= $timeTo) {
                    return true;
                }
            }
        }

        return false;
    }

    private static function time(\DateTime $dateTime): \DateTime
    {
        return new \DateTime('0000-00-00 ' . $dateTime->format('H:i:s'));
    }
}