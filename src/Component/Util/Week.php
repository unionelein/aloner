<?php declare(strict_types=1);
/**
 * This file is subject to the terms and conditions defined in file 'LICENSE', which is part of this source code
 * package. If the file is missing a copy can be found at:
 * https://gitlab.cybercoder.site/vj/policies-procedures-standards/blob/master/licensing/GHCM-LICENSE.
 */

namespace App\Component\Util;

use Webmozart\Assert\Assert;
use DateTime;

class Week
{
    public const MONDAY = 1;

    public const TUESDAY = 2;

    public const WEDNESDAY = 3;

    public const THURSDAY = 4;

    public const FRIDAY = 5;

    public const SATURDAY = 6;

    public const SUNDAY = 0;

    public const DAYS = [
        self::MONDAY    => 'Понедельник',
        self::TUESDAY   => 'Вторник',
        self::WEDNESDAY => 'Среда',
        self::THURSDAY  => 'Четверг',
        self::FRIDAY    => 'Пятница',
        self::SATURDAY  => 'Суббота',
        self::SUNDAY    => 'Воскресенье',
        7               => 'Воскресенье', // TODO: fix usages
    ];

    public const SHORT_DAYS = [
        self::MONDAY    => 'Пн',
        self::TUESDAY   => 'Вт',
        self::WEDNESDAY => 'Ср',
        self::THURSDAY  => 'Чт',
        self::FRIDAY    => 'Пт',
        self::SATURDAY  => 'Сб',
        self::SUNDAY    => 'Вс',
        7               => 'Вс',
    ];

    /**
     * @param string|DateTime $day
     *
     * @return int
     */
    public static function weekDay($day): int
    {
        $day     = Date::toDateTime($day);
        $weekDay = (int) $day->format('w');

        Assert::keyExists(self::DAYS, $weekDay);

        return $weekDay;
    }
}
