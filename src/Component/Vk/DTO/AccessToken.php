<?php declare(strict_types=1);

namespace App\Component\Vk\DTO;

use Webmozart\Assert\Assert;

class AccessToken
{
    /** @var int */
    private $userId;

    /** @var string */
    private $accessToken;

    /** @var null|\DateTime */
    private $expiresAt;

    /**
     * @param int $userId
     * @param string $accessToken
     * @param null|\DateTime $expiresAt
     */
    public function __construct(int $userId, string $accessToken, ?\DateTime $expiresAt)
    {
        Assert::notEmpty($accessToken, 'Empty vk access token given');

        $this->userId = $userId;
        $this->accessToken = $accessToken;
        $this->expiresAt = $expiresAt;
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
    public function getAccessToken(): string
    {
        return $this->accessToken;
    }

    /**
     * @return null|\DateTime
     */
    public function getExpiresAt(): ?\DateTime
    {
        return $this->expiresAt;
    }
}
