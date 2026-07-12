<?php

namespace App\Tests\Service;

use App\Entity\Reservation;
use App\Entity\Stay;
use App\Entity\Traveler;
use App\Repository\StayRepositoryInterface;
use App\Service\StockManagementService;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;

final class StockManagementServiceTest extends TestCase
{
    public function testReserveStockUsesLockedStayAndDecrementsIt(): void
    {
        $stay = new Stay();
        $stay->setStock(10);
        $stay->setPrice(500.0);
        $this->setEntityId($stay, 10);

        $reservation = new Reservation();
        $reservation->addStay($stay);
        $travelers = new ArrayCollection();
        for ($i = 0; $i < 3; ++$i) {
            $travelers->add(new Traveler());
        }
        $reservation->setTravelers($travelers);

        $repo = $this->createMock(StayRepositoryInterface::class);
        $repo->expects(self::once())
            ->method('findForUpdate')
            ->with(10)
            ->willReturn($stay);

        (new StockManagementService($repo))->reserveStock($reservation);

        self::assertSame(7, $stay->getStock()); // 10 - 3 travelers
    }

    public function testReserveStockRefusesInsufficientStockWithoutMutation(): void
    {
        $stay = new Stay();
        $stay->setStock(2);
        $this->setEntityId($stay, 10);

        $reservation = new Reservation();
        $reservation->addStay($stay);
        $reservation->setTravelers(new ArrayCollection([new Traveler(), new Traveler(), new Traveler()]));

        $repo = $this->createMock(StayRepositoryInterface::class);
        $repo->expects(self::once())->method('findForUpdate')->with(10)->willReturn($stay);

        try {
            (new StockManagementService($repo))->reserveStock($reservation);
            self::fail('Le stock insuffisant doit être refusé.');
        } catch (\App\Service\NotEnoughStockException) {
            self::assertSame(2, $stay->getStock());
        }
    }

    public function testReserveStockRefusesReservationWithoutStay(): void
    {
        $reservation = new Reservation();
        $repo = $this->createMock(StayRepositoryInterface::class);
        $repo->expects(self::never())->method('findForUpdate');

        $this->expectException(\DomainException::class);
        (new StockManagementService($repo))->reserveStock($reservation);
    }

    private function setEntityId(object $entity, int $id): void
    {
        $property = new \ReflectionProperty($entity, 'id');
        $property->setValue($entity, $id);
    }
}
