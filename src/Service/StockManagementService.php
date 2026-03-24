<?php

namespace App\Service;

use App\Repository\StaysRepository;

final class StockManagementService
{
    public function __construct(private readonly StaysRepository $stayRepo)
    {
    }

    public function decrementStock($reservation): int
    {
        $realStock = $this->stayRepo->findStockByid($reservation->getStays()[0]->getId());
        $stay = $reservation->getStays()[0];
        $nbtravelers = count($reservation->getTravelers());
        $stay->setStock($realStock - $nbtravelers);

        return $realStock;
    }
}
