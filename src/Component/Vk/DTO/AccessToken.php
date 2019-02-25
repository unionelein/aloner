<?php

namespace App\Component\Vk\DTO;

class AccessToken
{
    /** @var int */
    private $userId;

    /** @var string */
    private $accessToken;

    /** @var \DateTime */
    private $expiresAt;

    /** @var array */
    private $extraFields;

    /**
     * @param int $userId
     * @param string $accessToken
     * @param \DateTime $expiresAt
     * @param array $extraFields
     */
    public function __construct(int $userId, string $accessToken, \DateTime $expiresAt, array $extraFields = [])
    {
        $this->userId = $userId;
        $this->accessToken = $accessToken;
        $this->expiresAt = $expiresAt;
        $this->extraFields = $extraFields;
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
     * @return \DateTime
     */
    public function getExpiresAt(): \DateTime
    {
        return $this->expiresAt;
    }

    /**
     * @return array
     */
    public function getExtraFields(): array
    {
        return $this->extraFields;
    }
}