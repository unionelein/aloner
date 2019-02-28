<?php

namespace App\Security\Authenticator;

use App\Entity\User;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;

class RememberMeAuthenticator extends AbstractGuardAuthenticator
{
    /** @var string */
    private $route;

    /** @var Security */
    private $security;

    /** @var UrlGeneratorInterface */
    private $urlGenerator;

    public function __construct(Security $security, UrlGeneratorInterface $urlGenerator)
    {
        $this->route        = 'app_remember_me';
        $this->security     = $security;
        $this->urlGenerator = $urlGenerator;
    }

    public function supports(Request $request)
    {
        /** @var null|User $user */
        $user = $this->security->getUser();

        return $request->attributes->get('_route') === $this->route
            && $user
            && $user->isFullFilled();
    }

    public function getCredentials(Request $request)
    {
        $request->query->set('_remember_me', true);

        return [];
    }

    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        return $this->security->getUser();
    }

    public function checkCredentials($credentials, UserInterface $user)
    {
        return true;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        return new RedirectResponse($this->urlGenerator->generate('app_main'));
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        $targetPath = $request->getSession()->get('_security.'.$providerKey.'.target_path');

        if ($targetPath && $targetPath !== $this->route) {
            return new RedirectResponse($targetPath);
        }

        return new RedirectResponse($this->urlGenerator->generate('app_main'));
    }

    public function start(Request $request, AuthenticationException $authException = null)
    {
        return new RedirectResponse($this->urlGenerator->generate('app_remember_me'));
    }

    public function supportsRememberMe()
    {
        return true;
    }
}
