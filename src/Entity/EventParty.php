<?php declare(strict_types=1);

namespace App\Entity;

use App\Component\Model\VO\TimeInterval;
use App\Component\Util\Date;
use App\Entity\VO\History\JoinData;
use App\Entity\VO\PeopleComposition;
use App\Entity\VO\MeetingOptions;
use App\Entity\VO\SearchCriteria;
use App\Entity\VO\Sex;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;
use Webmozart\Assert\Assert as WebmozAssert;
use Webmozart\Assert\Assert;

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

    public const STATUSES = [
        self::STATUS_PENDING => 'Ожидаем участников',
        self::STATUS_PLANING => 'Заполнение плана',
        self::STATUS_READY   => 'Время и место встречи выбраны. Встречайтесь, веселитесь и возвращайтесь, чтобы поделиться эмоциями',
        self::STATUS_REVIEWS => 'Заполнение карты симпатий',
        self::STATUS_DONE    => 'Окончено',
    ];

    public const AGE_GROUPS = [
        [15, 17],
        [18, 22],
        [23, 28],
        [29, 36],
        [37, 45],
        [46, 55],
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
     * @ORM\JoinColumn(name="ep_event_id", referencedColumnName="event_id", nullable=false)
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
     * @var PeopleComposition
     *
     * @ORM\Embedded(class="App\Entity\VO\PeopleComposition", columnPrefix="ep_")
     */
    private $peopleComposition;

    /**
     * @var MeetingOptions
     *
     * @ORM\Embedded(class="App\Entity\VO\MeetingOptions", columnPrefix="ep_")
     */
    private $meetingOptions;

    /**
     * @var EPHistory[]|ArrayCollection
     * @ORM\OneToMany(targetEntity="EPHistory", cascade={"persist","remove"}, mappedBy="eventParty")
     */
    private $histories;

    /**
     * @var EPMessage[]|ArrayCollection
     * @ORM\OneToMany(targetEntity="EPMessage", cascade={"persist","remove"}, mappedBy="eventParty")
     * @ORM\OrderBy({"createdAt"="ASC"})
     */
    private $messages;

    /**
     * @param Event             $event
     * @param PeopleComposition $composition
     */
    public function __construct(Event $event, PeopleComposition $composition)
    {
        Assert::greaterThanEq($composition->getNumberOfPeople(), $event->getPeopleRange()->getMin());
        Assert::lessThanEq($composition->getNumberOfPeople(), $event->getPeopleRange()->getMax());

        $this->users     = new ArrayCollection();
        $this->histories = new ArrayCollection();
        $this->messages  = new ArrayCollection();

        $this->event             = $event;
        $this->peopleComposition = $composition;
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

    public function markAsReviews(): void
    {
        $this->status = self::STATUS_REVIEWS;
    }

    /**
     * @return ArrayCollection|EPHistory[]
     */
    public function getHistories(): ArrayCollection
    {
        return $this->histories;
    }

    /**
     * @return PeopleComposition
     */
    public function getPeopleComposition(): PeopleComposition
    {
        return $this->peopleComposition;
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

        if ($this->isPlaning()) {
            $this->status = self::STATUS_READY;
        }

        return $this;
    }

    /**
     * @return ArrayCollection|EPMessage[]
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

    /**
     * @return int
     */
    public function getPeopleRemaining(): int
    {
        return $this->peopleComposition->getNumberOfPeople() - $this->users->count();
    }

    public function canUserJoin(User $user): bool
    {
        return $this->hasSlotForUser($user)
            && $this->isValidAgeGroup($user->getAge())
            && $this->isValidSearchCriteria($user->getSearchCriteria());
    }

    /**
     * @param User $user
     *
     * @return bool
     */
    public function hasSlotForUser(User $user): bool
    {
        $sex = $user->getSex();
        WebmozAssert::notNull($sex);

        return $this->peopleComposition->getNumberOf($sex) > $this->getCurrentNumberOf($sex);
    }

    /**
     * @param Sex $sex
     *
     * @return int
     */
    public function getCurrentNumberOf(Sex $sex): int
    {
        return $this->users->filter(function (User $user) use ($sex) {
            return null !== $user->getSex() && $user->getSex()->toValue() === $sex->toValue();
        })->count();
    }

    /**
     * @param int $age
     *
     * @return bool
     */
    public function isValidAgeGroup(int $age): bool
    {
        $memberAge = $this->users->count() > 0 ? $this->users->first()->getAge() : null;

        foreach (self::AGE_GROUPS as [$min, $max]) {
            if ($age >= $min && $age <= $max) {
                return null === $memberAge || ($memberAge >= $min || $memberAge <= $max);
            }
        }

        return false;
    }

    /**
     * @param SearchCriteria $searchCriteria
     *
     * @return bool
     */
    public function isValidSearchCriteria(SearchCriteria $searchCriteria): bool
    {
        if (!($membersCriteria = $this->getUsersSearchCriteria())) {
            return true;
        }

        if ($membersCriteria->getDay() != $searchCriteria->getDay()) {
            return false;
        }

        $commonSearchCriteria = new SearchCriteria(
            $membersCriteria->getDay(),
            \max($membersCriteria->getTimeFrom(), $searchCriteria->getTimeFrom()),
            \min($membersCriteria->getTimeTo(), $searchCriteria->getTimeTo())
        );

        if ($commonSearchCriteria->getTimeFrom() > $commonSearchCriteria->getTimeTo()) {
            return false;
        }

        return null !==$this->event->findAvailableTimetableForSC($commonSearchCriteria);
    }

    /**
     * @return SearchCriteria|null
     */
    public function getUsersSearchCriteria(): ?SearchCriteria
    {
        if (0 === $this->users->count()) {
            return null;
        }

        /** @var SearchCriteria $membersCriteria */
        $membersCriteria = $this->users->first()->getSearchCriteria();
        foreach ($this->users as $user) {
            /** @var SearchCriteria $userCriteria */
            $userCriteria = $user->getSearchCriteria();

            if ($userCriteria->getTimeFrom() > $membersCriteria->getTimeFrom()) {
                $membersCriteria->setTimeFrom($userCriteria->getTimeFrom());
            }

            if ($userCriteria->getTimeTo() < $membersCriteria->getTimeTo()) {
                $membersCriteria->setTimeTo($userCriteria->getTimeTo());
            }

            // don't check day bcs it must be same
        }

        return $membersCriteria;
    }

    /**
     * @param User $user
     *
     * @return EventParty
     */
    public function addUser(User $user): self
    {
        if ($this->users->contains($user)) {
            return $this;
        }

        if (!$this->canUserJoin($user)) {
            throw new \LogicException('Unable to add user to event party');
        }

        $this->users[] = $user;
        $this->addHistory(new EPJoinHistory($this, $user, new JoinData($this->userNickname($user))));

        if ($this->isFilled()) {
            $this->status = self::STATUS_PLANING;
        }

        return $this;
    }

    /**
     * If in party already exists user with same name
     * we need to add sequence to user name
     *
     * @param User $user
     *
     * @return string
     */
    private function userNickname(User $user): string
    {
        /** @var null|EPJoinHistory $joinHistory */
        if ($joinHistory = $user->getLastEPHistoryFor($this, EPHistory::ACTION_JOIN)) {
            return $joinHistory->getData()->getNickname();
        }

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

    /**
     * @param User $user
     *
     * @return EventParty
     */
    public function removeUser(User $user): self
    {
        if (!$this->users->contains($user)) {
            return $this;
        }

        if ($this->isReviews() || $this->isDone()) {
            throw new \LogicException('User can not be removed from done event party');
        }

        $this->users->removeElement($user);
        $this->addHistory(new EPLeaveHistory($this, $user));

        $this->status         = self::STATUS_PENDING;
        $this->meetingOptions = null;
        $this->resetOfferHistories();

        if ($this->users->count() === 0) {
            $this->setDeletedAt(new \DateTime());
        }

        return $this;
    }

    private function resetOfferHistories(): void
    {
        foreach ($this->histories as $history) {
            if ($history instanceof EPOfferMOHistory || $history instanceof EPAnswerMOHistory) {
                $history->delete();
                $this->removeHistory($history);
            }
        }
    }

    /**
     * @return bool
     */
    public function isFilled(): bool
    {
        return $this->users->count() === $this->peopleComposition->getNumberOfPeople();
    }

    /**
     * @param EPHistory $history
     *
     * @return EventParty
     */
    public function addHistory(EPHistory $history): self
    {
        if (!$this->histories->contains($history)) {
            $this->histories[] = $history;
        }

        return $this;
    }

    /**
     * @param EPHistory $history
     *
     * @return EventParty
     */
    public function removeHistory(EPHistory $history): self
    {
        if ($this->histories->contains($history)) {
            $this->histories->removeElement($history);
        }

        return $this;
    }

    /**
     * @param User $user
     *
     * @return ArrayCollection|EPMessage
     */
    public function getMessagesFor(User $user): ArrayCollection
    {
        $historyJoin = $user->getLastEPHistoryFor($this, EPHistory::ACTION_JOIN);

        if (!$historyJoin) {
            return new ArrayCollection();
        }

        return $this->messages->filter(static function(EPMessage $message) use ($historyJoin) {
            return $message->getCreatedAt() > $historyJoin->getCreatedAt();
        });
    }

    /**
     * @param EPMessage $message
     *
     * @return EventParty
     */
    public function addMessage(EPMessage $message): self
    {
        if (!$this->messages->contains($message)) {
            $this->messages[] = $message;
        }

        return $this;
    }

    /**
     * @return EPHistory[]
     */
    public function getOffers(): array
    {
        $offerActions = [EPHistory::ACTION_MO_OFFER];

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
     *
     * @return EPHistory[]
     */
    public function getActiveOffers(User $user = null): array
    {
        $answerActions = [EPHistory::ACTION_MO_ANSWER];

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
     * @return EPHistory[]
     */
    public function getAnswersForOffer(EPHistory $offer)
    {
        $answerActions = [EPHistory::ACTION_MO_ANSWER];

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

    public function getAcceptedOfferUsers(EPHistory $offer): array
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

    public function isOfferRejected(EPHistory $offer): bool
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
