<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * DTO for a traveler (skill §8 Forms: "use DTOs, not entities directly").
 * Its mutable public properties work with Symfony Form CollectionType. Attributes
 * validate it with the same constraints as {@see \App\Entity\Traveler} before
 * conversion to the entity.
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
