<?php declare(strict_types=1);

namespace App\Component\Events;

use App\Entity\EventParty;
use App\Entity\EventPartyHistory;
use Symfony\Component\EventDispatcher\Event;

class MeetingPointOfferAnsweredEvent extends Event
{
    /** @var EventPartyHistory */
    private $offer;

    /** @var bool */
    private $answer;

    /** @var EventParty */
    private $eventParty;

    public function __construct(EventParty $eventParty, EventPartyHistory $offer, bool $answer)
    {
        $this->offer = $offer;
        $this->answer = $answer;
        $this->eventParty = $eventParty;
    }

    public function getOffer(): EventPartyHistory
    {
        return $this->offer;
    }

    public function getAnswer(): bool
    {
        return $this->answer;
    }

    public function getEventParty(): EventParty
    {
        return $this->eventParty;
    }
}