<?php declare(strict_types=1);

namespace App\Security;

use App\Entity\User;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Authorization\AccessDeniedHandlerInterface;

class AccessDeniedHandler implements AccessDeniedHandlerInterface
{
    /**
     * @var Security
     */
    private $security;

    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;

    public function __construct(Security $security, UrlGeneratorInterface $urlGenerator)
    {
        $this->security = $security;
        $this->urlGenerator = $urlGenerator;
    }

    public function handle(Request $request, AccessDeniedException $accessDeniedException)
    {
        /** @var null|User $user */
        $user = $this->security->getUser();

        if (!$user) {
            return new RedirectResponse($this->urlGenerator->generate('app_vk_auth'));
        }

        if (!$user->isFullFilled()) {
            return new RedirectResponse($this->urlGenerator->generate('app_fill_user'));
        }

        return new Response('no access', 403);
    }
}
