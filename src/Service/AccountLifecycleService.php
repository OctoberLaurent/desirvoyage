<?php

namespace App\Service;

use App\Entity\User;
use App\Enum\AccountActivationStatus;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Cas d'usage liés au cycle de vie du compte : activation, émission de liens
 * et changement de mot de passe. Le contrôleur conserve uniquement l'orchestration HTTP.
 */
final readonly class AccountLifecycleService
{
    public function __construct(
        private UserService $userService,
        private MailerService $mailer,
        private EntityManagerInterface $entityManager,
    ) {
    }

    public function activate(User $user, string $token, \DateTimeInterface $now = new \DateTime()): AccountActivationStatus
    {
        if (true === $user->getEnabled()) {
            return AccountActivationStatus::AlreadyActivated;
        }

        $currentToken = $user->getToken();
        if (null === $currentToken || !hash_equals($currentToken, $token)) {
            return AccountActivationStatus::Invalid;
        }

        $expiresAt = $user->getTokenExpire();
        if (null === $expiresAt || $expiresAt <= $now) {
            return AccountActivationStatus::Expired;
        }

        $user->setEnabled(true);
        $this->userService->resetToken($user);
        $this->entityManager->flush();

        return AccountActivationStatus::Activated;
    }

    public function resendActivation(User $user): void
    {
        if (true === $user->getEnabled()) {
            return;
        }

        $this->userService->generateToken($user);
        $this->entityManager->flush();
        $this->mailer->sendActivationMail($user);
    }

    public function requestPasswordReset(?User $user): void
    {
        if (null === $user) {
            return;
        }

        $this->userService->generateToken($user);
        $this->entityManager->flush();
        $this->mailer->sendResetPassword($user);
    }

    public function resetPassword(User $user, string $token, string $plainPassword, \DateTimeInterface $now = new \DateTime()): bool
    {
        $currentToken = $user->getToken();
        $expiresAt = $user->getTokenExpire();
        if (null === $currentToken || null === $expiresAt || !hash_equals($currentToken, $token) || $expiresAt <= $now) {
            return false;
        }

        $this->userService->setPassword($user, $plainPassword);
        $this->userService->resetToken($user);
        $this->entityManager->flush();

        return true;
    }

    public function changePassword(User $user, string $plainPassword): void
    {
        $this->userService->setPassword($user, $plainPassword);
        $this->entityManager->flush();
    }
}
