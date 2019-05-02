<?php declare(strict_types=1);

namespace App\Component\Messaging\EventParty\Model\Pusher;

use App\Component\Messaging\EventParty\Pusher;
use App\Component\Util\Date;
use App\Entity\EventParty;
use App\Entity\EventPartyHistory;
use App\Entity\User;
use App\Repository\UserRepository;

class MeetingPointOfferData extends AbstractPusherData
{
    /** @var User */
    private $user;

    /** @var EventParty */
    private $eventParty;

    /** @var EventPartyHistory */
    private $offer;

    public function __construct(User $user, EventParty $eventParty, EventPartyHistory $offer)
    {
        parent::__construct(Pusher::TYPE_MEETING_POINT_OFFER);

        $this->user = $user;
        $this->offer = $offer;
        $this->eventParty = $eventParty;
    }

    public function getTopicId(): string
    {
        return (string) $this->eventParty->getId();
    }

    public function toArray(): array
    {
        return [
            'userId'  => $this->user->getId(),
            'offerId' => $this->offer->getId(),
            'lines'   => $this->offer->generateOfferLines(),
        ];
    }
}