<?php declare(strict_types=1);

namespace App\Controller;

use App\Entity\VO\SearchCriteria;
use App\Form\Type\SearchCriteriaType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends BaseController
{
    /**
     * @Route("/", name="app_main")
     */
    public function main(Request $request, EntityManagerInterface $em)
    {
        $user = $this->getUser();

        if ($user->hasActiveEventParty()) {
            return $this->redirectToRoute('app_ep_current');
        }

        $searchCriteriaForm = $this->createForm(SearchCriteriaType::class, $user->getSearchCriteria())
            ->handleRequest($request);

        if ($searchCriteriaForm->isSubmitted() && $searchCriteriaForm->isValid()) {
            /** @var SearchCriteria $searchCriteria */
            $searchCriteria = $searchCriteriaForm->getData();

            $user->setSearchCriteria($searchCriteria);

            $em->persist($user);
            $em->flush();

            return $this->redirectToRoute('app_find_ep');
        }

        return $this->render('home/home.html.twig', [
            'searchCriteriaForm' => $searchCriteriaForm->createView(),
        ]);
    }
}
