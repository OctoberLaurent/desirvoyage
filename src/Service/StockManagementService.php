<?php

namespace App\Service;

use App\Entity\Reservation;
use App\Entity\Stays;
use App\Repository\StaysRepository;

final class StockManagementService
{
    public function __construct(private readonly StaysRepository $stayRepo)
    {
    }

    public function decrementStock(Reservation $reservation): int
    {
        $stay = $reservation->getStays()->first();
        if (!$stay instanceof Stays) {
            return 0;
        }
        $realStock = $this->stayRepo->findStockById($stay->getId() ?? 0);
        $nbtravelers = count($reservation->getTravelers());
        $stay->setStock($realStock - $nbtravelers);

        return $realStock;
    }
}
