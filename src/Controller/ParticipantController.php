<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Form\ParticipantType;
use App\Service\MyServices;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

/**
 * @Route("/participant")
 */
class ParticipantController extends AbstractController
{
    /**
     * @Route("/{id}", name="participant_profile",
     *     requirements={"id": "\d*"})
     */
    public function displayProfile($id, MyServices $service)
    {
        // Access denied if user not connected
        $this->denyAccessUnlessGranted("ROLE_USER");

        // Redirect if participant is inactive
        if(!$service->manageInactiveParticipant()){
            dump('redirect inactive participant');
            return $this->redirectToRoute('participant_inactive');
        }

        // Get the participant from database
        $participantRepo = $this->getDoctrine()->getRepository(Participant::class);
        $participant = $participantRepo->find($id);

        // error if not valid id
        if (empty($participant)) {
            throw $this->createNotFoundException("Cet utilisateur n'existe pas");
        }

        return $this->render("participant/profile.html.twig", [
            "participant" => $participant
        ]);
    }

    /**
     * @Route("/update/{id}", name="participant_update",
     *     requirements={"id": "\d*"})
     */
    public function update($id, EntityManagerInterface $em, Request $request, UserPasswordEncoderInterface $encoder, SluggerInterface $slugger, MyServices $service)
    {
        // Access denied if user not connected
        $this->denyAccessUnlessGranted("ROLE_USER");

        // Redirect if participant is inactive
        if(!$service->manageInactiveParticipant()){
            dump('redirect inactive participant');
            return $this->redirectToRoute('participant_inactive');
        }

        $participantRepo = $this->getDoctrine()->getRepository(Participant::class);
        $participant = $participantRepo->find($id);


        $participantForm = $this->createForm(ParticipantType::class, $participant);

        $participantForm->handleRequest($request);

        if ($participantForm->isSubmitted() && $participantForm->isValid()){

            // hash password
            $hashed = $encoder->encodePassword($participant, $participant->getPassword());
            $participant->setPassword($hashed);

            $em->persist($participant);
            $em->flush();

            $this->addFlash('success', 'Le profil a bien été modifié.');
            return $this->redirectToRoute('participant_profile', ['id' => $participant->getId()]);
        }

        return $this->render('participant/update.html.twig', [
            "participantForm" => $participantForm->createView(),
            "participant" => $participant
        ]);

    }


    /**
     * @Route("/inactive", name="participant_inactive")
     */
    public function redirectInactiveParticipant()
    {
        return $this->render('participant/inactive.html.twig');
    }
}
