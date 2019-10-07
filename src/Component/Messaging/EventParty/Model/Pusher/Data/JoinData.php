<?php declare(strict_types=1);

namespace App\Component\Messaging\EventParty\Model\Pusher\Data;

use App\Component\Messaging\EventParty\Pusher;
use App\Entity\EventParty;
use App\Entity\User;

class JoinData extends PusherData
{
    /** @var User */
    private $user;

    /** @var EventParty */
    private $eventParty;

    /**
     * @param User       $user
     * @param EventParty $eventParty
     */
    public function __construct(User $user, EventParty $eventParty)
    {
        $this->user       = $user;
        $this->eventParty = $eventParty;

        parent::__construct(PusherData::TYPE_JOIN, (string) $eventParty->getId());
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'userId'      => $this->user->getId(),
            'avatarPath'  => $this->user->getAvatarPath(),
            'nickname'    => $this->user->getNicknameIn($this->eventParty),
            'statusTitle' => EventParty::STATUSES[$this->eventParty->getStatus()],
        ];
    }
}
