<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class UserFixtures extends Fixture
{
    public function __construct(private readonly UserPasswordHasherInterface $passwordEncoder)
    {
    }

    #[\Override]
    public function load(ObjectManager $manager): void
    {
        $faker = \Faker\Factory::create('fr_FR');

        /** @var array<array{firstname: string, lastname: string, role: string, email: string}> $usersData */
        $usersData = [
            ['firstname' => 'Laurent', 'lastname' => 'Laurent', 'role' => 'ROLE_ADMIN', 'email' => 'laurent@lepl.at'],
            ['firstname' => 'user', 'lastname' => 'user', 'role' => 'ROLE_USER', 'email' => 'user@user.fr'],
        ];

        $datetime = new \DateTime();
        foreach ($usersData as $userData) {
            $user = new User();
            $user->setFirstname($userData['firstname']);
            $user->setLastname($userData['lastname']);
            $user->setEnabled(true);
            $user->setRoles([$userData['role']]);
            $user->setEmail($userData['email']);
            $user->setPassword($this->passwordEncoder->hashPassword($user, '123456'));
            $user->setToken(sha1($faker->userName));
            $user->setAddress($faker->streetAddress);
            $user->setCity($faker->city);
            $user->setCountry('France');
            $user->setPostalCode($faker->postcode);
            $user->setPhone($faker->phoneNumber);
            $user->setBirthday($datetime);

            $manager->persist($user);
        }

        $manager->flush();
    }
}
