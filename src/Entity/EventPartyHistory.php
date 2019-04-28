<?php declare(strict_types=1);

namespace App\Entity;

use App\Component\Model\DTO\EventPartyHistory\HistoryDataInterface;
use App\Component\Model\DTO\EventPartyHistory\JoinHistory;
use App\Component\Model\DTO\EventPartyHistory\LeaveHistory;
use App\Component\Model\DTO\EventPartyHistory\MeetingPointOfferAnswerHistory;
use App\Component\Model\DTO\EventPartyHistory\MeetingPointOfferHistory;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass="App\Repository\EventPartyHistoryRepository")
 */
class EventPartyHistory
{
    public const STATUS_DELETED = 0;

    public const STATUS_ACTIVE = 1;

    public const STATUSES = [
        self::STATUS_DELETED,
        self::STATUS_ACTIVE,
    ];

    public const ACTION_JOIN = 1;

    public const ACTION_LEAVE = 2;

    public const ACTION_MEETING_POINT_OFFER = 3;

    public const ACTION_MEETING_POINT_OFFER_ANSWER = 4;

    public const ACTIONS = [
        self::ACTION_JOIN,
        self::ACTION_LEAVE,
        self::ACTION_MEETING_POINT_OFFER,
        self::ACTION_MEETING_POINT_OFFER_ANSWER,
    ];

    public const PLAN_ACTIONS = [
        self::ACTION_MEETING_POINT_OFFER,
        self::ACTION_MEETING_POINT_OFFER_ANSWER,
    ];

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
     * @ORM\Column(type="smallint")
     */
    private $status;

    /**
     * @ORM\Column(type="json")
     */
    private $data;

    public function __construct(EventParty $eventParty, User $user, int $action, HistoryDataInterface $data)
    {
        $this->status = self::STATUS_ACTIVE;
        $this->eventParty = $eventParty;
        $this->user       = $user;
        $this->setAction($action);
        $this->setData($data);

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

    public function markAsDeleted(): void
    {
        $this->status = self::STATUS_DELETED;
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

    /**
     * @return JoinHistory|LeaveHistory|MeetingPointOfferHistory|MeetingPointOfferAnswerHistory
     */
    public function getData(): HistoryDataInterface
    {
        $data = (array) $this->data;

        switch ($this->action) {
            case self::ACTION_JOIN:
                return JoinHistory::fromArray($data);

            case self::ACTION_LEAVE:
                return LeaveHistory::fromArray($data);

            case self::ACTION_MEETING_POINT_OFFER:
                return MeetingPointOfferHistory::fromArray($data);

            case self::ACTION_MEETING_POINT_OFFER_ANSWER:
                return MeetingPointOfferAnswerHistory::fromArray($data);

            default:
                throw new \LogicException('Invalid action stored');
        }
    }

    private function setAction(int $action): void
    {
        if (!\in_array($action, self::ACTIONS, true)) {
            throw new \InvalidArgumentException('Invalid action given');
        }

        $this->action = $action;
    }

    private function setData(HistoryDataInterface $historyData)
    {
        if ((self::ACTION_JOIN === $this->action && !$historyData instanceof JoinHistory)
            || (self::ACTION_LEAVE === $this->action && !$historyData instanceof LeaveHistory)
            || (self::ACTION_MEETING_POINT_OFFER === $this->action && !$historyData instanceof MeetingPointOfferHistory)
        ) {
            throw new \InvalidArgumentException('Invalid data object given');
        }

        $this->data = $historyData->toArray();
    }
}
