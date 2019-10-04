<?php declare(strict_types=1);

namespace App\Entity;

use App\Component\Model\DTO\EventPartyHistory\HistoryDataInterface;
use App\Component\Model\DTO\EventPartyHistory\JoinHistory;
use App\Component\Model\DTO\EventPartyHistory\EmptyDataHistory;
use App\Component\Model\DTO\EventPartyHistory\AnswerToMeetingPointOfferHistory;
use App\Component\Model\DTO\EventPartyHistory\MeetingPointOfferHistory;
use App\Component\Util\Date;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;
use Webmozart\Assert\Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\EventPartyHistoryRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 */
class EventPartyHistory
{
    use SoftDeleteableEntity;

    public const ACTION_JOIN = 1;

    public const ACTION_LEAVE = 2;

    public const ACTION_MEETING_POINT_OFFER = 3;

    public const ACTION_ANSWER_TO_MEETING_POINT_OFFER = 4;

    public const ACTIONS = [
        self::ACTION_JOIN,
        self::ACTION_LEAVE,
        self::ACTION_MEETING_POINT_OFFER,
        self::ACTION_ANSWER_TO_MEETING_POINT_OFFER,
    ];

    public const PLAN_ACTIONS = [
        self::ACTION_MEETING_POINT_OFFER,
        self::ACTION_ANSWER_TO_MEETING_POINT_OFFER,
    ];

    private const ACTION_DATA_CLASSES = [
        self::ACTION_JOIN                          => JoinHistory::class,
        self::ACTION_LEAVE                         => EmptyDataHistory::class,
        self::ACTION_MEETING_POINT_OFFER           => MeetingPointOfferHistory::class,
        self::ACTION_ANSWER_TO_MEETING_POINT_OFFER => AnswerToMeetingPointOfferHistory::class,
    ];

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="eventPartyHistories")
     * @ORM\JoinColumn(nullable=true)
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
     * @ORM\Column(type="json")
     */
    private $data;

    public function __construct(EventParty $eventParty, User $user, int $action, HistoryDataInterface $data)
    {
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

    public function getUser(): ?User
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

    public function isJoinAction(): bool
    {
        return $this->action === self::ACTION_JOIN;
    }

    public function isSkipAction(): bool
    {
        return $this->action === self::ACTION_LEAVE;
    }

    public function delete(): self
    {
        $this->setDeletedAt(new \DateTime());

        return $this;
    }

    /**
     * @return JoinHistory|EmptyDataHistory|MeetingPointOfferHistory|AnswerToMeetingPointOfferHistory
     */
    public function getData(): HistoryDataInterface
    {
        $dataClass = self::ACTION_DATA_CLASSES[$this->action];

        return $dataClass::fromArray((array) $this->data);
    }

    public function generateOfferLines(): array
    {
        if (self::ACTION_MEETING_POINT_OFFER !== $this->action) {
            return [];
        }

        $lines = [];

        if (!$this->user->isWeb()) {
            $lines[] = 'Тут предложили встретиться в другое время/месте:';
        }

        $offerData = $this->getData();

        $timeStart = $offerData->getEventTimeStart();
        $timeEnd   = $offerData->getEventTimeEnd();

        $durationInterval = $timeStart && $timeEnd ?
            "(на {$timeStart->format('H:i')}-{$timeEnd->format('H:i')})"
            : null;

        $lines['point'] = \sprintf('Встречаемся %s в %s %s - %s',
            Date::convertDateToString($offerData->getMeetingDateTime()),
            $offerData->getMeetingDateTime()->format('H:i'),
            $durationInterval,
            $offerData->getMeetingPlace()
        );

        return $lines;
    }

    private function setAction(int $action): void
    {
        Assert::oneOf($action, self::ACTIONS);

        $this->action = $action;
    }

    private function setData(HistoryDataInterface $historyData)
    {
        Assert::notNull($this->action);

        $requiredClass = self::ACTION_DATA_CLASSES[$this->action];
        $givenClass    = \get_class($historyData);

        Assert::eq($givenClass, $requiredClass);

        $this->data = \json_decode(\json_encode($historyData->toArray()), true);
    }
}
