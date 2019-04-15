<?php declare(strict_types=1);

namespace App\Entity;

use App\Component\Vk\DTO\AccessToken;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\VkUserTokenRepository")
 */
class VkUserToken
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $vkUserId;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $token;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $expiresAt;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\User", inversedBy="vkToken", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    public function __construct(AccessToken $accessToken, User $user)
    {
        $this->vkUserId  = $accessToken->getUserId();
        $this->token     = $accessToken->getAccessToken();
        $this->expiresAt = $accessToken->getExpiresAt();

        $this->user = $user;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getToken(): string
    {
        return $this->token;
    }

    public function getExpiresAt(): ?\DateTime
    {
        return $this->expiresAt;
    }

    public function isExpired(): bool
    {
        return null !== $this->expiresAt && $this->expiresAt < new \DateTime();
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getVkUserId(): int
    {
        return $this->vkUserId;
    }
}
