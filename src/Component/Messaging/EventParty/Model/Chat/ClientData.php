<?php declare(strict_types=1);

namespace App\Component\Messaging\EventParty\Model\Chat;

use App\Entity\User;

class ClientData
{
    /** @var int */
    private $userId;

    /** @var string */
    private $username;

    /** @var int */
    private $eventPartyId;

    public function __construct(int $userId, string $username, int $eventPartyId)
    {
        $this->userId = $userId;
        $this->username = $username;
        $this->eventPartyId = $eventPartyId;
    }

    public static function extract(User $user): self
    {
        return new self(
            $user->getId(),
            $user->getNicknameFor($user->getActiveEventParty()),
            $user->getActiveEventParty()->getId()
        );
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
}
