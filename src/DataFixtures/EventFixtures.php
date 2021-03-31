<?php

namespace App\DataFixtures;

use App\Entity\Event;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class EventFixtures extends Fixture implements OrderedFixtureInterface
{

    public const EVENT = "event";

    function getOrder(): int
    {
        return 60;
    }

    public function load(ObjectManager $manager)
    {
        $faker = Factory::create();
        //Créer 5 events EN CREATION
        for ($i = 0; $i < 5; $i++) {
            $event = $this->createEvent($faker, "EC");
            $this->setReference(self::EVENT . $i, $event);
            $manager->persist($event);
        }
        //Créer 5 events OUVERTS
        for ($i = 5; $i < 10; $i++) {
            $event = $this->createEvent($faker, "OU");
            $this->setReference(self::EVENT . $i, $event);
            $manager->persist($event);
        }
        //Créer 5 events CLOTURES
        for ($i = 5; $i < 10; $i++) {
            $event = $this->createEvent($faker, "CL");
            $this->setReference(self::EVENT . $i, $event);
            $manager->persist($event);
        }
        //Créer 5 events ACTIVITE EN COURS
        for ($i = 10; $i < 15; $i++) {
            $event = $this->createEvent($faker, "AEC");
            $this->setReference(self::EVENT . $i, $event);
            $manager->persist($event);
        }
        //Créer 5 events ACTIVITE TERMINEE
        for ($i = 15; $i < 20; $i++) {
            $event = $this->createEvent($faker, "AT");
            $this->setReference(self::EVENT . $i, $event);
            $manager->persist($event);
        }
        //Créer 5 events ACTIVITE HISTORISEE
        for ($i = 20; $i < 25; $i++) {
            $event = $this->createEvent($faker, "AH");
            $this->setReference(self::EVENT . $i, $event);
            $manager->persist($event);
        }
        $manager->flush();

    }

    private function createEvent($faker, $stateParam)
    {

        $event = new Event();
        //set common data
        $campusOrganizer = $this->randomCampusOrganizer();
        $organizer = $this->randomOrganizer($campusOrganizer);
        $location = $this->randomLocation();
        $event->setOrganizer($organizer);
        $event->setCampusOrganizer($campusOrganizer);
        $event->setLocation($location);
        $event->setName($faker->realText(34));
        $event->setNbInscriptionsMax($faker->numberBetween(1, 20));
        $event->setInfosEvent($faker->realText(200));
        $duration = $this->durationCreator();
        $event->setDuration($duration);

        //set dates and state
        if ($stateParam == "EC") {
            $activityStartDate = $faker->dateTimeBetween('+30 days', '+60 days');
            $activityEndDate = $activityStartDate->add(new \DateInterval('PT'.$duration.'H'));
            $inscriptionsClotureDate = $faker->dateTimeBetween('+1 days', $activityStartDate);
            $state = $this->getReference(StateFixtures::EC);

        } elseif ($stateParam == "OU") {
            $activityStartDate = $faker->dateTimeBetween('+30 days', '+60 days');
            $activityEndDate = $activityStartDate->add(new \DateInterval('PT'.$duration.'H'));
            $inscriptionsClotureDate = $faker->dateTimeBetween('+1 days', $activityStartDate);
            $state = $this->getReference(StateFixtures::OU);

        } elseif ($stateParam == "CL") {
            $activityStartDate = $faker->dateTimeBetween('+1 days', '+30 days');
            $activityEndDate = $activityStartDate->add(new \DateInterval('PT'.$duration.'H'));
            $inscriptionsClotureDate = $faker->dateTimeBetween('-30 days', '-1 days');
            $state = $this->getReference(StateFixtures::CL);

        } elseif ($stateParam == "AEC") {
            $activityStartDate = $faker->dateTimeBetween('-'.$duration.' hours', '+'.$duration.' hours');
            $activityEndDate = $activityStartDate->add(new \DateInterval('PT'.$duration.'H'));
            $inscriptionsClotureDate = $faker->dateTimeBetween('-30 days', '-3 days');
            $state = $this->getReference(StateFixtures::AEC);

        } elseif ($stateParam == "AT") {
            $activityStartDate = $faker->dateTimeBetween('-29 days', '-1 days');
            $activityEndDate = $activityStartDate->add(new \DateInterval('PT'.$duration.'H'));
            $inscriptionsClotureDate = $faker->dateTimeBetween('-60 days', '-30 days');
            $state = $this->getReference(StateFixtures::AT);

        } elseif ($stateParam == "AH") {
            $activityStartDate = $faker->dateTimeBetween('-60 days', '-30 days');
            $activityEndDate = $activityStartDate->add(new \DateInterval('PT'.$duration.'H'));
            $inscriptionsClotureDate = $faker->dateTimeBetween('-90 days', '-61 days');
            $state = $this->getReference(StateFixtures::AH);

        } else {
            $activityStartDate = $faker->dateTimeBetween('+30 days', '+60 days');
            $activityEndDate = $activityStartDate->add(new \DateInterval('PT'.$duration.'H'));
            $inscriptionsClotureDate = $faker->dateTimeBetween('+1 days', $activityStartDate);
            $state = $this->getReference(StateFixtures::OU);
        }


        $event->setDateTimeStart($activityStartDate);
        $event->setDuration($duration);
        $event->setDateEndInscription($inscriptionsClotureDate);
        $event->setState($state);

        return $event;
    }

    private function durationCreator(){
        try {
            $duration = random_int(1, 48);
        } catch (\Exception $e) {
        }
        return $duration;
    }

    private function randomOrganizer($campus)
    {
        try {
            $random = random_int(0, 9);
            $random2 = random_int(10, 19);
        } catch (\Exception $e) {
        }
        switch ($campus) {
            case $this->getReference(CampusFixtures::CAMPUS_NANTES) :
                return $this->getReference(ParticipantFixtures::PARTICIPANT_NANTES . $random);
                break;
            case $this->getReference(CampusFixtures::CAMPUS_RENNES) :
                return $this->getReference(ParticipantFixtures::PARTICIPANT_RENNES . $random2);
                break;
            default :
                return $this->getReference(ParticipantFixtures::PARTICIPANT_ADMIN);
                break;
        }

    }

    private function randomCampusOrganizer()
    {
        try {
            $random = random_int(0, 1);
        } catch (\Exception $e) {
        }
        if ($random == 0) {
            return $this->getReference(CampusFixtures::CAMPUS_NANTES);
        } else {
            return $this->getReference(CampusFixtures::CAMPUS_RENNES);
        }

    }


    private function randomLocation()
    {
        $random1 = random_int(0, 4);
        $random2 = random_int(0, 4);
        return $this->getReference(LocationFixtures::LOCATION . $random1 . $random2);
    }


}
