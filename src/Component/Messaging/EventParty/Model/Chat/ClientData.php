<?php declare(strict_types=1);

namespace App\Component\Messaging\EventParty\Model\Chat;

use App\Entity\EventParty;
use App\Entity\User;
use Ratchet\ConnectionInterface;

class ClientData
{
    /** @var int */
    private $userId;

    /** @var string */
    private $username;

    /** @var int */
    private $eventPartyId;

    /** @var ConnectionInterface */
    private $connection;

    public function __construct(ConnectionInterface $connection, User $user, EventParty $eventParty)
    {
        $this->connection   = $connection;
        $this->userId       = $user->getId();
        $this->username     = $user->getNicknameIn($eventParty);
        $this->eventPartyId = $eventParty->getId();
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getEventPartyId(): int
    {
        return $this->eventPartyId;
    }

    public function getConnection(): ConnectionInterface
    {
        return $this->connection;
    }
}
