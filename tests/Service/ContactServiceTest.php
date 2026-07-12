<?php

namespace App\Tests\Service;

use App\Dto\ContactDto;
use App\Entity\Contact;
use App\Service\ContactService;
use App\Service\MailerService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * @covers \App\Service\ContactService
 */
final class ContactServiceTest extends TestCase
{
    public function testHandlePersistsContactAndSendsMail(): void
    {
        $persistedContact = null;
        $em = $this->createMock(EntityManagerInterface::class);
        $em->expects(self::once())->method('persist')->willReturnCallback(static function (Contact $contact) use (&$persistedContact): void {
            $persistedContact = $contact;
        });
        $em->expects(self::once())->method('flush');

        // MailerService est final : vraie instance avec MailerInterface mocké.
        $mailer = $this->createMock(MailerInterface::class);
        $mailer->expects(self::once())->method('send')->with(self::isInstanceOf(Email::class));
        $mailerService = new MailerService(self::createStub(UrlGeneratorInterface::class), $mailer);

        $service = new ContactService($em, $mailerService);

        $dto = new ContactDto();
        $dto->lastname = 'Doe';
        $dto->firstname = 'Jane';
        $dto->email = 'jane@example.com';
        $dto->description = 'Une demande de test.';

        $service->handle($dto);

        self::assertInstanceOf(Contact::class, $persistedContact);
        self::assertSame('Doe', $persistedContact->getLastname());
        self::assertSame('Jane', $persistedContact->getFirstname());
        self::assertSame('jane@example.com', $persistedContact->getEmail()->value());
    }
}
