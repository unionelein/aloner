<?php declare(strict_types=1);

namespace App\Component\Util;

class Date
{
    private const RESET_TIME = '00:00:00';

    private const RESET_DATE = '0000-01-01';

    /**
     * @param string|\DateTime $date
     *
     * @return \DateTime
     */
    public static function date($date): \DateTime
    {
        if ($date instanceof \DateTime) {
            return (clone $date)->modify(self::RESET_TIME);
        }

        if (\is_string($date)) {
            return (new \DateTime($date))->modify(self::RESET_TIME);
        }

        throw new \InvalidArgumentException('Invalid date argument given');
    }

    /**
     * @param string|\DateTime $date
     *
     * @return \DateTime
     */
    public static function time($date): \DateTime
    {
        if ($date instanceof \DateTime) {
            return (clone $date)->modify(self::RESET_DATE);
        }

        if (\is_string($date)) {
            return (new \DateTime($date))->modify(self::RESET_DATE);
        }

        throw new \InvalidArgumentException('Invalid date argument given');
    }

    public static function convertDateToString(\DateTime $dateTime): string
    {
        $today = new \DateTime();

        if (self::date($dateTime) == self::date($today)) {
            return 'Сегодня';
        }

        if (self::date($dateTime) == self::date($today)->modify('+1 day')) {
            return 'Завтра';
        }

        $day   = (int) $dateTime->format('d');
        $month = (int) $dateTime->format('m');
        $year  = ($dateTime->format('Y') === $today->format('Y')) ? '' : $dateTime->format('Y');

        return $day . ' ' . Month::MONTHS[$month] . ' ' . $year;
    }
}
