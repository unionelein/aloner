<?php

namespace App\Entity;

use App\Component\VO\Sex;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\EventPartyRepository")
 */
class EventParty
{
    public const STATUS_PENDING = 1;

    public const STATUS_PREPARATION = 2;

    public const STATUS_READY = 3;

    public const STATUS_DONE = 4;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\User", inversedBy="eventParties")
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

    public function __construct(Event $event)
    {
        $this->event = $event;

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
        if ($this->users->contains($user)) {
            return $this;
        }
        
        if (!$this->canAddUser($user)) {
            throw new \LogicException('Невозможно добавить пользователя в евент пати');
        }

        $this->users[] = $user;
        
        if ($this->isFilled()) {
            $this->status = self::STATUS_PREPARATION;
        }

        return $this;
    }

    public function canAddUser(User $user): bool
    {
        $maxNumber     = $user->getSex()->isFemale() ? $this->getNumberOfGirls() : $this->getNumberOfGuys();
        $currentNumber = $user->getSex()->isFemale() ? $this->getCurrentNumberOfGirls() : $this->getCurrentNumberOfGuys();

        if ($currentNumber >= $maxNumber) {
            return false;
        }
        
        return true;
    }

    public function removeUser(User $user): self
    {
        if ($this->users->contains($user)) {
            $this->users->removeElement($user);
            $this->status = self::STATUS_PENDING;
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
        return $this->meetingAt === null || $this->meetingAt > new \DateTime();
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

    public function getCurrentNumberOfGirls()
    {
        $girls = \array_filter($this->getUsers()->toArray(), function (User $user) {
            return $user->getSex()->isFemale();
        });

        return \count($girls);
    }

    public function getCurrentNumberOfGuys()
    {
        $guys = \array_filter($this->getUsers()->toArray(), function (User $user) {
            return $user->getSex()->isMale();
        });

        return \count($guys);
    }

    public function isFilled(): bool
    {
        return $this->getCurrentNumberOfGuys() === $this->getNumberOfGuys()
            && $this->getCurrentNumberOfGirls() === $this->getNumberOfGirls();
    }

    public function getStatus(): ?int
    {
        return $this->status;
    }
}
