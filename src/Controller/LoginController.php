<?php declare(strict_types=1);

namespace App\Controller;

use App\Component\Vk\Authentication\VkAuthService;
use App\Component\Vk\Authentication\VkSignUpService;
use App\Repository\UserRepository;
use App\Security\Authenticator\VkAuthenticator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;

class LoginController extends BaseController
{
    /**
     * @Route("/login", name="app_login")
     */
    public function login(
        Request $request,
        UserRepository $userRepo,
        VkAuthService $vkAuth,
        VkSignUpService $vkSignUpService,
        GuardAuthenticatorHandler $guardHandler,
        VkAuthenticator $vkAuthenticator
    ) {
        $accessCode  = $request->get('code');
        $redirectUrl = $this->generateUrl('app_login', [], UrlGeneratorInterface::ABSOLUTE_URL);

        if ($accessCode) {
            try {
                $accessToken = $vkAuth->getAccessToken($accessCode, $redirectUrl);
            } catch (\Exception $e) {
                return $this->render('login/login.html.twig', [
                    'vk_auth_url' => $vkAuth->getAuthorizeUrl($redirectUrl),
                ]);
            }

            $user = $userRepo->findOneBy(['vkUserId' => $accessToken->getUserId()]);

            if (!$user) {
                $user = $vkSignUpService->execute($accessToken);
            }

            return $guardHandler->authenticateUserAndHandleSuccess($user, $request, $vkAuthenticator, 'main');
        }

        return $this->render('login/login.html.twig', [
            'vk_auth_url' => $vkAuth->getAuthorizeUrl($redirectUrl),
        ]);
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout(): void
    {
    }
}
