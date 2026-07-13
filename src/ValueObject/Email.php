<?php

namespace App\ValueObject;

/**
 * Immutable value object representing a validated email address (skill §3 Value Objects).
 *
 * The RFC-format invariant is enforced at construction: every instance contains
 * a non-empty valid email address.
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

    #[\Override]
    public function __toString(): string
    {
        return $this->value;
    }
}
