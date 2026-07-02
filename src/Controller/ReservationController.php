<?php

namespace App\Controller;

use App\Dto\TravelersDto;
use App\Entity\Reservation;
use App\Entity\Stay;
use App\Form\ReservationOptionType;
use App\Form\TravelersType;
use App\Repository\OptionRepositoryInterface;
use App\Repository\StayRepositoryInterface;
use App\Service\NotEnoughStockException;
use App\Service\ReservationMergeService;
use App\Service\ReservationPricingService;
use App\Service\ReservationService;
use Doctrine\Common\Collections\ArrayCollection;
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
    public function index(Request $request, SessionInterface $session, StayRepositoryInterface $stayRepository): Response
    {
        $id = $request->query->get('stayid');
        $stay = $stayRepository->find($id);

        if (null === $stay) {
            $this->addFlash('red darken-4', 'Ce séjour est introuvable ou n\'existe plus.');

            return $this->redirectToRoute('travel_home');
        }

        $reservation = new Reservation();
        $reservation->addStay($stay);
        /** @var \App\Entity\User $user */
        $user = $this->getUser();
        $reservation->setUser($user);
        $session->set('reservation', $reservation);

        return $this->render('reservation/index.html.twig', [
            'stay' => $stay,
        ]);
    }

    /**
     * Configure option.
     */
    #[Route(path: '/configure/{id}', name: '_option')]
    public function configure(Stay $stays, SessionInterface $session, Request $request, int $id, ReservationMergeService $reservationMergeService, OptionRepositoryInterface $optionRepository): Response
    {
        $reservation = $this->getReservationFromSession($session);

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

        $form = $this->createForm(ReservationOptionType::class, $reservation, [
            'travel_id' => $travelId,
        ]);
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
        // Le formulaire bind un DTO (skill §8), pas l'entité Reservation.
        $dto = TravelersDto::fromReservation($reservation);
        $form = $this->createForm(TravelersType::class, $dto);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $reservation->setTravelers($dto->toTravelers());
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
    public function summary(SessionInterface $session, ReservationPricingService $pricingService, StayRepositoryInterface $stayRepository): Response
    {
        $reservation = $this->getReservationFromSession($session);
        if (null === $reservation) {
            return $this->redirectToRoute('reservation_list');
        }
        // Re-attache des stays managés : la réservation en session porte des entités
        // détachées dont le proxy Travel ne peut lazy-loader après désérialisation
        // (sinon {{ stay.travel.name }} lève « must not be accessed before init »).
        $this->refreshStays($reservation, $stayRepository);
        $nbtravelers = count($reservation->getTravelers());
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

    /**
     * Remplace les stays de la réservation en session par leurs versions managées
     * (chargées fraîchement depuis la DB), afin que les proxies Travel puissent
     * lazy-loader lors du rendu (la session sérialise des entités détachées).
     */
    private function refreshStays(Reservation $reservation, StayRepositoryInterface $stayRepository): void
    {
        $ids = [];
        foreach ($reservation->getStays() as $stay) {
            $id = $stay->getId();
            if (null !== $id) {
                $ids[] = $id;
            }
        }

        $refreshed = new ArrayCollection();
        foreach ($ids as $id) {
            $managed = $stayRepository->find($id);
            if (null !== $managed) {
                $refreshed->add($managed);
            }
        }

        $reservation->setStays($refreshed);
    }
}
