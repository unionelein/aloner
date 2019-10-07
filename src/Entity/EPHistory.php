<?php declare(strict_types=1);

namespace App\Entity;

use App\Entity\VO\History\HistoryDataInterface;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;

/**
 * @ORM\Table(name="ep_history")
 * @ORM\Entity(repositoryClass="App\Repository\EPHistoryRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 */
class EPHistory
{
    use SoftDeleteableEntity;

    public const ACTION_JOIN = 1;

    public const ACTION_LEAVE = 2;

    public const ACTION_MO_OFFER = 3;

    public const ACTION_MO_ANSWER = 4;

    public const ACTIONS = [
        self::ACTION_JOIN,
        self::ACTION_LEAVE,
        self::ACTION_MO_OFFER,
        self::ACTION_MO_ANSWER,
    ];

    /**
     * @var int
     *
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer", name="history_id")
     */
    protected $id;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="eventPartyHistories")
     * @ORM\JoinColumn(name="history_user_id", referencedColumnName="user_id", nullable=false)
     */
    protected $user;

    /**
     * @var EventParty
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\EventParty", inversedBy="histories")
     * @ORM\JoinColumn(name="history_ep_id", referencedColumnName="ep_id", nullable=false)
     */
    protected $eventParty;

    /**
     * @var \DateTime
     *
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime", name="history_created_at")
     */
    protected $createdAt;

    /**
     * @var int
     *
     * @ORM\Column(type="smallint", name="history_action")
     */
    protected $action;

    /**
     * @var null|EPHistory
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\EPOfferMOHistory", inversedBy="an")
     * @ORM\JoinColumn(name="history_related_history_id", referencedColumnName="history_id", nullable=true)
     */
    protected $offerHistory;

    /**
     * @var HistoryDataInterface
     *
     * @ORM\Column(type="json", name="history_data")
     */
    protected $data;

    /**
     * @param EventParty $eventParty
     * @param User       $user
     */
    protected function __construct(EventParty $eventParty, User $user)
    {
        $this->eventParty = $eventParty;
        $this->user       = $user;

        $eventParty->addHistory($this);
        $user->addEventPartyHistory($this);
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
        return clone $this->createdAt;
    }

    /**
     * @return int
     */
    public function getAction(): int
    {
        return $this->action;
    }

    public function delete(): void
    {
        $this->setDeletedAt(new \DateTime());
    }
}
