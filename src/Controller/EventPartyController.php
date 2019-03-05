<?php declare(strict_types=1);
/**
 * This file is subject to the terms and conditions defined in file 'LICENSE', which is part of this source code
 * package. If the file is missing a copy can be found at:
 * https://gitlab.cybercoder.site/vj/policies-procedures-standards/blob/master/licensing/GHCM-LICENSE.
 */

namespace App\Controller;

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
        $user = $this->getUser();
        if (!($eventParty = $user->getActiveEventParty())) {
            return $this->forward('App\Controller\MainController::main');
        }

        return $this->render('eventParty/event_party.html.twig', [
            'eventParty' => $eventParty,
        ]);
    }

    /**
     * @Route("/join, name="app_join_to_event_party")
     */
    public function join()
    {
        $user = $this->getUser();
        if ($user->hasActiveEventParty()) {
            return $this->forward('App\Controller\EventPartyController::currentEventParty');
        }

        return $this->forward('App\Controller\EventPartyController::currentEventParty');
    }
}
