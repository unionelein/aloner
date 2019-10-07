<?php declare(strict_types=1);
/**
 * This file is subject to the terms and conditions defined in file 'LICENSE', which is part of this source code
 * package. If the file is missing a copy can be found at:
 * https://gitlab.cybercoder.site/vj/policies-procedures-standards/blob/master/licensing/GHCM-LICENSE.
 */

namespace App\Entity;

use App\Entity\VO\History\JoinData;
use Doctrine\ORM\Mapping as ORM;

/**
 * User leave event party
 *
 * @ORM\Entity
 */
class EPJoinHistory extends EPHistory
{
    /**
     * @param EventParty $eventParty
     * @param User       $user
     * @param JoinData   $data
     */
    public function __construct(EventParty $eventParty, User $user, JoinData $data)
    {
        $this->action = self::ACTION_JOIN;
        $this->data   = $data->toArray();

        parent::__construct($eventParty, $user);
    }

    /**
     * @return JoinData
     */
    public function getData(): JoinData
    {
        return JoinData::fromArray($this->data);
    }
}
