<?php

namespace App\Tests\Service;

use App\Entity\Reservation;
use App\Entity\Stay;
use App\Entity\Traveler;
use App\Repository\StaysRepositoryInterface;
use App\Service\StockManagementService;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;

final class StockManagementServiceTest extends TestCase
{
    public function testDecrementStockSetsStayStockAndReturnsRealStock(): void
    {
        $stay = new Stay();
        $stay->setStock(10);
        $stay->setPrice(500.0);

        $reservation = new Reservation();
        $reservation->addStay($stay);
        $travelers = new ArrayCollection();
        for ($i = 0; $i < 3; ++$i) {
            $travelers->add(new Traveler());
        }
        $reservation->setTravelers($travelers);

        $repo = $this->createMock(StaysRepositoryInterface::class);
        $repo->expects(self::once())
            ->method('findStockById')
            ->willReturn(10);

        $realStock = (new StockManagementService($repo))->decrementStock($reservation);

        self::assertSame(10, $realStock);
        self::assertSame(7, $stay->getStock()); // 10 - 3 travelers
    }

    public function testDecrementStockReturnsZeroWhenNoStay(): void
    {
        $reservation = new Reservation();
        $repo = $this->createMock(StaysRepositoryInterface::class);
        $repo->expects(self::never())->method('findStockById');

        self::assertSame(0, (new StockManagementService($repo))->decrementStock($reservation));
    }
}
