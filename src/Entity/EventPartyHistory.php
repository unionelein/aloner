<?php

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
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
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

    public function __construct(EventParty $eventParty, User $user, int $action)
    {
        $this->eventParty = $eventParty;
        $this->user       = $user;
        $this->action     = $action;

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

    public function getCreatedAt(): ?\DateTimeInterface
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
}
