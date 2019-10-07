<?php declare(strict_types=1);
/**
 * This file is subject to the terms and conditions defined in file 'LICENSE', which is part of this source code
 * package. If the file is missing a copy can be found at:
 * https://gitlab.cybercoder.site/vj/policies-procedures-standards/blob/master/licensing/GHCM-LICENSE.
 */

namespace App\Entity;

use App\Entity\VO\History\MOAnswerData;
use Doctrine\ORM\Mapping as ORM;

/**
 * User answer to Meeting Options (MO) offer
 *
 * @ORM\Entity
 */
class EPAnswerMOHistory extends EPHistory
{
    /**
     * @param EventParty   $eventParty
     * @param User         $user
     * @param MOAnswerData $data
     */
    public function __construct(
        EventParty $eventParty,
        User $user,
        EPOfferMOHistory $offerHistory,
        MOAnswerData $data
    ) {
        $this->action       = self::ACTION_MO_ANSWER;
        $this->data         = $data->toArray();
        $this->offerHistory = $offerHistory;

        parent::__construct($eventParty, $user);
    }

    /**
     * @return MOAnswerData
     */
    public function getData(): MOAnswerData
    {
        return MOAnswerData::fromArray($this->data);
    }

    /**
     * @return EPOfferMOHistory
     */
    public function getOfferHistory(): EPOfferMOHistory
    {
        return $this->offerHistory;
    }
}
