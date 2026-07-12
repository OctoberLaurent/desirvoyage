<?php

namespace App\Tests\Service;

use App\Entity\User;
use App\Enum\AccountActivationStatus;
use App\Service\AccountLifecycleService;
use App\Service\MailerService;
use App\Service\UserService;
use App\ValueObject\Email;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class AccountLifecycleServiceTest extends TestCase
{
    public function testActivationRequiresTheCurrentUnexpiredTokenAndPersistsTheTransition(): void
    {
        $user = new User();
        $user->setEmail(new Email('owner@example.com'));
        $user->setPassword('hashed');
        $user->setToken('activation-token');
        $user->setTokenExpire(new \DateTime('2026-07-13 12:00:00'));

        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager->expects(self::once())->method('flush');

        $service = new AccountLifecycleService(
            new UserService(self::createStub(UserPasswordHasherInterface::class)),
            new MailerService(self::createStub(UrlGeneratorInterface::class), self::createStub(MailerInterface::class)),
            $entityManager,
        );

        $status = $service->activate($user, 'activation-token', new \DateTimeImmutable('2026-07-12 12:00:00'));

        self::assertSame(AccountActivationStatus::Activated, $status);
        self::assertTrue($user->getEnabled());
        self::assertNull($user->getToken());
        self::assertNull($user->getTokenExpire());
    }

    public function testActivationRejectsAnExpiredOrMismatchedTokenWithoutPersisting(): void
    {
        $user = new User();
        $user->setEmail(new Email('owner@example.com'));
        $user->setPassword('hashed');
        $user->setToken('activation-token');
        $user->setTokenExpire(new \DateTime('2026-07-12 11:00:00'));

        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager->expects(self::never())->method('flush');

        $service = new AccountLifecycleService(
            new UserService(self::createStub(UserPasswordHasherInterface::class)),
            new MailerService(self::createStub(UrlGeneratorInterface::class), self::createStub(MailerInterface::class)),
            $entityManager,
        );

        self::assertSame(AccountActivationStatus::Invalid, $service->activate($user, 'wrong-token', new \DateTimeImmutable('2026-07-12 12:00:00')));
        self::assertSame(AccountActivationStatus::Expired, $service->activate($user, 'activation-token', new \DateTimeImmutable('2026-07-12 12:00:00')));
        self::assertFalse($user->getEnabled() ?? true);
    }
}
