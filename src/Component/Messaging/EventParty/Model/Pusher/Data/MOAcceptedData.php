<?php declare(strict_types=1);

namespace App\Component\Messaging\EventParty\Model\Pusher\Data;

use App\Component\Messaging\EventParty\Pusher;
use App\Component\Util\Date;
use App\Entity\EventParty;
use App\Entity\VO\MeetingOptions;

class MOAcceptedData extends PusherData
{
    /** @var EventParty */
    private $eventParty;

    /** @var MeetingOptions */
    private $meetingOptions;

    /**
     * @param EventParty     $eventParty
     * @param MeetingOptions $MO
     */
    public function __construct(EventParty $eventParty, MeetingOptions $MO)
    {
        $this->eventParty     = $eventParty;
        $this->meetingOptions = $MO;

        parent::__construct(PusherData::TYPE_MO_ACCEPTED, (string) $eventParty->getId());
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'statusTitle'  => EventParty::STATUSES[$this->eventParty->getStatus()],
            'meetingPlace' => $this->meetingOptions->getMeetingPlace(),
            'meetingAt'    => Date::rusFormat($this->meetingOptions->getMeetingAt()),
        ];
    }
}
