<?php

namespace App\DataFixtures;

use App\Entity\Location;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class LocationFixtures extends Fixture implements OrderedFixtureInterface
{

    public const LOCATION = "location";


    public function load(ObjectManager $manager)
    {
        for ($y = 0; $y < 5; $y++) {
            for ($i = 0; $i < 5; $i++) {
                $faker = Factory::create();
                $location = new Location();
                $location->setName($faker->word);
                $location->setName($faker->company);
                $location->setStreet($faker->streetAddress);
                $location->setLatitude($faker->latitude);
                $location->setLongitude($faker->longitude);
                $location->setCity($this->getReference(CityFixtures::CITY . $i));
                $this->addReference(self::LOCATION . $y . $i, $location);
                $manager->persist($location);
            }

        }
        $manager->flush();

    }

    public function getOrder(): int
    {
        return 50;
    }
}
