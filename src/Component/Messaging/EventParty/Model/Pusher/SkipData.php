<?php declare(strict_types=1);
/**
 * This file is subject to the terms and conditions defined in file 'LICENSE', which is part of this source code
 * package. If the file is missing a copy can be found at:
 * https://gitlab.cybercoder.site/vj/policies-procedures-standards/blob/master/licensing/GHCM-LICENSE.
 */

namespace App\Component\Messaging\EventParty\Model\Pusher;

use App\Component\Messaging\EventParty\Pusher;
use App\Entity\EventParty;
use App\Entity\User;

class SkipData extends AbstractPusherData
{
    /** @var User */
    private $user;

    /** @var EventParty */
    private $eventParty;

    public function __construct(User $user, EventParty $eventParty)
    {
        parent::__construct(Pusher::TYPE_SKIP);

        $this->user = $user;
        $this->eventParty = $eventParty;
    }

    public function getTopicId(): string
    {
        return (string) $this->eventParty->getId();
    }

    public function toArray(): array
    {
        return [
            'userId'           => $this->user->getId(),
            'epStatus' => $this->eventParty->getCurrentStatusTitle(),
        ];
    }
}
