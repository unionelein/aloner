<?php declare(strict_types=1);
/**
 * This file is subject to the terms and conditions defined in file 'LICENSE', which is part of this source code
 * package. If the file is missing a copy can be found at:
 * https://gitlab.cybercoder.site/vj/policies-procedures-standards/blob/master/licensing/GHCM-LICENSE.
 */

namespace App\Component\Messaging\EventParty\Model\Pusher\Data;

use App\Component\Messaging\EventParty\Pusher;
use App\Entity\EventParty;
use App\Entity\User;

class SkipData extends PusherData
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
        $this->user       = $user;
        $this->eventParty = $eventParty;

        parent::__construct(PusherData::TYPE_SKIP, (string) $eventParty->getId());
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'userId'      => $this->user->getId(),
            'statusTitle' => $this->eventParty->getStatusName(),
        ];
    }
}
