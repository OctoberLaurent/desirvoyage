<?php

namespace App\Controller;

use App\Entity\Travel;
use App\Form\TravelSearchType;
use App\Repository\CategoriesRepository;
use App\Repository\TravelRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route(path: '/', name: 'travel')]
final class TravelController extends AbstractController
{
    /**
     * HomePage.
     */
    #[Route(path: '', name: '_home')]
    public function index(CategoriesRepository $categoriesReposotory, TravelRepository $travelReposotory): Response
    {
        // return 3 firsts categories
        $categories = $categoriesReposotory->findBy([], [], 3);

        // initializes the random travels
        $random_travels = [];

        // Get all travels
        $travels = $travelReposotory->findAll();

        // generate random key
        $keys = array_rand($travels, 6);

        // Creating a Random Object Array
        foreach ($keys as $key) {
            $random_travels[] = $travels[$key];
        }

        return $this->render('travel/index.html.twig', [
            'categories' => $categories,
            'travels' => $random_travels,
        ]);
    }

    /**
     * show all travels or travels in one category.
     */
    #[Route(path: '/travels/{page}', name: '_list')]
    public function travels(TravelRepository $travelRepository, Request $request, int $page = 1): Response
    {
        // get id category in get
        $category = $request->query->get('category');

        $form = $this->createForm(TravelSearchType::class, null);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            if (is_array($data)) {
                /** @var array<string, mixed> $data */
                $travels = $travelRepository->findTravelsByNameAndPrice($data);
            } else {
                $travels = [];
            }
        } elseif (null !== $category) {
            $travels = $travelRepository->findBy(
                ['categories' => $category]
            );
        } else {
            $travels = $travelRepository->findAll();
        }

        return $this->render('travel/alltravels.html.twig', [
            'travels' => $travels,
            'page' => $page,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Show One travel.
     */
    #[Route(path: '/travel/{slug}', name: '_show')]
    public function showOne(Travel $travel): Response
    {
        return $this->render('travel/showone.html.twig', [
            'travel' => $travel,
        ]);
    }

    /**
     * Show all categories.
     */
    #[Route(path: '/categories/', name: '_categorie_list')]
    public function showAllCategorie(CategoriesRepository $repo): Response
    {
        // retrieve all categories
        $categories = $repo->findAll();

        return $this->render('travel/allcategories.html.twig', [
            'categories' => $categories,
        ]);
    }

    #[Route(path: '/terms/', name: '_terms')]
    public function showTerms(): Response
    {
        return $this->render('travel/terms.html.twig');
    }
}
