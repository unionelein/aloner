<?php declare(strict_types=1);

namespace App\Component\User;

use App\Event\EPFilledEvent;
use App\Event\EPJoinedEvent;
use App\Event\MOAnsweredEvent;
use App\Event\MOOfferedEvent;
use App\Component\Infrastructure\TransactionalService;
use App\Entity\EPAnswerMOHistory;
use App\Entity\EPOfferMOHistory;
use App\Entity\EventParty;
use App\Entity\User;
use App\Entity\VO\History\MOAnswerData;
use App\Entity\VO\History\MOOfferData;
use App\Entity\VO\MeetingOptions;
use App\Entity\VO\SearchCriteria;
use App\Event\EPSkippedEvent;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class UserManager
{
    /** @var EventDispatcherInterface */
    private $dispatcher;

    /** @var EntityManagerInterface */
    private $em;

    /** @var UserRepository */
    private $userRepo;

    /**
     * @param UserRepository           $userRepo
     * @param EventDispatcherInterface $dispatcher
     * @param EntityManagerInterface   $em
     */
    public function __construct(
        UserRepository $userRepo,
        EventDispatcherInterface $dispatcher,
        EntityManagerInterface $em
    ) {
        $this->dispatcher    = $dispatcher;
        $this->em            = $em;
        $this->userRepo      = $userRepo;
    }

    /**
     * @param string $name
     *
     * @return User
     */
    public function create(string $name): User
    {
        $user = new User($name);

        $this->em->persist($user);
        $this->em->flush();

        return $user;
    }

    /**
     * @param User       $user
     * @param EventParty $eventParty
     */
    public function join(User $user, EventParty $eventParty): void
    {
        $user->joinToEventParty($eventParty);
        $this->em->flush();

        $this->dispatcher->dispatch(new EPJoinedEvent($user, $eventParty));

        if ($eventParty->isFilled()) {
            $this->dispatcher->dispatch(new EPFilledEvent($eventParty));

            if (false === $eventParty->getEvent()->isReservationRequired()) {
                $this->offerDefaultMO($eventParty);
            }
        }
    }

    /**
     * @param User       $user
     * @param EventParty $eventParty
     */
    public function skip(User $user, EventParty $eventParty): void
    {
        $user->skipEventParty($eventParty);
        $this->em->flush();

        $this->dispatcher->dispatch(new EPSkippedEvent($user, $eventParty));
    }

    /**
     * @param User $user
     */
    public function updateTempHash(User $user): void
    {
        $user->updateTempHash();
        $this->em->flush();
    }

    /**
     * @param User           $user
     * @param EventParty     $eventParty
     * @param MeetingOptions $MO
     */
    public function offerMO(User $user, EventParty $eventParty, MeetingOptions $MO): void
    {
        $offer = new EPOfferMOHistory($eventParty, $user, new MOOfferData($MO));

        $this->em->persist($offer);
        $this->em->flush();

        $this->dispatcher->dispatch(new MOOfferedEvent($offer));
    }

    /**
     * @param EventParty $eventParty
     */
    public function offerDefaultMO(EventParty $eventParty): void
    {
        /** @var SearchCriteria $usersSC */
        $usersSC = $eventParty->getUsersSearchCriteria();
        $event   = $eventParty->getEvent();
        $web     = $this->userRepo->getWebUser();

        if ($timetable = $event->findAvailableTimetableForSC($usersSC)) {
            $address = $event->getContacts()->getAddress();
            $time    = \max($timetable->getTimeFrom(), $usersSC->getTimeFrom());

            $this->offerMO($web, $eventParty, new MeetingOptions($time, $address));
        }
    }

    /**
     * @param User                $user
     * @param EventParty          $eventParty
     * @param EPOfferMOHistory    $offer
     * @param bool                $answer
     * @param MeetingOptions|null $newMO
     */
    public function answerMO(
        User $user,
        EventParty $eventParty,
        EPOfferMOHistory $offer,
        bool $answer,
        MeetingOptions $newMO = null
    ): void {
        $answerHistory = new EPAnswerMOHistory($eventParty, $user, $offer, new MOAnswerData($answer, $newMO));

        $this->em->persist($answerHistory);
        $this->em->flush();

        $this->dispatcher->dispatch(new MOAnsweredEvent($answerHistory));
    }
}
