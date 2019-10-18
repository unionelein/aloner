<?php declare(strict_types=1);
/**
 * This file is subject to the terms and conditions defined in file 'LICENSE', which is part of this source code
 * package. If the file is missing a copy can be found at:
 * https://gitlab.cybercoder.site/vj/policies-procedures-standards/blob/master/licensing/GHCM-LICENSE.
 */

namespace App\Entity\VO;

use Doctrine\ORM\Mapping as ORM;
use Webmozart\Assert\Assert;

/**
 * @ORM\Embeddable
 */
class Sex
{
    public const MALE = true;

    public const FEMALE = false;

    public const SEX = [
        self::MALE   => 'Парень',
        self::FEMALE => 'Девушка',
    ];

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", name="Sex", nullable=true)
     */
    private $sex;

    /**
     * @param bool $sex
     */
    public function __construct(bool $sex)
    {
        Assert::keyExists(self::SEX, $sex);

        $this->sex = $sex;
    }

    /**
     * @return bool
     */
    public function isMale(): bool
    {
        return self::MALE === $this->sex;
    }

    /**
     * @return bool
     */
    public function isFemale(): bool
    {
        return self::FEMALE === $this->sex;
    }

    /**
     * @return bool
     */
    public function toValue(): bool
    {
        return $this->sex;
    }
}
