<?php declare(strict_types=1);

namespace App\Entity;

use App\Component\Model\Collection\TimetableCollection;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass="App\Repository\EventRepository")
 */
class Event
{
    use TimestampableEntity;

    public const TIMETABLE_TYPE_VISIT = 1;

    public const TIMETABLE_TYPE_DAY = 2;

    public const TIMETABLE_TYPES = [
        self::TIMETABLE_TYPE_VISIT,
        self::TIMETABLE_TYPE_DAY,
    ];

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $title;

    /**
     * @ORM\Column(type="text")
     */
    private $description;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\City", inversedBy="events")
     * @ORM\JoinColumn(nullable=false)
     */
    private $city;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $address;

    /**
     * @ORM\Column(type="boolean")
     */
    private $reserveRequired;

    /**
     * @ORM\Column(type="smallint")
     */
    private $minNumberOfPeople;

    /**
     * @ORM\Column(type="smallint")
     */
    private $maxNumberOfPeople;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $site;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $price;

    /**
     * @ORM\Column(type="string", length=15, nullable=true)
     */
    private $phone;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $yandexMapSrc;

    /**
     * @var ArrayCollection|Timetable[]
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Timetable", mappedBy="event", cascade={"persist", "remove"})
     */
    private $timetables;

    /**
     * @ORM\Column(type="smallint", nullable=true)
     */
    private $timetableType;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $duration;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Media", cascade={"persist"})
     */
    private $media;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Cafe")
     */
    private $cafe;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $pathToCafeYandexMapSrc;

    public function __construct(string $title, string $description, City $city, string $address, bool $reserveRequired, int $minNumberOfPeople, int $maxNumberOfPeople)
    {
        $this->timetables = new ArrayCollection();
        $this->media      = new ArrayCollection();

        $this->title       = $title;
        $this->description = $description;

        $this->city            = $city;
        $this->address         = $address;
        $this->reserveRequired = $reserveRequired;

        $this->minNumberOfPeople = $minNumberOfPeople;
        $this->maxNumberOfPeople = $maxNumberOfPeople;

    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getCity(): City
    {
        return $this->city;
    }

    public function getAddress(): string
    {
        return $this->address;
    }

    public function isReserveRequired(): bool
    {
        return $this->reserveRequired;
    }

    public function getMinNumberOfPeople(): int
    {
        return $this->minNumberOfPeople;
    }

    public function getMaxNumberOfPeople(): int
    {
        return $this->maxNumberOfPeople;
    }

    public function getSite(): ?string
    {
        return $this->site;
    }

    public function setSite(?string $site): self
    {
        $this->site = $site;

        return $this;
    }

    public function getPrice(): ?string
    {
        return $this->price;
    }

    public function setPrice(?string $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getYandexMapSrc(): ?string
    {
        return $this->yandexMapSrc;
    }

    public function setYandexMapSrc(?string $yandexMapSrc): self
    {
        $this->yandexMapSrc = $yandexMapSrc;

        return $this;
    }

    public function getTimetables(): TimetableCollection
    {
        $timetableCollection = new TimetableCollection($this->timetableType);

        foreach ($this->timetables as $timetable) {
            $timetableCollection->add($timetable);
        }

        return $timetableCollection;
    }

    public function addTimetable(Timetable $timetable): self
    {
        if (!$this->timetables->contains($timetable)) {
            $this->timetables[] = $timetable;
        }

        return $this;
    }

    public function removeTimetable(Timetable $timetable): self
    {
        if ($this->timetables->contains($timetable)) {
            $this->timetables->removeElement($timetable);
        }

        return $this;
    }

    public function getTimetableType(): ?int
    {
        return $this->timetableType;
    }

    public function setTimetableType(?int $timetableType): self
    {
        $this->timetableType = $timetableType;

        return $this;
    }

    public function getDuration(): ?int
    {
        return $this->duration;
    }

    public function setDuration(?int $duration): self
    {
        $this->duration = $duration;

        return $this;
    }

    /**
     * @return ArrayCollection|Media[]
     */
    public function getMedia(): Collection
    {
        return $this->media;
    }

    public function addMedia(Media $media): self
    {
        if (!$this->media->contains($media)) {
            $this->media[] = $media;
        }

        return $this;
    }

    public function removeMedia(Media $media): self
    {
        if ($this->media->contains($media)) {
            $this->media->removeElement($media);
        }

        return $this;
    }

    public function getCafe(): ?Cafe
    {
        return $this->cafe;
    }

    public function setCafe(?Cafe $cafe): self
    {
        $this->cafe = $cafe;

        return $this;
    }

    public function getPathToCafeYandexMapSrc(): ?string
    {
        return $this->pathToCafeYandexMapSrc;
    }

    public function setPathToCafeYandexMapSrc(?string $pathToCafeYandexMapSrc): self
    {
        $this->pathToCafeYandexMapSrc = $pathToCafeYandexMapSrc;

        return $this;
    }
}
