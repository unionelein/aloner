<?php declare(strict_types=1);
/**
 * This file is subject to the terms and conditions defined in file 'LICENSE', which is part of this source code
 * package. If the file is missing a copy can be found at:
 * https://gitlab.cybercoder.site/vj/policies-procedures-standards/blob/master/licensing/GHCM-LICENSE.
 */

namespace App\Component\DTO\Entity;

use App\Entity\City;
use App\Entity\User;
use Symfony\Component\Validator\Constraints as Assert;

class UserDTO
{
    /**
     * @Assert\NotBlank()
     * @var null|string
     */
    private $name;

    /**
     * @Assert\NotNull()
     * @var null|bool
     */
    private $sex;

    /**
     * @Assert\NotBlank()
     * @var null|City
     */
    private $city;

    /**
     * @Assert\NotBlank()
     * @var null|\DateTimeInterface
     */
    private $birthday;

    /**
     * @Assert\NotBlank()
     * @var null|string
     */
    private $phone;

    /**
     * @param User $user
     *
     * @return UserDTO
     */
    public static function create(User $user): UserDTO
    {
        return (new self())->setName($user->getName())
            ->setBirthday($user->getBirthday())
            ->setCity($user->getCity())
            ->setSex($user->getSex()->toValue())
            ->setPhone($user->getPhone());
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string|null $name
     *
     * @return UserDTO
     */
    public function setName(?string $name): UserDTO
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return bool|null
     */
    public function getSex(): ?bool
    {
        return $this->sex;
    }

    /**
     * @param bool|null $sex
     *
     * @return UserDTO
     */
    public function setSex(?bool $sex): UserDTO
    {
        $this->sex = $sex;

        return $this;
    }

    /**
     * @return City|null
     */
    public function getCity(): ?City
    {
        return $this->city;
    }

    /**
     * @param City|null $city
     *
     * @return UserDTO
     */
    public function setCity(?City $city): UserDTO
    {
        $this->city = $city;

        return $this;
    }

    /**
     * @return \DateTimeInterface|null
     */
    public function getBirthday(): ?\DateTimeInterface
    {
        return $this->birthday;
    }

    /**
     * @param \DateTimeInterface|null $birthday
     *
     * @return UserDTO
     */
    public function setBirthday(?\DateTimeInterface $birthday): UserDTO
    {
        $this->birthday = $birthday;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getPhone(): ?string
    {
        return $this->phone;
    }

    /**
     * @param string|null $phone
     *
     * @return UserDTO
     */
    public function setPhone(?string $phone): UserDTO
    {
        $this->phone = $phone;

        return $this;
    }
}
