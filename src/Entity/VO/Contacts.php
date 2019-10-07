<?php declare(strict_types=1);
/**
 * This file is subject to the terms and conditions defined in file 'LICENSE', which is part of this source code
 * package. If the file is missing a copy can be found at:
 * https://gitlab.cybercoder.site/vj/policies-procedures-standards/blob/master/licensing/GHCM-LICENSE.
 */

namespace App\Entity\VO;

use App\Entity\City;

/**
 * @ORM\Embeddable
 */
class Contacts
{
    /**
     * @var string
     *
     * @ORM\Column(type="string", name="address", length=255)
     */
    private $address;

    /**
     * @var City
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\City")
     * @ORM\JoinColumn(name="city_id", referencedColumnName="city_id", nullable=false)
     */
    private $city;

    /**
     * @var string
     *
     * @ORM\Column(type="string", name="site", length=255, nullable=true)
     */
    private $site;

    /**
     * @var string
     *
     * @ORM\Column(type="string", name="phone", length=15, nullable=true)
     */
    private $phone;

    /**
     * @var string
     *
     * @ORM\Column(type="string", name="yandex_map", length=255, nullable=true)
     */
    private $yandexMap;

    /**
     * @param string $address
     * @param City   $city
     */
    public function __construct(string $address, City $city)
    {
        $this->address = $address;
        $this->city    = $city;
    }

    /**
     * @return string
     */
    public function getAddress(): string
    {
        return $this->address;
    }

    /**
     * @return City
     */
    public function getCity(): City
    {
        return $this->city;
    }

    /**
     * @return string
     */
    public function getSite(): string
    {
        return $this->site;
    }

    /**
     * @param string $site
     *
     * @return Contacts
     */
    public function setSite(string $site): self
    {
        $this->site = $site;

        return $this;
    }

    /**
     * @return string
     */
    public function getPhone(): string
    {
        return $this->phone;
    }

    /**
     * @param string $phone
     *
     * @return Contacts
     */
    public function setPhone(string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * @return string
     */
    public function getYandexMap(): string
    {
        return $this->yandexMap;
    }

    /**
     * @param string $yandexMap
     *
     * @return Contacts
     */
    public function setYandexMap(string $yandexMap): self
    {
        $this->yandexMap = $yandexMap;

        return $this;
    }
}
