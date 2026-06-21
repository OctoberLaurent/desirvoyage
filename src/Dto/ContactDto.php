<?php

namespace App\Dto;

/**
 * DTO immuable pour le formulaire de contact (skill §8 Forms : « use DTOs, not
 * entities directly »). L'entité {@see \App\Entity\Contact} est remplie par le
 * service applicatif, jamais bindée directement au formulaire.
 */
final readonly class ContactDto
{
    public function __construct(
        public ?string $lastname = null,
        public ?string $firstname = null,
        public ?string $email = null,
        public ?string $description = null,
    ) {
    }
}
