<?php

namespace App\EventSubscriber;

use App\Event\EPActionEvent;
use App\Event\EPJoinedEvent;
use App\Event\MOAcceptedEvent;
use App\Event\MOAnsweredEvent;
use App\Event\MOOfferedEvent;
use App\Component\Messaging\EventParty\Model\Pusher\Data\JoinData;
use App\Component\Messaging\EventParty\Model\Pusher\Data\MOAcceptedData;
use App\Component\Messaging\EventParty\Model\Pusher\Data\MOAnswerData;
use App\Component\Messaging\EventParty\Model\Pusher\Data\MOOfferData;
use App\Component\Messaging\EventParty\Model\Pusher\Data\SkipData;
use App\Component\Messaging\EventParty\PusherFacade;
use App\Entity\VO\MeetingOptions;
use App\Event\EPSkippedEvent;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class EPActionSubscriber implements EventSubscriberInterface
{
    /** @var PusherFacade */
    private $pusherFacade;

    /** @var EventDispatcherInterface */
    private $dispatcher;

    /** @var EntityManagerInterface */
    private $em;

    /**
     * @param PusherFacade             $pusherFacade
     * @param EventDispatcherInterface $dispatcher
     * @param EntityManagerInterface   $em
     */
    public function __construct(
        PusherFacade $pusherFacade,
        EventDispatcherInterface $dispatcher,
        EntityManagerInterface $em
    ) {
        $this->pusherFacade = $pusherFacade;
        $this->dispatcher   = $dispatcher;
        $this->em = $em;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents(): array
    {
        return [
            EPJoinedEvent::class   => 'onEventPartyJoin',
            EPSkippedEvent::class  => 'onEventPartySkip',
            MOOfferedEvent::class  => 'onMOOffered',
            MOAnsweredEvent::class => 'onMOAnswered',
            MOAcceptedEvent::class => 'onMOAccepted',
        ];
    }

    /**
     * @param EPActionEvent $event
     */
    public function onEventPartyJoin(EPActionEvent $event): void
    {
        $this->pusherFacade->send(new JoinData($event->getUser(), $event->getEventParty()));
    }

    /**
     * @param EPActionEvent $event
     */
    public function onEventPartySkip(EPActionEvent $event): void
    {
        $this->pusherFacade->send(new SkipData($event->getUser(), $event->getEventParty()));
    }

    /**
     * @param MOOfferedEvent $event
     */
    public function onMOOffered(MOOfferedEvent $event): void
    {
        $offer = $event->getOffer();

        $this->pusherFacade->send(new MOOfferData($offer->getUser(), $offer->getEventParty(), $offer));
    }

    /**
     * @param MOAnsweredEvent $event
     */
    public function onMOAnswered(MOAnsweredEvent $event): void
    {
        $answer = $event->getAnswer();
        $offer  = $answer->getOfferHistory();
        $ep     = $answer->getEventParty();

        $this->pusherFacade->send(new MOAnswerData($answer->getUser(), $ep, $answer));

        if ($offer->isAccepted()) {
            $data = $offer->getData();
            $mo   = new MeetingOptions($data->getMeetingAt(), $data->getMeetingPlace());

            $this->dispatcher->dispatch(new MOAcceptedEvent($ep, $mo));
        }
    }

    /**
     * @param MOAcceptedEvent $event
     */
    public function onMOAccepted(MOAcceptedEvent $event): void
    {
        $meetingOptions = $event->getMeetingOptions();
        $eventParty     = $event->getEventParty();

        $eventParty->setMeetingOptions($meetingOptions);
        $this->em->persist($eventParty);
        $this->em->flush();

        $this->pusherFacade->send(new MOAcceptedData($eventParty, $meetingOptions));
    }
}
