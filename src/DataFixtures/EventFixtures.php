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

        for ($y = 0; $y < 5; $y++) {
            for ($i = 0; $i < 5; $i++) {
                $faker = Factory::create();
                $event = new Event();


                $campusOrganizer = $this->randomCampusOrganizer();
                $organizer = $this->randomOrganizer($campusOrganizer);
                $location = $this->randomLocation();


                $event->setOrganizer($organizer);
                $event->setCampusOrganizer($campusOrganizer);
                $event->setLocation($location);

                $event->setName($faker->colorName);

                $eventDatesArray = $this->logicalDates($faker);

                $event->setDateTimeStart($eventDatesArray['sd']);
                $event->setDuration($eventDatesArray['dur']);
                $event->setDateEndInscription($eventDatesArray['cd']);
                $event->setState($eventDatesArray['st']);

                $event->setNbInscriptionsMax($faker->numberBetween(1, 20));
                $event->setInfosEvent($faker->userName);


                $this->setReference(self::EVENT . $i, $event);
                $manager->persist($event);
            }

        }
        $manager->flush();

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

    private function logicalDates($faker)
    {
//        $eventDatesArray = [];
        $now = new \DateTime();
        try {
            $duration = random_int(1, 48);
        } catch (\Exception $e) {
        }
        $startDate = $faker->dateTimeThisYear;
        $dateEndInsc = $this->getDateCloture($faker, $startDate);
        $dateEndEvent = $startDate;
        $dateEndEvent->add(new \DateInterval('PT'.$duration.'H'));

        $state = $this->setStateFromDates($startDate, $now, $dateEndInsc, $dateEndEvent);


        $eventDatesArray = ['sd' => $startDate, 'cd' => $dateEndInsc, 'dur' => $duration, 'ed' => $dateEndEvent, 'st' => $state];

        return $eventDatesArray;

    }

    private function getDateCloture($faker, $startDate)
    {
        do {
            $dateEndInsc = $faker->dateTimeThisYear;
        } while ($dateEndInsc > $startDate);

        return $dateEndInsc;
    }

    private function setStateFromDates($sd, $n, $cd ,$ed) {
    $state = '';
    $hd = $sd->add(new \DateInterval('P1M'));
        if($sd > $n && $cd > $n) {
            try {
                $r = random_int(0, 2);
            } catch (\Exception $e) {
            }
            switch ($r) {
                case 0 : $state = $this->getReference(StateFixtures::EC); break;
                case 1 : $state = $this->getReference(StateFixtures::OU); break;
                case 2 : $state = $this->getReference(StateFixtures::AN); break;
                default : $state = $this->getReference(StateFixtures::EC); break;
            }
        } elseif ($sd > $n && $cd < $n) {
            try {
                $r = random_int(0, 1);
            } catch (\Exception $e) {
            }
            switch ($r) {
                case 0 : $state = $this->getReference(StateFixtures::CL); break;
                case 1 : $state = $this->getReference(StateFixtures::AN); break;
                default : $state = $this->getReference(StateFixtures::CL); break;
            }
        } elseif ($sd < $n && $cd < $n && $ed > $n) {
            try {
                $r = random_int(0, 1);
            } catch (\Exception $e) {
            }
            switch ($r) {
                case 0 : $state = $this->getReference(StateFixtures::AEC); break;
                case 1 : $state = $this->getReference(StateFixtures::AN); break;
                default : $state = $this->getReference(StateFixtures::AEC); break;
            }
        } elseif ($sd < $n && $cd < $n && $ed < $n && $hd > $n) {
            try {
                $r = random_int(0, 1);
            } catch (\Exception $e) {
            }
            switch ($r) {
                case 0 : $state = $this->getReference(StateFixtures::AT); break;
                case 1 : $state = $this->getReference(StateFixtures::AN); break;
                default : $state = $this->getReference(StateFixtures::AT); break;
            }
        } elseif ($sd < $n && $cd < $n && $ed < $n && $hd < $n) {
            $state = $this->getReference(StateFixtures::AH);
        }


        return $state;
    }

}
