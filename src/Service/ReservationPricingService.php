<?php

namespace App\Service;

use App\Entity\Reservation;
use App\Entity\Stay;
use App\ValueObject\Money;

/**
 * Calcule le prix d'une réservation : (prix séjour + somme options) × nombre
 * de voyageurs. Logique métier extraite du contrôleur (skill §1 SRP, §3 Service).
 */
final class ReservationPricingService
{
    /**
     * Calcule et applique le prix total à la réservation.
     *
     * @return array{total: float, options: float} le total et la part options (× voyageurs)
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
