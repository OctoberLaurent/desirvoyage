<?php

namespace App\Service;

use App\Entity\Reservation;
use App\Entity\Stay;
use App\ValueObject\Money;

/**
 * Calculates the reservation price: (stay price + option total) × travelers.
 * Business logic is extracted from the controller (skill §1 SRP, §3 Service).
 */
final class ReservationPricingService
{
    /**
     * Calculates and applies the total price to the reservation.
     *
     * @return array{total: float, options: float} total and option share (× travelers)
     */
    public function applyPrice(Reservation $reservation): array
    {
        $nbTravelers = $reservation->getTravelers()->count();

        $firstStay = $reservation->getStays()->first();
        $stayPrice = $firstStay instanceof Stay ? $firstStay->getPrice() : Money::fromCents(0);

        $optionsPrice = Money::fromCents(0);
        foreach ($reservation->getOptions() as $option) {
            $optionsPrice = $optionsPrice->add($option->getPrice());
        }

        $totalPriceOptions = $optionsPrice->multiply($nbTravelers);
        $total = $stayPrice->add($optionsPrice)->multiply($nbTravelers);
        $reservation->setPrice($total);

        return ['total' => $total->amount(), 'options' => $totalPriceOptions->amount()];
    }
}
