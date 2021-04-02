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
        $stateAH = $this->getStateByShortLabel($states, 'AH');

        $eventRepo = $eventController->getDoctrine()->getRepository(Event::class);
        $events = $eventRepo->findAll();

        $now = new \DateTime();

        // Archive all events ended 1 month ago
        $dateArchive = $now;
        $dateArchive->modify('-'. 30 .' days');
        foreach($events as $event) {
            $dateEvent = $event->getDateTimeStart();
            if ( $event->getState() != $stateAH and $dateEvent < $dateArchive) {
                dump('update');
                $event->setState($stateAH);
                $em->persist($event);
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
