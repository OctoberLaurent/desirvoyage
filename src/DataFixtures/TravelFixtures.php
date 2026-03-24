<?php

namespace App\DataFixtures;

use App\Entity\Categories;
use App\Entity\Formality;
use App\Entity\Options;
use App\Entity\Stays;
use App\Entity\Travel;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class TravelFixtures extends Fixture
{
    #[\Override]
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        /** @var array<string, string> $categoriesData */
        $categoriesData = [
            'Promo' => 'default.png',
            'Canarie' => 'default.png',
            'Gréce' => 'default.png',
            'Thaïlande' => 'default.png',
            'Caraïbes' => 'default.png',
            'Tunisie' => 'default.png',
            'Espagne' => 'default.png',
            'Mexique' => 'default.png',
            'Portugal' => 'default.png',
        ];

        // CATEGORIES
        foreach ($categoriesData as $categoryTitle => $imageUrl) {
            $category = new Categories();
            $category->setTitle($categoryTitle);
            $category->setUrl($imageUrl);

            $manager->persist($category);
        }

        for ($i = 0; $i < 10; ++$i) {
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
            $stay->setStock(mt_rand(10, 100));

            $travel->addStay($stay);

            $manager->persist($stay);

            // OPTIONS
            $option = new Options();
            $option->setName($faker->sentence($nbWords = 3, $variableNbWords = true));
            $option->setDescription($faker->sentence($nbWords = 3, $variableNbWords = true));
            $option->setType($faker->sentence($nbWords = 3, $variableNbWords = true));
            $option->setPrice($faker->randomFloat(2, 10, 100));

            $travel->addOptions($option);

            $manager->persist($option);

            // FORMALITY
            $formality = new Formality();
            $formality->setDestination($faker->countryCode);
            $formality->setDescription($faker->sentence($nbWords = 3, $variableNbWords = true));
            $travel->addFormality($formality);

            // PICTURES

            $manager->persist($travel);
        }

        $manager->flush();
    }
}
