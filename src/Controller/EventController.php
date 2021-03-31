<?php

namespace App\Controller;


use App\Entity\Event;
use App\Entity\SearchEvents;
use App\Form\SearchEventsType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;


/**
 * @Route("/event")
 */
class EventController extends AbstractController
{
    /**
     * @Route("/list", name="display_events")
     */
    public function displayEvents(Request $request)
    {
        // Access denied if user not connected
        $this->denyAccessUnlessGranted("ROLE_USER");

        // Create the form
        $searchEvents = new SearchEvents();
        $searchEventsForm = $this->createForm(SearchEventsType::class, $searchEvents);


        // Get the data from the form
        $searchEventsForm->handleRequest($request);

        // Get the events from database
        $eventRepo = $this->getDoctrine()->getRepository(Event::class);
        $events = $eventRepo->filterEvents($searchEvents);

        return $this->render("event/list.html.twig", [
            "events" => $events,
            "searchEventsForm" => $searchEventsForm->createView()
        ]);
    }

}
