<?php

namespace App\Controller;

use App\Entity\Campus;
use App\Entity\Participant;
use App\Form\AdminParticipantType;
use App\Form\FileUploadType;
use App\Form\ParticipantType;
use App\Service\CSVUploader;
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
    public function addParticipants(Request $request, EntityManagerInterface $em, UserPasswordEncoderInterface $encoder, MailerInterface $mailer, CSVUploader $file_uploader): Response
    {
        $this->denyAccessUnlessGranted("ROLE_ADMIN");

        $campusRepo = $this->getDoctrine()->getRepository(Campus::class);
        $campuses = $campusRepo->findAll();

        $participant = new Participant();

        $adminCSVForm = $this->createForm(FileUploadType::class);
        $adminCSVForm->handleRequest($request);

        if ($adminCSVForm->isSubmitted() && $adminCSVForm->isValid()) {
            $file = $adminCSVForm['upload_file']->getData();
            if ($file) {
                $file_name = $file_uploader->upload($file);
                if ($file_name !== null) {
                    $directory = $file_uploader->getTargetDirectory();
                    $full_path = $directory . '/' . $file_name;
                    if (($handle = fopen($full_path, 'r')) !== false) {
                        $usersArray = [];
                        fgetcsv($handle, 1000, ",");
                        while (($data = fgetcsv($handle, 1000, ',')) !== false) {
                            $user = new Participant();
                            $campus = $campusRepo->findOneBy(['id' => intval($data[0])]);
                            $user->setCampus($campus);
                            $user->setUserName($data[1]);
                            $user->setName($data[2]);
                            $user->setFirstName($data[3]);
                            $user->setMail($data[4]);
                            $user->setAdmin($data[6]);
                            $user->setActive($data[7]);
                            $user->setUpdatedAt(new \DateTime());
                            $hashed = $encoder->encodePassword($participant, $data[5]);
                            $user->setPassword($hashed);
                            $em->persist($user);
                            $em->flush();
                            $usersArray[] = $user;
                        }
                        fclose($handle);
                        $this->addFlash('success', 'Données insérées.');
                    }
                } else {
                    // ... handle exception if something happens during file upload
                    $this->addFlash('error', 'Echec à l\'insertion de données.');
                }
            }
        }


        $adminParticipantForm = $this->createForm(AdminParticipantType::class, $participant);
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
                Login : ' . $adminParticipantForm->get('userName')->getData() . '
                Mot de passe : ' . $adminParticipantForm->get('password')->getData() . '
                Pour plus de sécurité, nous préconisons de modifier votre mot de passe dès la première connexion. 
                Cordialement, 
                L\'equipe de Sortir.fr')
                ->to($adminParticipantForm->get('mail')->getData());
            $mailer->send($email);


            $this->addFlash('success', 'L\'utilisateur a bien été créé.');
        }


        return $this->render('/admin/addParticipants.html.twig', [
            "adminParticipantForm" => $adminParticipantForm->createView(), 'campuses' => $campuses, 'adminCSVForm' => $adminCSVForm->createView()]);
    }


}
