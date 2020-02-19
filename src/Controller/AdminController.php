<?php

namespace App\Controller;

use App\Entity\Reservation;
use App\Service\MakeSerialService;
use App\Repository\StaysRepository;
use App\Repository\TravelRepository;
use App\Repository\ReservationRepository;
use App\Repository\UserRepository;
use Symfony\Component\Routing\Annotation\Route;
use EasyCorp\Bundle\EasyAdminBundle\Controller\EasyAdminController;
use Symfony\Component\Mime\Message;

/**
 * @Route("/admin")
*/
class AdminController extends EasyAdminController
{
    /**
     * @Route("/dashboard", name="admin_dashboard")
     */
    public function dashboard(ReservationRepository $repo, TravelRepository $travelsRepository, 
    StaysRepository $stayRepository, UserRepository $userRepo)
    {
        $totalStock = $stayRepository->findAllStock();
        
        $reservations = $repo->findAll();
        $numbersOfTrips = count($reservations);

        $travels = $travelsRepository->findAll();
        $numbersOfTrips = count($travels);

        $nbUsers = $userRepo->findNumberOfReservation();

        return $this->render('admin/dashboard.html.twig', [
            'reservations' => $reservations,
            'travels' => $travels,
            'numbersOfTrips' => $numbersOfTrips,
            'totalStock' => $totalStock,
            'nbUsers' => $nbUsers
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