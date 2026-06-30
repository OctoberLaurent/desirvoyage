<?php

namespace App\Service;

use App\Dto\ContactDto;
use App\Entity\Contact;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Traite une demande de contact : crée l'entité {@see Contact} à partir du DTO,
 * la persiste, puis notifie l'utilisateur par mail (skill §3 Service — le
 * contrôleur ne fait que l'orchestration HTTP).
 */
final readonly class ContactService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private MailerService $mailer,
    ) {
    }

    public function handle(ContactDto $dto): Contact
    {
        $contact = new Contact();
        $contact->setLastname($dto->lastname ?? '');
        $contact->setFirstname($dto->firstname ?? '');
        $contact->setEmail($dto->email ?? '');
        $contact->setDescription($dto->description ?? '');
        $contact->setSendDate(new \DateTime());

        $this->entityManager->persist($contact);
        $this->entityManager->flush();

        $this->mailer->sendContactMessage($contact->getEmail());

        return $contact;
    }
}
