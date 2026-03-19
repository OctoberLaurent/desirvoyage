<?php

namespace App\DataFixtures;

use DateTime;
use App\Entity\User;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;


class UserFixtures extends Fixture
{

    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        
        $this->passwordEncoder = $encoder;

    }

    public function load(ObjectManager $manager): void
    {
        $faker = \Faker\Factory::create('fr_FR');
        // datas
        $firstNameTab = ["Laurent","user" ];
        $lastNameTab = ["Laurent","user" ];
        $roleTab = ["ROLE_ADMIN", "ROLE_USER"];
        $mailTab = ["laurent@lepl.at", "user@user.fr"];

        $datetime=new \Datetime;
        for($i=0; $i < count($firstNameTab); $i++){

        $user = new User();

        $user->setFirstname($firstNameTab[$i]);
        $user->setLastname($lastNameTab[$i]);
        $user->setEnabled(true);
        $user->setRoles([$roleTab[$i]]);
        $user->setEmail($mailTab[$i]);
        $user->setPassword($this->passwordEncoder->encodePassword($user, "123456"));
        $user->setToken(sha1($faker->userName));
        $user->setAddress($faker->streetAddress);
        $user->setCity($faker->city);
        $user->setCountry("France");
        $user->setPostalCode($faker->postcode);
        $user->setPhone($faker->phoneNumber);
        $user->setBirthday($datetime);

        $manager->persist($user);
        }
        
        $manager->flush();
    }
}
