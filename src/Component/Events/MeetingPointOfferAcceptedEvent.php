<?php declare(strict_types=1);

namespace App\Component\Events;

use App\Entity\EventParty;
use Symfony\Component\EventDispatcher\Event;

class MeetingPointOfferAcceptedEvent extends Event
{
    /** @var EventParty */
    private $eventParty;

    /** var string */
    private $place;

    /** @var \DateTime */
    private $meetingDateTime;

    public function __construct(EventParty $eventParty, string $place, \DateTime $meetingDateTime)
    {
        $this->eventParty = $eventParty;
        $this->place = $place;
        $this->meetingDateTime = $meetingDateTime;
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
}