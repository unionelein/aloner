<?php declare(strict_types=1);

namespace App\Component\User;

use App\Component\EventParty\EventPartyManager;
use App\Component\Events\EPEvent;
use App\Component\Events\Events;
use App\Component\Events\EPActionEvent;
use App\Component\Events\MOAnsweredEvent;
use App\Component\Events\MOOfferedEvent;
use App\Component\Infrastructure\TransactionalService;
use App\Component\Model\DTO\EventPartyHistory\AnswerToMeetingPointOfferHistory;
use App\Component\Model\DTO\EventPartyHistory\MeetingPointOfferHistory;
use App\Component\Model\DTO\Form\MeetingPointData;
use App\Component\Model\VO\TimeInterval;
use App\Entity\EPAnswerMOHistory;
use App\Entity\EPOfferMOHistory;
use App\Entity\EventParty;
use App\Entity\EPHistory;
use App\Entity\User;
use App\Entity\VO\History\MOAnswerData;
use App\Entity\VO\History\MOOfferData;
use App\Entity\VO\MeetingOptions;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class UserManager
{
    /** @var TransactionalService */
    private $transactional;

    /** @var EventDispatcherInterface */
    private $dispatcher;

    /** @var EntityManagerInterface */
    private $em;

    /** @var UserRepository */
    private $userRepo;

    /**
     * @param TransactionalService     $transactional
     * @param UserRepository           $userRepo
     * @param EventDispatcherInterface $dispatcher
     * @param EntityManagerInterface   $em
     */
    public function __construct(
        TransactionalService $transactional,
        UserRepository $userRepo,
        EventDispatcherInterface $dispatcher,
        EntityManagerInterface $em
    ) {
        $this->transactional = $transactional;
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

        return $user;
    }

    /**
     * @param User       $user
     * @param EventParty $eventParty
     */
    public function join(User $user, EventParty $eventParty): void
    {
        $this->transactional->execute(static function (EntityManagerInterface $em) use ($user, $eventParty) {
            $user->joinToEventParty($eventParty);

            $em->persist($user);
            $em->persist($eventParty);
            $em->flush();
        });

        $this->dispatcher->dispatch(Events::EP_JOIN, new EPActionEvent($user, $eventParty));

        if ($eventParty->isFilled()) {
            $this->dispatcher->dispatch(Events::EP_FILLED, new EPEvent($eventParty));

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
        $this->transactional->execute(static function (EntityManagerInterface $em) use ($user, $eventParty) {
            $user->skipEventParty($eventParty);

            $em->persist($user);
            $em->persist($eventParty);
            $em->flush();
        });

        $this->dispatcher->dispatch(Events::EP_SKIP, new EPActionEvent($user, $eventParty));
    }

    /**
     * @param User $user
     */
    public function updateTempHash(User $user): void
    {
        $user->updateTempHash();

        $this->em->persist($user);
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

        $this->dispatcher->dispatch(Events::MO_OFFERED, new MOOfferedEvent($offer));
    }

    /**
     * @param EventParty $eventParty
     */
    public function offerDefaultMO(EventParty $eventParty): void
    {
        $event     = $eventParty->getEvent();
        $web       = $this->userRepo->getWebUser();
        $timetable = $event->findAvailableTimetableForSC($eventParty->getUsersSearchCriteria());
        $address   = $event->getContacts()->getAddress();

        if ($timetable) {
            $this->offerMO($web, $eventParty, new MeetingOptions($timetable->getTimeFrom(), $address));
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

        $this->dispatcher->dispatch(Events::MO_ANSWERED, new MOAnsweredEvent($answerHistory));
    }
}
