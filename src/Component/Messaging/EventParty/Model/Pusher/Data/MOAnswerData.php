<?php declare(strict_types=1);

namespace App\Component\Messaging\EventParty\Model\Pusher\Data;

use App\Component\Util\Date;
use App\Entity\EPAnswerMOHistory;
use App\Entity\EventParty;
use App\Entity\User;

class MOAnswerData extends PusherData
{
    /** @var User */
    private $user;

    /** @var EPAnswerMOHistory */
    private $answer;

    /**
     * @param User              $user
     * @param EventParty        $eventParty
     * @param EPAnswerMOHistory $answer
     */
    public function __construct(User $user, EventParty $eventParty, EPAnswerMOHistory $answer)
    {
        $this->user   = $user;
        $this->answer = $answer;

        parent::__construct(PusherData::TYPE_MO_ANSWER, (string) $eventParty->getId());
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        $data = $this->answer->getData();

        $arr = [
            'userId'  => $this->user->getId(),
            'offerId' => $this->answer->getOfferHistory()->getId(),
            'answer'  => $data->getAnswer(),
        ];

        if (false === $data->getAnswer()) {
            $arr['newMeetingAt']    = Date::rusFormat($data->getNewMeetingAt());
            $arr['newMeetingPlace'] = $data->getNewMeetingPlace();
        }

        return $arr;
    }
}
