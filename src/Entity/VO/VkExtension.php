<?php declare(strict_types=1);

namespace App\Entity\VO;

use App\Component\Vk\DTO\AccessToken;

class VkExtension
{
    private const VK_URL = 'https://vk.com';

    /**
     * @var int
     */
    private $userId;

    /**
     * @var string
     */
    private $token;

    /**
     * @var null|\DateTime
     */
    private $expiresAt;

    /**
     * @param AccessToken $accessToken
     */
    public function __construct(AccessToken $accessToken)
    {
        $this->userId  = $accessToken->getUserId();
        $this->token     = $accessToken->getAccessToken();
        $this->expiresAt = $accessToken->getExpiresAt();
    }

    /**
     * @return string
     */
    public function getToken(): string
    {
        return $this->token;
    }

    /**
     * @return \DateTime|null
     */
    public function getExpiresAt(): ?\DateTime
    {
        return $this->expiresAt;
    }

    /**
     * @return bool
     */
    public function isExpired(): bool
    {
        return null !== $this->expiresAt && $this->expiresAt < new \DateTime();
    }

    /**
     * @return int
     */
    public function getUserId(): int
    {
        return $this->userId;
    }

    /**
     * @return string
     */
    public function getUserUrl(): string
    {
        return self::VK_URL . '/id' . $this->userId;
    }
}
