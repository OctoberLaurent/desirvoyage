<?php

namespace App\Tests\Service;

use App\Entity\Reservation;
use App\Entity\Stays;
use App\Entity\Traveler;
use App\Repository\ReservationRepositoryInterface;
use App\Service\ExpiredReservationCleanupService;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

final class ExpiredReservationCleanupServiceTest extends TestCase
{
    public function testExpiredReservationIsCancelledAndStockReleased(): void
    {
        $stay = new Stays();
        $stay->setStock(10);
        $stay->setPrice(500.0);

        $expired = $this->buildReservation($stay, 2, new \DateTime('-20 minutes'));

        $repo = $this->createMock(ReservationRepositoryInterface::class);
        $repo->method('findUnpaid')->willReturn([$expired]);

        $em = $this->createMock(EntityManagerInterface::class);
        $em->expects(self::once())->method('remove')->with($expired);
        $em->expects(self::atLeastOnce())->method('flush');

        $result = (new ExpiredReservationCleanupService($repo, $em))->cleanup();

        self::assertSame(1, $result['cancelled']);
        self::assertSame(2, $result['seats_released']);
        self::assertSame(1, $result['total_unpaid']);
        self::assertSame(12, $stay->getStock()); // 10 + 2 released
    }

    public function testRecentReservationIsKept(): void
    {
        $stay = new Stays();
        $stay->setStock(10);

        $recent = $this->buildReservation($stay, 1, new \DateTime('-5 minutes'));

        $repo = $this->createMock(ReservationRepositoryInterface::class);
        $repo->method('findUnpaid')->willReturn([$recent]);

        $em = $this->createMock(EntityManagerInterface::class);
        $em->expects(self::never())->method('remove');

        $result = (new ExpiredReservationCleanupService($repo, $em))->cleanup();

        self::assertSame(0, $result['cancelled']);
        self::assertSame(0, $result['seats_released']);
        self::assertSame(10, $stay->getStock()); // unchanged
    }

    private function buildReservation(Stays $stay, int $travelersCount, \DateTime $createdDate): Reservation
    {
        $reservation = new Reservation();
        $reservation->addStay($stay);
        $reservation->setCreatedDate($createdDate);

        $travelers = new ArrayCollection();
        for ($i = 0; $i < $travelersCount; ++$i) {
            $travelers->add(new Traveler());
        }
        $reservation->setTravelers($travelers);

        return $reservation;
    }
}
