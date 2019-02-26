<?php

namespace App\Controller;


use App\Component\Vk\Authentication\VkAuthService;
use App\Component\Vk\Authentication\VkSignUpService;
use App\Component\Vk\VkClient;
use App\Repository\VkUserTokenRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class SecurityController extends AbstractController
{
    /**
     * @Route("/vk_auth", name="app_vk_auth")
     */
    public function vkAuth(
        Request $request,
        VkAuthService $vkAuthService,
        VkSignUpService $vkSignUpService,
        VkUserTokenRepository $vkTokenRepo
    ) {
        $accessCode  = $request->get('code');
        $redirectUrl = $this->generateUrl('app_vk_auth');

        if ($accessCode) {
            $accessToken = $vkAuthService->getAccessToken($accessCode, $redirectUrl);
            $vkToken     = $vkTokenRepo->findOneBy(['vkUserId' => $accessToken->getUserId()]);

            if (!$vkToken) {
                // create new user with vk token
                $user    = $vkSignUpService->execute($accessToken);
                $vkToken = $user->getVkToken();
            }

            return $this->redirectToRoute('app_vk_auth', [
                'user_id'      => $vkToken->getVkUserId(),
                'token_hash'   => $vkToken->getToken(),
                '_remember_me' => true,
            ]);
        }

        return $this->render('security/vk_auth.html.twig', [
            'clientId'     => '123321123',
            'version'      => VkClient::VERSION,
            'redirect_uri' => $redirectUrl,
        ]);
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout()
    {
    }
}
