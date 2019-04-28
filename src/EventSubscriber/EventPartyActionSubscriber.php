<?php

namespace App\EventSubscriber;

use App\Component\Events\Events;
use App\Component\Events\EventPartyActionEvent;
use App\Component\Events\MeetingPointOfferAcceptedEvent;
use App\Component\Events\MeetingPointOfferAnsweredEvent;
use App\Component\Events\MeetingPointOfferedEvent;
use App\Component\Messaging\EventParty\Model\Pusher\JoinData;
use App\Component\Messaging\EventParty\Model\Pusher\MeetingPointOfferAcceptedData;
use App\Component\Messaging\EventParty\Model\Pusher\MeetingPointOfferAnswerData;
use App\Component\Messaging\EventParty\Model\Pusher\MeetingPointOfferData;
use App\Component\Messaging\EventParty\Model\Pusher\SkipData;
use App\Component\Messaging\EventParty\PusherFacade;
use App\Component\User\UserManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class EventPartyActionSubscriber implements EventSubscriberInterface
{
    /** @var PusherFacade */
    private $pusherFacade;

    /** @var EventDispatcherInterface */
    private $dispatcher;
    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(PusherFacade $pusherFacade, EventDispatcherInterface $dispatcher, EntityManagerInterface $em)
    {
        $this->pusherFacade = $pusherFacade;
        $this->dispatcher   = $dispatcher;
        $this->em = $em;
    }

    public static function getSubscribedEvents()
    {
        return [
            Events::JOIN_TO_EVENT_PARTY   => 'onEventPartyJoin',
            Events::SKIP_EVENT_PARTY      => 'onEventPartySkip',
            Events::MEETING_POINT_OFFERED => 'onMeetingPointOffered',
            Events::MEETING_POINT_OFFER_ANSWERED => 'onMeetingPointOfferAnswered',
            Events::MEETING_POINT_OFFER_ACCEPTED => 'onMeetingPointOfferAccepted',
        ];
    }
    
    public function onEventPartyJoin(EventPartyActionEvent $event): void
    {
        $this->pusherFacade->send(
            new JoinData($event->getUser(), $event->getEventParty())
        );

        if ($event->getEventParty()->isFilled()) {
            $this->dispatcher->dispatch(Events::EVENT_PARTY_FILLED, $event);
        }
    }

    public function onEventPartySkip(EventPartyActionEvent $event): void
    {
        $this->pusherFacade->send(
            new SkipData($event->getUser(), $event->getEventParty())
        );
    }

    public function onMeetingPointOffered(MeetingPointOfferedEvent $event)
    {
        $this->pusherFacade->send(
            new MeetingPointOfferData(
                $event->getUser(),
                $event->getEventParty(),
                $event->getOffer()->getId(),
                $event->getPlace(),
                $event->getMeetingDateTime()
            )
        );
    }

    public function onMeetingPointOfferAnswered(MeetingPointOfferAnsweredEvent $event)
    {
        $eventParty = $event->getEventParty();
        $offer      = $event->getOffer();

        $this->pusherFacade->send(
            new MeetingPointOfferAnswerData($eventParty->getId(), $offer->getId(), $event->getAnswer())
        );

        if ($eventParty->isOfferAccepted($offer)) {
            $day   = clone $offer->getData()->getDay();
            $time  = clone $offer->getData()->getTime();
            $place = $offer->getData()->getPlace();

            $meetingDateTime = $day->modify($time->format('H:i:s'));

            $this->dispatcher->dispatch(
                Events::MEETING_POINT_OFFER_ACCEPTED,
                new MeetingPointOfferAcceptedEvent($eventParty, $place, $meetingDateTime)
            );
        }
    }

    public function onMeetingPointOfferAccepted(MeetingPointOfferAcceptedEvent $event)
    {
        $eventParty = $event->getEventParty();

        $eventParty->setMeetingAt($event->getMeetingDateTime());
        $eventParty->setMeetingPlace($event->getPlace());
        $eventParty->markAsReady();

        $this->em->persist($eventParty);
        $this->em->flush();

        $this->pusherFacade->send(new MeetingPointOfferAcceptedData($eventParty));
    }
}
