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
final class ReservationService
{
    public function __construct(
        private readonly MakeSerialService $serialService,
        private readonly StockManagementService $stockManagementService,
        private readonly ReservationMergeService $mergeService,
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    /**
     * Persiste la réservation et renvoie l'entité managed (avec son id).
     *
     * @throws NotEnoughStockException si plus assez de places
     */
    public function validate(Reservation $reservation): Reservation
    {
        if (null === $reservation->getCreatedDate()) {
            $reservation->setSerial($this->serialService->makeSerial());
            $reservation->setCreatedDate(new \DateTime('now'));

            $realStock = $this->stockManagementService->decrementStock($reservation);
            if (count($reservation->getTravelers()) > $realStock) {
                throw NotEnoughStockException::create();
            }
        } else {
            $reservation->setUpdateAt(new \DateTime('now'));
        }

        $merged = $this->mergeService->reservationMerge($reservation);
        $this->entityManager->persist($merged);
        $this->entityManager->flush();

        return $merged;
    }
}
