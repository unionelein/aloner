<?php declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass="App\Repository\EventPartyMessageRepository")
 */
class EventPartyMessage
{
    public const MAX_MESSAGE_LENGTH = 500;

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
     * @ORM\Column(type="text")
     */
    private $message;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\EventParty", inversedBy="messages")
     * @ORM\JoinColumn(nullable=false)
     */
    private $eventParty;

    /**
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    public function __construct(User $user, EventParty $eventParty, string $message, \DateTime $createdAt = null)
    {
        $this->user = $user;
        $this->message = $message;
        $this->createdAt = $createdAt;
        $this->eventParty = $eventParty;

        $this->eventParty->addMessage($this);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getEventParty(): EventParty
    {
        return $this->eventParty;
    }

    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }
}
