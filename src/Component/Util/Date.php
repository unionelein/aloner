<?php declare(strict_types=1);

namespace App\Component\Util;

use DateTime;
use Webmozart\Assert\Assert;
use LogicException;

class Date
{
    private const RESET_TIME = '00:00:00';

    private const RESET_DATE = '0000-01-01';

    /**
     * @param string|DateTime $date
     *
     * @return DateTime
     */
    public static function date($date): DateTime
    {
        $date = self::toDateTime($date);

        return (clone $date)->modify(self::RESET_TIME);
    }

    /**
     * @param string|DateTime $time
     *
     * @return DateTime
     */
    public static function time($time): DateTime
    {
        $time = self::toDateTime($time);

        return $time->modify(self::RESET_DATE);
    }

    /**
     * @param string|DateTime $dateTime1
     * @param string|DateTime $dateTime2
     * @param string          $unit available units: "sec", "min", "hour"
     *
     * @return int floor value of dateTime difference
     */
    public static function diff($dateTime1, $dateTime2, string $unit = 'min'): int
    {
        $timestamp1 = self::toDateTime($dateTime1)->getTimestamp();
        $timestamp2 = self::toDateTime($dateTime2)->getTimestamp();

        $diffSec = $timestamp2 - $timestamp1;

        switch ($unit) {
            case 'sec':  return $diffSec;
            case 'min':  return (int) floor($diffSec / 60);
            case 'hour': return (int) floor($diffSec / (60 * 60));
            default: throw new LogicException("Invalid time diff unit given: {$unit}");
        }
    }

    /**
     * @param DateTime $dateTime
     *
     * @return string
     */
    public static function rusFormat(DateTime $dateTime): string
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

    /**
     * @param string|DateTime $dateTime
     *
     * @return DateTime
     */
    public static function toDateTime($dateTime): DateTime
    {
        $dateTime = $dateTime instanceof DateTime ? $dateTime : new DateTime($dateTime);

        Assert::isInstanceOf($dateTime, DateTime::class);

        return $dateTime;
    }
}
