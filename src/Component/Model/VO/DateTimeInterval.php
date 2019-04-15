<?php declare(strict_types=1);

namespace App\Component\Model\VO;

class DateTimeInterval
{
    /** @var \DateTime */
    private $timeFrom;

    /** @var \DateTime */
    private $timeTo;

    public function __construct(\DateTime $timeFrom, \DateTime $timeTo)
    {
        $this->timeFrom = $timeFrom;
        $this->timeTo = $timeTo;
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

    public static function time(\DateTime $dateTime): \DateTime
    {
        return new \DateTime('0000-01-01 ' . $dateTime->format('H:i:s'));
    }
}
