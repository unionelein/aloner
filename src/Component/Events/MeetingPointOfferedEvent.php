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

    /** @var EventPartyHistory */
    private $offer;

    public function __construct(User $user, EventParty $eventParty, EventPartyHistory $offer)
    {
        $this->user = $user;
        $this->eventParty = $eventParty;
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

    public function getOffer(): EventPartyHistory
    {
        return $this->offer;
    }
}