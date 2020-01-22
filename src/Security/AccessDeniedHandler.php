<?php declare(strict_types=1);

namespace App\Security;

use App\Entity\User;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Authorization\AccessDeniedHandlerInterface;

class AccessDeniedHandler implements AccessDeniedHandlerInterface
{
    /** @var Security */
    private $security;

    /** @var UrlGeneratorInterface */
    private $router;

    /**
     * @param Security        $security
     * @param RouterInterface $router
     */
    public function __construct(Security $security, RouterInterface $router)
    {
        $this->security = $security;
        $this->router   = $router;
    }

    /**
     * User exists, but has no access
     *
     * {@inheritdoc}
     */
    public function handle(Request $request, AccessDeniedException $accessDeniedException)
    {
        /** @var null|User $user */
        $user = $this->security->getUser();

        if (!$user) {
            return new RedirectResponse($this->router->generate('app_login'));
        }

        if (!$user->hasRole(User::ROLE_USER)) {
            return new RedirectResponse($this->router->generate('app_user_account'));
        }

        return new Response('no access', 403);
    }
}
