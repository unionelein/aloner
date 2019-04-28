<?php declare(strict_types=1);

namespace App\Component\User;

use App\Component\Events\Events;
use App\Component\Events\EventPartyActionEvent;
use App\Component\Events\MeetingPointOfferAnsweredEvent;
use App\Component\Events\MeetingPointOfferedEvent;
use App\Component\Infrastructure\TransactionalService;
use App\Component\Model\DTO\EventPartyHistory\MeetingPointOfferAnswerHistory;
use App\Component\Model\DTO\EventPartyHistory\MeetingPointOfferHistory;
use App\Component\Model\DTO\Form\MeetingPointData;
use App\Entity\EventParty;
use App\Entity\EventPartyHistory;
use App\Entity\User;
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

    public function __construct(
        TransactionalService $transactional,
        EventDispatcherInterface $dispatcher,
        EntityManagerInterface $em
    ) {
        $this->transactional = $transactional;
        $this->dispatcher    = $dispatcher;
        $this->em            = $em;
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
        $user->updateTempHash();

        $this->em->persist($user);
        $this->em->flush();
    }

    public function offerMeetingPoint(User $user, EventParty $eventParty, MeetingPointData $meetingPointData): void
    {
        $place = $meetingPointData->getPlace();
        $day   = clone $meetingPointData->getDay();
        $time  = clone $meetingPointData->getTime();

        $history = new EventPartyHistory(
            $eventParty,
            $user,
            EventPartyHistory::ACTION_MEETING_POINT_OFFER,
            new MeetingPointOfferHistory($place, $day, $time)
        );

        $this->em->persist($history);
        $this->em->flush();

        $meetingDateTime = $day->modify($time->format('H:i:s'));

        $this->dispatcher->dispatch(
            Events::MEETING_POINT_OFFERED,
            new MeetingPointOfferedEvent($user, $eventParty, $history, $place, $meetingDateTime)
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
            EventPartyHistory::ACTION_MEETING_POINT_OFFER_ANSWER,
            new MeetingPointOfferAnswerHistory($offerId, $answer)
        );

        $this->em->persist($history);
        $this->em->flush();

        $this->dispatcher->dispatch(
            Events::MEETING_POINT_OFFER_ANSWERED,
            new MeetingPointOfferAnsweredEvent($offerHistory->getEventParty(), $offerHistory, $answer)
        );
    }
}
