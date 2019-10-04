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

class SecurityController extends BaseController
{
    /**
     * @Route("/vk_auth", name="app_vk_auth")
     */
    public function vkAuth(
        Request $request,
        UserRepository $userRepo,
        VkAuthService $vkAuth,
        VkSignUpService $vkSignUpService,
        GuardAuthenticatorHandler $guardHandler,
        VkAuthenticator $vkAuthenticator
    ) {
        $accessCode  = $request->get('code');
        $redirectUrl = $this->generateUrl('app_vk_auth', [], UrlGeneratorInterface::ABSOLUTE_URL);

        if ($accessCode) {
            try {
                $accessToken = $vkAuth->getAccessToken($accessCode, $redirectUrl);
            } catch (\Exception $e) {
                return $this->render('security/vk_auth.html.twig', [
                    'vk_auth_url' => $vkAuth->getAuthorizeUrl($redirectUrl),
                ]);
            }

            $user = $userRepo->findOneBy(['vk.userId' => $accessToken->getUserId()]);

            if (!$user) {
                $user = $vkSignUpService->execute($accessToken);
            }

            return $guardHandler->authenticateUserAndHandleSuccess($user, $request, $vkAuthenticator, 'main');
        }

        return $this->render('security/vk_auth.html.twig', [
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
