<?php

namespace App\Service;

use App\Entity\Reservation;
use App\Entity\Stay;
use App\Repository\StaysRepositoryInterface;

final readonly class StockManagementService
{
    public function __construct(private StaysRepositoryInterface $stayRepo)
    {
    }

    public function decrementStock(Reservation $reservation): int
    {
        $stay = $reservation->getStays()->first();
        if (!$stay instanceof Stay) {
            return 0;
        }
        $realStock = $this->stayRepo->findStockById($stay->getId() ?? 0);
        $nbtravelers = count($reservation->getTravelers());
        $stay->setStock($realStock - $nbtravelers);

        return $realStock;
    }
}
