<?php

namespace App\Service\Payment;

/**
 * Payment port (Adapter pattern). Allows Stripe, PayPal, or another provider to
 * be swapped without changing calling code (skill §2 Adapter, §1 DIP).
 */
interface PaymentGatewayInterface
{
    /**
     * @param int    $amountCents amount in cents
     * @param string $currency    ISO 4217 currency code (for example, "eur")
     * @param string $description label sent to the gateway
     * @param string $source      payment token returned by the gateway to the client
     *
     * @throws PaymentFailedException when payment is rejected or fails technically
     */
    public function charge(int $amountCents, string $currency, string $description, string $source, string $idempotencyKey): ChargedPayment;
}
