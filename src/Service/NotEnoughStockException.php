<?php

namespace App\Service;

/**
 * Levée quand le stock d'un séjour est insuffisant pour valider une réservation.
 */
final class NotEnoughStockException extends \RuntimeException
{
    public static function create(): self
    {
        return new self('Il ne reste pas suffisamment de place, merci de choisir un autre voyage ou une autre période');
    }
}
