<?php

namespace App\ValueObject;

/**
 * Value Object immuable représentant un montant monétaire (skill §3 Value Objects).
 *
 * Conserve l'euro en `float` dans la couche de persistance (colonne Doctrine
 * inchangée — pas de migration) tout en exposant un concept monétaire typé et
 * sûr à la frontière de l'entité (getPrice(): Money). L'arithmétique en Twig
 * passe par les méthodes {@see self::multiply()} / {@see self::divide()} plutôt
 * que par les opérateurs natifs (un VO n'est pas multipliable par `int` en Twig).
 *
 * @see \App\Entity\Reservation::$price
 * @see \App\Entity\Stay::$price
 * @see \App\Entity\Option::$price
 */
final readonly class Money implements \Stringable
{
    public function __construct(
        private float $amount,
        private string $currency = 'EUR',
    ) {
        if ('' === trim($this->currency)) {
            throw new \InvalidArgumentException('Money requires a non-empty currency code.');
        }
    }

    public function amount(): float
    {
        return $this->amount;
    }

    public function currency(): string
    {
        return $this->currency;
    }

    public function multiply(int|float $factor): self
    {
        return new self($this->amount * (float) $factor, $this->currency);
    }

    public function divide(int|float $divisor): self
    {
        if (0.0 === (float) $divisor) {
            throw new \InvalidArgumentException('Money divide by zero.');
        }

        return new self($this->amount / (float) $divisor, $this->currency);
    }

    public function add(self $other): self
    {
        if ($this->currency !== $other->currency) {
            throw new \InvalidArgumentException('Cannot add Money with different currencies.');
        }

        return new self($this->amount + $other->amount, $this->currency);
    }

    public function equals(self $other): bool
    {
        return $this->amount === $other->amount && $this->currency === $other->currency;
    }

    /**
     * Rendu identique au `float` natif ({@see (string)} cast) pour préserver
     * l'affichage Twig existant (« 1200 », « 1234.56 »).
     */
    public function __toString(): string
    {
        return (string) $this->amount;
    }
}
