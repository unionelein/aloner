<?php declare(strict_types=1);

namespace App\Component\User;

use App\Component\EventParty\EventPartyManager;
use App\Component\Events\EventPartyEvent;
use App\Component\Events\Events;
use App\Component\Events\EventPartyActionEvent;
use App\Component\Events\MeetingPointOfferAnsweredEvent;
use App\Component\Events\MeetingPointOfferedEvent;
use App\Component\Infrastructure\TransactionalService;
use App\Component\Model\DTO\EventPartyHistory\AnswerToMeetingPointOfferHistory;
use App\Component\Model\DTO\EventPartyHistory\MeetingPointOfferHistory;
use App\Component\Model\DTO\Form\MeetingPointData;
use App\Component\Model\VO\TimeInterval;
use App\Entity\EventParty;
use App\Entity\EventPartyHistory;
use App\Entity\User;
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

        if ($eventParty->isFilled()) {
            $this->dispatcher->dispatch(Events::EVENT_PARTY_FILLED, new EventPartyEvent($eventParty));

            // if no reserve required we can just offer supposed datetime
            if ($eventParty->getEvent()->isReserveRequired() === false) {
                $webUser   = $this->userRepo->getWebUser();
                $place     = $eventParty->getEvent()->getAddress();
                $meetingAt = $eventParty->generateMeetingAt();
                $interval  = $eventParty->generateEventTimeInterval($meetingAt);

                $this->offerMeetingPoint($webUser, $eventParty, $place, $meetingAt, $interval);
            }
        }
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
        $user->updateTempHash();

        $this->em->persist($user);
        $this->em->flush();
    }

    public function offerMeetingPoint(
        User $user,
        EventParty $eventParty,
        string $meetingPlace,
        \DateTime $meetingDateTime,
        TimeInterval $eventTimeInterval = null
    ): void {
        $eventTimeInterval = $eventTimeInterval ?? $eventParty->generateEventTimeInterval($meetingDateTime);

        $offer = new EventPartyHistory(
            $eventParty,
            $user,
            EventPartyHistory::ACTION_MEETING_POINT_OFFER,
            new MeetingPointOfferHistory($meetingPlace, $meetingDateTime, $eventTimeInterval)
        );

        $this->em->persist($offer);
        $this->em->flush();

        $this->dispatcher->dispatch(
            Events::MEETING_POINT_OFFERED,
            new MeetingPointOfferedEvent($user, $eventParty, $offer)
        );
    }

    public function answerOnMeetingPointOffer(User $user, int $offerId, bool $answer)
    {
        $offerHistory = $this->em->getRepository(EventPartyHistory::class)->find($offerId);

        if (!$offerHistory) {
            throw new \InvalidArgumentException('No offer found');
        }

        $history = new EventPartyHistory(
            $offerHistory->getEventParty(),
            $user,
            EventPartyHistory::ACTION_ANSWER_TO_MEETING_POINT_OFFER,
            new AnswerToMeetingPointOfferHistory($offerId, $answer)
        );

        $this->em->persist($history);
        $this->em->flush();

        $this->dispatcher->dispatch(
            Events::MEETING_POINT_OFFER_ANSWERED,
            new MeetingPointOfferAnsweredEvent($offerHistory->getEventParty(), $offerHistory, $answer)
        );
    }
}
