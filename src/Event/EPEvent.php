<?php declare(strict_types=1);

namespace App\Event;

use App\Entity\EventParty;
use Symfony\Contracts\EventDispatcher\Event;

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
