<?php

namespace App\DataFixtures;

use DateTime;
use Faker\Factory;
use App\Entity\Stays;
use App\Entity\Travel;
use App\Entity\Options;
use App\Entity\Formality;
use App\Entity\Categories;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Validator\Constraints\Date;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;


class TravelFixtures extends Fixture
{

    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('fr_FR');

        for($i=0; $i < 10;$i++){
            // TRAVELS
            $travel = new Travel();
            $travel->setName($faker->sentence($nbWords = 3, $variableNbWords = true));
            $travel->setSubtitle($faker->sentence($nbWords = 3, $variableNbWords = true));
            $travel->setDescriptions($faker->sentence($nbWords = 3, $variableNbWords = true));

            // CATEGORIES
            $category = new Categories();
            $category->setTitle($faker->sentence($nbWords = 3, $variableNbWords = true));
            $category->addTravel($travel);
            
            $manager->persist($category);

            // STAYS
             $max = mt_rand(1,3);
            for($count=0; $count < $max;$count++){
            $stay = new Stays();
            $sdate = $faker->dateTimeBetween($startDate = '+30 days', $endDate = '+365 days', $timezone = null);
            $edate = clone $sdate;
            $nb_jours = mt_rand(5, 30);
            $edate->modify('+'.$nb_jours.' day');
            $stay->setStarDate($sdate);
            $stay->setDepature($faker->city);
            $stay->setEndDate($edate);
            $stay->setArrival($faker->city);
            $stay->setPrice($faker->randomFloat($nbMaxDecimals = 2, $min = 700, $max = 8000));
           
            $travel-> addStay($stay);
            
            $manager->persist($stay);
            }
            
            //OPTIONS
            $option = new Options();
            $option->setName($faker->sentence($nbWords = 3, $variableNbWords = true));
            $option->setDescription($faker->sentence($nbWords = 3, $variableNbWords = true));
            $option->setType($faker->sentence($nbWords = 3, $variableNbWords = true));

            $travel->addOptions($option);

            $manager->persist($option);

            // FORMALITY
            $formality = new Formality();
            $formality->setDestination($faker->countryCode );
            $formality->setDescription($faker->sentence($nbWords = 3, $variableNbWords = true));
            $travel->addFormality($formality);

            //PICTURES

            $manager->persist($travel);
        }
        
        $manager->flush();
    }
}
