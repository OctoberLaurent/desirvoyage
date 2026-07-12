<?php

namespace App\ValueObject;

/**
 * Value Object immuable représentant un montant monétaire (skill §3 Value Objects).
 *
 * Le montant est toujours conservé en centimes afin d'éviter les erreurs
 * d'arrondi des nombres flottants. Les conversions en euros n'ont lieu qu'aux
 * frontières (formulaires, Twig et API externes).
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
     * Rendu identique au `float` natif ({@see (string)} cast) pour préserver
     * l'affichage Twig existant (« 1200 », « 1234.56 »).
     */
    #[\Override]
    public function __toString(): string
    {
        return rtrim(rtrim(number_format($this->amount(), 2, '.', ''), '0'), '.');
    }
}
