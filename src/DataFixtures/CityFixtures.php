<?php

namespace App\DataFixtures;

use App\Entity\City;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class CityFixtures extends Fixture
{

    public const CITY="city";


    public function load(ObjectManager $manager)
    {
        for ($i = 0; $i < 5; $i++) {
            $faker=Factory::create();
            $city = new City();
            $city->setName($faker->city);
            $city->setZipCode($faker->randomNumber(5, true));
            $this->addReference(self::CITY.$i, $city);
            $manager->persist($city);
        }
        $manager->flush();

    }

}
