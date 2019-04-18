<?php declare(strict_types=1);

namespace App\Component\EventParty;

use App\Component\Model\VO\DateTimeInterval;
use App\Entity\Event;
use App\Entity\SearchCriteria;
use App\Entity\Timetable;
use App\Entity\User;

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
    public static function isUserTimeFitForEventParty(
        array $timetables,
        DateTimeInterval $searchInterval,
        DateTimeInterval $usersInterval,
        int $minsForDayEvent,
        int $allowedMinsOffset = 0
    ): bool{
        // Actually, users can go little bit earlier
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

    public static function isUserTimeFitForEvent(
        User $user,
        Event $event,
        int $minsForDayEvent = Timetable::MIN_MINS_FOR_DAY_EVENT,
        int $allowedMinsOffset = SearchCriteria::ALLOWED_MINS_OFFSET
    ): bool{
        // TODO: LOOKS LIKE BUG, EVENT WAS CREATED WITH AVAILABLE 60MIN (90 NEEDED)
        // Actually, users can go little bit earlier
        $searchTimeFrom = DateTimeInterval::time($user->getSearchCriteria()->getTimeFrom())->modify("-{$allowedMinsOffset} min");
        $searchTimeTo   = DateTimeInterval::time($user->getSearchCriteria()->getTimeTo())->modify("+{$allowedMinsOffset} min");

        foreach ($event->getTimetables()->get() as $timetable) {
            $timeFrom = DateTimeInterval::time($timetable->getTimeFrom());
            $timeTo   = DateTimeInterval::time($timetable->getTimeTo());

            if ($timetable->getType() === Timetable::TYPE_DAY) {
                $maxTimeFrom = \max([$timeFrom, $searchTimeFrom]);
                $minTimeTo   = \min([$timeTo, $searchTimeTo]);

                $availableMins = ($minTimeTo->getTimestamp() - $maxTimeFrom->getTimestamp()) / 60;

                if ($availableMins >= $minsForDayEvent) {
                    return true;
                }
            }

            if ($timetable->getType() === Timetable::TYPE_VISIT) {
                if ($searchTimeFrom <= $timeFrom && $searchTimeTo >= $timeTo) {
                    return true;
                }
            }
        }

        return false;
    }
}
