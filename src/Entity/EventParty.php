<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\EventPartyRepository")
 */
class EventParty
{
    public const STATUS_PENDING = 1;

    public const STATUS_READY = 2;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\User", inversedBy="eventParty")
     */
    private $users;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Event")
     * @ORM\JoinColumn(nullable=false)
     */
    private $event;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $meetingAt;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $meetingPoint;

    /**
     * @ORM\Column(type="integer")
     */
    private $numberOfGirls;

    /**
     * @ORM\Column(type="integer")
     */
    private $numberOfGuys;

    /**
     * @ORM\Column(type="integer")
     */
    private $status;

    public function __construct()
    {
        $this->status = self::STATUS_PENDING;
        $this->users  = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection|User[]
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users[] = $user;
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        if ($this->users->contains($user)) {
            $this->users->removeElement($user);
        }

        return $this;
    }

    public function getEvent(): ?Event
    {
        return $this->event;
    }

    public function setEvent(?Event $event): self
    {
        $this->event = $event;

        return $this;
    }

    public function getMeetingAt(): ?\DateTimeInterface
    {
        return $this->meetingAt;
    }

    public function setMeetingAt(?\DateTimeInterface $meetingAt): self
    {
        $this->meetingAt = $meetingAt;

        return $this;
    }

    public function getMeetingPoint(): ?string
    {
        return $this->meetingPoint;
    }

    public function setMeetingPoint(?string $meetingPoint): self
    {
        $this->meetingPoint = $meetingPoint;

        return $this;
    }

    public function isActive(): bool
    {
        return $this->meetingAt > new \DateTime();
    }

    public function getNumberOfGirls(): ?int
    {
        return $this->numberOfGirls;
    }

    public function setNumberOfGirls(int $numberOfGirls): self
    {
        $this->numberOfGirls = $numberOfGirls;

        return $this;
    }

    public function getNumberOfGuys(): ?int
    {
        return $this->numberOfGuys;
    }

    public function setNumberOfGuys(int $numberOfGuys): self
    {
        $this->numberOfGuys = $numberOfGuys;

        return $this;
    }

    public function getNumberOfPeople(): int
    {
        return $this->getNumberOfGuys() + $this->getNumberOfGirls();
    }

    public function getStatus(): ?int
    {
        return $this->status;
    }

    public function setStatus(int $status): self
    {
        $this->status = $status;

        return $this;
    }
}
