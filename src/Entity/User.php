<?php declare(strict_types=1);

namespace App\Entity;

use App\Component\Infrastructure\HashGenerator;
use App\Component\Infrastructure\ResourceLocator;
use App\Component\Util\Date;
use App\Entity\VO\SearchCriteria;
use App\Entity\VO\Sex;
use App\Entity\VO\VkExtension;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Webmozart\Assert\Assert as WebmozAssert;

/**
 * @ORM\Table(name="user")
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 */
class User implements UserInterface
{
    public const WEB_ID = 1;

    public const ROLE_PARTIAL_REG = 'ROLE_PARTIAL_REG';

    public const ROLE_FULL_REG = 'ROLE_FULL_REG';

    /**
     * @var int
     *
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer", name="user_id")
     */
    private $id;

    /**
     * @var string
     *
     * @Assert\NotBlank()
     * @ORM\Column(type="string", name="user_name", length=50)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(type="string", name="user_login", length=100, unique=true)
     * @Gedmo\Slug(fields={"name"})
     */
    private $login;

    /**
     * @var array<string>
     *
     * @ORM\Column(type="json", name="user_roles")
     */
    private $roles = [];

    /**
     * @var null|VkExtension
     *
     * @ORM\Embedded(class="App\Entity\VO\VkExtension", columnPrefix="user_")
     */
    private $vk;

    /**
     * @var null|Sex
     *
     * @Assert\NotNull()
     * @ORM\Embedded(class="App\Entity\VO\Sex", columnPrefix="user_")
     */
    private $sex;

    /**
     * @var null|City
     *
     * @Assert\NotBlank()
     * @ORM\ManyToOne(targetEntity="App\Entity\City", inversedBy="users")
     * @ORM\JoinColumn(name="user_city_id", referencedColumnName="city_id", nullable=true)
     */
    private $city;

    /**
     * @var null|\DateTime
     *
     * @Assert\NotBlank()
     * @ORM\Column(type="date", name="user_birthday", nullable=true)
     */
    private $birthday;

    /**
     * @var null|string
     *
     * @ORM\Column(type="string", name="user_avatar_path", length=255, nullable=true)
     */
    private $avatarPath;

    /**
     * @var null|SearchCriteria
     *
     * @ORM\Embedded(class="App\Entity\VO\SearchCriteria", columnPrefix="user_")
     */
    private $searchCriteria;

    /**
     * @var ArrayCollection|EventParty[]
     *
     * @ORM\ManyToMany(targetEntity="App\Entity\EventParty", mappedBy="users")
     * @ORM\OrderBy({"id"="DESC"})
     */
    private $eventParties;

    /**
     * @var ArrayCollection|EventPartyHistory[]
     *
     * @ORM\OneToMany(targetEntity="App\Entity\EventPartyHistory", mappedBy="user")
     * @ORM\OrderBy({"createdAt"="DESC"})
     */
    private $eventPartyHistories;

    /**
     * @var string
     *
     * @ORM\Column(type="string", name="user_temp_hash", length=50)
     */
    private $tempHash;

    /**
     * @param string $name
     */
    public function __construct(string $name)
    {
        $this->eventParties        = new ArrayCollection();
        $this->eventPartyHistories = new ArrayCollection();

        $this->name = $name;
        $this->updateTempHash();

        $this->addRole(self::ROLE_PARTIAL_REG);
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return bool
     */
    public function isWeb(): bool
    {
        return self::WEB_ID === $this->id;
    }

    /**
     * @return string
     */
    public function getLogin(): string
    {
        return $this->login;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return $this->login ?? $this->name . ' (unsaved)';
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;

        return \array_unique($roles);
    }

    /**
     * @param array $roles
     *
     * @return User
     */
    public function setRoles(array $roles): self
    {
        $this->roles = \array_unique($roles);

        return $this;
    }

    /**
     * @param string $role
     *
     * @return User
     */
    public function addRole(string $role): self
    {
        if (!\in_array($role, $this->roles, true)) {
            $this->roles[] = $role;
        }

        return $this;
    }

    /**
     * @param string $role
     *
     * @return bool
     */
    public function hasRole(string $role): bool
    {
        return \in_array($role, $this->roles, true);
    }

    /**
     * @see UserInterface
     */
    public function getPassword()
    {
        // not needed for apps that do not check user passwords
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed for apps that do not check user passwords
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return User
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return VkExtension|null
     */
    public function getVk(): ?VkExtension
    {
        return $this->vk;
    }

    /**
     * @param VkExtension $vk
     *
     * @return User
     */
    public function setVk(VkExtension $vk): self
    {
        $this->vk = $vk;

        return $this;
    }

    /**
     * @return Sex|null
     */
    public function getSex(): ?Sex
    {
        return $this->sex;
    }

    /**
     * @param Sex $sex
     *
     * @return User
     */
    public function setSex(Sex $sex): self
    {
        $this->sex = $sex;

        return $this;
    }

    /**
     * @return bool
     */
    public function isFilled(): bool
    {
        return $this->city
            && $this->birthday
            && $this->sex
            && $this->vk;
    }

    /**
     * @return City|null
     */
    public function getCity(): ?City
    {
        return $this->city;
    }

    /**
     * @param City $city
     *
     * @return User
     */
    public function setCity(City $city): self
    {
        $this->city = $city;

        return $this;
    }

    /**
     * @return \DateTime|null
     */
    public function getBirthday(): ?\DateTime
    {
        return $this->birthday;
    }

    /**
     * @param \DateTime $birthday
     *
     * @return User
     */
    public function setBirthday(\DateTime $birthday): self
    {
        $this->birthday = $birthday;

        return $this;
    }

    /**
     * @return ArrayCollection|EventParty[]
     */
    public function getEventParties(): ArrayCollection
    {
        return $this->eventParties;
    }

    /**
     * @return ArrayCollection|EventPartyHistory[]
     */
    public function getEventPartyHistories(): ArrayCollection
    {
        return $this->eventPartyHistories;
    }

    /**
     * @param EventPartyHistory $history
     *
     * @return User
     */
    public function addEventPartyHistory(EventPartyHistory $history): self
    {
        if (!$this->eventPartyHistories->contains($history)) {
            $this->eventPartyHistories[] = $history;
        }

        return $this;
    }

    /**
     * @param string $avatarPath
     *
     * @return User
     */
    public function setAvatarPath(string $avatarPath): self
    {
        $this->avatarPath = $avatarPath;

        return $this;
    }

    /**
     * @return string
     */
    public function getAvatarPath(): string
    {
        if ($this->avatarPath) {
            return $this->avatarPath;
        }

        if ($this->sex) {
            return ResourceLocator::getDefaultAvatarForSex($this->sex);
        }

        return ResourceLocator::getDefaultAvatar();
    }

    /**
     * @return SearchCriteria|null
     */
    public function getSearchCriteria(): ?SearchCriteria
    {
        return $this->searchCriteria;
    }

    /**
     * @param SearchCriteria $searchCriteria
     *
     * @return User
     */
    public function setSearchCriteria(SearchCriteria $searchCriteria): self
    {
        $this->searchCriteria = $searchCriteria;

        return $this;
    }

    /**
     * @return string
     */
    public function getTempHash(): string
    {
        return $this->tempHash;
    }

    /**
     * @return User
     */
    public function updateTempHash(): self
    {
        $this->tempHash = HashGenerator::createUnique();

        return $this;
    }

    /**
     * @return int
     */
    public function getAge(): int
    {
        WebmozAssert::notNull($this->birthday, "У пользователя #{$this->id} не выставлена дата рождения ");

        return (int) $this->birthday->diff(new \DateTime())->format('%y');
    }

    /**
     * @param EventParty $eventParty
     *
     * @return User
     */
    public function joinToEventParty(EventParty $eventParty): self
    {
        WebmozAssert::true($this->isFilled(), "Пользователь #{$this->id} не заполнил все данные");

        if (!$this->eventParties->contains($eventParty)) {
            $this->eventParties[] = $eventParty;
            $eventParty->addUser($this);
        }

        return $this;
    }

    /**
     * @return EventParty|null
     */
    public function findLastActiveEventParty(): ?EventParty
    {
        foreach ($this->getEventParties() as $eventParty) {
            if (!$eventParty->isDone()) {
                return $eventParty;
            }
        }

        return null;
    }

    /**
     * @return bool
     */
    public function hasActiveEventParty(): bool
    {
        return null !== $this->findLastActiveEventParty();
    }

    /**
     * @return ArrayCollection|EventParty[]
     */
    public function getSkippedEventParties(): ArrayCollection
    {
        $skipped = new ArrayCollection();

        foreach ($this->eventPartyHistories as $history) {
            if ($history->isSkipAction()) {
                $skipped[] = $history->getEventParty();
            }
        }

        return $skipped;
    }

    /**
     * @param \DateTime|null $day
     *
     * @return ArrayCollection|EventParty[]
     */
    public function getSkippedEvents(\DateTime $day = null): Collection
    {
        $day     = $day ? Date::date($day) : null;
        $nextDay = $day ? (clone $day)->modify('+1 day') : null;

        $events = new ArrayCollection();
        foreach ($this->eventPartyHistories as $history) {
            if (!$history->isSkipAction()) {
                continue;
            }

            if ($day && ($history->getCreatedAt() < $day || $history->getCreatedAt() > $nextDay)) {
                continue;
            }

            $events[] = $history->getEventParty()->getEvent();
        }

        return $events;
    }

    /**
     * @param EventParty $eventParty
     *
     * @return User
     */
    public function skipEventParty(EventParty $eventParty): self
    {
        if ($this->eventParties->contains($eventParty)) {
            $this->eventParties->removeElement($eventParty);
            $eventParty->removeUser($this);
        }

        return $this;
    }

    /**
     * @param EventParty $eventParty
     * @param int        $action
     *
     * @return EventPartyHistory|null
     */
    public function getLastEPHistoryFor(EventParty $eventParty, int $action): ?EventPartyHistory
    {
        foreach ($this->eventPartyHistories as $history) {
            if ($history->getEventParty() === $eventParty && $history->getAction() === $action) {
                return $history;
            }
        }

        return null;
    }

    /**
     * @param EventParty $eventParty
     *
     * @return string
     */
    public function getNicknameIn(EventParty $eventParty): string
    {
        if ($history = $this->getLastEPHistoryFor($eventParty, EventPartyHistory::ACTION_JOIN)) {
            return $history->getData()->getNickname() ?? $this->getName();
        }

        return $this->getName();
    }
}
