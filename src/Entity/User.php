<?php

namespace App\Entity;

use App\Component\App\Infrastructure\ResourceLocator;
use App\Component\Vk\DTO\AccessToken;
use App\Component\VO\Sex;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 */
class User implements UserInterface
{
    public const ROLE_PARTIAL_REG = 'ROLE_PARTIAL_REG';

    public const ROLE_FULL_REG = 'ROLE_FULL_REG';

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Assert\NotBlank()
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     * @Gedmo\Slug(fields={"name"})
     */
    private $login;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\VkUserToken", mappedBy="user", cascade={"persist", "remove"})
     */
    private $vkToken;

    /**
     * @Assert\NotNull()
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $sex;

    /**
     * @Assert\NotBlank()
     * @ORM\ManyToOne(targetEntity="App\Entity\City", inversedBy="users")
     */
    private $city;

    /**
     * @Assert\NotBlank()
     * @ORM\Column(type="date", nullable=true)
     */
    private $birthday;

    /**
     * @Assert\NotBlank()
     * @ORM\Column(type="string", length=15, nullable=true)
     */
    private $phone;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\EventParty", mappedBy="users")
     */
    private $eventParties;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\EventParty")
     */
    private $skippedEventParties;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $avatar;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\SearchCriteria", mappedBy="user", cascade={"persist", "remove"})
     */
    private $searchCriteria;

    public function __construct(string $name)
    {
        $this->name = $name;

        $this->addRole(self::ROLE_PARTIAL_REG);
        $this->eventParties = new ArrayCollection();
        $this->skippedEventParties = new ArrayCollection();
        $this->setSearchCriteria(new SearchCriteria($this));
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLogin(): ?string
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
        return (string) $this->name;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;

        return \array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = \array_unique($roles);

        return $this;
    }

    public function addRole(string $role): self
    {
        if (!\in_array($role, $this->roles, true)) {
            $this->roles[] = $role;
        }

        return $this;
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
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getVkToken(): ?VkUserToken
    {
        return $this->vkToken;
    }

    public function addVkToken(AccessToken $accessToken): self
    {
        $this->vkToken = new VkUserToken($accessToken, $this);

        return $this;
    }

    public function getSex(): ?Sex
    {
        return null !== $this->sex ? new Sex($this->sex) : null;
    }

    public function setSex(Sex $sex): self
    {
        $this->sex = $sex->toValue();

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function isFullFilled(): bool
    {
        return $this->phone
            && $this->city
            && $this->birthday
            && null !== $this->sex;
    }

    public function getCity(): ?City
    {
        return $this->city;
    }

    public function setCity(?City $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getBirthday(): ?\DateTimeInterface
    {
        return $this->birthday;
    }

    public function setBirthday(?\DateTimeInterface $birthday): self
    {
        $this->birthday = $birthday;

        return $this;
    }

    /**
     * @return Collection|EventParty[]
     */
    public function getEventParties(): Collection
    {
        return $this->eventParties;
    }

    public function joinToEventParty(EventParty $eventParty): self
    {
        if (!$this->eventParties->contains($eventParty)) {
            $this->eventParties[] = $eventParty;
            $eventParty->addUser($this);
        }

        return $this;
    }

    public function getActiveEventParty(): ?EventParty
    {
        foreach ($this->getEventParties() as $eventParty) {
            if ($eventParty->isActive()) {
                return $eventParty;
            }
        }

        return null;
    }

    public function hasActiveEventParty(): bool
    {
        return null !== $this->getActiveEventParty();
    }

    /**
     * @return Collection|EventParty[]
     */
    public function getSkippedEventParties(): Collection
    {
        return $this->skippedEventParties;
    }

    public function skipEventParty(EventParty $skippedEventParty): self
    {
        if (!$this->skippedEventParties->contains($skippedEventParty)) {
            $skippedEventParty->removeUser($this);
            $this->skippedEventParties[] = $skippedEventParty;
        }

        return $this;
    }

    public function getAvatar(): ?string
    {
        return $this->avatar;
    }

    public function setAvatar(string $avatar): self
    {
        $this->avatar = $avatar;

        return $this;
    }

    public function getAvatarPath(): string
    {
        if ($this->avatar) {
            return ResourceLocator::USER_AVATAR_DIR . $this->avatar;
        }

        if ($sex = $this->getSex()) {
            return ResourceLocator::USER_AVATAR_DIR . ($sex->isMale() ? 'guy_default.png' : 'girl_default.png');
        }

        return ResourceLocator::USER_AVATAR_DIR . 'unknown_default.png';
    }

    public function getSearchCriteria(): SearchCriteria
    {
        return $this->searchCriteria;
    }

    private function setSearchCriteria(SearchCriteria $searchCriteria): self
    {
        $this->searchCriteria = $searchCriteria;

        // set the owning side of the relation if necessary
        if ($this !== $searchCriteria->getUser()) {
            $searchCriteria->setUser($this);
        }

        return $this;
    }
}
