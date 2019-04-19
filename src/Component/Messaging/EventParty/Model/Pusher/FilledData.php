<?php declare(strict_types=1);

namespace App\Component\Messaging\EventParty\Model\Pusher;

use App\Component\Messaging\EventParty\Pusher;
use App\Entity\EventParty;

class FilledData extends AbstractPusherData
{
    /** @var EventParty */
    private $eventParty;

    public function __construct(EventParty $eventParty)
    {
        parent::__construct(Pusher::TYPE_FILLED);

        $this->eventParty = $eventParty;
    }

    public function getTopicId(): string
    {
        return (string) $this->eventParty->getId();
    }

    public function toArray(): array
    {
        return [
            'eventPartyStatus' => $this->eventParty->getCurrentStatusTitle(),
        ];
    }
}