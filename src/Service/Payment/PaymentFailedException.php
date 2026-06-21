<?php

namespace App\Service\Payment;

/**
 * Levée quand un paiement est refusé ou échoue techniquement.
 * Domaine : exception spécifique (skill §3 « Domain-specific exceptions », §7 « Null return → Exception »).
 */
final class PaymentFailedException extends \RuntimeException
{
    public static function fromReason(string $reason): self
    {
        return new self('Le paiement a échoué : '.$reason);
    }
}
