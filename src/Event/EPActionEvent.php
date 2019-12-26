<?php declare(strict_types=1);

namespace App\Event;

use App\Entity\EventParty;
use App\Entity\User;
use Symfony\Contracts\EventDispatcher\Event;

class EPActionEvent extends Event
{
    /** @var User */
    private $user;

    /** @var EventParty */
    private $eventParty;

    /**
     * @param User       $user
     * @param EventParty $eventParty
     */
    public function __construct(User $user, EventParty $eventParty)
    {
        $this->user = $user;
        $this->eventParty = $eventParty;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @return EventParty
     */
    public function getEventParty(): EventParty
    {
        return $this->eventParty;
    }
}
