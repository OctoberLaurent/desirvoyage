<?php

namespace App\Service\Payment;

/**
 * Port de paiement (Adapter pattern). Permet de permuter Stripe / PayPal / etc.
 * sans modifier le code appelant (skill §2 Adapter, §1 DIP).
 */
interface PaymentGatewayInterface
{
    /**
     * @param int    $amountCents montant en centimes
     * @param string $currency    code devise ISO 4217 (ex. "eur")
     * @param string $description libellé transmis au gateway
     * @param string $source      token de paiement renvoyé par le gateway côté client
     *
     * @throws PaymentFailedException si le paiement est refusé ou technique
     */
    public function charge(int $amountCents, string $currency, string $description, string $source): ChargedPayment;
}
