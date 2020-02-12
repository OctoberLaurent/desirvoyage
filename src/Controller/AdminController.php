<?php

namespace App\Controller;

use App\Repository\TravelRepository;
use App\Repository\ReservationRepository;
use Symfony\Component\Routing\Annotation\Route;
use EasyCorp\Bundle\EasyAdminBundle\Controller\EasyAdminController;

/**
 * @Route("/admin")
*/
class AdminController extends EasyAdminController
{
    /**
     * @Route("/dashboard", name="admin_dashboard")
     */
    public function dashboard(ReservationRepository $repo, TravelRepository $travelsRepository)
    {

        $reservations = $repo->findAll();
        $numbersOfTrips = count($reservations);

        $travels = $travelsRepository->findAll();
        $numbersOfTrips = count($travels);

        return $this->render('admin/dashboard.html.twig', [
            'reservations' => $reservations,
            'travels' => $travels,
            'numbersOfTrips' => $numbersOfTrips
        ]);
    }
}