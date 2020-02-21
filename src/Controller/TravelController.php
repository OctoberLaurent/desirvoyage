<?php

namespace App\Controller;

use App\Entity\Travel;
use App\Form\TravelSearchType;
use App\Repository\TravelRepository;
use App\Repository\CategoriesRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


/**
 * @Route("/", name="travel")
 */
class TravelController extends AbstractController
{
	/**
	 * @Route("", name="_home")
	 */
	public function index(CategoriesRepository $categoriesReposotory, TravelRepository $travelReposotory)
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
			$random_travels[] = ($travels[$key]);

		}

		return $this->render('travel/index.html.twig', [
			'categories' => $categories,
			'travels' => $random_travels
		]);
	}

	/**
	 * @Route("/travels/{page}", name="_list")
	 */
	public function travels(TravelRepository $travelRepository, Request $request, $page = 1)
	{
		// get category 
		$category = $request->query->get('category');

		$form = $this->createForm(TravelSearchType::class, null);

		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {

			$travels = $travelRepository->findTravelsByNameAndPrice($form->getData());

		} elseif ($category) {
			$travels = $travelRepository->findBy(
				['categories' => $category]
			);
		} else {
			$travels = $travelRepository->findall();
		}

		return $this->render('travel/alltravels.html.twig', [
			'travels' => $travels,
			'page' => $page,
			'form' => $form->createView(),
		]);
	}

	/**
	 * Show One trave
	 * 
	 * @Route("/travel/{slug}", name="_show")
	 */
	public function showOne(Travel $travel)
	{
		return $this->render('travel/showone.html.twig', [
			'travel' => $travel
		]);
	}

	/**
	 * @Route("/categories/", name="_categorie_list")
	 */
	public function showAllCategorie(CategoriesRepository $repo)
	{
		$categories = $repo->findAll();

		return $this->render('travel/allcategories.html.twig', [
			'categories' => $categories
		]);
	}

	/**
	 * @Route("/terms/", name="_terms")
	 */
	public function showTerms()
	{
		return $this->render('travel/terms.html.twig');
	}

}
