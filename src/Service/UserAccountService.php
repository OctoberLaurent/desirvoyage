<?php

namespace App\Service;

use App\Dto\EditUserDto;
use App\Dto\RegisterDto;
use App\Entity\User;
use App\ValueObject\Email;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Cas d'usage d'inscription et de mise à jour du profil utilisateur.
 */
final readonly class UserAccountService
{
    public function __construct(
        private UserService $userService,
        private MailerService $mailer,
        private EntityManagerInterface $entityManager,
    ) {
    }

    public function register(RegisterDto $dto): void
    {
        $user = new User();
        $this->applyRegistrationDto($dto, $user);
        $this->userService->setPassword($user, $dto->password ?? '');
        $user->setRoles(['ROLE_USER']);
        $this->userService->generateToken($user);

        $this->entityManager->persist($user);
        $this->entityManager->flush();
        $this->mailer->sendActivationMail($user);
    }

    public function updateProfile(User $user, EditUserDto $dto): void
    {
        $user->setLastname($dto->lastname ?? '');
        $user->setFirstname($dto->firstname ?? '');
        if (null !== $dto->birthday) {
            $user->setBirthday($dto->birthday);
        }
        $user->setAddress($dto->address ?? '');
        $user->setAdditionalAddress($dto->additionalAddress);
        $user->setPostalCode($dto->postalCode ?? '');
        $user->setCity($dto->city ?? '');
        $user->setCountry($dto->country ?? '');
        $user->setPhone($dto->phone ?? '');
        $user->setEmail(new Email($dto->email ?? ''));

        $this->entityManager->flush();
    }

    private function applyRegistrationDto(RegisterDto $dto, User $user): void
    {
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
    }
}
