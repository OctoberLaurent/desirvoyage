<?php

namespace App\Controller;

use App\Entity\Reservation;
use App\Service\MakeSerialService;
use App\Repository\StaysRepository;
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
    public function dashboard(ReservationRepository $repo, TravelRepository $travelsRepository, StaysRepository $stayRepository, MakeSerialService $service)
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

    /**
     * @route("/show/{id}", name="admin_show")
     */
    public function show(Reservation $reservation){

        return $this->render('admin/show.html.twig', [
            'reservation' => $reservation,
            
        ]);
    }

}