<?php

namespace App\Service;

use App\Entity\Reservation;
use App\Entity\Stay;
use App\Repository\ReservationRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Annule les réservations non payées depuis plus de 15 minutes et libère
 * les sièges correspondants (cas d'utilisation métier, skill §3 Service).
 *
 * La durée d'expiration est configurable (défaut 15 minutes).
 */
final readonly class ExpiredReservationCleanupService
{
    /** Durée d'expiration en minutes. */
    public const int DEFAULT_EXPIRATION_MINUTES = 15;

    public function __construct(
        private ReservationRepositoryInterface $reservationRepository,
        private EntityManagerInterface $entityManager,
    ) {
    }

    /**
     * @return array{cancelled: int, seats_released: int, total_unpaid: int}
     */
    public function cleanup(int $expirationMinutes = self::DEFAULT_EXPIRATION_MINUTES): array
    {
        $createdBefore = new \DateTimeImmutable(sprintf('-%d minutes', $expirationMinutes));

        return $this->entityManager->wrapInTransaction(function () use ($createdBefore): array {
            $expiredReservations = $this->reservationRepository->findExpiredPending($createdBefore);
            $cancelled = 0;
            $seats = 0;

            foreach ($expiredReservations as $reservation) {
                $stay = $reservation->getStays()->first();
                if (!$stay instanceof Stay) {
                    continue;
                }
                $this->entityManager->lock($stay, \Doctrine\DBAL\LockMode::PESSIMISTIC_WRITE);
                $nbTravelers = $this->releaseSeats($stay, $reservation);
                $seats += $nbTravelers;
                $reservation->expire();
                ++$cancelled;
            }

            return [
                'cancelled' => $cancelled,
                'seats_released' => $seats,
                'total_unpaid' => count($expiredReservations),
            ];
        });
    }

    /**
     * Remonte le stock du séjour du nombre de voyageurs de la réservation.
     *
     * @return int nombre de sièges libérés
     */
    private function releaseSeats(Stay $stay, Reservation $reservation): int
    {
        $nbTravelers = count($reservation->getTravelers());
        $stay->setStock($stay->getStock() + $nbTravelers);

        return $nbTravelers;
    }
}
