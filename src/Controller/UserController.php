<?php declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Form\Type\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends BaseController
{
    /**
     * @Route("/account", name="app_user_account")
     */
    public function account(Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(UserType::class, $this->getUser())
            ->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var User $user */
            $user = $form->getData();
            $user->addRole(User::ROLE_USER);

            $em->persist($user);
            $em->flush();

            return $this->redirectToRoute('app_main');
        }

        return $this->render('user/account.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
