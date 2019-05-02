<?php declare(strict_types=1);

namespace App\Component\Model\DTO\EventPartyHistory;

use App\Component\Model\VO\TimeInterval;
use App\Component\Util\Date;

class MeetingPointOfferHistory implements HistoryDataInterface
{
    /** @var string */
    private $meetingPlace;

    /** @var \DateTime */
    private $meetingDateTime;

    /** @var \DateTime */
    private $eventTimeStart;

    /** @var \DateTime */
    private $eventTimeEnd;

    public function __construct(string $meetingPlace, \DateTime $meetingDateTime, ?TimeInterval $eventTimeInterval)
    {
        $this->meetingPlace = $meetingPlace;
        $this->meetingDateTime = $meetingDateTime;

        if ($eventTimeInterval) {
            $this->eventTimeStart = $eventTimeInterval->getFrom();
            $this->eventTimeEnd   = $eventTimeInterval->getTo();
        }
    }

    /**
     * @return MeetingPointOfferHistory
     */
    public static function fromArray(array $data): HistoryDataInterface
    {
        $start = $data['eventTimeStart']['date'] ?? null;
        $end   = $data['eventTimeEnd']['date'] ?? null;

        $timeInterval = $start && $end ? new TimeInterval(new \DateTime($start), new \DateTime($end)) : null;

        return new self($data['meetingPlace'], new \DateTime($data['meetingDateTime']['date']), $timeInterval);
    }

    public function getMeetingPlace(): string
    {
        return $this->meetingPlace;
    }

    public function getMeetingDateTime(): \DateTime
    {
        return $this->meetingDateTime;
    }

    public function getEventTimeStart(): ?\DateTime
    {
        return $this->eventTimeStart;
    }

    public function getEventTimeEnd(): ?\DateTime
    {
        return $this->eventTimeEnd;
    }

    public function toArray(): array
    {
        return \get_object_vars($this);
    }
}