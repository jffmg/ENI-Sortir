<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Form\ParticipantType;
use App\Form\SearchEventsType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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
    public function update($id, EntityManagerInterface $em, Request $request, UserPasswordEncoderInterface $encoder, SluggerInterface $slugger)
    {
        // Access denied if user not connected
        $this->denyAccessUnlessGranted("ROLE_USER");

        $participantRepo = $this->getDoctrine()->getRepository(Participant::class);
        $participant = $participantRepo->find($id);

//        $participant->setPictureFile(
//            new File($this->getParameter('pictures_directory').'/'.$participant->getPicture())
//        );

        $participantForm = $this->createForm(ParticipantType::class, $participant);

        $participantForm->handleRequest($request);

        if ($participantForm->isSubmitted() && $participantForm->isValid()){

            // hash password
            $hashed = $encoder->encodePassword($participant, $participant->getPassword());
            $participant->setPassword($hashed);

            /** @var UploadedFile $pictureFile */
            /*$participant->$participantForm->get('picture')->getData();
            if($pictureFile) {
                $originalFileName = pathinfo($pictureFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFileName = $slugger->slug($originalFileName);
                $newFileName = $safeFileName.'-'.uniqid().'.'.$pictureFile->guessExtension();
                try {
                    $pictureFile->move(
                        $this->getParameter('pictures_directory'),
                        $newFileName
                    );
                } catch (FileException $e) {
                    // todo handle exception if something happens during file upload
                }
                $participant->setPicture(
                    new File($newFileName)
                );
            }*/




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
}
