<?php

namespace App\Tests\Service;

use App\Entity\Options;
use App\Entity\Reservation;
use App\Entity\Stays;
use App\Entity\Traveler;
use App\Service\ReservationPricingService;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;

final class ReservationPricingServiceTest extends TestCase
{
    public function testPriceIsStayPlusOptionsMultipliedByTravelers(): void
    {
        $stay = new Stays();
        $stay->setPrice(700.0);

        $option = new Options();
        $option->setPrice(100.0);

        $reservation = $this->buildReservation($stay, [$option], 3);

        $result = (new ReservationPricingService())->applyPrice($reservation);

        // total = (700 + 100) × 3 = 2400 ; options = 100 × 3 = 300
        self::assertSame(2400.0, $result['total']);
        self::assertSame(300.0, $result['options']);
        self::assertSame(2400.0, $reservation->getPrice());
    }

    public function testPriceWithNoOption(): void
    {
        $stay = new Stays();
        $stay->setPrice(500.0);

        $reservation = $this->buildReservation($stay, [], 2);

        $result = (new ReservationPricingService())->applyPrice($reservation);

        self::assertSame(1000.0, $result['total']);
        self::assertSame(0.0, $result['options']);
    }

    /**
     * @param list<Options> $options
     */
    private function buildReservation(Stays $stay, array $options, int $travelersCount): Reservation
    {
        $reservation = new Reservation();
        $reservation->addStay($stay);
        foreach ($options as $option) {
            $reservation->addOption($option);
        }

        $travelers = new ArrayCollection();
        for ($i = 0; $i < $travelersCount; ++$i) {
            $travelers->add(new Traveler());
        }
        $reservation->setTravelers($travelers);

        return $reservation;
    }
}
