<?php

namespace App\Controller;


use App\Entity\City;
use App\Entity\Event;
use App\Entity\SearchEvents;
use App\Entity\State;
use App\Form\EventType;
use App\Form\SearchEventsType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;


class EventController extends AbstractController
{
    /**
     * @Route("/", name="main_home")
     */
    public function displayEvents(Request $request)
    {
        // Access denied if user not connected
        $this->denyAccessUnlessGranted("ROLE_USER");

        // Create the form
        $searchEvents = new SearchEvents();
        $searchEvents->setStartDate(new \DateTime());

        $endDate = new \DateTime();
        $endDate->modify('+'. 90 .' days');

        $searchEvents->setEndDate( $endDate);
        $searchEventsForm = $this->createForm(SearchEventsType::class, $searchEvents);

        // Get the data from the form
        $searchEventsForm->handleRequest($request);
        $user = $this->getUser();

        // Get the date from database
        $stateRepo = $this->getDoctrine()->getRepository(State::class);
        $stateEC = $stateRepo->findOneBy(['shortLabel' => 'EC']);
        $stateOU = $stateRepo->findOneBy(['shortLabel' => 'OU']);
        $stateCL = $stateRepo->findOneBy(['shortLabel' => 'CL']);
        $eventRepo = $this->getDoctrine()->getRepository(Event::class);
        $events = $eventRepo->filterEvents($searchEvents, $user);

        return $this->render("event/list.html.twig", [
            "events" => $events,
            "stateEC" => $stateEC,
            "stateOU" => $stateOU,
            "stateCL" => $stateCL,
            "searchEventsForm" => $searchEventsForm->createView()
        ]);
    }
    // functionality 2002: "Créer une sortie"

    /**
     * @Route("/event/add", name="event_add")
     * @param EntityManagerInterface $em
     */
    public function add(EntityManagerInterface $em, Request $request)
    {
        // block access to non-connected users
        $this->denyAccessUnlessGranted("ROLE_USER");
        // creating a new instance of Event
        $event = new Event();

        // create a new instance of EventForm
        $eventForm = $this->createForm(EventType::class, $event);

        // we have to display the cities from those saved in base
        // get the cities from database
        $cityRepo = $this->getDoctrine()->getRepository(City::class);
        $cities = $cityRepo->findAll();

        $eventForm->handleRequest($request);

        if ($eventForm->isSubmitted() && $eventForm->isValid()) {
//            // status is "En création" by default at this stage
            //todo assign default status
//            $state = new State();
//            $state->setLabel('En création');
//            $event->setState($state);

            // organizer is the Participant creating the event
            /** @var \App\Entity\User */
            $organizer = $this->getUser();
            $event->setOrganizer($organizer);

            $em->persist($event);
            $em->flush();
            $this->addFlash('success', 'La sortie a bien été créée.');

            return $this->redirectToRoute('event_detail', [
                'id' => $event->getId()
            ]);
        }

        // displaying form
        return $this->render('event/add.hml.twig', [
            "eventForm" => $eventForm->createView(),
            "cities" => $cities
        ]);
    }

    /**
     * @Route("/event/detail/{id}", name="event_detail")
     */
    public function detail($id)
    {
        // Access denied if user not connected
        $this->denyAccessUnlessGranted("ROLE_USER");

        // Get the participant from database
        $eventRepo = $this->getDoctrine()->getRepository(Event::class);
        $event = $eventRepo->find($id);

        // error if not valid id
        if (empty($event)) {
            throw $this->createNotFoundException("Cette sortie n'existe pas");
        }

        return $this->render("event/detail.html.twig", [
            "event" => $event
        ]);
    }
}
