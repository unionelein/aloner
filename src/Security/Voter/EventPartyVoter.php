<?php

namespace App\Security\Voter;

use App\Entity\EventParty;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class EventPartyVoter extends Voter
{
    public const SEE = 'SEE';

    public const PARTICIPANT = 'PARTICIPATE'; // if user inside event party

    public const OFFER_MO = 'OFFER_MO';

    private const ACTIONS = [
        self::SEE,
        self::PARTICIPANT,
        self::OFFER_MO,
    ];

    /**
     * {@inheritdoc}
     */
    protected function supports($attribute, $subject): bool
    {
        return in_array($attribute, self::ACTIONS, true)
            && $subject instanceof EventParty;
    }

    /**
     * @param string         $attribute
     * @param EventParty     $ep
     * @param TokenInterface $token
     *
     * @return bool
     */
    protected function voteOnAttribute($attribute, $ep, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        switch ($attribute) {
            case self::SEE:
                return $ep->getUsers()->contains($user);

            case self::PARTICIPANT:
                return $ep->getUsers()->contains($user);

            case self::OFFER_MO:
                return $ep->getUsers()->contains($user);
        }

        return false;
    }
}
