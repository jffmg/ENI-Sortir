<?php

namespace App\DataFixtures;

use App\Entity\Participant;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class ParticipantFixtures extends Fixture implements DependentFixtureInterface
{
    private $encoder;

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
        $participant->setMail("admin@test.fr");
        $participant->setAdmin(true);
        $participant->setActive(true);
        $participant->setCampus($this->getReference(CampusFixtures::CAMPUS_NANTES));;

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

            $participant->setCampus($this->getReference(CampusFixtures::CAMPUS_RENNES));;

            $manager->persist($participant);
        }

        $manager->flush();
    }

    /**
     * @inheritDoc
     */
    public function getDependencies(): array
    {
        return array(CampusFixtures::class);
    }

}
