<?php declare(strict_types=1);

namespace App\Controller;

use App\Component\Vk\Authentication\VkAuthService;
use App\Component\Vk\Authentication\VkSignUpService;
use App\Repository\VkUserExtensionRepository;
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
        VkAuthService $vkAuth,
        VkSignUpService $vkSignUpService,
        VkUserExtensionRepository $vkExtensionRepo,
        GuardAuthenticatorHandler $guardHandler,
        VkAuthenticator $vkAuthenticator
    ) {
        $accessCode  = $request->get('code');
        $redirectUrl = $this->generateUrl('app_vk_auth', [], UrlGeneratorInterface::ABSOLUTE_URL);

        if ($accessCode && ($accessToken = $vkAuth->getAccessToken($accessCode, $redirectUrl))) {
            $vkExtension = $vkExtensionRepo->findOneBy(['vkUserId' => $accessToken->getUserId()]);

            if (!$vkExtension) {
                // create new user with vk extension
                $user = $vkSignUpService->execute($accessToken);
                $vkExtension = $user->getVkExtension();
            }

            return $guardHandler->authenticateUserAndHandleSuccess(
                $vkExtension->getUser(),
                $request,
                $vkAuthenticator,
                'main'
            );
        }

        return $this->render('security/vk_auth.html.twig', [
            'vk_auth_url' => $vkAuth->getAuthorizeUrl($redirectUrl),
        ]);
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout()
    {
    }
}
