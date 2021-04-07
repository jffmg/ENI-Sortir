<?php

namespace App\Controller;


use App\Entity\Campus;
use App\Entity\City;
use App\Entity\Event;
use App\Entity\Location;
use App\Entity\Participant;
use App\Entity\SearchEvents;
use App\Entity\State;
use App\Form\CancelType;
use App\Form\EventType;
use App\Form\LocationType;
use App\Form\SearchEventsType;
use App\Service\MyServices;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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

        // Redirect if participant is inactive
        if(!$service->manageInactiveParticipant()){
            dump('redirect inactive participant');
            return $this->redirectToRoute('participant_inactive');
        }

        // Update the events state - Now is done by admin and running every minute
        $service->updateState($em,$logger);

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

        // Get the data from database
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
    public function add(EntityManagerInterface $em, Request $request, MyServices $service)
    {
        // Access denied if user not connected
        $this->denyAccessUnlessGranted("ROLE_USER");

        // Redirect if participant is inactive
        if(!$service->manageInactiveParticipant()){
            dump('redirect inactive participant');
            return $this->redirectToRoute('participant_inactive');
        }

        /*dump($request);*/
        // block access to non-connected users
        $this->denyAccessUnlessGranted("ROLE_USER");
        // creating a new instance of Event
        $event = new Event();
        $newLocation = new Location();

        // create a new instance of EventForm
        $eventForm = $this->createForm(EventType::class, $event);


        //add location form
        $addLocationForm = $this->createForm(LocationType::class, $newLocation);

        // we have to display the cities from those saved in base
        // get the cities from database
        $cityRepo = $this->getDoctrine()->getRepository(City::class);
        $cities = $cityRepo->findAll();


                $eventForm->handleRequest($request);
                $addLocationForm->handleRequest($request);

            if ($eventForm->isSubmitted() && $eventForm->isValid()) {
                // status is "En création" by default at this stage
                $stateRepo = $this->getDoctrine()->getRepository(State::class);
                $state = $stateRepo->findOneBy(['shortLabel' => 'EC']);
                $event->setState($state);

                // organizer is the Participant creating the event
                /** @var \App\Entity\User */
                $organizer = $this->getUser();
                $event->setOrganizer($organizer);
                dump($organizer);

                // campus is the campus associated to the organizer
                $campus = $organizer->getCampus();
                $event->setCampusOrganizer($campus);

                // affect location to event
                $locationId = $request->request->get('event-location');
                $locationRepo = $this->getDoctrine()->getRepository(Location::class);
                $location = $locationRepo->find($locationId);

                $event->setLocation($location);

                $em->persist($event);
                $em->flush();
                $this->addFlash('success', 'La sortie a bien été créée.');

                return $this->redirectToRoute('event_detail', [
                    'id' => $event->getId()
                ]);
            }

            if ($addLocationForm->isSubmitted() && $addLocationForm->isValid()) {
                $cityId = $_POST["hidden-city"];
                $city = $cityRepo->find($cityId);
                $newLocation->setCity($city);
                $em->persist($newLocation);
                $em->flush();


            }



        // display form
        return $this->render('event/add.hml.twig', ["eventForm" => $eventForm->createView(), 'addLocationForm' => $addLocationForm->createView(),
            "cities" => $cities,]);
    }

    /**
     * @Route("/event/add/ajax/{inputCity}", methods={"GET"})
     */
    public function fetchLocationsByCity(Request $request, $inputCity, MyServices $service): Response
    {
        // Access denied if user not connected
        $this->denyAccessUnlessGranted("ROLE_USER");

        // Redirect if participant is inactive
        if(!$service->manageInactiveParticipant()){
            dump('redirect inactive participant');
            return $this->redirectToRoute('participant_inactive');
        }

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
    public function fetchInfosByLocation(Request $request, $inputLocation, MyServices $service): Response
    {
        // Access denied if user not connected
        $this->denyAccessUnlessGranted("ROLE_USER");

        // Redirect if participant is inactive
        if(!$service->manageInactiveParticipant()){
            dump('redirect inactive participant');
            return $this->redirectToRoute('participant_inactive');
        }

        // get infos associated to location selected by user
        $locationRepo = $this->getDoctrine()->getRepository(Location::class);
        $location = $locationRepo->find($inputLocation);

        // create standard entity to put fetched location infos
        $loc = new \stdClass();
        $loc->id = $location->getId();
        $loc->name = $location->getName();
        $loc->street = $location->getStreet();
        $loc->latitude = $location->getLatitude();
        $loc->longitude = $location->getLongitude();

        // serialize the object to pass on to js
        $serializer = new Serializer([new ObjectNormalizer()], [new JsonEncoder()]);

        return new Response($serializer->serialize($loc, 'json'));
    }

    /**
     * @Route("/event/publish/{id}", name="event_publish", requirements={"id": "\d*"})
     */
    public function publish(EntityManagerInterface $em, int $id, MyServices $service)
    {
        // Access denied if user not connected
        $this->denyAccessUnlessGranted("ROLE_USER");

        // Redirect if participant is inactive
        if(!$service->manageInactiveParticipant()){
            dump('redirect inactive participant');
            return $this->redirectToRoute('participant_inactive');
        }

        dump($id);
        // get event from database
        $event = $em->getRepository(Event::class)
            ->find($id);
        if (empty($event)) {
            throw $this->createNotFoundException("Cette sortie n'existe pas");
        }
        dump($event);

        // get "ouverte" status from database
        $stateRepo = $this->getDoctrine()->getRepository(State::class);
        $state = $stateRepo->findOneBy(['shortLabel' => 'OU']);
        dump($state);

        // set event to "ouverte" status
        $event->setState($state);
        dump($event);

        $em->persist($event);
        $em->flush();

        $this->addFlash('success', 'La sortie est maintenant ouverte.');
        // return to homepage
        return $this->redirectToRoute("main_home");
    }

    /**
     * @Route("/event/update/{id}", name="event_update", requirements={"id": "\d*"})
     */
    public function updateEvent(EntityManagerInterface $em, Request $request, int $id, MyServices $service)
    {
        // Access denied if user not connected
        $this->denyAccessUnlessGranted("ROLE_USER");

        // Redirect if participant is inactive
        if(!$service->manageInactiveParticipant()){
            dump('redirect inactive participant');
            return $this->redirectToRoute('participant_inactive');
        }

        $eventRepo = $this->getDoctrine()->getRepository(Event::class);
        $event = $eventRepo->find($id);

        dump($id);
        if ($this->getUser() !== $event->getOrganizer()){
            $this->redirectToRoute("main_home");
        } else {


            $eventForm = $this->createForm(EventType::class, $event);

            // get the cities from database
            $cityRepo = $this->getDoctrine()->getRepository(City::class);
            $cities = $cityRepo->findAll();

            // get the locations from database
            $locationRepo = $this->getDoctrine()->getRepository(Location::class);
            $locations = $locationRepo->findAll();

            $eventForm->handleRequest($request);

            if ($eventForm->isSubmitted() && $eventForm->isValid()) {

                // affect location to event
                $locationId = $request->request->get('event-location');
                $location = $locationRepo->find($locationId);

                $event->setLocation($location);

                $em->persist($event);
                $em->flush();
                $this->addFlash('success', 'La sortie a bien été modifiée.');

                return $this->redirectToRoute('event_detail', [
                    'id' => $event->getId()
                ]);
            }

            // display form
            return $this->render('event/updateEvent.html.twig', [
                "eventForm" => $eventForm->createView(),
                "event" => $event,
                "locations" => $locations,
                "cities" => $cities,
            ]);

        }
    }

    /**
     * @Route("/event/detail/{id}", name="event_detail", requirements={"id": "\d*"})
     */
    public function detail($id, MyServices $service)
    {
        // Access denied if user not connected
        $this->denyAccessUnlessGranted("ROLE_USER");

        // Redirect if participant is inactive
        if(!$service->manageInactiveParticipant()){
            dump('redirect inactive participant');
            return $this->redirectToRoute('participant_inactive');
        }

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
     * @Route("/event/subscribe/{eventId}/{userId}", name="event_subscribe")
     */
    public function subscribe(EntityManagerInterface $em, $eventId, $userId, MyServices $service)
    {
        // Access denied if user not connected
        $this->denyAccessUnlessGranted("ROLE_USER");

        // Redirect if participant is inactive
        if(!$service->manageInactiveParticipant()){
            dump('redirect inactive participant');
            return $this->redirectToRoute('participant_inactive');
        }

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
     * @Route("/event/unsubscribe/{eventId}/{userId}", name="event_unsubscribe")
     */
    public function unsubscribe(EntityManagerInterface $em, $eventId, $userId, MyServices $service)
    {

        // Access denied if user not connected
        $this->denyAccessUnlessGranted("ROLE_USER");

        // Redirect if participant is inactive
        if(!$service->manageInactiveParticipant()){
            dump('redirect inactive participant');
            return $this->redirectToRoute('participant_inactive');
        }

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
    public function delete(EntityManagerInterface $em, int $id, Request $request, MyServices $service)
    {
        // Access denied if user not connected
        $this->denyAccessUnlessGranted("ROLE_USER");

        // Redirect if participant is inactive
        if(!$service->manageInactiveParticipant()){
            dump('redirect inactive participant');
            return $this->redirectToRoute('participant_inactive');
        }

        // Get the event from database
        $eventRepo = $this->getDoctrine()->getRepository(Event::class);
        $event = $eventRepo->find($id);


        // error if not valid id
        if (empty($event)) {
            throw $this->createNotFoundException("Cette sortie n'existe pas");
        }

        // delete event from database
        $em->remove($event);
        $em->flush();

        // display success message
        $this->addFlash('success', 'La sortie a été supprimée');

        //redirect to home
        return $this->redirectToRoute("main_home");
    }

    /**
     * @Route("/event/cancel/{id}", name="event_cancel")
     */
    public function cancel(Request $request, EntityManagerInterface $em, $id, MyServices $service)
    {
        // Access denied if user not connected
        $this->denyAccessUnlessGranted("ROLE_USER");

        // Redirect if participant is inactive
        if(!$service->manageInactiveParticipant()){
            dump('redirect inactive participant');
            return $this->redirectToRoute('participant_inactive');
        }

        // Get the participant from database
        $eventRepo = $this->getDoctrine()->getRepository(Event::class);
        $event = $eventRepo->find($id);


        // error if not valid id
        if (empty($event)) {
            throw $this->createNotFoundException("Cette sortie n'existe pas");
        }

        $cancelForm = $this->createForm(CancelType::class, $event);

        $cancelForm->handleRequest($request);


        if ($cancelForm->isSubmitted() && $cancelForm->isValid()) {
            $cancelReason = $_POST["app-cancel-reason"];
//            dump($cancelReason);
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

    /**
     * @Route("/event/add/location", name="addlocation")
     */
    public function addLocation()
    {



        return $this->render("event/addLocation.html.twig");
    }



}
