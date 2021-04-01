<?php

namespace App\Controller;


use App\Entity\Event;
use App\Entity\Participant;
use App\Entity\SearchEvents;
use App\Entity\State;
use App\Form\EventType;
use App\Form\SearchEventsType;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
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

        $now = new \DateTime();
        $endDate = new \DateTime();
        $endDate->modify('+' . 90 . ' days');

        $searchEvents->setEndDate($endDate);
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
            "now" => $now,
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
        // blocking access to non-connected users
        $this->denyAccessUnlessGranted("ROLE_USER");
        // creating a new instance of Event
        $event = new Event();

        // creating a new instance of EventForm
        $eventForm = $this->createForm(EventType::class, $event);

        $eventForm->handleRequest($request);

        if ($eventForm->isSubmitted() && $eventForm->isValid()) {
            // status is "En création" by default at this stage
            $state = new State();
            $state->setLabel('En création');
            $event->setState($state);

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
            "eventForm" => $eventForm->createView()
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

    /**
     * @Route("/event/subscribe/{eventId}{userId}", name="event_subscribe")
     */
    public function subscribe(EntityManagerInterface $em, $eventId, $userId)
    {

        $event = $em->getRepository(Event::class)
            ->findOneBy(['id' => $eventId]);
        $participant = $em->getRepository(Participant::class)
            ->findOneBy(['id' => $userId]);
        $event->addParticipant($participant);

        $em->persist($event);
        $em->flush();

        /*dd($event);*/

        return $this->redirectToRoute("main_home");

    }

    /**
     * @Route("/event/unsubscribe/{eventId}{userId}", name="event_unsubscribe")
     */
    public function unsubscribe(EntityManagerInterface $em, $eventId, $userId)
    {


        $event = $em->getRepository(Event::class)
            ->findOneBy(['id' => $eventId]);

        if (empty($event)) {
            throw $this->createNotFoundException("Cette sortie n'existe pas");
        }

        $participant = $em->getRepository(Participant::class)
            ->findOneBy(['id' => $userId]);

        if (empty($participant)) {
            throw $this->createNotFoundException("Ce participant n'existe pas");
        }

        $event->removeParticipant($participant);

        dump($event);

        $em->persist($event);
        $em->flush();

        /*dd($event);*/

        return $this->redirectToRoute("main_home");
        /*return new Response(null, 204);*/

    }


}
