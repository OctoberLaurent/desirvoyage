<?php

namespace App\Service;

use App\Entity\Reservation;
use App\Entity\Stay;
use App\Repository\StayRepositoryInterface;

final readonly class StockManagementService
{
    public function __construct(private StayRepositoryInterface $stayRepo)
    {
    }

    public function reserveStock(Reservation $reservation): void
    {
        $stay = $reservation->getStays()->first();
        if (!$stay instanceof Stay) {
            throw new \DomainException('La réservation doit contenir un séjour.');
        }

        $stayId = $stay->getId();
        if (null === $stayId) {
            throw new \DomainException('Le séjour doit être persisté avant de réserver son stock.');
        }

        $lockedStay = $this->stayRepo->findForUpdate($stayId);
        if (!$lockedStay instanceof Stay) {
            throw new \DomainException('Le séjour demandé est introuvable.');
        }

        $travelerCount = $reservation->getTravelers()->count();
        if ($travelerCount < 1) {
            throw new \DomainException('La réservation doit contenir au moins un voyageur.');
        }
        if ($travelerCount > $lockedStay->getStock()) {
            throw NotEnoughStockException::create();
        }

        $lockedStay->setStock($lockedStay->getStock() - $travelerCount);
        $reservation->setStays(new \Doctrine\Common\Collections\ArrayCollection([$lockedStay]));
    }
}
