<?php

namespace App\DataFixtures;

use DateTime;
use App\Entity\User;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;


class UserFixtures extends Fixture
{

    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        
        $this->passwordEncoder = $encoder;

    }

    public function load(ObjectManager $manager)
    {
        // datas
        $firstNameTab = ["Laurent","user" ];
        $lastNameTab = ["Laurent","user" ];
        $roleTab = ["ROLE_ADMIN", "ROLE_UER"];
        $mailTab = ["laurent@lepl.at", "user@user.fr"];

        $datetime=new Datetime;
        for($i=0; $i < 3;$i++){

        $person = file_get_contents('https://randomuser.me/api/?nat=fr');
        $person = json_decode($person);
        $user = new User();

        $user->setFirstname($firstNameTab[$i]);
        $user->setLastname($lastNameTab[$i]);
        $user->setEnabled(true);
        $user->setRoles([$roleTab[$i]]);
        $user->setEmail($mailTab[$i]);
        $user->setPassword($this->passwordEncoder->encodePassword($user, "123456"));
        $user->setToken($person->results[0]->login->sha1);
        $user->setAddress($person->results[0]->location->street->number);
        $user->setAddress($person->results[0]->location->street->name);
        $user->setCity($person->results[0]->location->city);
        $user->setCountry($person->results[0]->location->country);
        $user->setPostalCode($person->results[0]->location->postcode);
        $user->setPhone($person->results[0]->cell);
        $user->setBirthday($datetime);

        $manager->persist($user);
        }
        
        $manager->flush();
    }
}
