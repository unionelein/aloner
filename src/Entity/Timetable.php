<?php

namespace App\Entity;

use App\Component\Model\VO\TimeInterval;
use Doctrine\ORM\Mapping as ORM;
use Webmozart\Assert\Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TimetableRepository")
 */
class Timetable
{
    public const MONDAY = 1;

    public const TUESDAY = 2;

    public const WEDNESDAY = 3;

    public const THURSDAY = 4;

    public const FRIDAY = 5;

    public const SATURDAY = 6;

    public const SUNDAY = 0;

    public const WEEK_DAYS = [
        self::MONDAY    => 'Понедельник',
        self::TUESDAY   => 'Вторник',
        self::WEDNESDAY => 'Среда',
        self::THURSDAY  => 'Четверг',
        self::FRIDAY    => 'Пятница',
        self::SATURDAY  => 'Суббота',
        self::SUNDAY    => 'Воскресенье',
    ];

    public const SHORT_WEEK_DAYS = [
        self::MONDAY    => 'Пн',
        self::TUESDAY   => 'Вт',
        self::WEDNESDAY => 'Ср',
        self::THURSDAY  => 'Чт',
        self::FRIDAY    => 'Пт',
        self::SATURDAY  => 'Сб',
        self::SUNDAY    => 'Вс',
    ];

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Event", inversedBy="timetables")
     * @ORM\JoinColumn(nullable=false)
     */
    private $event;

    /**
     * @ORM\Column(type="smallint")
     */
    private $weekDay;

    /**
     * @ORM\Column(type="time")
     */
    private $timeFrom;

    /**
     * @ORM\Column(type="time")
     */
    private $timeTo;

    public function __construct(
        Event $event,
        int $weekDay,
        \DateTime $timeFrom,
        \DateTime $timeTo
    ) {
        $this->setWeekDay($weekDay);

        $this->timeFrom = TimeInterval::time($timeFrom);
        $this->timeTo   = TimeInterval::time($timeTo);

        $this->event = $event;
        $event->addTimetable($this);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEvent(): Event
    {
        return $this->event;
    }

    public function getTimeFrom(): \DateTime
    {
        return TimeInterval::time($this->timeFrom);
    }

    public function getTimeTo(): \DateTime
    {
        return TimeInterval::time($this->timeTo);
    }

    public function getWeekDay(): int
    {
        return $this->weekDay;
    }

    private function setWeekDay(int $weekDay): self
    {
        Assert::keyExists(self::WEEK_DAYS, $weekDay);

        $this->weekDay = $weekDay;

        return $this;
    }
}
