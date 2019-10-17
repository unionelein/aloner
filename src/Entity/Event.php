<?php declare(strict_types=1);

namespace App\Entity;

use App\Component\Util\Week;
use App\Entity\VO\Contacts;
use App\Entity\VO\Range;
use App\Entity\VO\SearchCriteria;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Gedmo\Mapping\Annotation as Gedmo;
use Webmozart\Assert\Assert as WebmozAssert;

/**
 * @ORM\Table(name="event")
 * @ORM\Entity(repositoryClass="App\Repository\EventRepository")
 */
class Event
{
    use TimestampableEntity;

    /**
     * @var int
     *
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer", name="event_id")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string", name="event_name", length=255)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(type="text", name="event_desc", nullable=true)
     */
    private $description;

    /**
     * @var Contacts
     *
     * @ORM\Embedded(class="App\Entity\VO\Contacts", columnPrefix="event_")
     */
    private $contacts;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", name="event_reservation_req")
     */
    private $reservationRequired = false;

    /**
     * @var Range
     *
     * @ORM\Embedded(class="App\Entity\VO\Range", columnPrefix="event_people_")
     */
    private $peopleRange;

    /**
     * Contains unit
     *
     * @var string
     *
     * @ORM\Column(type="string", name="event_price", length=20, nullable=true)
     */
    private $price;

    /**
     * Duration of event in minutes
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="event_duration")
     */
    private $duration;

    /**
     * @var ArrayCollection|Timetable[]
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Timetable", mappedBy="event", cascade={"persist", "remove"})
     */
    private $timetables;

    /**
     * @var Media[]
     *
     * @ORM\ManyToMany(targetEntity="App\Entity\Media", cascade={"persist"})
     * @ORM\JoinTable(name="event_media_map",
     *     joinColumns={@ORM\JoinColumn(name="emp_event_id", referencedColumnName="event_id", onDelete="CASCADE")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="emp_media_id", referencedColumnName="media_id", onDelete="CASCADE")}
     * )
     */
    private $media;

    /**
     * @param string   $name
     * @param int      $duration    Duration of event in minutes.
     * @param Contacts $contacts
     * @param Range    $peopleRange Min and max count of people.
     */
    public function __construct(string $name, int $duration, Contacts $contacts, Range $peopleRange)
    {
        $this->timetables = new ArrayCollection();
        $this->media      = new ArrayCollection();

        $this->name        = $name;
        $this->duration    = $duration;
        $this->contacts    = $contacts;
        $this->peopleRange = $peopleRange;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param string $description
     *
     * @return Event
     */
    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return Contacts
     */
    public function getContacts(): Contacts
    {
        return $this->contacts;
    }

    /**
     * @return bool
     */
    public function isReservationRequired(): bool
    {
        return $this->reservationRequired;
    }

    /**
     * @param bool $reservationRequired
     *
     * @return Event
     */
    public function setReservationRequired(bool $reservationRequired): self
    {
        $this->reservationRequired = $reservationRequired;

        return $this;
    }

    /**
     * @return Range
     */
    public function getPeopleRange(): Range
    {
        return $this->peopleRange;
    }

    /**
     * @return string|null
     */
    public function getPrice(): ?string
    {
        return $this->price;
    }

    /**
     * @param string $price
     *
     * @return Event
     */
    public function setPrice(string $price): self
    {
        $this->price = $price;

        return $this;
    }

    /**
     * @param null|int $weekDay
     *
     * @return ArrayCollection|Timetable[]
     */
    public function getTimetables(int $weekDay = null): ArrayCollection
    {
        if (null === $weekDay) {
            return $this->timetables;
        }

        WebmozAssert::keyExists(Week::DAYS, $weekDay);

        return $this->timetables->filter(function (Timetable $timetable) use ($weekDay) {
            return $timetable->getWeekDay() === $weekDay;
        });
    }

    /**
     * @param Timetable $timetable
     *
     * @return Event
     */
    public function addTimetable(Timetable $timetable): self
    {
        if (!$this->timetables->contains($timetable)) {
            $this->timetables[] = $timetable;
        }

        return $this;
    }

    /**
     * @param Timetable $timetable
     *
     * @return Event
     */
    public function removeTimetable(Timetable $timetable): self
    {
        if ($this->timetables->contains($timetable)) {
            $this->timetables->removeElement($timetable);
        }

        return $this;
    }

    /**
     * @return int
     */
    public function getDuration(): int
    {
        return $this->duration;
    }

    /**
     * @return ArrayCollection|Media[]
     */
    public function getMedia(): ArrayCollection
    {
        return $this->media;
    }

    /**
     * @param Media $media
     *
     * @return Event
     */
    public function addMedia(Media $media): self
    {
        if (!$this->media->contains($media)) {
            $this->media[] = $media;
        }

        return $this;
    }

    /**
     * @param Media $media
     *
     * @return Event
     */
    public function removeMedia(Media $media): self
    {
        if ($this->media->contains($media)) {
            $this->media->removeElement($media);
        }

        return $this;
    }

    /**
     * @param SearchCriteria $searchCriteria
     *
     * @return Timetable|null
     */
    public function findAvailableTimetableForSC(SearchCriteria $searchCriteria): ?Timetable
    {
        $dayTimetables = $this->getTimetables(Week::weekDay($searchCriteria->getDay()));

        foreach ($dayTimetables as $timetable) {
            $startTime = $timetable->getTimeFrom();

            if ($searchCriteria->getTimeFrom() >= $startTime || $searchCriteria->getTimeTo() < $startTime) {
                continue;
            }

            $availableTime = $searchCriteria->getTimeTo()->diff($timetable->getTimeFrom());
            $availableMins = $availableTime->h * 60 + $availableTime->i;

            if ($availableMins > $this->duration/2) {
                return $timetable;
            }
        }

        return null;
    }
}
