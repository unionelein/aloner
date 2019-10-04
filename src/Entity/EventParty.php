<?php declare(strict_types=1);

namespace App\Entity;

use App\Component\EventParty\AgeChecker;
use App\Component\EventParty\EventTimeChecker;
use App\Component\Model\DTO\EventPartyHistory\JoinHistory;
use App\Component\Model\DTO\EventPartyHistory\EmptyDataHistory;
use App\Component\Model\VO\TimeInterval;
use App\Component\Util\Date;
use App\Entity\VO\EPComposition;
use App\Entity\VO\MeetingOptions;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;

/**
 * @ORM\Table(name="event_party")
 * @ORM\Entity(repositoryClass="App\Repository\EventPartyRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 */
class EventParty
{
    use SoftDeleteableEntity;

    public const STATUS_PENDING = 1;

    public const STATUS_PLANING = 2;

    public const STATUS_READY = 3;

    public const STATUS_REVIEWS = 4;

    public const STATUS_DONE = 5;

    private const STATUSES = [
        self::STATUS_PENDING => 'Ожидаем еще {{ N }} человек',
        self::STATUS_PLANING => 'Заполнение плана',
        self::STATUS_READY   => 'Все! Встречаетесь {{ DATE_TIME }}, {{ PLACE }}. Приятно провести время! И не забудьте потом зайти сюда и поделиться впечатлениями',
        self::STATUS_REVIEWS => 'Заполнение карты симпатий',
        self::STATUS_DONE    => 'Окончено',
    ];

    // offset before event for default meeting point
    private const MEETING_TIME_OFFSET = 10;

    /**
     * @var int
     *
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer", name="ep_id")
     */
    private $id;

    /**
     * @var Event
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Event")
     * @ORM\JoinColumn(name="ep_event_id", nullable=false)
     */
    private $event;

    /**
     * @var ArrayCollection|User[]
     *
     * @ORM\ManyToMany(targetEntity="App\Entity\User", inversedBy="eventParties")
     * @ORM\JoinTable(name="ep_user_map",
     *     joinColumns={@ORM\JoinColumn(name="epu_ep_id", referencedColumnName="ep_id", onDelete="CASCADE")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="epu_user_id", referencedColumnName="user_id", onDelete="CASCADE")}
     * )
     */
    private $users;

    /**
     * @var int
     *
     * @ORM\Column(type="integer", name="ep_status")
     */
    private $status = self::STATUS_PENDING;

    /**
     * @var EPComposition
     *
     * @ORM\Embedded(class="Composition", columnPrefix="ep_")
     */
    private $composition;

    /**
     * @var MeetingOptions
     *
     * @ORM\Embedded(class="App\Entity\VO\MeetingOptions", columnPrefix="ep_")
     */
    private $meetingOptions;

    /**
     * @var EventPartyHistory[]|ArrayCollection
     * @ORM\OneToMany(targetEntity="App\Entity\EventPartyHistory", cascade={"persist","remove"}, mappedBy="eventParty")
     */
    private $histories;

    /**
     * @var EventPartyMessage[]|ArrayCollection
     * @ORM\OneToMany(targetEntity="App\Entity\EventPartyMessage", cascade={"persist","remove"}, mappedBy="eventParty")
     * @ORM\OrderBy({"createdAt"="ASC"})
     */
    private $messages;

    /**
     * @param Event         $event
     * @param EPComposition $epComposition
     */
    public function __construct(Event $event, EPComposition $epComposition)
    {
        $this->users     = new ArrayCollection();
        $this->histories = new ArrayCollection();
        $this->messages  = new ArrayCollection();

        $this->event       = $event;
        $this->composition = $epComposition;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return ArrayCollection|User[]
     */
    public function getUsers(): ArrayCollection
    {
        return $this->users;
    }

    /**
     * @return Event
     */
    public function getEvent(): Event
    {
        return $this->event;
    }

    /**
     * @return int
     */
    public function getStatus(): int
    {
        return $this->status;
    }

    /**
     * @return ArrayCollection|EventPartyHistory[]
     */
    public function getHistories(): ArrayCollection
    {
        return $this->histories;
    }

    /**
     * @return EPComposition
     */
    public function getComposition(): EPComposition
    {
        return $this->composition;
    }

    /**
     * @return MeetingOptions
     */
    public function getMeetingOptions(): MeetingOptions
    {
        return $this->meetingOptions;
    }

    /**
     * @param MeetingOptions $meetingOptions
     *
     * @return EventParty
     */
    public function setMeetingOptions(MeetingOptions $meetingOptions): self
    {
        $this->meetingOptions = $meetingOptions;

        return $this;
    }

    /**
     * @return ArrayCollection|EventPartyMessage[]
     */
    public function getMessages(): ArrayCollection
    {
        return $this->messages;
    }

    /**
     * @return bool
     */
    public function isPlaning(): bool
    {
        return self::STATUS_PLANING === $this->status;
    }

    /**
     * @return bool
     */
    public function isReady(): bool
    {
        return self::STATUS_READY === $this->status;
    }

    /**
     * @return bool
     */
    public function isReviews(): bool
    {
        return self::STATUS_REVIEWS === $this->status;
    }

    /**
     * @return bool
     */
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
            throw new \LogicException('Unable to add user to event party');
        }

        $this->users[] = $user;

        $historyData = new JoinHistory($this->createNicknameForUser($user));
        $this->addHistory(new EventPartyHistory($this, $user, EventPartyHistory::ACTION_JOIN, $historyData));

        if ($this->isFilled()) {
            $this->status = self::STATUS_PLANING;
        }

        return $this;
    }

    public function findFirstUser(): ?User
    {
        return $this->users->first();
    }

    public function removeUser(User $user): self
    {
        if ($this->isReviews() || $this->isDone()) {
            throw new \LogicException('User can not be removed from done event party');
        }

        if ($this->users->contains($user)) {
            $this->users->removeElement($user);
            $this->addHistory(new EventPartyHistory($this, $user, EventPartyHistory::ACTION_LEAVE, new EmptyDataHistory()));

            $this->revertAfterUserLeave();
        }

        return $this;
    }

    private function revertAfterUserLeave(): void
    {
        foreach ($this->histories as $history) {
            if (\in_array($history->getAction(), EventPartyHistory::PLAN_ACTIONS, true)) {
                $history->delete();
            }
        }

        $this->status       = self::STATUS_PENDING;
        $this->meetingPlace = null;
        $this->meetingAt    = null;

        if ($this->users->count() === 0) {
            $this->setDeletedAt(new \DateTime());
        }
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

        if (!$this->findAvailableTimetable($user)) {
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

        $searchDay   = Date::date($date);
        $selectedDay = $this->getUsersSearchCriteriaDate();

        return $searchDay == $selectedDay;
    }

    public function getUsersSearchCriteriaDate(): ?\DateTime
    {
        if ($this->users->count() === 0) {
            return null;
        }

        return $this->findFirstUser()->getSearchCriteria()->getDay();
    }

    public function findAvailableTimetable(User $user = null): ?Timetable
    {
        $user = $user ?? $this->findFirstUser();

        if (!$user) {
            return null;
        }

        return EventTimeChecker::findAvailableEventTimetableForUser(
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
            $userTimeFrom = $user->getSearchCriteria()->getTimeFrom();
            $userTimeTo   = $user->getSearchCriteria()->getTimeTo();

            if ($userTimeFrom > $timeFrom) {
                $timeFrom = $userTimeFrom;
            }

            if ($userTimeTo < $timeTo) {
                $timeTo = $userTimeTo;
            }
        }

        return new TimeInterval($timeFrom, $timeTo);
    }

    public function generateMeetingAt(): ?\DateTime
    {
        if ($this->getMeetingAt()) {
            return $this->getMeetingAt();
        }

        $availableTimetable = $this->findAvailableTimetable();

        if (!$availableTimetable) {
            return null;
        }

        $offset = self::MEETING_TIME_OFFSET;
        $time   = $availableTimetable->getTimeFrom();

        if ($this->getEvent()->getTimetableType() === Event::TIMETABLE_TYPE_DAY) {
            $time = $this->getUsersTimeInterval()->getFrom() > $availableTimetable->getTimeFrom()
                ? $this->getUsersTimeInterval()->getFrom()
                : $availableTimetable->getTimeFrom();
        }

        $time = $time->modify("-{$offset} min");
        $day  = $this->getUsersSearchCriteriaDate();

        return $day->modify($time->format('H:i:s'));
    }

    public function generateEventTimeInterval(\DateTime $meetingDateTime): ?TimeInterval
    {
        if (!$this->findFirstUser()) {
            throw new \LogicException('Unable to generate event time interval without any users in event party');
        }

        if (!$this->getEvent()->getDuration()) {
            return null;
        }

        $availableTimetable = EventTimeChecker::findAvailableEventTimetableForUser(
            $this->findFirstUser(),
            $this->getEvent(),
            new TimeInterval($meetingDateTime, $this->getUsersTimeInterval()->getTo()),
            (int) $meetingDateTime->format('w')
        );

        if (!$availableTimetable) {
            return null;
        }

        $offset = self::MEETING_TIME_OFFSET;

        if ($this->getEvent()->getTimetableType() === Event::TIMETABLE_TYPE_DAY) {
            $start = TimeInterval::time($meetingDateTime) > $availableTimetable->getTimeFrom()
                ? (clone $meetingDateTime)->modify("+{$offset} min")
                : $availableTimetable->getTimeFrom();

            $end = (clone $start)->modify("+{$this->getEvent()->getDuration()} min");

            return new TimeInterval($start, $end);
        }

        return new TimeInterval($availableTimetable->getTimeFrom(), $availableTimetable->getTimeTo());
    }

    public function getNumberOfPeople(): int
    {
        return $this->getNumberOfGuys() + $this->getNumberOfGirls();
    }

    public function getPeopleRemaining(): int
    {
        return $this->getNumberOfPeople() - $this->users->count();
    }

    public function getCurrentNumberOfGirls(): int
    {
        $girls = \array_filter($this->getUsers()->toArray(), function (User $user) {
            return $user->getSex()->isFemale();
        });

        return \count($girls);
    }

    public function getCurrentNumberOfGuys(): int
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

    public function getMessageHistoryFor(User $user)
    {
        $historyJoin = $user->getLastEPHistoryFor($this, EventPartyHistory::ACTION_JOIN);

        if (!$historyJoin) {
            return [];
        }

        return $this->messages->filter(static function(EventPartyMessage $message) use ($historyJoin) {
            return $message->getCreatedAt() > $historyJoin->getCreatedAt();
        });
    }

    public function addMessage(EventPartyMessage $message)
    {
        if (!$this->messages->contains($message)) {
            $this->messages[] = $message;
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

            case self::STATUS_READY:
                $meetingAtString = Date::convertDateToString($this->meetingAt) . ' ' . $this->meetingAt->format('H:i');

                $title = \str_replace('{{ PLACE }}', $this->meetingPlace, self::STATUSES[self::STATUS_READY]);
                $title = \str_replace('{{ DATE_TIME }}', $meetingAtString, $title);

                return $title;

            case self::STATUS_REVIEWS:
                return self::STATUSES[self::STATUS_REVIEWS];

            case self::STATUS_DONE:
                return self::STATUSES[self::STATUS_DONE];

            default:
                return 'Event status not found';
        }
    }

    /**
     * @return EventPartyHistory[]
     */
    public function getOffers(): array
    {
        $offerActions = [EventPartyHistory::ACTION_MEETING_POINT_OFFER];

        $offers = [];
        foreach ($this->histories as $history) {
            if (\in_array($history->getAction(), $offerActions, true)) {
                $offers[] = $history;
            }
        }

        return $offers;
    }

    /**
     * @param User $user
     * @return EventPartyHistory[]
     */
    public function getActiveOffers(User $user = null): array
    {
        $answerActions = [EventPartyHistory::ACTION_ANSWER_TO_MEETING_POINT_OFFER];

        $answeredIds = [];
        foreach ($this->histories as $history) {
            $isAnswer = \in_array($history->getAction(), $answerActions, true);

            if ($isAnswer && $history->getUser() === $user) {
                $answeredIds[] = $history->getData()->getOfferId();
            }
        }

        $offers = [];
        foreach ($this->getOffers() as $offer) {
            $isAnswered = \in_array($offer->getId(), $answeredIds, true);

            if (!$isAnswered && $offer->getUser() !== $user) {
                $offers[] = $offer;
            }
        }

        return $offers;
    }

    /**
     * @return EventPartyHistory[]
     */
    public function getAnswersForOffer(EventPartyHistory $offer)
    {
        $answerActions = [EventPartyHistory::ACTION_ANSWER_TO_MEETING_POINT_OFFER];

        $answers = [];
        foreach ($this->histories as $history) {
            if (\in_array($history->getAction(), $answerActions, true)
                && $history->getData()->getOfferId() === $offer->getId()
            ) {
                $answers[] = $history;
            }
        }

        return $answers;
    }

    public function getAcceptedOfferUsers(EventPartyHistory $offer): array
    {
        $answers = $this->getAnswersForOffer($offer);

        $acceptedUsers = [];

        if ($this->users->contains($offer->getUser())) {
            $acceptedUsers[] = $offer->getUser();
        }

        foreach ($answers as $answer) {
            if ($this->users->contains($answer->getUser()) && $answer->getData()->getAnswer() === true) {
                $acceptedUsers[] = $answer->getUser();
            }
        }

        return $acceptedUsers;
    }

    public function isOfferAccepted(EventPartyHistory $offer): bool
    {
        return $this->users->count() === \count($this->getAcceptedOfferUsers($offer));
    }

    public function isOfferRejected(EventPartyHistory $offer): bool
    {
        $answers = $this->getAnswersForOffer($offer);

        foreach ($answers as $answer) {
            if ($answer->getData()->getAnswer() === false) {
                return true;
            }
        }

        return false;
    }
}
