<?php

namespace App\DataFixtures;

use App\Entity\Stays;
use App\Entity\Travel;
use App\Entity\Categories;
use App\Entity\Options;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Validator\Constraints\Date;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Faker\Factory;


class TravelFixtures extends Fixture
{

    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('fr_FR');

        for($i=0; $i < 10;$i++){

            // TRAVELS
            $travel = new Travel();
            $travel->setName('test '.$i);
            $travel->setSubtitle('test'.$i);
            $travel->setDescriptions('test'.$i);
            $travel->setDescriptions('test'.$i);

            // CATEGORIES
            $category = new Categories();
            $category->setTitle('test '.$i);
            $category->addTravel($travel);
            
            $manager->persist($category);

            // STAYS
            $date = new DateTime();
            $stay = new Stays();
            $stay->setStarDate($date);
            $stay->setDepature('ville');
            $stay->setEndDate($date);
            $stay->setArrival('ville');
            $stay->setPrice(1000.00);
            
            $travel-> addStay($stay);
            
            $manager->persist($stay);

            //OPTIONS
            $option = new Options();
            $option->setName('test');
            $option->setDescription('test');
            $option->setType('test');

            $travel->addOption($option);

            $manager->persist($option);


        $manager->persist($travel);
        }
        
        $manager->flush();
    }

}
