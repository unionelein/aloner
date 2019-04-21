<?php

namespace App\Security\Voter;

use App\Entity\EventParty;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class EventPartyVoter extends Voter
{
    public const DO_ACTIONS = 'DO_ACTIONS';

    protected function supports($attribute, $subject)
    {
        return \in_array($attribute, [self::DO_ACTIONS]) && $subject instanceof EventParty;
    }

    /**
     * @param EventParty $eventParty
     */
    protected function voteOnAttribute($attribute, $eventParty, TokenInterface $token)
    {
        $user = $token->getUser();

        if (!$user instanceof UserInterface) {
            return false;
        }

        switch ($attribute) {
            case self::DO_ACTIONS:
                return $eventParty->getUsers()->contains($user);
        }

        return false;
    }
}
