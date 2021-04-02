<?php

namespace App\Service;

use App\Controller\EventController;
use App\Entity\Event;
use App\Entity\State;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MyServices extends AbstractController
{
    public function updateState(EventController $eventController, EntityManagerInterface $em){
        dump('Call the updateState function');

        // Get the date from database
        $stateRepo = $eventController->getDoctrine()->getRepository(State::class);
        $states = $stateRepo->findAll();

        $stateOU = $this->getStateByShortLabel($states, 'OU');
        $stateCL = $this->getStateByShortLabel($states, 'CL');
        $stateAEC = $this->getStateByShortLabel($states, 'AEC');
        $stateAT = $this->getStateByShortLabel($states, 'AT');
        $stateAH = $this->getStateByShortLabel($states, 'AH');

        $eventRepo = $eventController->getDoctrine()->getRepository(Event::class);
        $events = $eventRepo->findAll();

        $now = new \DateTime();

        foreach($events as $event) {
            $state = $event->getState();

            $dateArchive = clone ($now);
            $dateArchive->modify('-'. 30 .' days');

            $duration = $event->getDuration();
            $dateEvent = $event->getDateTimeStart();
            $dateEndEvent = clone($dateEvent);
            $dateEndEvent->modify('+' . $duration . ' hours');
            //$dateEndEvent->add(new \Dateinterval('PT'.$duration.'H'));

            // Archive all events ended 1 month ago
            if ( $state != $stateAH and $dateEvent < $dateArchive)
            {
                $event->setState($stateAH);
                $em->persist($event);
            }

            // Update status to AEC
            if ($state == $stateOU or $state == $stateCL) {
                if ($dateEvent < $now and $dateEndEvent > $now) {
                    $event->setState($stateAEC);
                    $em->persist($event);
                }
            }

            // Update status to AT
            if ($state == $stateOU or $state == $stateCL or $state == $stateAEC) {
                if($dateEndEvent < $now and $dateEvent > $dateArchive)
                {
                    dump('doit updater à AT');
                    $event->setState($stateAT);
                    $em->persist($event);
                }
            }
        }

        // update the database
        $em->flush();
    }



    public function getStateByShortLabel(Array $states, string $label) {
        $result = null;
        if ($states) {
            foreach ($states as $state) {
                if ($state->getShortLabel() == $label) {
                    $result = $state;
                    break;
                }
            }
        }
        return $result;
    }

}
