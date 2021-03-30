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

     function getOrder() : int {
        return 60;
    }

    public function load(ObjectManager $manager)
    {
        for ($y = 0; $y < 5; $y++) {
            for ($i = 0; $i < 5; $i++) {
                $faker = Factory::create();
                $event = new Event();


                $campusOrganizer = $this->randomCampusOrganizer();
                $organizer = $this->randomOrganizer($campusOrganizer);
                $state = $this->randomState();
                $location = $this->randomLocation();


                $event->setOrganizer($organizer);
                $event->setCampusOrganizer($campusOrganizer);
                $event->setState($state);
                $event->setLocation($location);

                $event->setName($faker->realText(50,2));
                $event->setDateTimeStart($faker->dateTimeThisYear);
                $event->setDuration($faker->numberBetween(1, 10));
                $event->setDateEndInscription($faker->dateTimeThisYear);
                $event->setNbInscriptionsMax($faker->numberBetween(1, 20));
                $event->setInfosEvent($faker->realText(240,2));




                $this->setReference(self::EVENT . $i, $event);
                $manager->persist($event);
            }

        }
        $manager->flush();

    }

    private function randomOrganizer($campus){
        try {
            $random = random_int(0, 9);
        } catch (\Exception $e) {
        }
        switch ($campus) {
             case "campus_nantes" : return $this->getReference(ParticipantFixtures::PARTICIPANT_NANTES.$random); break;
             case "campus_rennes" : return $this->getReference(ParticipantFixtures::PARTICIPANT_RENNES.$random); break;
             default : return $this->getReference(ParticipantFixtures::PARTICIPANT_ADMIN); break;
         }

    }

    private function randomCampusOrganizer(){
        try {
            $random = random_int(0, 1);
        } catch (\Exception $e) {
        }
        if($random == 0) {
              return $this->getReference(CampusFixtures::CAMPUS_NANTES);
          } else {
              return $this->getReference(CampusFixtures::CAMPUS_RENNES);
          }

    }

    private function randomState(){
        try {
            $random = random_int(0, 6);
        } catch (\Exception $e) {
        }
        switch ($random) {
            case 0 : return $this->getReference(StateFixtures::EC) ; break;
            case 1 : return $this->getReference(StateFixtures::OU) ; break;
            case 2 : return $this->getReference(StateFixtures::AN) ; break;
            case 3 : return $this->getReference(StateFixtures::CL) ; break;
            case 4 : return $this->getReference(StateFixtures::AEC) ; break;
            case 5 : return $this->getReference(StateFixtures::AT) ; break;
            case 6 : return $this->getReference(StateFixtures::AH) ; break;
            default : return $this->getReference(StateFixtures::EC) ; break;
        }
    }

    private function randomLocation(){
        $random1 = random_int(0, 4);
        $random2 = random_int(0, 4);
        return $this->getReference(LocationFixtures::LOCATION.$random1.$random2);
    }

}
