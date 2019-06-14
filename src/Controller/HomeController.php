<?php declare(strict_types=1);

namespace App\Controller;

use App\Entity\SearchCriteria;
use App\Entity\User;
use App\Form\SearchCriteriaType;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @IsGranted(User::ROLE_PARTIAL_REG)
 * @Route("/")
 */
class HomeController extends BaseController
{
    /**
     * @Route("/", name="app_main")
     */
    public function main(Request $request, EntityManagerInterface $em)
    {
        $user = $this->getUser();

        if ($user->hasActiveEventParty()) {
            return $this->redirectToRoute('app_event_party_current');
        }

        $searchCriteriaForm = $this->createForm(SearchCriteriaType::class, $user->getSearchCriteria())
            ->handleRequest($request);

        if ($searchCriteriaForm->isSubmitted() && $searchCriteriaForm->isValid()) {
            /** @var SearchCriteria $searchCriteria */
            $searchCriteria = $searchCriteriaForm->getData();

            $em->persist($searchCriteria);
            $em->flush();

            return $this->redirectToRoute('app_join_to_event_party');
        }

        return $this->render('home/home.html.twig', [
            'searchCriteriaForm' => $searchCriteriaForm->createView(),
        ]);
    }
}
