<?php declare(strict_types=1);
/**
 * This file is subject to the terms and conditions defined in file 'LICENSE', which is part of this source code
 * package. If the file is missing a copy can be found at:
 * https://gitlab.cybercoder.site/vj/policies-procedures-standards/blob/master/licensing/GHCM-LICENSE.
 */

namespace App\EventSubscriber;

use App\Component\Events\EPActionEvent;
use App\Component\Events\EPEvent;
use App\Component\Events\Events;
use App\Component\Messaging\EventParty\Model\Pusher\Data\FilledData;
use App\Component\Messaging\EventParty\PusherFacade;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class EPProgressSubscriber implements EventSubscriberInterface
{
    /** @var PusherFacade */
    private $pusherFacade;

    /** @var EntityManagerInterface */
    private $em;

    /**
     * @param PusherFacade           $pusherFacade
     * @param EntityManagerInterface $em
     */
    public function __construct(
        PusherFacade $pusherFacade,
        EntityManagerInterface $em
    ) {
        $this->pusherFacade = $pusherFacade;
        $this->em           = $em;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents(): array
    {
        return [
            Events::EP_FILLED => 'onEventPartyFilled',
            Events::EP_LOAD   => 'onLoadEventParty',
        ];
    }

    /**
     * @param EPEvent $event
     */
    public function onEventPartyFilled(EPEvent $event): void
    {
        $this->pusherFacade->send(new FilledData($event->getEventParty()));
    }

    /**
     * @param EPActionEvent $event
     *
     * @throws \Exception
     */
    public function onLoadEventParty(EPActionEvent $event): void
    {

        $eventParty = $event->getEventParty();

        if ($eventParty->isReady() && new \DateTime() > $eventParty->getMeetingOptions()->getMeetingAt()) {
            $eventParty->markAsReviews();

            $this->em->persist($eventParty);
            $this->em->flush();
        }
    }
}
