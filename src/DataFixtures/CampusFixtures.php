<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CampusFixtures extends Fixture
{
    public const CAMPUS_NANTES="campus_nantes";
    public const CAMPUS_RENNES="campus_rennes";

    public function load(ObjectManager $manager)
    {
        $campus1 = new Campus();
        $campus1->setNom("Nantes");
        $this->addReference(self::CAMPUS_NANTES,$campus1);

        $manager->persist($campus1);
        $manager->flush();

        $campus2 = new Campus();
        $campus2->setNom("Nantes");
        $this->addReference(self::CAMPUS_NANTES,$campus2);

        $manager->persist($campus2);
        $manager->flush();
    }

}
