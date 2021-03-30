<?php

namespace App\DataFixtures;

use App\Entity\State;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class StateFixtures extends Fixture
{
    public const EC="en_cours";
    public const OU="ouverte";
    public const AN="annulee";
    public const CL="cloturee";
    public const AEC="activite_en_cours";
    public const AT="activite_terminee";
    public const AH="activite_historisee";

    public function load(ObjectManager $manager)
    {
        $state1 = new State();
        $state1->setLabel("En cours");
        $state1->setShortLabel("EC");
        $this->addReference(self::EC,$state1);

        $manager->persist($state1);
        $manager->flush();

        $state2 = new State();
        $state2->setLabel("Ouverte");
        $state2->setShortLabel("OU");
        $this->addReference(self::OU,$state2);

        $manager->persist($state2);
        $manager->flush();

        $state3 = new State();
        $state3->setLabel("Annulée");
        $state3->setShortLabel("AN");
        $this->addReference(self::AN,$state3);

        $manager->persist($state3);
        $manager->flush();

        $state4 = new State();
        $state4->setLabel("Cloturée");
        $state4->setShortLabel("CL");
        $this->addReference(self::CL,$state4);

        $manager->persist($state4);
        $manager->flush();

        $state5 = new State();
        $state5->setLabel("Activité en cours");
        $state5->setShortLabel("AEC");
        $this->addReference(self::AEC,$state5);

        $manager->persist($state5);
        $manager->flush();

        $state6 = new State();
        $state6->setLabel("Activité terminée");
        $state6->setShortLabel("AT");
        $this->addReference(self::AT,$state6);

        $manager->persist($state6);
        $manager->flush();

        $state7 = new State();
        $state7->setLabel("Activité historisée");
        $state7->setShortLabel("AH");
        $this->addReference(self::AH,$state7);

        $manager->persist($state7);
        $manager->flush();


    }

}
