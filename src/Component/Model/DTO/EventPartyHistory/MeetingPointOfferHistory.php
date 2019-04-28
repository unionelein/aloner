<?php declare(strict_types=1);

namespace App\Component\Model\DTO\EventPartyHistory;

use App\Component\Util\Date;

class MeetingPointOfferHistory implements HistoryDataInterface
{
    /** @var string */
    private $place;

    /** @var \DateTime */
    private $day;

    /** @var \DateTime */
    private $time;

    public function __construct(string $place, \DateTime $day, \DateTime $time)
    {
        $this->place = $place;
        $this->day = $day;
        $this->time = $time;
    }

    /**
     * @return MeetingPointOfferHistory
     */
    public static function fromArray(array $data): HistoryDataInterface
    {
        return new self($data['place'], new \DateTime($data['day']['date']), new \DateTime($data['time']['date']));
    }

    public function getPlace(): string
    {
        return $this->place;
    }

    public function getDay(): \DateTime
    {
        return $this->day;
    }

    public function getTime(): \DateTime
    {
        return $this->time;
    }

    public function meetingDateTimeString(): string
    {
        return \sprintf('%s, %s', Date::convertDateToString($this->day), $this->time->format('H:i'));
    }

    public function toArray(): array
    {
        return \get_object_vars($this);
    }
}