<?php declare(strict_types=1);

namespace App\Component\Messaging\EventParty\Model\Pusher\Data;

use App\Component\Messaging\EventParty\Pusher;
use App\Entity\EventParty;

class FilledData extends PusherData
{
    /** @var EventParty */
    private $eventParty;

    /**
     * @param EventParty $eventParty
     */
    public function __construct(EventParty $eventParty)
    {
        $this->eventParty = $eventParty;

        parent::__construct(PusherData::TYPE_FILLED, (string) $eventParty->getId());
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'statusTitle' => $this->eventParty->getStatusName(),
        ];
    }
}
