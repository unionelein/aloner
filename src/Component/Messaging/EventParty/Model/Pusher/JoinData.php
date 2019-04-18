<?php declare(strict_types=1);

namespace App\Component\Messaging\EventParty\Model\Pusher;

use App\Component\Messaging\EventParty\Pusher;
use App\Entity\EventParty;
use App\Entity\User;

class JoinData extends AbstractPusherData
{
    private $epId;

    public function __construct(User $user, EventParty $eventParty)
    {
        parent::__construct(Pusher::TYPE_JOIN);

        $this->epId = $eventParty->getId();
    }

    public function getTopicId(): string
    {
        return (string) $this->epId;
    }

    public function toArray(): array
    {
        return [
            'join' => 1,
        ];
    }
}
