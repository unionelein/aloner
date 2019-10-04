<?php declare(strict_types=1);

namespace App\Component\EventParty;

use App\Component\Events\EventPartyActionEvent;
use App\Component\Events\EventPartyEvent;
use App\Component\Events\Events;
use App\Component\Infrastructure\TransactionalService;
use App\Component\Model\DTO\EventPartyHistory\EmptyDataHistory;
use App\Entity\Event;
use App\Entity\EventParty;
use App\Entity\EventPartyHistory;
use App\Entity\User;
use App\Repository\EventRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Security\Core\Security;

class EventPartyManager
{
    /** @var EventRepository */
    private $eventRepo;

    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * @var TransactionalService
     */
    private $transactional;
    /**
     * @var UserRepository
     */
    private $userRepo;

    public function __construct(
        TransactionalService $transactional,
        EventDispatcherInterface $dispatcher,
        EventRepository $eventRepo,
        UserRepository $userRepo
    ) {
        $this->eventRepo  = $eventRepo;
        $this->dispatcher = $dispatcher;
        $this->transactional = $transactional;
        $this->userRepo = $userRepo;
    }

    public function createForUser(User $user): ?EventParty
    {
        $event = $this->findEventForUser($user);

        if (!$event) {
            return null;
        }

        $minNumOfEachSex = (int) \ceil($event->getMinNumberOfPeople() / 2);
        $maxNumOfEachSex = (int) \floor($event->getMaxNumberOfPeople() / 2);
        $numOfEachSex    = \random_int($minNumOfEachSex, $maxNumOfEachSex);

        return new EventParty($event, $numOfEachSex, $numOfEachSex);
    }

    private function findEventForUser(User $user): ?Event
    {
        $events = $this->eventRepo->findAppropriateEventsForUser($user);

        if (\count($events) === 0) {
            return null;
        }

        \shuffle($events);

        foreach ($events as $event) {
            if ($user->getSkippedEvents(new \DateTime())->contains($event)) {
                continue;
            }

            if (!EventTimeChecker::findAvailableEventTimetableForUser($user, $event)) {
                continue;
            }

            return $event;
        }

        return null;
    }
}
