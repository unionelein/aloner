<?php declare(strict_types=1);
/**
 * This file is subject to the terms and conditions defined in file 'LICENSE', which is part of this source code
 * package. If the file is missing a copy can be found at:
 * https://gitlab.cybercoder.site/vj/policies-procedures-standards/blob/master/licensing/GHCM-LICENSE.
 */

namespace App\Entity\VO;

use App\Component\Util\Date;
use Symfony\Component\Validator\Constraints as Assert;

class SearchCriteria
{
    /**
     * @var \DateTime
     *
     * @Assert\NotNull()
     */
    private $day;

    /**
     * @var \DateTime
     *
     * @Assert\NotNull()
     */
    private $timeFrom;

    /**
     * @var \DateTime
     *
     * @Assert\NotNull()
     */
    private $timeTo;

    /**
     * @param \DateTime $day
     * @param \DateTime $timeFrom
     * @param \DateTime $timeTo
     */
    public function __construct(\DateTime $day, \DateTime $timeFrom, \DateTime $timeTo)
    {
        $this->setDay($day);
        $this->setTimeFrom($timeFrom);
        $this->setTimeTo($timeTo);
    }

    /**
     * @return \DateTime
     */
    public function getTimeFrom(): \DateTime
    {
        return Date::time($this->timeFrom);
    }

    /**
     * @param \DateTime $timeFrom
     *
     * @return SearchCriteria
     */
    public function setTimeFrom(\DateTime $timeFrom): self
    {
        $this->timeFrom = Date::time($timeFrom);

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getTimeTo(): \DateTime
    {
        return Date::time($this->timeTo);
    }

    /**
     * @param \DateTime $timeTo
     *
     * @return SearchCriteria
     */
    public function setTimeTo(\DateTime $timeTo): self
    {
        $this->timeTo = Date::time($timeTo);

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getDay(): \DateTime
    {
        return Date::date($this->day);
    }

    /**
     * @param \DateTime $day
     *
     * @return SearchCriteria
     */
    public function setDay(\DateTime $day): self
    {
        $this->day = Date::date($day);

        return $this;
    }
}
