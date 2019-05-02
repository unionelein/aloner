<?php declare(strict_types=1);

namespace App\EventSubscriber;

use App\Component\Events\EventPartyActionEvent;
use App\Component\Events\Events;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class EventPartyStateListener implements EventSubscriberInterface
{
    /** @var EntityManagerInterface */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public static function getSubscribedEvents()
    {
        return [
            Events::LOAD_EVENT_PARTY=> 'onLoadEventParty',
        ];
    }

    public function onLoadEventParty(EventPartyActionEvent $event)
    {
        $eventParty = $event->getEventParty();

        if ($eventParty->isReady() && new \DateTime() > $eventParty->getMeetingAt()) {
            $eventParty->markAsFillReviews();

            $this->em->persist($eventParty);
            $this->em->flush();
        }
    }
}