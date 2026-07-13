<?php

namespace App\Service;

/**
 * Thrown when a stay has insufficient stock to validate a reservation.
 */
final class NotEnoughStockException extends \RuntimeException
{
    public static function create(): self
    {
        return new self('Il ne reste pas suffisamment de place, merci de choisir un autre voyage ou une autre période');
    }
}
