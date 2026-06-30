<?php

namespace App\Service;

use App\Entity\Reservation;
use App\Entity\Stay;

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
        $stayPrice = $firstStay instanceof Stay ? $firstStay->getPrice() : 0.0;

        $optionsPrice = 0.0;
        foreach ($reservation->getOptions() as $option) {
            $optionsPrice += $option->getPrice();
        }

        $totalPriceOptions = $optionsPrice * $nbTravelers;
        $total = ($stayPrice + $optionsPrice) * $nbTravelers;
        $reservation->setPrice($total);

        return ['total' => $total, 'options' => $totalPriceOptions];
    }
}
