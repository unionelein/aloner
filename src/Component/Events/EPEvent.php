<?php declare(strict_types=1);

namespace App\Component\Events;

use App\Entity\EventParty;
use Symfony\Component\EventDispatcher\Event;

class EPEvent extends Event
{
    /** @var EventParty */
    private $eventParty;

    /**
     * @param EventParty $eventParty
     */
    public function __construct(EventParty $eventParty)
    {
        $this->eventParty = $eventParty;
    }

    /**
     * @return EventParty
     */
    public function getEventParty(): EventParty
    {
        return $this->eventParty;
    }
}
