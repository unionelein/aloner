<?php

namespace App\Entity;

use App\Component\Util\Date;
use App\Component\Util\Week;
use Doctrine\ORM\Mapping as ORM;
use Webmozart\Assert\Assert;

/**
 * @ORM\Table(name="timetable")
 * @ORM\Entity(repositoryClass="App\Repository\TimetableRepository")
 */
class Timetable
{
    public const TYPE_VISIT = 1;

    public const TYPE_DAY = 2;

    public const TYPES = [
        self::TYPE_VISIT,
        self::TYPE_DAY,
    ];

    /**
     * @var int
     *
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer", name="timetable_id")
     */
    private $id;

    /**
     * @var Event
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Event", inversedBy="timetables")
     * @ORM\JoinColumn(name="timetable_event_id", referencedColumnName="event_id", nullable=false)
     */
    private $event;

    /**
     * @var int
     *
     * @ORM\Column(type="smallint", name="timetable_type")
     */
    private $type;

    /**
     * @var int
     *
     * @ORM\Column(type="smallint", name="timetable_week_day")
     */
    private $weekDay;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="time", name="timetable_time_from")
     */
    private $timeFrom;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="time", name="timetable_time_to")
     */
    private $timeTo;

    /**
     * @param Event     $event
     * @param int       $type
     * @param int       $weekDay @see Week::weekDay
     * @param \DateTime $timeFrom
     * @param \DateTime $timeTo
     */
    public function __construct(
        Event $event,
        int $type,
        int $weekDay,
        \DateTime $timeFrom,
        \DateTime $timeTo
    ) {
        Assert::keyExists(Week::DAYS, $weekDay);
        Assert::oneOf($type, self::TYPES);

        $this->weekDay  = $weekDay;
        $this->timeFrom = Date::time($timeFrom);
        $this->timeTo   = Date::time($timeTo);
        $this->type     = $type;

        $this->event = $event;
        $event->addTimetable($this);
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return Event
     */
    public function getEvent(): Event
    {
        return $this->event;
    }

    /**
     * @return \DateTime
     */
    public function getTimeFrom(): \DateTime
    {
        return clone $this->timeFrom;
    }

    /**
     * @return \DateTime
     */
    public function getTimeTo(): \DateTime
    {
        return clone $this->timeTo;
    }

    /**
     * @return int
     */
    public function getWeekDay(): int
    {
        return $this->weekDay;
    }
}
