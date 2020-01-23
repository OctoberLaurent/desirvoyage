<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/travels")
 */
class TravelController extends AbstractController
{
    /**
     * @Route("", name="travel")
     */
    public function index()
    {
        return $this->render('travel/travel.html.twig', [
            'controller_name' => 'TravelController',
        ]);
    }
}
