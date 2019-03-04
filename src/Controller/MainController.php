<?php declare(strict_types=1);
/**
 * This file is subject to the terms and conditions defined in file 'LICENSE', which is part of this source code
 * package. If the file is missing a copy can be found at:
 * https://gitlab.cybercoder.site/vj/policies-procedures-standards/blob/master/licensing/GHCM-LICENSE.
 */

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/")
 */
class MainController extends BaseController
{
    /**
     * @Route("/", name="app_main")
     */
    public function main()
    {
        return $this->render('main/main.html.twig');
    }
}
