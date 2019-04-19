<?php declare(strict_types=1);

namespace App\Entity;

use App\Component\Model\VO\TimeInterval;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\SearchCriteriaRepository")
 */
class SearchCriteria
{
    public const ALLOWED_MINS_OFFSET = 30;

    public const DEFAULT_TIME_FROM = '18:00';

    public const DEFAULT_TIME_TO = '23:00';

    use TimestampableEntity;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="time", nullable=false)
     */
    private $timeFrom;

    /**
     * @ORM\Column(type="time", nullable=false)
     */
    private $timeTo;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\User", inversedBy="searchCriteria", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $day;

    public function __construct(User $user)
    {
        $this->user = $user;

        $this->day = new \DateTime();

        $this->timeFrom = TimeInterval::time(new \DateTime(self::DEFAULT_TIME_FROM));
        $this->timeTo   = TimeInterval::time(new \DateTime(self::DEFAULT_TIME_TO));
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTimeFrom(): \DateTime
    {
        return TimeInterval::time($this->timeFrom);
    }

    public function setTimeFrom(\DateTime $timeFrom): self
    {
        $this->timeFrom = TimeInterval::time($timeFrom);

        return $this;
    }

    public function getTimeTo(): \DateTime
    {
        return TimeInterval::time($this->timeTo);
    }

    public function setTimeTo(\DateTime $timeTo): self
    {
        $this->timeTo = TimeInterval::time($timeTo);

        return $this;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getDay(): \DateTime
    {
        return $this->day;
    }

    public function setDay(\DateTime $day): self
    {
        $this->day = $day;

        return $this;
    }

    public function getTimeInterval(): TimeInterval
    {
        return new TimeInterval($this->timeFrom, $this->timeTo);
    }
}
