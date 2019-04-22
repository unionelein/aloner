<?php declare(strict_types=1);

namespace App\Component\Messaging\EventParty\Model\Pusher;

use App\Component\Messaging\EventParty\Pusher;
use App\Entity\EventParty;
use App\Entity\User;

class PlaceOfferData extends AbstractPusherData
{
    /** @var User */
    private $user;

    /** @var EventParty */
    private $eventParty;

    /** @var string */
    private $meetingPlace;

    public function __construct(User $user, EventParty $eventParty, string $meetingPlace)
    {
        parent::__construct(Pusher::TYPE_PLACE_OFFER);

        $this->user = $user;
        $this->eventParty = $eventParty;
        $this->meetingPlace = $meetingPlace;
    }

    public function getTopicId(): string
    {
        return (string) $this->eventParty->getId();
    }

    public function toArray(): array
    {
        return [
            'userId'       => $this->user->getId(),
            'meetingPlace' => $this->meetingPlace,
        ];
    }
}