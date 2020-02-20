<?php

namespace App\Controller;

use App\Entity\Reservation;
use App\Repository\ContactRepository;
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
	 * Dashboard
	 *
	 * @Route("/dashboard", name="admin_dashboard")
	 */
	public function dashboard(ReservationRepository $reservationRepository, TravelRepository $travelsRepository,
							  StaysRepository $stayRepository, UserRepository $userRepo, ContactRepository $contactRepository)
	{
		// get stock all of stays
		$totalStock = $stayRepository->findAllStock();
		// find all reseravtion
		$reservations = $reservationRepository->findAll();
		$numbersOfTrips = count($reservations);
		// retrieves the number of messages
		$nbMessages = $contactRepository->countMessages();
		// retrieves trips and counts them
		$travels = $travelsRepository->findAll();
		$numbersOfTrips = count($travels);
		// retrieves the number of reservation
		$nbUsers = $userRepo->findNumberOfReservation();

		return $this->render('admin/dashboard.html.twig', [
			'reservations' => $reservations,
			'travels' => $travels,
			'numbersOfTrips' => $numbersOfTrips,
			'totalStock' => $totalStock,
			'nbUsers' => $nbUsers,
			'nbMessages' => $nbMessages
		]);
	}

	/**
	 * @route("/show/{id}", name="admin_show")
	 */
	public function show(Reservation $reservation)
	{

		return $this->render('admin/show.html.twig', [
			'reservation' => $reservation,

		]);
	}

	/**
	 * @route("/delete-reservation/{id}", name="admin_reservation_delete")
	 */
	public function delete(Reservation $reservation)
	{

		$em = $this->getDoctrine()->getManager();
		$em->remove($reservation);
		$em->flush();

		return $this->redirectToRoute('admin_dashboard');
	}

}
