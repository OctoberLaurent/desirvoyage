<?php

namespace App\Controller;

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
        $category = $request->query->get('category');
        
        if ($category){   
            $travels = $travelRepository->findBy(
                ['categories' => $category]
            );
        } else {
            $travels = $travelRepository->findall();
        }

        return $this->render('travel/travels.html.twig', [
            'travels' => $travels,
        ]);
    }
}
