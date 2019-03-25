<?php

namespace App\Controller;

use App\Component\Vk\Authentication\VkAuthService;
use App\Component\Vk\Authentication\VkSignUpService;
use App\Repository\VkUserTokenRepository;
use App\Security\Authenticator\VkAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
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
        VkUserTokenRepository $vkTokenRepo,
        GuardAuthenticatorHandler $guardHandler,
        VkAuthenticator $vkAuthenticator
    ) {
        $accessCode  = $request->get('code');
        $redirectUrl = $this->generateUrl('app_vk_auth', [], UrlGeneratorInterface::ABSOLUTE_URL);

        if ($accessCode && ($accessToken = $vkAuth->getAccessToken($accessCode, $redirectUrl))) {
            $vkToken = $vkTokenRepo->findOneBy(['vkUserId' => $accessToken->getUserId()]);

            if (!$vkToken) {
                // create new user with vk token
                $user = $vkSignUpService->execute($accessToken);
                $vkToken = $user->getVkToken();
            }

            return $guardHandler->authenticateUserAndHandleSuccess(
                $vkToken->getUser(),
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
     * @Route("/user_temp_hash", name="app_user_temp_hash")
     */
    public function userTempHash(EntityManagerInterface $em): JsonResponse
    {
        $user = $this->getUser();
        $user->updateTempHash();

        $em->persist($user);
        $em->flush();

        return $this->json(['tempHash' => $user->getTempHash()]);
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout()
    {
    }
}
