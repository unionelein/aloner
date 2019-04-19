<?php declare(strict_types=1);

namespace App\Entity;

use App\Component\EventParty\AgeChecker;
use App\Component\EventParty\EventTimeChecker;
use App\Component\Model\VO\TimeInterval;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\EventPartyRepository")
 */
class EventParty
{
    public const STATUS_PENDING = 1;

    public const STATUS_PLANING = 2;

    public const STATUS_READY = 3;

    public const STATUS_DONE = 4;

    private const STATUSES = [
        self::STATUS_PENDING => 'Ожидаем еще {{ N }} человек',
        self::STATUS_PLANING => 'Заполнение плана',
    ];

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var ArrayCollection|User[]
     *
     * @ORM\ManyToMany(targetEntity="App\Entity\User", inversedBy="eventParties")
     */
    private $users;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Event")
     * @ORM\JoinColumn(nullable=false)
     */
    private $event;

    /**
     * @ORM\Column(type="integer")
     */
    private $status;

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
     * @ORM\OneToMany(targetEntity="App\Entity\EventPartyHistory", cascade={"persist","remove"}, mappedBy="eventParty")
     */
    private $histories;

    public function __construct(Event $event, int $numberOfGuys, int $numberOfGirls)
    {
        $this->event         = $event;
        $this->numberOfGuys  = $numberOfGuys;
        $this->numberOfGirls = $numberOfGirls;

        $this->status = self::STATUS_PENDING;

        $this->users     = new ArrayCollection();
        $this->histories = new ArrayCollection();
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

    public function getEvent(): Event
    {
        return $this->event;
    }

    public function getStatus(): int
    {
        return $this->status;
    }

    /**
     * @return Collection|EventPartyHistory[]
     */
    public function getHistories(): Collection
    {
        return $this->histories;
    }

    public function getMeetingAt(): ?\DateTime
    {
        return $this->meetingAt;
    }

    public function setMeetingAt(?\DateTime $meetingAt): self
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

    public function getNumberOfGirls(): ?int
    {
        return $this->numberOfGirls;
    }

    public function getNumberOfGuys(): ?int
    {
        return $this->numberOfGuys;
    }

    public function isDone(): bool
    {
        return self::STATUS_DONE === $this->status;
    }

    public function addUser(User $user): self
    {
        if ($this->users->contains($user)) {
            return $this;
        }

        if (!$this->canUserJoin($user)) {
            throw new \LogicException('Невозможно добавить пользователя в евент пати');
        }

        $this->users[] = $user;

        $nickname = $this->createNicknameForUser($user);
        $this->addHistory(new EventPartyHistory($this, $user, EventPartyHistory::ACTION_JOIN, $nickname));

        if ($this->isFilled()) {
            $this->status = self::STATUS_PLANING;
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        if ($this->users->contains($user)) {
            $this->users->removeElement($user);
            $this->addHistory(new EventPartyHistory($this, $user, EventPartyHistory::ACTION_LEAVE));

            $this->status = self::STATUS_PENDING;
        }

        return $this;
    }

    public function canUserJoin(User $user): bool
    {
        if (!$this->hasSlotForUser($user)) {
            return false;
        }

        if (!AgeChecker::isAgeAcceptableFor($user->getAge(), $this->getUsersAge())) {
            return false;
        }

        if (!$this->isAppropriateDay($user->getSearchCriteria()->getDay())) {
            return false;
        }

        if (!$this->isAppropriateTimeForUser($user)) {
            return false;
        }

        return true;
    }

    public function hasSlotForUser(User $user): bool
    {
        $maxNumber     = $user->getSex()->isFemale() ? $this->getNumberOfGirls() : $this->getNumberOfGuys();
        $currentNumber = $user->getSex()->isFemale() ? $this->getCurrentNumberOfGirls() : $this->getCurrentNumberOfGuys();

        return $maxNumber > $currentNumber;
    }

    public function getUsersAge(): array
    {
        return \array_map(static function (User $user) {
            return $user->getAge();
        }, $this->users->toArray());
    }

    public function isAppropriateDay(\DateTime $date): bool
    {
        if ($this->users->count() === 0) {
            return true;
        }

        /** @var User $firstUser */
        $firstUser = $this->users->first();

        $searchDay   = $date->format('d');
        $selectedDay = $firstUser->getSearchCriteria()->getDay()->format('d');

        return $searchDay === $selectedDay;
    }

    public function isAppropriateTimeForUser(User $user): bool
    {
        return EventTimeChecker::isEventTimeAppropriateForUser(
            $user,
            $this->getEvent(),
            $this->getUsersTimeInterval()
        );
    }

    /**
     * If in party already exists user with same name we need to add sequence to user name
     */
    private function createNicknameForUser(User $user): string
    {
        $existingNames = [];
        foreach ($this->users as $epUser) {
            if ($epUser !== $user) {
                $existingNames[] = \strtolower($epUser->getName());
            }
        }

        if (!\in_array(\strtolower($user->getName()), $existingNames, true)) {
            return $user->getName();
        }

        $i = 2;
        do {
            $nickname = "{$user->getName()} {$i}";
            ++$i;
        } while (\in_array(\strtolower($nickname), $existingNames, true));

        return $nickname;
    }

    public function getUsersTimeInterval(): TimeInterval
    {
        $timeFrom = TimeInterval::timeDayStart();
        $timeTo   = TimeInterval::timeDayEnd();

        foreach ($this->users as $user) {
            $userTimeFrom = TimeInterval::time($user->getSearchCriteria()->getTimeFrom());
            $userTimeTo   = TimeInterval::time($user->getSearchCriteria()->getTimeTo());

            if ($userTimeFrom > $timeFrom) {
                $timeFrom = $userTimeFrom;
            }

            if ($userTimeTo < $timeTo) {
                $timeTo = $userTimeTo;
            }
        }

        return new TimeInterval($timeFrom, $timeTo);
    }

    public function getNumberOfPeople(): int
    {
        return $this->getNumberOfGuys() + $this->getNumberOfGirls();
    }

    public function getPeopleRemaining(): int
    {
        return $this->getNumberOfPeople() - $this->users->count();
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

    public function addHistory(EventPartyHistory $history): self
    {
        if (!$this->histories->contains($history)) {
            $this->histories[] = $history;
        }

        return $this;
    }

    public function getCurrentStatusTitle(): string
    {
        switch ($this->status) {
            case self::STATUS_PENDING:
                $desc  = self::STATUSES[self::STATUS_PENDING];
                $title = \str_replace('{{ N }}', $this->getPeopleRemaining(), $desc);

                // ожидаем 1 человекА
                if ($this->getPeopleRemaining() < 5) {
                    $title .= 'а';
                }

                return $title . " ({$this->users->count()}/{$this->getNumberOfPeople()})";

            case self::STATUS_PLANING:
                return self::STATUSES[self::STATUS_PLANING];

            default:
                return 'Event status not found';
        }
    }
}
