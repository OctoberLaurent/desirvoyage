<?php

namespace App\Controller;

use App\Entity\Travel;
use App\Form\TravelSearchType;
use App\Repository\TravelRepository;
use App\Repository\CategoriesRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class TravelController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(CategoriesRepository $categoriesReposotory)
    {
        $categories = $categoriesReposotory->findBy([],[],3);

        return $this->render('travel/index.html.twig', [
            'categories' => $categories
        ]);
    }

    /**
     * @Route("/travels", name="travel_list")
     */
    public function travels(TravelRepository $travelRepository, Request $request)
    {
        // get category if exist
        $category = $request->query->get('category');
        
        $form = $this->createForm( TravelSearchType::class, null);

        $form->handleRequest($request);
            
        if ($form->isSubmitted() && $form->isValid()) {
            
            $search = ($request->request->get('travel_search'));
            $travels = ($travelRepository->findTravelsByNameAndPrice($search["maxprice"], $search["search"], $search["startdate"], $search["enddate"]));

        } elseif($category){   
            $travels = $travelRepository->findBy(
                ['categories' => $category]
            );
        } else {
            $travels = $travelRepository->findall();
        }

        return $this->render('travel/travels.html.twig', [
            'travels' => $travels,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/travel/{slug}", name="travel")
     */
    public function showOne(Travel $travel)
    {
        
        return $this->render('travel/showone.html.twig', [
            'travel' => $travel
        ]);
    }

    /**
     * @Route("/categories/", name="categorie_list")
     */
    public function showAllCategorie(CategoriesRepository $repo)
    {
        $categories = $repo->findAll();

        return $this->render('travel/allcategories.html.twig', [
            'categories' => $categories
        ]);
    }

}
