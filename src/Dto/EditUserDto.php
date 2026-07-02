<?php

namespace App\Dto;

use App\Entity\User;

/**
 * DTO pour le formulaire d'édition du profil (skill §8 Forms : « use DTOs, not
 * entities directly »). Mutable à propriétés publiques (Symfony Form). Pré-rempli
 * depuis l'entité {@see User} via {@see self::fromUser()}, puis re-mappé sur
 * l'entité lors de la soumission.
 */
class EditUserDto
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

    public static function fromUser(User $user): self
    {
        $dto = new self();
        $dto->lastname = $user->getLastname();
        $dto->firstname = $user->getFirstname();
        $dto->birthday = $user->getBirthday();
        $dto->address = $user->getAddress();
        $dto->additionalAddress = $user->getAdditionalAddress();
        $dto->postalCode = $user->getPostalCode();
        $dto->city = $user->getCity();
        $dto->country = $user->getCountry();
        $dto->phone = $user->getPhone();
        $dto->email = $user->getEmail()->value();

        return $dto;
    }
}
