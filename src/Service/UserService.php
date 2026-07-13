<?php

namespace App\Service;

use App\Entity\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * Manages activation and reset tokens as well as password hashing for a
 * {@see User} (skill §3 Service — logic extracted from the security controller).
 */
final readonly class UserService
{
    public function __construct(private UserPasswordHasherInterface $passwordHasher)
    {
    }

    public function generateToken(User $user): void
    {
        $token = bin2hex(random_bytes(64));
        $expire = new \DateTime('1 day');

        $user->setToken($token);
        $user->setTokenExpire($expire);
    }

    public function resetToken(User $user): void
    {
        $user->setToken(null);
        $user->setTokenExpire(null);
    }

    /**
     * Hashes and applies the user's new password.
     */
    public function setPassword(User $user, string $plainPassword): void
    {
        $user->setPassword($this->passwordHasher->hashPassword($user, $plainPassword));
    }
}
