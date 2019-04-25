<?php declare(strict_types=1);

namespace App\Component\Messaging\EventParty\Model\Pusher;

use App\Component\Messaging\EventParty\Pusher;
use App\Component\Util\Date;
use App\Entity\EventParty;
use App\Entity\User;

class MeetingPointOfferData extends AbstractPusherData
{
    /** @var User */
    private $user;

    /** @var EventParty */
    private $eventParty;

    /** @var string */
    private $place;

    /** @var \DateTime */
    private $meetingDateTime;

    public function __construct(User $user, EventParty $eventParty, string $place, \DateTime $meetingDateTime)
    {
        parent::__construct(Pusher::TYPE_MEETING_POINT_OFFER);

        $this->user = $user;
        $this->eventParty = $eventParty;
        $this->place = $place;
        $this->meetingDateTime = $meetingDateTime;
    }

    public function getTopicId(): string
    {
        return (string) $this->eventParty->getId();
    }

    public function toArray(): array
    {
        return [
            'userId'                => $this->user->getId(),
            'place'                 => $this->place,
            'meetingDateTimeString' => \sprintf('%s, %s',
                Date::convertDateToString($this->meetingDateTime),
                $this->meetingDateTime->format('H:i')
            ),
        ];
    }
}