<?php

namespace App\Dto;

/**
 * DTO pour le formulaire d'inscription (skill §8 Forms : « use DTOs, not
 * entities directly »). Mutable à propriétés publiques car Symfony Form doit
 * pouvoir écrire dans les propriétés (le pattern readonly nécessite une factory
 * empty_data verbeuse ; la séparation du concept de l'entité est l'objectif).
 *
 * NB: `agreeTerms` est un champ `mapped => false` (validation seule), absent du DTO.
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
