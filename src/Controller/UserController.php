<?php

namespace App\Controller;

use App\Component\DTO\Entity\UserDTO;
use App\Component\VO\Sex;
use App\Entity\User;
use App\Form\FillUserType;
use App\Security\Authenticator\VkAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;
use Symfony\Component\Security\Http\RememberMe\RememberMeServicesInterface;

class UserController extends BaseController
{
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
        $user = $this->getUser();
        $form = $this->createForm(FillUserType::class, UserDTO::create($user))
            ->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UserDTO $userDTO */
            $userDTO = $form->getData();

            $user->setName($userDTO->getName())
                ->setCity($userDTO->getCity())
                ->setBirthday($userDTO->getBirthday())
                ->setSex(new Sex($userDTO->getSex()))
                ->setPhone($userDTO->getPhone());

            $user->addRole(User::ROLE_FULL_REG);

            $em->persist($user);
            $em->flush();

            $response = $this->redirectToRoute('app_main');
            // authenticate when update role
            $guardHandler->authenticateUserAndHandleSuccess($user, $request, $vkAuthenticator, 'main');
            $rememberMeServices->loginSuccess($request, $response, $tokenStorage->getToken());

            return $response;
        }

        return $this->render('user/fill_user.twig', [
            'form' => $form->createView(),
        ]);
    }
}
