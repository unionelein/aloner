<?php

namespace App\Controller;

use App\Component\Vk\VkApiClient;
use App\Component\Vk\VkAuthService;
use App\Entity\User;
use App\Repository\VkUserTokenRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class SecurityController extends AbstractController
{
    /**
     * @Route("/vk_auth", name="app_vk_auth")
     */
    public function vkAuth(Request $request, VkAuthService $vkAuthService, VkUserTokenRepository $vkTokenRepo)
    {
        $accessCode  = $request->get('code');
        $redirectUrl = $this->generateUrl('app_vk_auth');

        if (!$accessCode) {
            return $this->render('security/vk_auth.html.twig', [
                'clientId'     => '123321123',
                'version'      => VkApiClient::VERSION,
                'redirect_uri' => $redirectUrl,
            ]);
        }

        $accessToken = $vkAuthService->getAccessToken($accessCode, $redirectUrl);
        $vkToken     = $vkTokenRepo->findOneBy(['vkUserId' => $accessToken->getUserId()]);
        if ($vkToken) {

            $user = new User();
            $user->setName($userId)
                ->setLogin($token);

            $em->persist($user);
            $em->flush();
        }

        $request->query->set('_remember_me', true);

        return $this->redirectToRoute();
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout()
    {

    }
}
