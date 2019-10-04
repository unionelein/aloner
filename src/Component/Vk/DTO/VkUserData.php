<?php declare(strict_types=1);
/**
 * This file is subject to the terms and conditions defined in file 'LICENSE', which is part of this source code
 * package. If the file is missing a copy can be found at:
 * https://gitlab.cybercoder.site/vj/policies-procedures-standards/blob/master/licensing/GHCM-LICENSE.
 */

namespace App\Component\Vk\DTO;

use App\Entity\VO\Sex;
use Webmozart\Assert\Assert;

class VkUserData
{
    /** @var int */
    private $userId;

    /** @var string */
    private $firstName;

    /** @var string */
    private $lastName;

    /** @var null|string */
    private $cityName;

    /** @var null|Sex */
    private $sex;

    /** @var null|\DateTime */
    private $birthday;

    /** @var string */
    private $photo50;

    /**
     * @param int    $userId
     * @param string $firstName
     * @param string $lastName
     */
    public function __construct(int $userId, string $firstName, string $lastName)
    {
        Assert::notEmpty($firstName);
        Assert::notEmpty($lastName);

        $this->userId = $userId;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
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
    public function getFirstName(): string
    {
        return $this->firstName;
    }

    /**
     * @return string
     */
    public function getLastName(): string
    {
        return $this->lastName;
    }

    /**
     * @return string|null
     */
    public function getCityName(): ?string
    {
        return $this->cityName;
    }

    /**
     * @param null|string $cityName
     *
     * @return VkUserData
     */
    public function setCityName(?string $cityName): self
    {
        $this->cityName = $cityName;

        return $this;
    }

    /**
     * @return null|Sex
     */
    public function getSex(): ?Sex
    {
        return $this->sex;
    }

    /**
     * @param null|Sex $sex
     *
     * @return VkUserData
     */
    public function setSex(?Sex $sex): self
    {
        $this->sex = $sex;

        return $this;
    }

    /**
     * @return null|\DateTime
     */
    public function getBirthday(): ?\DateTime
    {
        return $this->birthday;
    }

    /**
     * @param null|\DateTime $birthday
     *
     * @return VkUserData
     */
    public function setBirthday(?\DateTime $birthday): self
    {
        $this->birthday = $birthday;

        return $this;
    }

    /**
     * @return string
     */
    public function getPhoto50(): string
    {
        return $this->photo50;
    }

    /**
     * @param string $photo50
     *
     * @return VkUserData
     */
    public function setPhoto50(string $photo50): self
    {
        $this->photo50 = $photo50;

        return $this;
    }
}
