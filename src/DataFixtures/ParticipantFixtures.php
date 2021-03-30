<?php

namespace App\DataFixtures;

use App\Entity\Participant;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class ParticipantFixtures extends Fixture implements OrderedFixtureInterface
{
    private $encoder;

    public const PARTICIPANT_ADMIN="participant_admin";
    public const PARTICIPANT_NANTES="participant_nantes";
    public const PARTICIPANT_RENNES="participant_rennes";

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        // créer le profil admin
        $participant = new Participant();
        $participant->setUserName("admin");
        $participant->setName("Dupuis");
        $participant->setFirstName("Paul");
        $participant->setMail("participant1@gmail.com");
        $participant->setAdmin(true);
        $participant->setActive(true);
        $participant->setCampus($this->getReference(CampusFixtures::CAMPUS_NANTES));;

        $this->addReference(self::PARTICIPANT_ADMIN, $participant);
        $password = $this->encoder->encodePassword($participant, 'test');
        $participant->setPassword($password);

        $manager->persist($participant);

        // Créer 10 participants du campus de Nantes
        for ($i = 0; $i < 10; $i++) {
            $faker=Factory::create();
            $participant = new Participant();
            $participant->setUserName("participant" .$i);
            $participant->setName($faker->lastname);
            $participant->setFirstName($faker->firstname);
            $participant->setMail($faker->email);
            $participant->setAdmin(false);
            $participant->setActive(true);

            $password = $this->encoder->encodePassword($participant, "test");
            $participant->setPassword($password);

            $this->addReference(self::PARTICIPANT_NANTES.$i, $participant);
            $participant->setCampus($this->getReference(CampusFixtures::CAMPUS_NANTES));;
            $manager->persist($participant);
        }

        $manager->flush();

        // Créer 10 participants du campus de Rennes
        for ($i = 10; $i < 20; $i++) {
            $faker=Factory::create();
            $participant = new Participant();
            $participant->setUserName("participant" .$i);
            $participant->setName($faker->lastname);
            $participant->setFirstName($faker->firstname);
            $participant->setMail($faker->email);
            $participant->setAdmin(false);
            $participant->setActive(true);

            $password = $this->encoder->encodePassword($participant, "test");
            $participant->setPassword($password);

            $this->addReference(self::PARTICIPANT_RENNES.$i, $participant);
            $participant->setCampus($this->getReference(CampusFixtures::CAMPUS_RENNES));;

            $manager->persist($participant);
        }

        $manager->flush();
    }


    public function getOrder(): int
    {
        return 30;
    }
}
