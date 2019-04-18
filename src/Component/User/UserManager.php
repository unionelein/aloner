<?php declare(strict_types=1);

namespace App\Component\User;

use App\Component\Events\Events;
use App\Component\Events\EventPartyActionEvent;
use App\Component\Infrastructure\TransactionalService;
use App\Entity\EventParty;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class UserManager
{
    /** @var TransactionalService */
    private $transactional;

    /** @var EventDispatcherInterface */
    private $dispatcher;

    public function __construct(TransactionalService $transactional, EventDispatcherInterface $dispatcher)
    {
        $this->transactional = $transactional;
        $this->dispatcher    = $dispatcher;
    }

    public function join(User $user, EventParty $eventParty): void
    {
        $this->transactional->execute(static function (EntityManagerInterface $em) use ($user, $eventParty) {
            $user->joinToEventParty($eventParty);

            $em->persist($user);
            $em->persist($eventParty);
            $em->flush();
        });

        $this->dispatcher->dispatch(
            Events::JOIN_TO_EVENT_PARTY,
            new EventPartyActionEvent($user, $eventParty)
        );
    }

    public function skip(User $user, EventParty $eventParty): void
    {
        $this->transactional->execute(static function (EntityManagerInterface $em) use ($user, $eventParty) {
            $user->skipEventParty($eventParty);

            $em->persist($user);
            $em->persist($eventParty);
            $em->flush();
        });

        $this->dispatcher->dispatch(
            Events::SKIP_EVENT_PARTY,
            new EventPartyActionEvent($user, $eventParty)
        );
    }

    public function updateTempHash(User $user): void
    {
        $this->transactional->execute(static function (EntityManagerInterface $em) use ($user) {
            $user->updateTempHash();

            $em->persist($user);
            $em->flush();
        });
    }
}
