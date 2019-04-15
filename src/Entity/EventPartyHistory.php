<?php declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass="App\Repository\EventPartyHistoryRepository")
 */
class EventPartyHistory
{
    public const ACTION_JOIN = 1;

    public const ACTION_LEAVE = 2;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="eventPartyHistories")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\EventParty", inversedBy="histories")
     * @ORM\JoinColumn(nullable=false)
     */
    private $eventParty;

    /**
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="smallint")
     */
    private $action;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $nickname;

    public function __construct(EventParty $eventParty, User $user, int $action, ?string $nickname = null)
    {
        $this->eventParty = $eventParty;
        $this->user       = $user;
        $this->action     = $action;
        $this->nickname   = $nickname;

        $eventParty->addHistory($this);
        $user->addEventPartyHistory($this);

    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getEventParty(): EventParty
    {
        return $this->eventParty;
    }

    public function getCreatedAt(): ?\DateTime
    {
        return $this->createdAt;
    }

    public function getAction(): int
    {
        return $this->action;
    }

    public function isActionJoin(): bool
    {
        return $this->action === self::ACTION_JOIN;
    }

    public function isActionSkip(): bool
    {
        return $this->action === self::ACTION_LEAVE;
    }

    public function getNickname(): string
    {
        return $this->nickname ?? $this->user->getName();
    }
}
