<?php

namespace App\Service\Payment;

/**
 * Thrown when a payment is rejected or fails technically.
 * Domain-specific exception (skill §3 "Domain-specific exceptions", §7 "Null return → Exception").
 */
final class PaymentFailedException extends \RuntimeException
{
    public static function fromReason(string $reason): self
    {
        return new self('Le paiement a échoué : '.$reason);
    }
}
