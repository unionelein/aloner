<?php declare(strict_types=1);

namespace App\Component\EventParty;

use App\Component\Event\EventManager;
use App\Component\EventParty\Exception\NoEventsForUserException;
use App\Entity\EventParty;
use App\Entity\User;
use App\Entity\VO\PeopleComposition;
use App\Repository\EventPartyRepository;
use Doctrine\ORM\EntityManagerInterface;

class EventPartyManager
{
    /** @var EventPartyRepository */
    private $epRepo;

    /** @var EntityManagerInterface */
    private $em;

    /** @var EventManager */
    private $eventManager;

    /**
     * @param EventPartyRepository   $epRepo
     * @param EntityManagerInterface $em
     * @param EventManager           $eventManager
     */
    public function __construct(
        EventPartyRepository $epRepo,
        EntityManagerInterface $em,
        EventManager $eventManager
    ) {
        $this->epRepo  = $epRepo;
        $this->em = $em;
        $this->eventManager = $eventManager;
    }

    /**
     * @param User $user
     *
     * @return EventParty
     * @throws \Exception
     */
    public function createForUser(User $user): EventParty
    {
        $event = $this->eventManager->findForUser($user);

        if (!$event) {
            throw new NoEventsForUserException('No events for user found');
        }

        $numOfPeople = $event->getPeopleRange()->randomEven();
        $composition = new PeopleComposition($numOfPeople / 2, $numOfPeople / 2);

        $eventParty = new EventParty($event, $composition);

        $this->em->persist($eventParty);
        $this->em->flush();

        return $eventParty;
    }

    /**
     * @param User $user
     *
     * @return null|EventParty
     * @throws \Exception
     */
    public function findForUser(User $user): ?EventParty
    {
        $eventParties = $this->epRepo->findAvailableForUser($user);
        $this->sortByRelevance($eventParties);

        $today = new \DateTime();
        foreach ($eventParties as $eventParty) {
            if ($user->getSkippedEventParties()->contains($eventParty)) {
                continue;
            }

            if ($user->getSkippedEvents($today)->contains($eventParty->getEvent())) {
                continue;
            }

            if (!$eventParty->canUserJoin($user)) {
                continue;
            }

            return $eventParty;
        }

        return null;
    }

    /**
     * @param EventParty[] $eventParties
     */
    private function sortByRelevance(array &$eventParties): void
    {
        \usort($eventParties, function (EventParty $ep1, EventParty $ep2) {
            if ($ep1->getPeopleRemaining() === $ep2->getPeopleRemaining()) {
                return $ep2->getUsers()->count() <=> $ep1->getUsers()->count();
            }

            return $ep1->getPeopleRemaining() <=> $ep2->getPeopleRemaining();
        });
    }
}
