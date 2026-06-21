<?php

namespace App\Service;

use App\Entity\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * Gère les jetons d'activation/réinitialisation et le hashage du mot de passe
 * d'un {@see User} (skill §3 Service — logique extraite du contrôleur de sécurité).
 */
final class UserService
{
    public function __construct(private readonly UserPasswordHasherInterface $passwordHasher)
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
     * Hash et applique le nouveau mot de passe à l'utilisateur.
     */
    public function setPassword(User $user, string $plainPassword): void
    {
        $user->setPassword($this->passwordHasher->hashPassword($user, $plainPassword));
    }
}
