<?php declare(strict_types=1);

namespace App\Component\Events;

use App\Entity\EventParty;
use App\Entity\EventPartyHistory;
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

    /** @var EventPartyHistory */
    private $offer;

    public function __construct(User $user, EventParty $eventParty, EventPartyHistory $offer, string $place, \DateTime $meetingDateTime)
    {
        $this->user = $user;
        $this->eventParty = $eventParty;
        $this->place = $place;
        $this->meetingDateTime = $meetingDateTime;
        $this->offer = $offer;
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

    public function getMeetingDateTime(): \DateTime
    {
        return $this->meetingDateTime;
    }

    public function getOffer(): EventPartyHistory
    {
        return $this->offer;
    }
}