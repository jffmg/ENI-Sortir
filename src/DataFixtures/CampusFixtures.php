<?php

namespace App\DataFixtures;

use App\Entity\Campus;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class CampusFixtures extends Fixture implements OrderedFixtureInterface
{
    public const CAMPUS_NANTES="campus_nantes";
    public const CAMPUS_RENNES="campus_rennes";

    public function load(ObjectManager $manager)
    {
        $campus1 = new Campus();
        $campus1->setName("Nantes");
        $this->addReference(self::CAMPUS_NANTES,$campus1);

        $manager->persist($campus1);
        $manager->flush();

        $campus2 = new Campus();
        $campus2->setName("Rennes");
        $this->addReference(self::CAMPUS_RENNES,$campus2);

        $manager->persist($campus2);
        $manager->flush();
    }

    public function getOrder(): int
    {
        return 20;
    }
}
