<?php

namespace App\Service\Payment;

/**
 * Immutable result of a successful payment (Value Object, skill §3).
 */
final readonly class ChargedPayment
{
    /**
     * @param string $id          identifier returned by the gateway (for example, a Stripe charge ID)
     * @param int    $amountCents amount actually charged, in cents
     */
    public function __construct(
        public string $id,
        public int $amountCents,
    ) {
    }
}
