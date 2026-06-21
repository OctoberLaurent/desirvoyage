<?php

namespace App\Service\Payment;

/**
 * Résultat immuable d'un paiement réussi (Value Object, skill §3).
 */
final readonly class ChargedPayment
{
    /**
     * @param string $id          identifiant renvoyé par le gateway (ex. charge_id Stripe)
     * @param int    $amountCents montant réellement débité, en centimes
     */
    public function __construct(
        public string $id,
        public int $amountCents,
    ) {
    }
}
