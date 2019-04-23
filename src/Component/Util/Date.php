<?php declare(strict_types=1);

namespace App\Component\Util;

class Date
{
    private const JANUARY = 'января';

    private const FEBRUARY = 'февраля';

    private const MARCH = 'марта';

    private const APRIL = 'апреля';

    private const MAY = 'мая';

    private const JUNE = 'июня';

    private const JULY = 'июля';

    private const AUGUST = 'августа';

    private const SEPTEMBER = 'сентября';

    private const OCTOBER = 'октября';

    private const NOVEMBER = 'ноября';

    private const DECEMBER = 'декабря';

    private const MONTHS = [
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

    public static function convertDateToString(\DateTime $dateTime): string
    {
        $today = new \DateTime();

        if (self::date($dateTime) === self::date($today)) {
            return 'Сегодня';
        }

        if (self::date($dateTime) === self::date($today)->modify('+1 day')) {
            return 'Завтра';
        }

        $day   = (int) $dateTime->format('d');
        $month = (int) $dateTime->format('m');
        $year  = ($dateTime->format('Y') === $today->format('Y')) ? '' : $dateTime->format('Y');

        return $day . ' ' . self::MONTHS[$month] . ' ' . $year;
    }

    public static function date(\DateTime $dateTime): \DateTime
    {
        return $dateTime->modify('00:00:00');
    }
}