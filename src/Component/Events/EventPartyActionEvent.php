<?php declare(strict_types=1);

namespace App\Component\Events;

use App\Entity\EventParty;
use App\Entity\User;
use Symfony\Component\EventDispatcher\Event;

class EventPartyActionEvent extends Event
{
    private $user;

    private $eventParty;

    public function __construct(User $user, EventParty $eventParty)
    {
        $this->user = $user;
        $this->eventParty = $eventParty;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getEventParty(): EventParty
    {
        return $this->eventParty;
    }
}