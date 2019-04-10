<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
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

    public const MONDAY = 1;

    public const TUESDAY = 2;

    public const WEDNESDAY = 3;

    public const THURSDAY = 4;

    public const FRIDAY = 5;

    public const SATURDAY = 6;

    public const SUNDAY = 7;

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

    public function __construct(
        Event $event,
        int $weekDay,
        \DateTimeInterface $timeFrom,
        \DateTimeInterface $timeTo,
        int $type
    ) {
        $this->setType($type);
        $this->setWeekDay($weekDay);

        $this->event    = $event;
        $this->timeFrom = $timeFrom;
        $this->timeTo   = $timeTo;

        $event->addTimetable($this);
    }

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

    /**
     * @ORM\Column(type="smallint")
     */
    private $type;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEvent(): Event
    {
        return $this->event;
    }

    public function getTimeFrom(): \DateTimeInterface
    {
        return $this->timeFrom;
    }

    public function getTimeTo(): \DateTimeInterface
    {
        return $this->timeTo;
    }

    public function getType(): int
    {
        return $this->type;
    }

    public function getWeekDay(): int
    {
        return $this->weekDay;
    }

    private function setType(int $type): self
    {
        if (!\in_array($type, self::TYPES, true)) {
            throw new \InvalidArgumentException('Такого типа времени работы не сущуствует');
        }

        $this->type = $type;

        return $this;
    }

    private function setWeekDay(int $weekDay): self
    {
        if (!\array_key_exists($weekDay, self::WEEK_DAYS)) {
            throw new \InvalidArgumentException('Такого дня недели нет');
        }

        $this->weekDay = $weekDay;

        return $this;
    }
}
