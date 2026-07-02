<?php

namespace App\Tests\Service;

use App\Entity\User;
use App\Service\UserService;
use App\ValueObject\Email;
use PHPUnit\Framework\TestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class UserServiceTest extends TestCase
{
    public function testGenerateTokenSetsTokenAndExpiryOneDayAhead(): void
    {
        $user = $this->buildUser();
        $service = new UserService($this->createMock(UserPasswordHasherInterface::class));

        $service->generateToken($user);

        $token = $user->getToken();
        self::assertNotNull($token);
        self::assertSame(128, strlen($token)); // 64 bytes hex = 128 chars
        self::assertNotNull($user->getTokenExpire());
        self::assertGreaterThan(new \DateTime(), $user->getTokenExpire());
    }

    public function testResetTokenClearsTokenAndExpiry(): void
    {
        $user = $this->buildUser();
        $service = new UserService($this->createMock(UserPasswordHasherInterface::class));
        $service->generateToken($user);

        $service->resetToken($user);

        self::assertNull($user->getToken());
        self::assertNull($user->getTokenExpire());
    }

    public function testSetPasswordHashesAndApplies(): void
    {
        $user = $this->buildUser();
        $hasher = $this->createMock(UserPasswordHasherInterface::class);
        $hasher->expects(self::once())
            ->method('hashPassword')
            ->with($user, 'plain123')
            ->willReturn('hashed-pw');

        (new UserService($hasher))->setPassword($user, 'plain123');

        self::assertSame('hashed-pw', $user->getPassword());
    }

    private function buildUser(): User
    {
        $user = new User();
        // Champs obligatoires pour pouvoir hasher/toString sans fatal
        $user->setEmail(new Email('user@example.com'));
        $user->setPassword('current');

        return $user;
    }
}
