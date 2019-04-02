<?php declare(strict_types=1);

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
