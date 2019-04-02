<?php declare(strict_types=1);

namespace App\Component\EventParty;

use App\Component\Infrastructure\TransactionalService;
use App\Entity\EventParty;
use App\Entity\User;
use App\Repository\EventRepository;
use Doctrine\ORM\EntityManagerInterface;

class EventPartyService
{
    /**
     * @var EventRepository
     */
    private $eventRepo;

    /**
     * @var TransactionalService
     */
    private $transactional;

    public function __construct(EventRepository $eventRepo, TransactionalService $transactional)
    {
        $this->eventRepo = $eventRepo;
        $this->transactional = $transactional;
    }

    public function createForUser(User $user): EventParty
    {
        $events = $this->eventRepo->findAppropriateEventsForUser($user);

        if (!\count($events)) {
            throw new \LogicException('Нет евентов для юзера');
        }

        \shuffle($events);
        $event = \reset($events);

        $girlsNum = \rand(1, 3);
        $guysNum  = \rand(1, 3);

        return new EventParty($event, $girlsNum, $guysNum);
    }

    public function join(User $user, EventParty $eventParty)
    {
        $this->transactional->execute(function (EntityManagerInterface $em) use ($user, $eventParty) {
            $user->joinToEventParty($eventParty);

            $em->persist($user);
            $em->persist($eventParty);
        });
    }

    public function skip(User $user, EventParty $eventParty)
    {
        $this->transactional->execute(function (EntityManagerInterface $em) use ($user, $eventParty) {
            $user->skipEventParty($eventParty);

            $em->persist($user);
            $em->persist($eventParty);
        });
    }
}
