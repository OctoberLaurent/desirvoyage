<?php

namespace App\Service;

use App\Entity\Reservation;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Valide une réservation : génère le numéro de série, décrémente le stock,
 * fusionne l'entité (session → managed) et persiste. La transaction (flush)
 * est dans le service, pas dans le contrôleur (skill §3, §8 Doctrine).
 *
 * Lève {@see NotEnoughStockException} si le stock est insuffisant.
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
     * Persiste la réservation et renvoie l'entité managed (avec son id).
     *
     * @throws NotEnoughStockException si plus assez de places
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
