<?php declare(strict_types=1);
/**
 * This file is subject to the terms and conditions defined in file 'LICENSE', which is part of this source code
 * package. If the file is missing a copy can be found at:
 * https://gitlab.cybercoder.site/vj/policies-procedures-standards/blob/master/licensing/GHCM-LICENSE.
 */

namespace App\Entity\VO;

class MeetingOptions
{
    /**
     * @var \DateTime
     */
    private $meetingAt;

    /**
     * @var string
     */
    private $meetingPlace;

    /**
     * @param \DateTime $meetingAt
     * @param string    $meetingPlace
     */
    public function __construct(\DateTime $meetingAt, string $meetingPlace)
    {
        $this->meetingAt    = $meetingAt;
        $this->meetingPlace = $meetingPlace;
    }

    /**
     * @return \DateTime
     */
    public function getMeetingAt(): \DateTime
    {
        return $this->meetingAt;
    }

    /**
     * @return string
     */
    public function getMeetingPlace(): string
    {
        return $this->meetingPlace;
    }
}
