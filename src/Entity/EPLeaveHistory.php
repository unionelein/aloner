<?php declare(strict_types=1);
/**
 * This file is subject to the terms and conditions defined in file 'LICENSE', which is part of this source code
 * package. If the file is missing a copy can be found at:
 * https://gitlab.cybercoder.site/vj/policies-procedures-standards/blob/master/licensing/GHCM-LICENSE.
 */

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * User join to event party
 *
 * @ORM\Entity
 */
class EPLeaveHistory extends EPHistory
{
    /**
     * @param EventParty $eventParty
     * @param User       $user
     */
    public function __construct(EventParty $eventParty, User $user)
    {
        $this->action = self::ACTION_LEAVE;
        $this->data   = [];

        parent::__construct($eventParty, $user);
    }
}
