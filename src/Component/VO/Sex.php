<?php declare(strict_types=1);
/**
 * This file is subject to the terms and conditions defined in file 'LICENSE', which is part of this source code
 * package. If the file is missing a copy can be found at:
 * https://gitlab.cybercoder.site/vj/policies-procedures-standards/blob/master/licensing/GHCM-LICENSE.
 */

namespace App\Component\VO;

class Sex
{
    public const FEMALE    = 0;

    public const MALE      = 1;

    public const SEX = [
        self::FEMALE    => 'Девушка',
        self::MALE      => 'Парень',
    ];

    /** @var bool|null */
    private $sex;

    public function __construct(?bool $isMan)
    {
        $this->sex = $isMan;
    }

    public function isMale(): bool
    {
        return self::MALE === $this->sex;
    }

    public function isFemale(): bool
    {
        return self::FEMALE === $this->sex;
    }

    public function isUndefined(): bool
    {
        return null === $this->sex;
    }

    public function toValue(): ?bool
    {
        return $this->sex;
    }
}
