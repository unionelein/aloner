<?php declare(strict_types=1);

namespace App\Component\Model\DTO\Form;

class MeetingPointData
{
    /** @var string */
    private $place;

    /** @var \DateTime */
    private $day;

    /** @var \DateTime */
    private $time;

    public function getPlace(): ?string
    {
        return $this->place;
    }

    public function setPlace(?string $place): void
    {
        $this->place = $place;
    }

    public function getDay(): ?\DateTime
    {
        return $this->day;
    }

    public function setDay(?\DateTime $day): void
    {
        $this->day = $day;
    }

    public function getTime(): ?\DateTime
    {
        return $this->time;
    }

    public function setTime(?\DateTime $time): void
    {
        $this->time = $time;
    }
}