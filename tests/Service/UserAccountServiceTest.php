<?php

namespace App\Tests\Service;

use App\Dto\EditUserDto;
use App\Dto\RegisterDto;
use App\Entity\User;
use App\Service\MailerService;
use App\Service\UserAccountService;
use App\Service\UserService;
use App\ValueObject\Email;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email as MimeEmail;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class UserAccountServiceTest extends TestCase
{
    public function testRegisterCreatesPersistsAndNotifiesAUserFromTheDto(): void
    {
        $hasher = $this->createMock(UserPasswordHasherInterface::class);
        $hasher->expects(self::once())->method('hashPassword')->with(self::isInstanceOf(User::class), 'plain-password')->willReturn('hashed-password');
        $registeredUser = null;
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager->expects(self::once())->method('persist')->willReturnCallback(static function (User $user) use (&$registeredUser): void {
            $registeredUser = $user;
        });
        $entityManager->expects(self::once())->method('flush');
        $router = $this->createMock(UrlGeneratorInterface::class);
        $router->expects(self::once())->method('generate')->willReturn('https://example.test/activate');
        $mailer = $this->createMock(MailerInterface::class);
        $mailer->expects(self::once())->method('send')->with(self::isInstanceOf(MimeEmail::class));

        (new UserAccountService(
            new UserService($hasher),
            new MailerService($router, $mailer),
            $entityManager,
        ))->register($this->registrationDto());

        self::assertInstanceOf(User::class, $registeredUser);
        $user = $registeredUser;
        self::assertSame('Jane', $user->getFirstname());
        self::assertSame('Doe', $user->getLastname());
        self::assertSame('jane@example.com', $user->getEmail()->value());
        self::assertSame('hashed-password', $user->getPassword());
        self::assertContains('ROLE_USER', $user->getRoles());
        self::assertNotNull($user->getToken());
    }

    public function testUpdateProfileAppliesTheDtoAndFlushesTheManagedUser(): void
    {
        $user = new User();
        $user->setEmail(new Email('before@example.com'));
        $user->setPassword('hashed');
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager->expects(self::never())->method('persist');
        $entityManager->expects(self::once())->method('flush');

        $dto = $this->registrationDto();
        $dto->firstname = 'Updated';
        $dto->email = 'updated@example.com';

        (new UserAccountService(
            new UserService(self::createStub(UserPasswordHasherInterface::class)),
            new MailerService(self::createStub(UrlGeneratorInterface::class), self::createStub(MailerInterface::class)),
            $entityManager,
        ))->updateProfile($user, EditUserDto::fromUser($this->userFromDto($dto)));

        self::assertSame('Updated', $user->getFirstname());
        self::assertSame('updated@example.com', $user->getEmail()->value());
    }

    private function registrationDto(): RegisterDto
    {
        $dto = new RegisterDto();
        $dto->lastname = 'Doe';
        $dto->firstname = 'Jane';
        $dto->birthday = new \DateTime('1990-05-15');
        $dto->address = '1 rue de Paris';
        $dto->additionalAddress = null;
        $dto->postalCode = '75001';
        $dto->city = 'Paris';
        $dto->country = 'FR';
        $dto->phone = '0612345678';
        $dto->email = 'jane@example.com';
        $dto->password = 'plain-password';

        return $dto;
    }

    private function userFromDto(RegisterDto $dto): User
    {
        $user = new User();
        $user->setLastname($dto->lastname ?? '');
        $user->setFirstname($dto->firstname ?? '');
        $user->setBirthday($dto->birthday ?? new \DateTime());
        $user->setAddress($dto->address ?? '');
        $user->setAdditionalAddress($dto->additionalAddress);
        $user->setPostalCode($dto->postalCode ?? '');
        $user->setCity($dto->city ?? '');
        $user->setCountry($dto->country ?? '');
        $user->setPhone($dto->phone ?? '');
        $user->setEmail(new Email($dto->email ?? ''));
        $user->setPassword('hashed');

        return $user;
    }
}
