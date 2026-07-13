<?php

namespace App\Service;

use App\Dto\ContactDto;
use App\Entity\Contact;
use App\ValueObject\Email;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Handles a contact request: creates the {@see Contact} entity from the DTO,
 * persists it, then emails the user (skill §3 Service — the controller only
 * orchestrates HTTP).
 */
final readonly class ContactService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private MailerService $mailer,
    ) {
    }

    public function handle(ContactDto $dto): void
    {
        $contact = new Contact();
        $contact->setLastname($dto->lastname ?? '');
        $contact->setFirstname($dto->firstname ?? '');
        $contact->setEmail(new Email($dto->email ?? ''));
        $contact->setDescription($dto->description ?? '');
        $contact->setSendDate(new \DateTime());

        $this->entityManager->persist($contact);
        $this->entityManager->flush();

        $this->mailer->sendContactMessage($contact->getEmail()->value());
    }
}
