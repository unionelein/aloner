<?php declare(strict_types=1);

namespace App\Entity\VO\History;

use App\Component\Model\VO\TimeInterval;
use App\Component\Util\Date;
use App\Entity\VO\MeetingOptions;

class MOOfferData implements HistoryDataInterface
{
    /** @var string */
    private $meetingPlace;

    /** @var \DateTime */
    private $meetingAt;

    /**
     * MOOfferData constructor.
     *
     * @param MeetingOptions $MO
     */
    public function __construct(MeetingOptions $MO)
    {
        $this->meetingPlace = $MO->getMeetingPlace();
        $this->meetingAt    = $MO->getMeetingAt();
    }

    /**
     * @return string
     */
    public function getMeetingPlace(): string
    {
        return $this->meetingPlace;
    }

    /**
     * @return \DateTime
     */
    public function getMeetingAt(): \DateTime
    {
        return $this->meetingAt;
    }

    /**
     * @return MOOfferData
     */
    public static function fromArray(array $data): HistoryDataInterface
    {
        $date  = new \DateTime($data['meetingAt']['date']);
        $place = $data['meetingPlace'];

        return new self(new MeetingOptions($date, $place));
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return \get_object_vars($this);
    }
}
