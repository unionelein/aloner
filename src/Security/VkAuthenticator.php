<?php

namespace App\Security;

use App\Entity\User;
use App\Repository\VkUserTokenRepository;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;

class VkAuthenticator extends AbstractGuardAuthenticator
{
    /** @var UrlGeneratorInterface */
    private $urlGenerator;

    /** @var VkUserTokenRepository */
    private $vkTokenRepo;

    public function __construct(UrlGeneratorInterface $urlGenerator, VkUserTokenRepository $vkTokenRepo)
    {
        $this->urlGenerator = $urlGenerator;
        $this->vkTokenRepo  = $vkTokenRepo;
    }

    public function supports(Request $request)
    {
        return 'app_vk_auth' === $request->attributes->get('_route')
            && null !== $request->get('user_id')
            && null !== $request->get('token_hash');
    }

    public function getCredentials(Request $request)
    {
        return [
            'userId' => $request->get('user_id'),
            'token'  => $request->get('token_hash'),
        ];
    }

    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        $token = $this->vkTokenRepo->findOneBy(['vkUserId' => $credentials['userId']]);

        if (!$token) {
            throw new \Exception('No token for user found');
        }

        return $token->getUser();
    }

    /**
     * @param mixed $credentials
     * @param UserInterface|User $user
     * @return bool
     */
    public function checkCredentials($credentials, UserInterface $user)
    {
        $token = $user->getVkToken();

        if ($token->isExpired()) {
            return false;
        }

        return $token->getToken() === $credentials['token'];
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        return new RedirectResponse($this->urlGenerator->generate('app_vk_auth'));
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        if ($targetPath = $request->getSession()->get('_security.'.$providerKey.'.target_path')) {
            return new RedirectResponse($targetPath);
        }

        return new RedirectResponse($this->urlGenerator->generate('app_main'));
    }

    public function start(Request $request, AuthenticationException $authException = null)
    {
        $vkAuthUrl = $this->urlGenerator->generate('app_vk_auth');

        return new RedirectResponse($vkAuthUrl);
    }

    public function supportsRememberMe()
    {
        return true;
    }
}
