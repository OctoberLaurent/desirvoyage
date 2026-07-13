<?php

namespace App\Service;

use App\Entity\Reservation;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Validates a reservation: generates the serial number, reserves stock, merges
 * the entity (session → managed), and persists it. The transaction (flush)
 * belongs in the service, not the controller (skill §3, §8 Doctrine).
 *
 * Throws {@see NotEnoughStockException} when stock is insufficient.
 */
final readonly class ReservationService
{
    public function __construct(
        private MakeSerialService $serialService,
        private StockManagementService $stockManagementService,
        private ReservationMergeService $mergeService,
        private EntityManagerInterface $entityManager,
    ) {
    }

    /**
     * Persists the reservation and returns the managed entity with its ID.
     *
     * @throws NotEnoughStockException when not enough seats are available
     */
    public function validate(Reservation $reservation): Reservation
    {
        return $this->entityManager->wrapInTransaction(function () use ($reservation): Reservation {
            if (null === $reservation->getCreatedDate()) {
                $reservation->setSerial($this->serialService->makeSerial());
                $reservation->setCreatedDate(new \DateTime());
                $this->stockManagementService->reserveStock($reservation);
            } else {
                $reservation->setUpdatedAt(new \DateTime());
            }

            $merged = $this->mergeService->reservationMerge($reservation);
            $this->entityManager->persist($merged);

            return $merged;
        });
    }
}
