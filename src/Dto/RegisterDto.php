<?php

namespace App\Dto;

/**
 * DTO for the registration form (skill §8 Forms: "use DTOs, not entities
 * directly"). It has mutable public properties because Symfony Form must write
 * to them. A readonly pattern would require a verbose `empty_data` factory;
 * separating the DTO from the entity is the goal.
 *
 * Note: `agreeTerms` is a `mapped => false` validation-only field and is absent
 * from this DTO.
 */
class RegisterDto
{
    public ?string $lastname = null;
    public ?string $firstname = null;
    public ?\DateTimeInterface $birthday = null;
    public ?string $address = null;
    public ?string $additionalAddress = null;
    public ?string $postalCode = null;
    public ?string $city = null;
    public ?string $country = null;
    public ?string $phone = null;
    public ?string $email = null;
    public ?string $password = null;
}
