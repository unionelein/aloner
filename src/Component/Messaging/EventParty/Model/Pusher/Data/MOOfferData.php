<?php declare(strict_types=1);

namespace App\Component\Messaging\EventParty\Model\Pusher\Data;

use App\Component\Messaging\EventParty\Pusher;
use App\Component\Util\Date;
use App\Component\Util\Month;
use App\Entity\EPOfferMOHistory;
use App\Entity\EventParty;
use App\Entity\EPHistory;
use App\Entity\User;
use App\Repository\UserRepository;

class MOOfferData extends PusherData
{
    /** @var User */
    private $user;

    /** @var EPOfferMOHistory */
    private $offer;

    /**
     * @param User       $user
     * @param EventParty $eventParty
     * @param EPHistory  $offer
     */
    public function __construct(User $user, EventParty $eventParty, EPOfferMOHistory $offer)
    {
        $this->user       = $user;
        $this->offer      = $offer;

        parent::__construct(PusherData::TYPE_MO_OFFER, (string) $eventParty->getId());
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        $data = $this->offer->getData();

        return [
            'userId'       => $this->user->getId(),
            'offerId'      => $this->offer->getId(),
            'meetingAt'    => Date::rusFormat($data->getMeetingAt()),
            'meetingPlace' => $data->getMeetingPlace(),
        ];
    }
}
