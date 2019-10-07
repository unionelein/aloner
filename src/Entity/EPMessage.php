<?php declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Table(name="ep_message")
 * @ORM\Entity(repositoryClass="App\Repository\EPMessageRepository")
 */
class EPMessage
{
    public const MAX_MSG_LENGTH = 500;

    /**
     * @var int
     *
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer", name="message_id")
     */
    private $id;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(name="message_user_id", referencedColumnName="user_id", nullable=false)
     */
    private $user;

    /**
     * @var string
     *
     * @ORM\Column(type="text", name="message_message")
     */
    private $message;

    /**
     * @var EventParty
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\EventParty", inversedBy="messages")
     * @ORM\JoinColumn(name="message_ep_id", referencedColumnName="ep_id", nullable=false)
     */
    private $eventParty;

    /**
     * @var \DateTime
     *
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime", name="message_created_at")
     */
    private $createdAt;

    /**
     * @param User           $user
     * @param EventParty     $eventParty
     * @param string         $message
     * @param \DateTime|null $createdAt
     */
    public function __construct(User $user, EventParty $eventParty, string $message, \DateTime $createdAt = null)
    {
        $this->user = $user;
        $this->message = $message;
        $this->createdAt = $createdAt;
        $this->eventParty = $eventParty;

        $this->eventParty->addMessage($this);
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @return EventParty
     */
    public function getEventParty(): EventParty
    {
        return $this->eventParty;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }
}
