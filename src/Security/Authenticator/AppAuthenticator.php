<?php declare(strict_types=1);

namespace App\Security\Authenticator;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;
use Symfony\Component\Security\Http\RememberMe\RememberMeServicesInterface;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

class AppAuthenticator extends AbstractGuardAuthenticator
{
    use TargetPathTrait;

    /** @var RouterInterface */
    private $router;

    /** @var RememberMeServicesInterface */
    private $rememberMeService;

    /**
     * @param RouterInterface             $router
     * @param RememberMeServicesInterface $rememberMeServices
     */
    public function __construct(RouterInterface $router, RememberMeServicesInterface $rememberMeServices)
    {
        $this->router            = $router;
        $this->rememberMeService = $rememberMeServices;
    }

    /**
     * Called when authentication is needed, but it's not sent (no user). Only for entry_point authenticator
     *
     * {@inheritdoc}
     */
    public function start(Request $request, AuthenticationException $authException = null)
    {
        return new RedirectResponse($this->router->generate('app_login'));
    }

    /**
     * {@inheritdoc}
     */
    public function supports(Request $request): bool
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getCredentials(Request $request)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function getUser($credentials, UserProviderInterface $userProvider)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function checkCredentials($credentials, UserInterface $user)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        $response = null;

        // Initial requested by user url
        if ($targetPath = $this->getTargetPath($request->getSession(), $providerKey)) {
            $response = new RedirectResponse($targetPath);
        }

        if (!$response) {
            $response = new RedirectResponse($this->router->generate('app_main'));
        }

        $this->rememberMeService->loginSuccess($request, $response, $token);

        return $response;
    }

    /**
     * This will be executed only if supports method will return true. Not for authenticateUserAndHandleSuccess.
     *
     * {@inheritdoc}
     */
    public function supportsRememberMe()
    {
    }
}