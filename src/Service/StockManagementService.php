<?php

namespace App\Service;

use App\Repository\StaysRepository;

class StockManagementService
{
    private $stayRepo;

    public function __construct(StaysRepository $stayRepo){

        $this->stayRepo  = $stayRepo;
    }

    public function decrementStock($reservation){

        $realStock = $this->stayRepo->findStockByid($reservation->getStays()[0]->getId());
        $stay = $reservation->getStays()[0];
        $nbtravelers =  count($reservation->getTravelers());
        $stay->setStock($realStock - $nbtravelers);

        return $realStock;

    }
}