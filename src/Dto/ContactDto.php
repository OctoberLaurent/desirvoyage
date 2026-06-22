<?php

namespace App\Dto;

/**
 * DTO pour le formulaire de contact (skill §8 Forms : « use DTOs, not entities
 * directly »). L'entité {@see \App\Entity\Contact} est remplie par le service
 * applicatif, jamais bindée au formulaire. Mutable à propriétés publiques pour
 * permettre l'écriture par Symfony Form.
 */
class ContactDto
{
    public ?string $lastname = null;
    public ?string $firstname = null;
    public ?string $email = null;
    public ?string $description = null;
}
