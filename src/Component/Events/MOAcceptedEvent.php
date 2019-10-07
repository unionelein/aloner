<?php declare(strict_types=1);

namespace App\Component\Events;

use App\Entity\EventParty;
use App\Entity\VO\MeetingOptions;
use Symfony\Component\EventDispatcher\Event;

class MOAcceptedEvent extends Event
{
    /** @var EventParty */
    private $eventParty;

    /** @var MeetingOptions */
    private $meetingOptions;

    /**
     * @param EventParty     $eventParty
     * @param MeetingOptions $meetingOptions
     */
    public function __construct(EventParty $eventParty, MeetingOptions $meetingOptions)
    {
        $this->eventParty     = $eventParty;
        $this->meetingOptions = $meetingOptions;
    }

    /**
     * @return EventParty
     */
    public function getEventParty(): EventParty
    {
        return $this->eventParty;
    }

    /**
     * @return MeetingOptions
     */
    public function getMeetingOptions(): MeetingOptions
    {
        return $this->meetingOptions;
    }
}
