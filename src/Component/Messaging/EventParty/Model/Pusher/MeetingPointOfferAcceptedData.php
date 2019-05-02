<?php declare(strict_types=1);

namespace App\Component\Messaging\EventParty\Model\Pusher;

use App\Component\Messaging\EventParty\Pusher;
use App\Component\Util\Date;
use App\Entity\EventParty;

class MeetingPointOfferAcceptedData extends AbstractPusherData
{
    /** @var EventParty */
    private $eventParty;

    public function __construct(EventParty $eventParty)
    {
        parent::__construct(Pusher::TYPE_MEETING_POINT_OFFER_ACCEPTED);

        $this->eventParty = $eventParty;
    }

    public function getTopicId(): string
    {
        return (string) $this->eventParty->getId();
    }

    public function toArray(): array
    {
        return [
            'epStatus' => $this->eventParty->getCurrentStatusTitle(),
            'place' => $this->eventParty->getMeetingPlace(),
            'meetingDateTimeString' => \sprintf('%s в %s',
                Date::convertDateToString($this->eventParty->getMeetingAt()),
                $this->eventParty->getMeetingAt()->format('H:i')
            ),
        ];
    }
}