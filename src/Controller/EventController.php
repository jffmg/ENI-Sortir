<?php

namespace App\Controller;


use App\Entity\Event;
use App\Entity\Campus;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;


/**
 * @Route("/event")
 */
class EventController extends AbstractController
{
    /**
     * @Route("/list", name="display_events")
     */
    public function displayEvents()
    {
        // Access denied if user not connected
        $this->denyAccessUnlessGranted("ROLE_USER");

        // Get the campus from database
        $campusRepo = $this->getDoctrine()->getRepository(Campus::class);
        $campus = $campusRepo->findAll();

        // Get the data from the form
        $campusSelected = null;

        // Get the events from database
        $eventRepo = $this->getDoctrine()->getRepository(Event::class);
        $events = $eventRepo->filterEvents($campusSelected);

        return $this->render("event/list.html.twig", [
            "events" => $events,
            "campus" =>$campus
        ]);
    }

}
