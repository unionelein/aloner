<?php declare(strict_types=1);

namespace App\Component\Events;

use App\Entity\EventParty;
use App\Entity\User;
use Symfony\Component\EventDispatcher\Event;

class PlaceOfferedEvent extends Event
{
    /** @var User */
    private $user;

    /** @var EventParty */
    private $eventParty;

    /** @var string */
    private $place;

    public function __construct(User $user, EventParty $eventParty, string $place)
    {
        $this->user = $user;
        $this->eventParty = $eventParty;
        $this->place = $place;
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
}