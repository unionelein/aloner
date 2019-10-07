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

    /**
     * @param \DateTime $dateTime
     *
     * @return string
     */
    public static function rusFormat(\DateTime $dateTime): string
    {
        $time = $dateTime->format('H:i');

        switch (true) {
            case self::date($dateTime) == self::date(''):
                $date = 'Сегодня';
                break;
            case self::date($dateTime) == self::date('+1 day'):
                $date = 'Завтра';
                break;
            default:
                $date  = $dateTime->format('d') . ' ' . Month::month($dateTime);
        }

        return "{$date} в {$time}";
    }
}
