<?php declare(strict_types=1);

namespace App\Entity;

use App\Component\Model\Collection\TimetableCollection;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\EventRepository")
 */
class Event
{
    use TimestampableEntity;

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
     * @Gedmo\Slug(fields={"title"})
     * @ORM\Column(type="string", length=255)
     */
    private $slug;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\City", inversedBy="events")
     * @ORM\JoinColumn(nullable=false)
     */
    private $city;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $site;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $address;

    /**
     * @var ArrayCollection|Timetable[]
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Timetable", mappedBy="event", cascade={"persist", "remove"})
     */
    private $timetables;

    /**
     * @ORM\Column(type="string", length=15, nullable=true)
     */
    private $phone;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Media", cascade={"persist"})
     */
    private $media;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $priceText;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $yandexMapSrc;

    /**
     * @ORM\Column(type="smallint")
     */
    private $minNumberOfPeople;

    /**
     * @ORM\Column(type="smallint")
     */
    private $maxNumberOfPeople;

    public function __construct(string $title, City $city, int $minNumberOfPeople, int $maxNumberOfPeople)
    {
        $this->timetables = new ArrayCollection();
        $this->media = new ArrayCollection();

        $this->title = $title;
        $this->city  = $city;
        $this->minNumberOfPeople = $minNumberOfPeople;
        $this->maxNumberOfPeople = $maxNumberOfPeople;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function getCity(): ?City
    {
        return $this->city;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getSite(): ?string
    {
        return $this->site;
    }

    public function setSite(string $site): self
    {
        $this->site = $site;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(string $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getTimetables(): TimetableCollection
    {
        $timetableCollection = new TimetableCollection();

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

    public function getEventTimeLengths(\DateTime $day = null): array
    {
        $timetables = $day
            ? $this->getTimetables()->getForWeekDay((int) $day->format('w'))
            : $this->timetables->toArray();

        $lengths = \array_map(static function (Timetable $timetable) {
            return $timetable->getTimeLength();
        }, $timetables);

        return \array_unique($lengths);
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

    /**
     * @return Collection|Media[]
     */
    public function getMedia(): Collection
    {
        return $this->media;
    }

    public function addMedia(Media $medium): self
    {
        if (!$this->media->contains($medium)) {
            $this->media[] = $medium;
        }

        return $this;
    }

    public function removeMedia(Media $medium): self
    {
        if ($this->media->contains($medium)) {
            $this->media->removeElement($medium);
        }

        return $this;
    }

    public function getPriceText(): ?string
    {
        return $this->priceText;
    }

    public function setPriceText(?string $priceText): self
    {
        $this->priceText = $priceText;

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

    public function getMinNumberOfPeople(): int
    {
        return $this->minNumberOfPeople;
    }

    public function getMaxNumberOfPeople(): int
    {
        return $this->maxNumberOfPeople;
    }
}
