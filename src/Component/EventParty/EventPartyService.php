<?php declare(strict_types=1);

namespace App\Component\EventParty;

use App\Component\Events;
use App\Component\Events\EventPartyActionEvent;
use App\Component\Infrastructure\TransactionalService;
use App\Entity\EventParty;
use App\Entity\User;
use App\Repository\EventRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;

class EventPartyService
{
    /** @var EventRepository */
    private $eventRepo;

    /** @var TransactionalService */
    private $transactional;

    /** @var EventDispatcher */
    private $dispatcher;

    public function __construct(
        EventRepository $eventRepo,
        TransactionalService $transactional,
        EventDispatcher $dispatcher
    ) {
        $this->eventRepo = $eventRepo;
        $this->transactional = $transactional;
        $this->dispatcher = $dispatcher;
    }

    public function createForUser(User $user): EventParty
    {
        $events = $this->eventRepo->findAppropriateEventsForUser($user);

        if (!\count($events)) {
            throw new \LogicException('Нет евентов для юзера');
        }

        \shuffle($events);
        $event = \reset($events);

        $numOfPeople = \random_int($event->getMinNumberOfPeople(), $event->getMaxNumberOfPeople());

        if (($numOfPeople % 2) !== 0) {
            $numOfPeople > $event->getMinNumberOfPeople() ? $numOfPeople-- : $numOfPeople++;
        }

        return new EventParty($event, $numOfPeople/2, $numOfPeople/2);
    }

    public function join(User $user, EventParty $eventParty): void
    {
        $this->transactional->execute(function (EntityManagerInterface $em) use ($user, $eventParty) {
            $user->joinToEventParty($eventParty);

            $em->persist($user);
            $em->persist($eventParty);
        });

        $this->dispatcher->dispatch(
            Events::JOIN_TO_EVENT_PARTY,
            new EventPartyActionEvent($user, $eventParty)
        );
    }

    public function skip(User $user, EventParty $eventParty): void
    {
        $this->transactional->execute(function (EntityManagerInterface $em) use ($user, $eventParty) {
            $user->skipEventParty($eventParty);

            $em->persist($user);
            $em->persist($eventParty);
        });

        $this->dispatcher->dispatch(
            Events::SKIP_EVENT_PARTY,
            new EventPartyActionEvent($user, $eventParty)
        );
    }
}
