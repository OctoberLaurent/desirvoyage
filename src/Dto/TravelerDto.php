<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * DTO pour un voyageur (skill §8 Forms : « use DTOs, not entities directly »).
 * Mutable à propriétés publiques (Symfony Form CollectionType). Validé via
 * attributs (NotBlank + Length/Email/LessThan, miroir des contraintes de
 * {@see \App\Entity\Traveler}) avant conversion en entité.
 */
final class TravelerDto
{
    #[Assert\NotBlank(message: 'Saisir votre nom')]
    #[Assert\Length(min: 3, max: 80, minMessage: 'Your lastname must be at least {{ limit }} characters long.', maxMessage: 'Your lastname cannot be longer than {{ limit }} characters.')]
    public ?string $lastname = null;

    #[Assert\NotBlank(message: 'Saisir votre prenom')]
    #[Assert\Length(min: 3, max: 80, minMessage: 'Your firstname must be at least {{ limit }} characters long.', maxMessage: 'Your firstname cannot be longer than {{ limit }} characters.')]
    public ?string $firstname = null;

    #[Assert\NotBlank(message: 'Saisir votre email')]
    #[Assert\Email(message: "The email '{{ value }}' is not a valid email.")]
    public ?string $email = null;

    #[Assert\NotBlank(message: 'Saisir votre date de naissance')]
    #[Assert\LessThan('-13 years')]
    public ?\DateTimeInterface $birthday = null;
}
