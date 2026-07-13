<?php

namespace App\Dto;

/**
 * DTO for the contact form (skill §8 Forms: "use DTOs, not entities directly").
 * The {@see \App\Entity\Contact} entity is populated by the application service,
 * never bound directly to the form. Its public mutable properties allow Symfony
 * Form to write values to it.
 */
class ContactDto
{
    public ?string $lastname = null;
    public ?string $firstname = null;
    public ?string $email = null;
    public ?string $description = null;
}
