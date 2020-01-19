<?php declare(strict_types=1);

namespace App\Security\Authenticator;

use App\Entity\User;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;

class VkAuthenticator extends AbstractGuardAuthenticator
{
    /** @var RouterInterface */
    private $router;

    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    public function supports(Request $request)
    {
        return false;
    }

    public function getCredentials(Request $request)
    {
        return [];
    }

    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        return null;
    }

    public function checkCredentials($credentials, UserInterface $user)
    {
        return true;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        return new RedirectResponse($this->router->generate('app_login'));
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        /** @var null|User $user */
        $user = $token->getUser();

        if ($user && !$user->isFilled()) {
            return new RedirectResponse($this->router->generate('app_account'));
        }

        if ($targetPath = $request->getSession()->get('_security.'.$providerKey.'.target_path')) {
            return new RedirectResponse($targetPath);
        }

        return new RedirectResponse($this->router->generate('app_main'));
    }

    public function start(Request $request, AuthenticationException $authException = null)
    {
        return new RedirectResponse($this->router->generate('app_login'));
    }

    public function supportsRememberMe()
    {
        return true;
    }
}
