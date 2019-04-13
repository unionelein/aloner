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
    public function getTimeFrom(): \DateTime
    {
        return $this->timeFrom;
    }

    /**
     * @return \DateTime
     */
    public function getTimeTo(): \DateTime
    {
        return $this->timeTo;
    }
}