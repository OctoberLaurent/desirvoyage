<?php

namespace App\Controller;

use DateTime;
use App\Entity\Stays;
use App\Entity\Reservation;
use App\Form\TravelersType;
use App\Service\MakeSerialService;
use App\Form\ReservationOptionType;
use App\Repository\StaysRepository;
use App\Repository\OptionsRepository;
use App\Service\StockManagementService;
use App\Service\ReservationMergeService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;


#[Route(path: '/reservation', name: 'reservation')]
class ReservationController extends AbstractController
{
	/**
     * Start configure travel
     *
     *
     * @IsGranted("ROLE_USER")
     */
    #[Route(path: '', name: '_index')]
    public function index(Request $request, SessionInterface $session, StaysRepository $stayRepository, OptionsRepository $optionRepository)
	{
		// retrieve travel by get method
		$id = $request->query->get('stayid');
		// find stay by id
		$stay = $stayRepository->find($id);
		// create a new travel object
		$reservation = new Reservation();
		// add stay in reservation
		$reservation->addStay($stay);
		$reservation->setUser($this->getUser());
		// create a new session and add reservation
		$session->set('reservation', $reservation);

		return $this->render('reservation/index.html.twig', [
			'stay' => $stay
		]);
	}

	/**
     * Configure option
     *
     *
     * @IsGranted("ROLE_USER")
     */
    #[Route(path: '/configure/{id}', name: '_option')]
    public function configure(Stays $stays, SessionInterface $session, Request $request,
							  $id, ReservationMergeService $seservationMergeService) 
	{
		// get session
		$reservation = $session->get('reservation');

		$seservationMergeService->reservationOptionsMerge($reservation);
		// insert $reservation in form
		$form = $this->createForm(ReservationOptionType::class, $reservation);
		// retrieve request
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {

			$session->set('reservation', $reservation);
			return $this->redirectToRoute('reservation_traveler', ['id' => $id]);
		}

		return $this->render('reservation/configureOption.html.twig', [
			'stays' => $stays,
			'form' => $form->createView(),
			'reservation' => $reservation
		]);
	}

	/**
     * configure travelers
     *
     *
     * @IsGranted("ROLE_USER")
     */
    #[Route(path: '/configure/configureTravelers/{id}', name: '_traveler')]
    public Function configureTravelers(Request $request, SessionInterface $session, $id)
	{
		if ($session->get('reservation') == null) {
			return $this->redirectToRoute('reservation_list');
		};
		// get session
		$reservation = $session->get('reservation');
		// insert $reservation in form
		$form = $this->createForm(TravelersType::class, $reservation);
		// retrieve request
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {

			$session->set('reservation', $reservation);
			return $this->redirectToRoute('reservation_summary');
		}

		return $this->render('reservation/configureTravelers.html.twig', [
			'form' => $form->createView(),
			'reservation' => $reservation
		]);

	}

	/**
     * show configuration travel and calculate cost
     *
     *
     * @IsGranted("ROLE_USER")
     */
    #[Route(path: '/summary', name: '_summary')]
    public function summary(SessionInterface $session) 
	{
		if ($session->get('reservation') == null) {
			return $this->redirectToRoute('reservation_list');
		};
		$reservation = $session->get('reservation');
		// get numbers of travelers
		$nbtravelers = count($reservation->getTravelers());
		// if there is no registered traveler
		if ($nbtravelers < 1) {

			$id = $reservation->getStays()[0]->getId();

			$this->addFlash(
				'orange',
				'OUPS ! Il n\'y a personne d\'enregistré sur ce voyoyage'
			);

			return $this->redirectToRoute("reservation_traveler", ['id' => $id]);

		}

		// get price stays for 1 traveler
		$stayPrice = $reservation->getStays()[0]->getPrice();
		// get price for all options
		$options = $reservation->getOptions();
		$optionsPrice = 0;
		foreach ($options as $option) {
			$optionsPrice += $option->getPrice();
		}
		// set total price
		$totalPriceOptions = ($optionsPrice) * $nbtravelers;
		$totalPrice = ($stayPrice + $optionsPrice) * $nbtravelers;
		$reservation->setPrice($totalPrice);

		return $this->render('reservation/summary.html.twig', [
			'reservation' => $reservation,
			'totalPrice' => $totalPrice,
			'totalPriceOptions' => $totalPriceOptions
		]);
	}

	/**
     * Validate for travel payment
     *
     *
     * @IsGranted("ROLE_USER")
     */
    #[Route(path: '/validate/', name: '_validate')]
    public function validate(SessionInterface $session, MakeSerialService $service,
							 ReservationMergeService $seservationMergeService, StockManagementService $stockManagementService)
	{
		if ($session->get('reservation') == null) {
			return $this->redirectToRoute('reservation_list');
		};
		// reservation in session
		$reservation = $session->get('reservation');

		// make serial and date
		if ($reservation->getSerial() == null) {

			$reservation->setSerial($service->makeSerial());
			$reservation->setCreateddate(new \DateTime('now'));
			// stock management
			$realStock = $stockManagementService->decrementStock($reservation);

			// if stays is not in stock
			if (count($reservation->getTravelers()) > $realStock) {

				$this->addFlash(
					'red darken-4',
					'Il ne reste pas suffisamment de place 
                merci de choisir un autre voyage ou une autre période '
				);

				return $this->redirectToRoute("travel_list");
			}

		} else {
			$reservation->setUpdateAt(new \DateTime('now'));
		}

		$entityManager = $this->getDoctrine()->getManager();
		// manage persist in service
		$merged = $seservationMergeService->reservationMerge($reservation);

		$entityManager->persist($merged);

		$entityManager->flush();

		$id = $merged->getId();
		$session->clear();
		$session->set('id', $id);
		return $this->redirectToRoute('payment_create', ['id' => $id]);
	}

	/**
     * reservations list
     *
     *
     * @IsGranted("ROLE_USER")
     */
    #[Route(path: '/list/', name: '_list')]
    public function reservationsList()
	{
		$user = $this->getUser();
		$reservation = $user->getReservations();

		return $this->render('reservation/reservationlist.html.twig', [
			'user' => $user,
			'reservation' => $reservation,
		]);

	}

	/**
     * remove travel in session
     *
     *
     * @IsGranted("ROLE_USER")
     */
    #[Route(path: '/remove/', name: '_remove')]
    public function remove(SessionInterface $session)
	{
		$session->set('reservation', null);

		$this->addFlash('red darken-4', 'Vous avez annulé votre voyage');

		return $this->redirectToRoute("travel_home");
	}
}