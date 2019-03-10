<?php declare(strict_types=1);
/**
 * This file is subject to the terms and conditions defined in file 'LICENSE', which is part of this source code
 * package. If the file is missing a copy can be found at:
 * https://gitlab.cybercoder.site/vj/policies-procedures-standards/blob/master/licensing/GHCM-LICENSE.
 */

namespace App\Controller;

use App\Component\App\EventPartyFinder;
use App\Component\App\EventPartyService;
use App\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @IsGranted(User::ROLE_PARTIAL_REG)
 * @Route("/event_party")
 */
class EventPartyController extends BaseController
{
    /**
     * @Route("/", name="app_current_event_party")
     */
    public function currentEventParty()
    {
        $user       = $this->getUser();
        $eventParty = $user->getActiveEventParty();

        if (!$eventParty) {
            return $this->forward('App\Controller\MainController::main');
        }

        return $this->render('eventParty/event_party.html.twig', [
            'eventParty' => $eventParty,
        ]);
    }

    /**
     * @Route("/join", name="app_join_to_event_party")
     */
    public function join(EventPartyFinder $eventPartyFinder, EventPartyService $eventPartyService)
    {
        $user = $this->getUser();

        if ($user->hasActiveEventParty()) {
            return $this->forward('App\Controller\EventPartyController::currentEventParty');
        }

        $eventParty = $eventPartyFinder->findForUser($user) ?? $eventPartyService->createForUser($user);
        $user->joinToEventParty($eventParty);

        return $this->forward('App\Controller\EventPartyController::currentEventParty');
    }
}
