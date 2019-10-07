<?php declare(strict_types=1);
/**
 * This file is subject to the terms and conditions defined in file 'LICENSE', which is part of this source code
 * package. If the file is missing a copy can be found at:
 * https://gitlab.cybercoder.site/vj/policies-procedures-standards/blob/master/licensing/GHCM-LICENSE.
 */

namespace App\Component\Util;

use Webmozart\Assert\Assert;

class Month
{
    public const JANUARY = 'январь';

    public const FEBRUARY = 'февраль';

    public const MARCH = 'март';

    public const APRIL = 'апрель';

    public const MAY = 'май';

    public const JUNE = 'июнь';

    public const JULY = 'июль';

    public const AUGUST = 'август';

    public const SEPTEMBER = 'сентябрь';

    public const OCTOBER = 'октябрь';

    public const NOVEMBER = 'ноябрь';

    public const DECEMBER = 'декабрь';

    public const MONTHS = [
        1  => self::JANUARY,
        2  => self::FEBRUARY,
        3  => self::MARCH,
        4  => self::APRIL,
        5  => self::MAY,
        6  => self::JUNE,
        7  => self::JULY,
        8  => self::AUGUST,
        9  => self::SEPTEMBER,
        10 => self::OCTOBER,
        11 => self::NOVEMBER,
        12 => self::DECEMBER,
    ];

    /**
     * @param \DateTime $date
     *
     * @return string
     */
    public static function month(\DateTime $date): string
    {
        $monthNum = (int) $date->format('m');
        Assert::keyExists(self::MONTHS, $monthNum);

        return self::MONTHS[$monthNum];
    }
}
