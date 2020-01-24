<?php

namespace App\Controller;

use App\Entity\Travel;
use App\Form\TravelType;
use App\Repository\TravelRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/travels")
 */
class TravelController extends AbstractController
{
    /**
     * @Route("", name="travels")
     */
    public function travels(TravelRepository $repo)
    {
        $travels = $repo->findAll();

        return $this->render('travel/travel.html.twig', [
            'travels' => $travels
        ]);
    }

    /**
     * @Route("/new", name="new_travel")
     */
    public function newTravel(Request $request)
    {
        $travel = new Travel();
        $form = $this->createForm(TravelType::class, $travel);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
        $task = $form->getData();

        return $this->redirectToRoute('home');
    }

        return $this->render('travel/new.html.twig', [
            'form' => $form->createView()
        ]);
    }

}
