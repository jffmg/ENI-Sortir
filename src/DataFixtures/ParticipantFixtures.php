<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\DBAL\Types\IntegerType;
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
        $faker=Factory::create();
        $participant = new Participant();

        // créer le profil admin
        $participant->setPseudo("admin");
        $participant->setNom("Dupuis");
        $participant->setPrenom("Paul");
        $participant->setMail("participant1@gmail.com");
        $participant->setAdministrateur(true);
        $participant->setActif(true);
        $participant->setCampus($this->getReference(CampusFixtures::CAMPUS_Nantes));;

        $password = $this->encoder->encodePassword($participant, 'test');
        $participant->setPassword($password);

        $manager->persist($participant);

        // Créer 10 participants du campus de Nantes
        for ($i = 0; $i < 10; $i++) {
            $this->creerUnParticipant($participant,$faker,$i);
            $participant->setCampus($this->getReference(CampusFixtures::CAMPUS_NANTES));;
            $manager->persist($participant);
        }

        $manager->flush();

        // Créer 10 participants du campus de Rennes
        for ($i = 10; $i < 20; $i++) {
            $this->creerUnParticipant($participant,$faker,$i);
            $participant->setCampus($this->getReference(CampusFixtures::CAMPUS_RENNES));;
            $manager->persist($participant);
        }

        $manager->flush();
    }

    public function creerUnParticipant(Participant $participant,Faker $faker,IntegerType $i) {
        $participant->setPseudo("participant" .$i);
        $participant->setName($faker->lastname);
        $participant->setPrenom($faker->firstname);
        $participant->setMail($faker->mail);
        $participant->setAdministrateur(false);
        $participant->setActif(true);
        $password = $this->encoder->encodePassword($participant, 'test');
        $participant->setPassword($password);
    }

    /**
     * @inheritDoc
     */
    public function getDependencies(): array
    {
        return array(CampusFixtures::class);
    }

}
