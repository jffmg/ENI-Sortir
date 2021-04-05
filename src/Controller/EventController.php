<?php

namespace App\Controller;


use App\Entity\City;
use App\Entity\Event;
use App\Entity\Location;
use App\Entity\Participant;
use App\Entity\SearchEvents;
use App\Entity\State;
use App\Form\CancelType;
use App\Form\EventType;
use App\Form\SearchEventsType;
use App\Service\MyServices;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;


class EventController extends AbstractController
{
    /**
     * @Route("/", name="main_home")
     */
    public function displayEvents(Request $request, EntityManagerInterface $em, MyServices $service, LoggerInterface $logger)
    {
        // Access denied if user not connected
        $this->denyAccessUnlessGranted("ROLE_USER");

        // Update the events state - Now is done by admin and running every minute
        //$service->updateState($em,$logger);

        // Create the form
        $searchEvents = new SearchEvents();
        $searchEvents->setStartDate(new \DateTime());

        $now = new \DateTime();
        $endDate = new \DateTime();
        $endDate->modify('+' . 90 . ' days');

        $searchEvents->setEndDate($endDate);

        $user = $this->getUser();
        $participantRepo = $this->getDoctrine()->getRepository(Participant::class);
        $participant = $participantRepo->find($user);
        $searchEvents->setCampus($participant->getCampus());

        $searchEventsForm = $this->createForm(SearchEventsType::class, $searchEvents);


        // Get the data from the form
        $searchEventsForm->handleRequest($request);

        // Get the date from database
        $stateRepo = $this->getDoctrine()->getRepository(State::class);
        $states = $stateRepo->findAll();
        $stateEC = $service->getStateByShortLabel($states, 'EC');
        $stateOU = $service->getStateByShortLabel($states, 'OU');
        $stateCL = $service->getStateByShortLabel($states, 'CL');
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
            // status is "En création" by default at this stage
            $stateRepo = $this->getDoctrine()->getRepository()(State::class);
            $state = $stateRepo->findOneBy(['shortLabel' => 'EC']);
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

        // display form
        return $this->render('event/add.hml.twig', [
            "eventForm" => $eventForm->createView(),
            "cities" => $cities
        ]);
    }

    /**
     * @Route("/event/add/ajax/{inputCity}", methods={"GET"})
     */
    public function fetchLocationsByCity(Request $request, $inputCity): Response
    {
        // get locations associated to city selected by user
        $locationRepo = $this->getDoctrine()->getRepository(\App\Entity\Location::class);
        $locations = $locationRepo->findByCityId($inputCity);

        // get zipcode of city selected by user
        $cityRepo = $this->getDoctrine()->getRepository(\App\Entity\City::class);
        $city = $cityRepo->find($inputCity);

        // Create the array with the locations / separated from DB (if not separated, we had some circular issues)
        $result = array();
        foreach ($locations as $location) {

            $loc = new \stdClass();
            $loc->id = $location->getId();
            $loc->name = $location->getName();
            // we'll need the zipcode too for display
            $loc->zipcode = $city->getZipCode();

            $result[] = $loc;
        }

        // serialize $locations to return them
        $serializer = new Serializer([new ObjectNormalizer()], [new JsonEncoder()]);

        return new Response($serializer->serialize($result, 'json'));
    }

    /**
     * @Route("/event/add/ajax/location/{inputLocation}", methods={"GET"})
     */
    public function fetchInfosByLocation(Request $request, $inputLocation): Response
    {
        // get infos associated to location selected by user
        $locationRepo = $this->getDoctrine()->getRepository(Location::class);
        $location = $locationRepo->find($inputLocation);

        $loc = new \stdClass();
        $loc->id = $location->getId();
        $loc->name = $location->getName();
        $loc->street = $location->getStreet();
        $loc->latitude = $location->getLatitude();
        $loc->longitude = $location->getLongitude();

        $serializer = new Serializer([new ObjectNormalizer()], [new JsonEncoder()]);

        return new Response($serializer->serialize($loc, 'json'));
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
        if (empty($event)) {
            throw $this->createNotFoundException("Cette sortie n'existe pas");
        }
        $participant = $em->getRepository(Participant::class)
            ->findOneBy(['id' => $userId]);
        if (empty($participant)) {
            throw $this->createNotFoundException("Ce participant n'existe pas");
        }

        $event->addParticipant($participant);

        if (count($event->getParticipants()) == $event->getNbInscriptionsMax()) {
            $state = $em->getRepository(State::class)
                ->findOneBy(['shortLabel' => 'CL']);
            $event->setState($state);
        }

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

        $now = new \DateTime();

        if (count($event->getParticipants()) < $event->getNbInscriptionsMax() && $event->getDateEndInscription() > $now) {
            $state = $em->getRepository(State::class)
                ->findOneBy(['shortLabel' => 'OU']);
            $event->setState($state);
        }


        $em->persist($event);
        $em->flush();

        /*dd($event);*/

        return $this->redirectToRoute("main_home");
        /*return new Response(null, 204);*/

    }

    /**
     * @Route("/event/delete/{id}", name="event_delete")
     */
    public function delete(EntityManagerInterface $em, $id)
    {
        // todo IMPLEMENTER LA FONCTION POUR SUPPRIMER L'EVENT DE LA DB
        /*CREATE REAL FUNCTION TO DELETE FROM DATABASE*/
        $var = 'DELETED';
        dd($var);
    }

    /**
     * @Route("/event/cancel/{id}", name="event_cancel")
     */
    public function cancel(Request $request, EntityManagerInterface $em, $id)
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

        $cancelForm = $this->createForm(CancelType::class);

        $cancelForm->handleRequest($request);


        if ($cancelForm->isSubmitted() && $cancelForm->isValid()) {
            $cancelReason = $_POST["app-cancel-reason"];
            $event->setInfosEvent($cancelReason);
            $state = $em->getRepository(State::class)
                ->findOneBy(['shortLabel' => 'AN']);
            $event->setState($state);
            $em->persist($event);
            $em->flush();

            return $this->redirectToRoute("main_home");
        }


        return $this->render("event/cancel.html.twig", ["cancelForm" => $cancelForm->createView(),
            "event" => $event
        ]);
    }

}
