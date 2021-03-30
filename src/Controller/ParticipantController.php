<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Form\ParticipantType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("/participant")
 */
class ParticipantController extends AbstractController
{
    /**
     * @Route("/{id}", name="participant_profile",
     *     requirements={"id": "\d*"})
     */
    public function displayProfile($id)
    {
        // Access denied if user not connected
        $this->denyAccessUnlessGranted("ROLE_USER");

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
    public function update($id, EntityManagerInterface $em, Request $request, UserPasswordEncoderInterface $encoder)
    {
        // Access denied if user not connected
        $this->denyAccessUnlessGranted("ROLE_USER");

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

            $this->addFlash('success', 'Le profil a été modifié');
            return $this->redirectToRoute('participant_profile', ['id' => $participant->getId()]);
        }

        return $this->render('participant/update.html.twig', [
            "participantForm" => $participantForm->createView(),
            "participant" => $participant
        ]);

    }
}
