<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Webmozart\Assert\Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\MediaRepository")
 */
class Media
{
    public const TYPE_IMAGE = 1;

    public const TYPE_VIDEO = 2;

    public const TYPE_IFRAME = 3;

    public const TYPES = [
        self::TYPE_IMAGE => 'Изображение',
        self::TYPE_VIDEO => 'Видео',
        self::TYPE_IFRAME => 'Виджет',
    ];

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer", name="media_id")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $src;

    /**
     * @ORM\Column(type="smallint")
     */
    private $type;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $alt;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $videoPoster;

    public function __construct(string $src, int $type, string $alt = null, ?string $videoPoster = null)
    {
        $this->src = $src;
        $this->alt = $alt;
        $this->setType($type);
        $this->videoPoster = $videoPoster;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSrc(): ?string
    {
        return $this->src;
    }

    public function getType(): ?int
    {
        return $this->type;
    }

    private function setType(int $type): self
    {
        Assert::keyExists(self::TYPES, $type);

        $this->type = $type;

        return $this;
    }

    public function getAlt(): ?string
    {
        return $this->alt;
    }

    public function setAlt(?string $alt): self
    {
        $this->alt = $alt;

        return $this;
    }

    public function getVideoPoster(): ?string
    {
        return $this->videoPoster;
    }

    public function setVideoPoster(?string $videoPoster): self
    {
        $this->videoPoster = $videoPoster;

        return $this;
    }
}
