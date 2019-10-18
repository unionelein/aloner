<?php declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Form\Type\UserType;
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
        $form = $this->createForm(UserType::class, $this->getUser())
            ->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var User $user */
            $user = $form->getData();

            if ($user->isFilled()) {
                $user->addRole(User::ROLE_FULL_REG);
            }

            $em->persist($user);
            $em->flush();

            $response = $this->redirectToRoute('app_main');
            // authenticate user when update role
            $guardHandler->authenticateUserAndHandleSuccess($user, $request, $vkAuthenticator, 'main');
            $rememberMeServices->loginSuccess($request, $response, $tokenStorage->getToken());

            return $response;
        }

        return $this->render('user/fill_user.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
