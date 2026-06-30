<?php

namespace App\Controller;

use App\Entity\Reservation;
use App\Entity\Stay;
use App\Form\ReservationOptionType;
use App\Form\TravelersType;
use App\Repository\OptionRepository;
use App\Repository\StayRepository;
use App\Service\NotEnoughStockException;
use App\Service\ReservationMergeService;
use App\Service\ReservationPricingService;
use App\Service\ReservationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route(path: '/reservation', name: 'reservation')]
#[IsGranted('ROLE_USER')]
final class ReservationController extends AbstractController
{
    /**
     * Start configure travel.
     */
    #[Route(path: '', name: '_index')]
    public function index(Request $request, SessionInterface $session, StayRepository $stayRepository): Response
    {
        // retrieve travel by get method
        $id = $request->query->get('stayid');
        // find stay by id
        $stay = $stayRepository->find($id);

        if (null === $stay) {
            $this->addFlash('red darken-4', 'Ce séjour est introuvable ou n\'existe plus.');

            return $this->redirectToRoute('travel_home');
        }

        // create a new travel object
        $reservation = new Reservation();
        // add stay in reservation
        $reservation->addStay($stay);
        /** @var \App\Entity\User $user */
        $user = $this->getUser();
        $reservation->setUser($user);
        // create a new session and add reservation
        $session->set('reservation', $reservation);

        return $this->render('reservation/index.html.twig', [
            'stay' => $stay,
        ]);
    }

    /**
     * Configure option.
     */
    #[Route(path: '/configure/{id}', name: '_option')]
    public function configure(Stay $stays, SessionInterface $session, Request $request, int $id, ReservationMergeService $reservationMergeService, OptionRepository $optionRepository): Response
    {
        // get session
        $reservation = $this->getReservationFromSession($session);

        // Check if reservation exists in session — if not, initialize it from the stay
        if (null === $reservation) {
            $reservation = new Reservation();
            $reservation->addStay($stays);
            /** @var \App\Entity\User $user */
            $user = $this->getUser();
            $reservation->setUser($user);
            $session->set('reservation', $reservation);
        } else {
            // Replace detached stays with the managed entity from ParamConverter
            // to avoid lazy-loading issues on serialized entities
            foreach ($reservation->getStays() as $oldStay) {
                $reservation->removeStay($oldStay);
            }
            $reservation->addStay($stays);
        }

        $reservationMergeService->reservationOptionsMerge($reservation);

        // Get travel ID for form options
        $travelId = null;
        $travel = $stays->getTravel();
        if (null !== $travel) {
            $travelId = $travel->getId();
        }

        // insert $reservation in form
        $form = $this->createForm(ReservationOptionType::class, $reservation, [
            'travel_id' => $travelId,
        ]);
        // retrieve request
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $session->set('reservation', $reservation);

            return $this->redirectToRoute('reservation_traveler', ['id' => $id]);
        }

        // Get travel options for display in template
        $travelOptions = [];
        $travel = $stays->getTravel();
        if (null !== $travel) {
            $travelOptions = $optionRepository->findOptions($travel->getId() ?? 0)->getQuery()->getResult();
        }

        return $this->render('reservation/configureOption.html.twig', [
            'stays' => $stays,
            'form' => $form->createView(),
            'reservation' => $reservation,
            'travelOptions' => $travelOptions,
        ]);
    }

    /**
     * configure travelers.
     */
    #[Route(path: '/configure/configureTravelers/{id}', name: '_traveler')]
    public function configureTravelers(Request $request, SessionInterface $session, int $id): Response
    {
        $reservation = $this->getReservationFromSession($session);
        if (null === $reservation) {
            return $this->redirectToRoute('reservation_list');
        }
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
            'reservation' => $reservation,
        ]);
    }

    /**
     * show configuration travel and calculate cost.
     */
    #[Route(path: '/summary', name: '_summary')]
    public function summary(SessionInterface $session, ReservationPricingService $pricingService): Response
    {
        $reservation = $this->getReservationFromSession($session);
        if (null === $reservation) {
            return $this->redirectToRoute('reservation_list');
        }
        // get numbers of travelers
        $nbtravelers = count($reservation->getTravelers());
        // if there is no registered traveler
        if ($nbtravelers < 1) {
            $firstStay = $reservation->getStays()->first();
            $id = $firstStay instanceof Stay ? $firstStay->getId() : 0;

            $this->addFlash(
                'orange',
                'OUPS ! Il n\'y a personne d\'enregistré sur ce voyage'
            );

            return $this->redirectToRoute('reservation_traveler', ['id' => $id]);
        }

        $price = $pricingService->applyPrice($reservation);

        return $this->render('reservation/summary.html.twig', [
            'reservation' => $reservation,
            'totalPrice' => $price['total'],
            'totalPriceOptions' => $price['options'],
        ]);
    }

    /**
     * Validate for travel payment.
     */
    #[Route(path: '/validate/', name: '_validate')]
    public function validate(SessionInterface $session, ReservationService $reservationService): RedirectResponse
    {
        $reservation = $this->getReservationFromSession($session);
        if (null === $reservation) {
            return $this->redirectToRoute('reservation_list');
        }

        try {
            $merged = $reservationService->validate($reservation);
        } catch (NotEnoughStockException $e) {
            $this->addFlash('red darken-4', $e->getMessage());

            return $this->redirectToRoute('travel_list');
        }

        $id = $merged->getId();
        $session->clear();
        $session->set('id', $id);

        return $this->redirectToRoute('payment_create', ['id' => $id]);
    }

    /**
     * reservations list.
     */
    #[Route(path: '/list/', name: '_list')]
    public function reservationsList(): Response
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();
        $reservation = $user->getReservations();

        return $this->render('reservation/reservationlist.html.twig', [
            'user' => $user,
            'reservation' => $reservation,
        ]);
    }

    /**
     * remove travel in session.
     */
    #[Route(path: '/remove/', name: '_remove')]
    public function remove(SessionInterface $session): RedirectResponse
    {
        $session->set('reservation', null);

        $this->addFlash('red darken-4', 'Vous avez annulé votre voyage');

        return $this->redirectToRoute('travel_home');
    }

    private function getReservationFromSession(SessionInterface $session): ?Reservation
    {
        $reservation = $session->get('reservation');

        return $reservation instanceof Reservation ? $reservation : null;
    }
}
