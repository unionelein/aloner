<?php

namespace App\Controller;


use App\Component\Vk\Authentication\VkAuthService;
use App\Component\Authentication\VkSignUpService;
use App\Entity\User;
use App\Repository\VkUserTokenRepository;
use App\Security\Authenticator\VkAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;
use Symfony\Component\Security\Http\RememberMe\RememberMeServicesInterface;

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
     * @IsGranted(User::ROLE_PARTIAL_REG)
     * @Route("/fill_user", name="app_fill_user")
     */
    public function fillUser(
        Request $request,
        EntityManagerInterface $em,
        GuardAuthenticatorHandler $guardHandler,
        VkAuthenticator $vkAuthenticator,
        RememberMeServicesInterface $rememberMeServices,
        TokenStorageInterface $tokenStorage
    ): Response {
        $sex = $request->get('sex');
        $phone = $request->get('phone');

        if ($request->isMethod('POST') && null !== $sex && $phone) {
            $user = $this->getUser();
            $user->setPhone($phone);
            $user->setSex((bool)$sex);
            $user->addRole(User::ROLE_FULL_REG);

            $em->persist($user);
            $em->flush();

            $response = $this->redirectToRoute('app_main');
            // authenticate when update role
            $guardHandler->authenticateUserAndHandleSuccess($user, $request, $vkAuthenticator, 'main');
            $rememberMeServices->loginSuccess($request, $response, $tokenStorage->getToken());

            return $response;
        }

        return $this->render('security/fill_user.twig');
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout()
    {
    }
}
