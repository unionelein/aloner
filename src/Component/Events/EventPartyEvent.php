<?php declare(strict_types=1);

namespace App\Component\Events;

use App\Entity\EventParty;
use Symfony\Component\EventDispatcher\Event;

class EventPartyEvent extends Event
{
    private $eventParty;

    public function __construct(EventParty $eventParty)
    {
        $this->eventParty = $eventParty;
    }

    public function getEventParty(): EventParty
    {
        return $this->eventParty;
    }
}