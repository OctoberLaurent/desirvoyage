<?php

namespace App\Mate;

use App\Repository\ReservationRepository;
use Mcp\Capability\Attribute\McpTool;

class ReservationSummaryTool
{
    public function __construct(
        private ReservationRepository $reservationRepository,
    ) {
    }

    #[McpTool(
        name: 'reservation-summary',
        description: 'Récupère le résumé détaillé d\'une réservation à partir de son numéro de série. '
                   .'Inclut les informations voyage, voyageurs, options et paiement.'
    )]
    public function getSummary(string $serial): array
    {
        $reservation = $this->reservationRepository->findOneBy(['serial' => $serial]);

        if (null === $reservation) {
            return [
                'success' => false,
                'error' => sprintf('Aucune réservation trouvée avec le numéro "%s".', $serial),
            ];
        }

        // Informations sur les séjours (voyages réservés)
        $stays = [];
        foreach ($reservation->getStays() as $stay) {
            $stays[] = [
                'travel' => $stay->getTravel()?->getName(),
                'departure' => $stay->getDepature(),
                'arrival' => $stay->getArrival(),
                'startDate' => $stay->getStarDate()?->format('d/m/Y'),
                'endDate' => $stay->getEndDate()?->format('d/m/Y'),
                'price' => $stay->getPrice(),
            ];
        }

        // Informations sur les voyageurs
        $travelers = [];
        foreach ($reservation->getTravelers() as $traveler) {
            $travelers[] = [
                'firstName' => $traveler->getFirstname(),
                'lastName' => $traveler->getLastname(),
            ];
        }

        // Informations sur les options
        $options = [];
        foreach ($reservation->getOptions() as $option) {
            $options[] = [
                'name' => $option->getName(),
                'price' => $option->getPrice(),
            ];
        }

        return [
            'success' => true,
            'serial' => $reservation->getSerial(),
            'totalPrice' => $reservation->getPrice(),
            'createdAt' => $reservation->getCreatedDate()?->format('d/m/Y H:i'),
            'stays' => $stays,
            'travelers' => $travelers,
            'options' => $options,
            'hasPayment' => null !== $reservation->getPayment(),
        ];
    }

    #[McpTool(
        name: 'user-reservations',
        description: 'Liste toutes les réservations d\'un utilisateur donné (par son email).'
    )]
    public function getUserReservations(string $email): array
    {
        $reservations = $this->reservationRepository->createQueryBuilder('r')
            ->join('r.user', 'u')
            ->where('u.email = :email')
            ->setParameter('email', $email)
            ->getQuery()
            ->getResult();

        if (empty($reservations)) {
            return [
                'success' => false,
                'error' => sprintf('Aucune réservation trouvée pour "%s".', $email),
            ];
        }

        return [
            'success' => true,
            'count' => count($reservations),
            'reservations' => array_map(fn ($r) => [
                'serial' => $r->getSerial(),
                'price' => $r->getPrice(),
                'createdAt' => $r->getCreatedDate()?->format('d/m/Y'),
            ], $reservations),
        ];
    }
}
