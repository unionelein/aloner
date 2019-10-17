<?php declare(strict_types=1);
/**
 * This file is subject to the terms and conditions defined in file 'LICENSE', which is part of this source code
 * package. If the file is missing a copy can be found at:
 * https://gitlab.cybercoder.site/vj/policies-procedures-standards/blob/master/licensing/GHCM-LICENSE.
 */

namespace App\Entity\VO;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Embeddable
 */
class MeetingOptions
{
    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="meeting_at", nullable=true)
     */
    private $meetingAt;

    /**
     * @var string
     *
     * @ORM\Column(type="string", name="meeting_place", length=255, nullable=true)
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
