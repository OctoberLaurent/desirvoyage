<?php

namespace App\ValueObject;

/**
 * Value Object immuable représentant une adresse e-mail validée (skill §3 Value Objects).
 *
 * L'invariant (format RFC) est garanti à la construction : toute instance
 * existante contient une adresse e-mail valide et non vide.
 *
 * @see \App\Entity\User::$email
 * @see \App\Entity\Contact::$email
 */
final readonly class Email implements \Stringable
{
    private string $value;

    public function __construct(string $value)
    {
        $trimmed = trim($value);
        if ('' === $trimmed || false === filter_var($trimmed, \FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException(\sprintf('Adresse e-mail invalide : "%s".', $value));
        }

        $this->value = $trimmed;
    }

    public function value(): string
    {
        return $this->value;
    }

    public function equals(self $other): bool
    {
        return 0 === strcasecmp($this->value, $other->value);
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
