<?php declare(strict_types=1);

namespace App\Component\Model\VO;

class TimeInterval
{
    /** @var \DateTime */
    private $timeFrom;

    /** @var \DateTime */
    private $timeTo;

    public function __construct(\DateTime $timeFrom, \DateTime $timeTo)
    {
        $this->timeFrom = self::time($timeFrom);
        $this->timeTo   = self::time($timeTo);
    }

    /**
     * @return \DateTime
     */
    public function getFrom(): \DateTime
    {
        return $this->timeFrom;
    }

    /**
     * @return \DateTime
     */
    public function getTo(): \DateTime
    {
        return $this->timeTo;
    }

    public static function timeDayStart(): \DateTime
    {
        return self::time(new \DateTime('00:00:00'));
    }

    public static function timeDayEnd(): \DateTime
    {
        return self::time(new \DateTime('23:59:59'));
    }

    public static function fullDayTimeInterval(): self
    {
        return new self(
            self::timeDayStart(),
            self::timeDayEnd()
        );
    }

    public static function time(\DateTime $dateTime): \DateTime
    {
        return new \DateTime('0000-01-01 ' . $dateTime->format('H:i:s'));
    }
}
