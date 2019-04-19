<?php declare(strict_types=1);

namespace App\Component\Messaging\EventParty\Model\Pusher;

use App\Component\Messaging\EventParty\Pusher;
use App\Entity\EventParty;
use App\Entity\User;

class JoinData extends AbstractPusherData
{
    /** @var User */
    private $user;

    /** @var EventParty */
    private $eventParty;

    public function __construct(User $user, EventParty $eventParty)
    {
        parent::__construct(Pusher::TYPE_JOIN);

        $this->user = $user;
        $this->eventParty = $eventParty;
    }

    public function getTopicId(): string
    {
        return (string) $this->eventParty->getId();
    }

    public function toArray(): array
    {
        return [
            'userId'           => $this->user->getId(),
            'avatarPath'       => $this->user->getAvatarPath(),
            'nickName'         => $this->user->getNicknameIn($this->eventParty),
            'eventPartyStatus' => $this->eventParty->getCurrentStatusTitle(),
        ];
    }
}
