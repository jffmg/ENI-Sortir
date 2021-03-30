<?php

namespace App\Controller;


use App\Entity\Event;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;


/**
 * @Route("/event")
 */
class EventController extends AbstractController
{
    /**
     * @Route("", name="display_events")
     */
    public function displayEvents()
    {
        // Access denied if user not connected
        $this->denyAccessUnlessGranted("ROLE_USER");

        // Get the events from database
        $eventRepo = $this->getDoctrine()->getRepository(Event::class);
        $events = $eventRepo->findAll();

        return $this->render("event/list.html.twig", [
            "events" => $events
        ]);
    }

}
