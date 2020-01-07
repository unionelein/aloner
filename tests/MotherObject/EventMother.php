<?php declare(strict_types=1);

namespace App\Tests\MotherObject;

use App\Component\Util\Date;
use App\Component\Util\Week;
use App\Entity\City;
use App\Entity\Event;
use App\Entity\Timetable;
use App\Entity\VO\Contacts;
use App\Entity\VO\Range;

class EventMother
{
    /**
     * @param City $city
     *
     * @return Event
     */
    public static function create(City $city): Event
    {
        $event = new Event(
            'event_name',
            45,
            new Contacts('address', $city),
            new Range(2, 20)
        );

        foreach (Week::DAYS as $weekDay => $dayName) {
            $timetable = new Timetable(
                $event,
                Timetable::TYPE_DAY,
                $weekDay,
                Date::time('00:00'),
                Date::time('23:59'),
            );

            $event->addTimetable($timetable);
        }

        return $event;
    }

    /**
     * @param City $city
     *
     * @return Event
     */
    public static function withTodayTimeTable(City $city): Event
    {
        $event = self::create($city);

        $timetable = new Timetable(
            $event,
            Timetable::TYPE_DAY,
            Week::weekDay('now'),
            Date::time('00:00'),
            Date::time('23:59'),
        );

        $event->addTimetable($timetable);

        return $event;
    }
}