<?php declare(strict_types=1);

namespace App\Controller;

use App\Component\Vk\Authentication\VkAuthService;
use App\Component\Vk\Authentication\VkSignUpService;
use App\Repository\UserRepository;
use App\Security\Authenticator\AppAuthenticator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;
use Symfony\Component\Security\Http\RememberMe\RememberMeServicesInterface;
use Exception;

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
        AppAuthenticator $appAuthenticator,
        RememberMeServicesInterface $rememberMeServices,
        TokenStorageInterface $tokenStorage
    ) {
        $accessCode  = $request->get('code');
        $redirectUrl = $this->generateUrl('app_login', [], UrlGeneratorInterface::ABSOLUTE_URL);

        if ($accessCode) {
            try {
                $accessToken = $vkAuth->getAccessToken($accessCode, $redirectUrl);
            } catch (Exception $e) {
                return $this->render('login/login.html.twig', [
                    'vk_auth_url' => $vkAuth->getAuthorizeUrl($redirectUrl),
                ]);
            }

            $user = $userRepo->findOneBy(['vkUserId' => $accessToken->getUserId()]);

            if (!$user) {
                $user = $vkSignUpService->execute($accessToken);
            }

            $response = $guardHandler->authenticateUserAndHandleSuccess($user, $request, $appAuthenticator, 'main');
            $response = $response ?? $this->redirectToRoute('app_user_account');
            $rememberMeServices->loginSuccess($request, $response, $tokenStorage->getToken());

            return $response;
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
