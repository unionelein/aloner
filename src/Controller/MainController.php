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
 * @Route("/")
 */
class MainController extends BaseController
{
    /**
     * @Route("/", name="app_main")
     */
    public function main()
    {
        $user = $this->getUser();
        if ($user->hasActiveEventParty()) {
            return $this->forward('App\Controller\EventPartyController::currentEventParty');
        }

        return $this->render('main/main.html.twig');
    }
}
