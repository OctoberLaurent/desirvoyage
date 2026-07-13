<?php

namespace App\ValueObject;

/**
 * Immutable value object representing a monetary amount (skill §3 Value Objects).
 *
 * The amount is always stored in cents to prevent floating-point rounding
 * errors. Conversions to euros happen only at boundaries (forms, Twig, and
 * external APIs).
 *
 * @see \App\Entity\Reservation::$price
 * @see \App\Entity\Stay::$price
 * @see \App\Entity\Option::$price
 */
final readonly class Money implements \Stringable
{
    private function __construct(
        private int $cents,
        private string $currency = 'EUR',
    ) {
        if ('' === trim($this->currency)) {
            throw new \InvalidArgumentException('Money requires a non-empty currency code.');
        }
    }

    public static function fromEuros(int|float|string $amount, string $currency = 'EUR'): self
    {
        if (!is_numeric($amount)) {
            throw new \InvalidArgumentException('Money requires a numeric amount.');
        }

        return new self((int) round((float) $amount * 100.0, 0, PHP_ROUND_HALF_UP), strtoupper($currency));
    }

    public static function fromCents(int $cents, string $currency = 'EUR'): self
    {
        return new self($cents, strtoupper($currency));
    }

    public function cents(): int
    {
        return $this->cents;
    }

    public function amount(): float
    {
        return $this->cents / 100;
    }

    public function currency(): string
    {
        return $this->currency;
    }

    public function multiply(int|float $factor): self
    {
        return new self((int) round((float) $this->cents * (float) $factor, 0, PHP_ROUND_HALF_UP), $this->currency);
    }

    public function divide(int|float $divisor): self
    {
        if (0.0 === (float) $divisor) {
            throw new \InvalidArgumentException('Money divide by zero.');
        }

        return new self((int) round((float) $this->cents / (float) $divisor, 0, PHP_ROUND_HALF_UP), $this->currency);
    }

    public function add(self $other): self
    {
        if ($this->currency !== $other->currency) {
            throw new \InvalidArgumentException('Cannot add Money with different currencies.');
        }

        return new self($this->cents + $other->cents, $this->currency);
    }

    public function equals(self $other): bool
    {
        return $this->cents === $other->cents && $this->currency === $other->currency;
    }

    /**
     * Preserves native float-like rendering to keep existing Twig output
     * unchanged ("1200", "1234.56").
     */
    #[\Override]
    public function __toString(): string
    {
        return rtrim(rtrim(number_format($this->amount(), 2, '.', ''), '0'), '.');
    }
}
