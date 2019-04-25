<?php declare(strict_types=1);

namespace App\Component\Events;

use App\Entity\EventParty;
use App\Entity\User;
use Symfony\Component\EventDispatcher\Event;

class MeetingPointOfferedEvent extends Event
{
    /** @var User */
    private $user;

    /** @var EventParty */
    private $eventParty;

    /** @var string */
    private $place;

    /** @var \DateTime */
    private $meetingDateTime;

    public function __construct(User $user, EventParty $eventParty, string $place, \DateTime $meetingDateTime)
    {
        $this->user = $user;
        $this->eventParty = $eventParty;
        $this->place = $place;
        $this->meetingDateTime = $meetingDateTime;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getEventParty(): EventParty
    {
        return $this->eventParty;
    }

    public function getPlace(): string
    {
        return $this->place;
    }

    /**
     * @return \DateTime
     */
    public function getMeetingDateTime(): \DateTime
    {
        return $this->meetingDateTime;
    }
}