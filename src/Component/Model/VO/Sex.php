<?php declare(strict_types=1);
/**
 * This file is subject to the terms and conditions defined in file 'LICENSE', which is part of this source code
 * package. If the file is missing a copy can be found at:
 * https://gitlab.cybercoder.site/vj/policies-procedures-standards/blob/master/licensing/GHCM-LICENSE.
 */

namespace App\Component\Model\VO;

class Sex
{
    public const FEMALE = false;

    public const MALE = true;

    public const SEX = [
        self::FEMALE    => 'Девушка',
        self::MALE      => 'Парень',
    ];

    /** @var bool */
    private $sex;

    public function __construct(bool $sex)
    {
        $this->sex = $sex;
    }

    public function isMale(): bool
    {
        return self::MALE === $this->sex;
    }

    public function isFemale(): bool
    {
        return self::FEMALE === $this->sex;
    }

    public function toValue(): bool
    {
        return $this->sex;
    }
}
