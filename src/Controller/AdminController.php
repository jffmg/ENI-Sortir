<?php

namespace App\Controller;

use App\Entity\Campus;
use App\Entity\Participant;
use App\Form\AdminParticipantType;
use App\Form\ParticipantType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

/**
 * Class AdminController
 * @package App\Controller
 * @Route("/admin", name="admin_")
 */
class AdminController extends AbstractController
{

    /**
     * @var UserPasswordEncoderInterface
     */
    private UserPasswordEncoderInterface $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    /**
     * @Route("/manager", name="manager")
     */
    public function manager(): Response
    {
        $this->denyAccessUnlessGranted("ROLE_ADMIN");

        return $this->render('/admin/admin.html.twig');
    }

    /**
     * @Route("/add", name="addParticipants")
     */
    public function addParticipants(Request $request, EntityManagerInterface $em, UserPasswordEncoderInterface $encoder, MailerInterface $mailer): Response
    {
        $this->denyAccessUnlessGranted("ROLE_ADMIN");

        $campusRepo = $this->getDoctrine()->getRepository(Campus::class);
        $campuses = $campusRepo->findAll();

        $participant = new Participant();

        $adminParticipantForm = $this->createForm(AdminParticipantType::class, $participant);
        dump($participant);
        if ($participant->getAdmin() == null) {
            $adminParticipantForm->get('admin')->setData('Non');
        }

        $adminParticipantForm->handleRequest($request);


        if ($adminParticipantForm->isSubmitted() && $adminParticipantForm->isValid()) {
            if ($participant->getUpdatedAt() == null) {
                $participant->setUpdatedAt(new \DateTime());
            }
            $hashed = $encoder->encodePassword($participant, $participant->getPassword());
            $participant->setPassword($hashed);
            $participant->setActive(1);
            $selectedCampus = $_POST["campus"];
            $campus = $campusRepo->findOneBy(['name' => $selectedCampus]);
            $participant->setCampus($campus);
            $em->persist($participant);
            $em->flush();
            dump($participant);

            //send email to the new user
            $email = (new Email())
                ->from('noreply@eni-sortir.fr')
                ->subject('Votre compte Sortir.fr - ENI Ecole informatique')
                ->text('Bonjour ' . $adminParticipantForm->get('firstName')->getData() . '
                Votre compte Sortir.fr a été créé.
                Voici vos informations de connexion : 
                Login : '.$adminParticipantForm->get('userName')->getData().'
                Mot de passe : '.$adminParticipantForm->get('password')->getData().'
                Pour plus de sécurité, nous préconisons de modifier votre mot de passe dès la première connexion. 
                Cordialement, 
                L\'equipe de Sortir.fr')
                ->to($adminParticipantForm->get('mail')->getData())
            ;
            $mailer->send($email);


            $this->addFlash('success', 'L\'utilisateur a bien été créé.');
        }


        return $this->render('/admin/addParticipants.html.twig', [
            "adminParticipantForm" => $adminParticipantForm->createView(), 'campuses' => $campuses]);
    }


}
