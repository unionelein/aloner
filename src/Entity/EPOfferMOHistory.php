<?php declare(strict_types=1);
/**
 * This file is subject to the terms and conditions defined in file 'LICENSE', which is part of this source code
 * package. If the file is missing a copy can be found at:
 * https://gitlab.cybercoder.site/vj/policies-procedures-standards/blob/master/licensing/GHCM-LICENSE.
 */

namespace App\Entity;

use App\Entity\VO\History\MOOfferData;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * User offer Meeting Options (MO)
 *
 * @ORM\Entity
 */
class EPOfferMOHistory extends EPHistory
{
    /**
     * @var ArrayCollection|EPAnswerMOHistory[]
     *
     * @ORM\OneToMany(targetEntity="App\Entity\EPAnswerMOHistory", mappedBy="offerHistory")
     */
    private $answerHistories;

    /**
     * @param EventParty  $eventParty
     * @param User        $user
     * @param MOOfferData $data
     */
    public function __construct(EventParty $eventParty, User $user, MOOfferData $data)
    {
        $this->answerHistories = new ArrayCollection();

        $this->action = self::ACTION_MO_OFFER;
        $this->data   = $data->toArray();

        parent::__construct($eventParty, $user);
    }

    /**
     * @return MOOfferData
     */
    public function getData(): MOOfferData
    {
        return MOOfferData::fromArray($this->data);
    }

    /**
     * @return ArrayCollection|EPAnswerMOHistory[]
     */
    public function getAnswerHistories(): Collection
    {
        return $this->answerHistories;
    }

    /**
     * @return bool
     */
    public function isAccepted(): bool
    {
        $acceptedUsers = new ArrayCollection();
        foreach ($this->answerHistories as $answerHistory) {
            if ($answerHistory->getData()->getAnswer() === true) {
                $acceptedUsers[] = $answerHistory->getUser();
            }
        }

        foreach ($this->eventParty->getUsers() as $user) {
            if (!$acceptedUsers->contains($user)) {
                return false;
            }
        }

        return true;
    }
}
