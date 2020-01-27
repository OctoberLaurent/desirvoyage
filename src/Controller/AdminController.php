<?php

namespace App\Controller;

use App\Repository\TravelRepository;
use Symfony\Component\Routing\Annotation\Route;
use EasyCorp\Bundle\EasyAdminBundle\Controller\EasyAdminController;
use Symfony\Component\Validator\Constraints\Length;

/**
 * @Route("/admin")
*/
class AdminController extends EasyAdminController
{
    /**
     * @Route("/dashboard", name="admin_dashboard")
     */
    public function dashboard(TravelRepository $repo)
    {

        $travels = $repo->findAll();
        $numbersOfTrips = count($travels);

        return $this->render('admin/dashboard.html.twig', [
            'travels' => $travels,
            'numbersOfTrips' => $numbersOfTrips
        ]);
    }
}