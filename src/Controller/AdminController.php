<?php

namespace App\Controller;

use App\Entity\Campus;
use App\Entity\City;
use App\Entity\Event;
use App\Entity\Participant;
use App\Form\AdminParticipantType;
use App\Form\CampusType;
use App\Form\CityType;
use App\Form\FileUploadType;
use App\Form\ParticipantsManagerType;
use App\Form\ParticipantType;
use App\Service\CSVUploader;
use App\Service\MyServices;
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
     * @Route("/portal", name="portal")
     */
    public function portal(MyServices $service): Response
    {
        $this->denyAccessUnlessGranted("ROLE_ADMIN");

        // Redirect if participant is inactive
        if(!$service->manageInactiveParticipant()){
            dump('redirect inactive participant');
            return $this->redirectToRoute('participant_inactive');
        }

        return $this->render('/admin/admin.html.twig');
    }

    /**
     * @Route("/add", name="addParticipants")
     */
    public function addParticipants(Request $request, EntityManagerInterface $em, UserPasswordEncoderInterface $encoder, MailerInterface $mailer, CSVUploader $file_uploader, MyServices $service): Response
    {
        $this->denyAccessUnlessGranted("ROLE_ADMIN");

        // Redirect if participant is inactive
        if(!$service->manageInactiveParticipant()){
            dump('redirect inactive participant');
            return $this->redirectToRoute('participant_inactive');
        }

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

    /**
     * @Route("/manager", name="manager", methods={"GET", "POST", "HEAD"})
     */
    public function manager(Request $request, MyServices $service): Response
    {
        $this->denyAccessUnlessGranted("ROLE_ADMIN");

        // Redirect if participant is inactive
        if(!$service->manageInactiveParticipant()){
            dump('redirect inactive participant');
            return $this->redirectToRoute('participant_inactive');
        }

        $participantRepo = $this->getDoctrine()->getRepository(Participant::class);
        $participants = $participantRepo->findAll();

        $selectionArray = [];
        $selectionToActivate = [];
        $selectionToDeactivate = [];
        $selectionToDelete = [];

        if (isset($_POST['selection'])) {
            $selectionArray = $_POST['selection'];
            dump($selectionArray);
            /*foreach($selectionArray as $selection) {
                if
            }*/

            if (isset($_POST['modify'])) {
                $participantsToModify = $participantRepo->findMultipleByIds($selectionArray);
                /*dump($participantsToModify);*/
                foreach ($participantsToModify as $p) {
                    if ($p->getActive() == 1) {
                        $selectionToDeactivate[] = $p->getId();
                    } else {
                        $selectionToActivate[] = $p->getId();
                    }
                }
                if (count($selectionToDeactivate) > 0) {
                    $participantRepo->deactivateSelection($selectionToDeactivate);
                }
                if (count($selectionToActivate) > 0) {
                    $participantRepo->activateSelection($selectionToActivate);
                }
                return $this->redirectToRoute('admin_manager');
            }

            if (isset($_POST['delete'])) {
                $participantsToModify = $participantRepo->findMultipleByIds($selectionArray);
                foreach ($participantsToModify as $p) {
                    $selectionToDelete[] = $p->getId();
                }
                $participantRepo->deleteSelection($selectionToDelete);
                return $this->redirectToRoute('admin_manager');
            }
        }

        return $this->render('/admin/manager.html.twig', ['participants' => $participants/*, 'participantManagerForm' => $participantManagerForm->createView()*/]);
    }

    /**
     * @Route("/cities", name="cities")
     */
    public function cities(EntityManagerInterface $em, Request $request, MyServices $service)
    {

        // Redirect if participant is inactive
        if(!$service->manageInactiveParticipant()){
            dump('redirect inactive participant');
            return $this->redirectToRoute('participant_inactive');
        }

        if (isset($_POST['search'])) {
            $keywordsString = $_POST['keywords'];
            $keywordsArray = explode(" ", trim($keywordsString));
            if (count($keywordsArray) > 0) {
                $cityRepo = $this->getDoctrine()->getRepository(City::class);
                $cities = $cityRepo->findByKeyWord($keywordsArray);
            } else {
                $cityRepo = $this->getDoctrine()->getRepository(City::class);
                $cities = $cityRepo->findAll();
            }
        } else {
            $cityRepo = $this->getDoctrine()->getRepository(City::class);
            $cities = $cityRepo->findAll();
        }

        if (isset($_POST['delete'])) {
            $cityId = $_POST['cityId'];
            $selectedCity = $cityRepo->find($cityId);
            $em->remove($selectedCity);
            $em->flush();
            return $this->redirectToRoute('admin_cities');
        }

        $cityForm = $this->createForm(CityType::class);
        $cityForm->handleRequest($request);

        if ($cityForm->isSubmitted() && $cityForm->isValid()) {
                $newCity = $cityForm->getData();
                $em->persist($newCity);
                $em->flush();
                return $this->redirectToRoute('admin_cities');
            }


        return $this->render('admin/cities.html.twig', ['cities' => $cities, 'cityForm' => $cityForm->createView()]);
    }

    /**
     * @Route("/cities/update/{id}", name="city_update")
     */
    public function cityUpdate(EntityManagerInterface $em, Request $request, $id, MyServices $service)
    {
        // Redirect if participant is inactive
        if(!$service->manageInactiveParticipant()){
            dump('redirect inactive participant');
            return $this->redirectToRoute('participant_inactive');
        }

        $cityRepo = $this->getDoctrine()->getRepository(City::class);
        $city = $cityRepo->find($id);


        $cityForm = $this->createForm(CityType::class);
        $cityForm->handleRequest($request);

        if ($cityForm->isSubmitted() && $cityForm->isValid()) {
            $updatedCity = $cityForm->getData();
            $newName = $updatedCity->getName();
            $newZip = $updatedCity->getZipCode();
            $city->setName($newName);
            $city->setZipCode($newZip);
            $em->flush();
            return $this->redirectToRoute('admin_cities');
        }

        return $this->render('admin/cityUpdate.html.twig', ['city' => $city, 'cityForm' => $cityForm->createView()]);
    }


    /**
     * @Route("/campuses", name="campuses")
     */
    public function campuses(EntityManagerInterface $em, Request $request, MyServices $service)
    {
        // Redirect if participant is inactive
        if(!$service->manageInactiveParticipant()){
            dump('redirect inactive participant');
            return $this->redirectToRoute('participant_inactive');
        }

        if (isset($_POST['search'])) {
            $keywordsString = $_POST['keywords'];
            $keywordsArray = explode(" ", trim($keywordsString));
            if (count($keywordsArray) > 0) {
                $campusRepo = $this->getDoctrine()->getRepository(Campus::class);
                $campuses = $campusRepo->findByKeyWord($keywordsArray);
            } else {
                $campusRepo = $this->getDoctrine()->getRepository(Campus::class);
                $campuses = $campusRepo->findAll();
            }
        } else {
            $campusRepo = $this->getDoctrine()->getRepository(Campus::class);
            $campuses = $campusRepo->findAll();
        }

        if (isset($_POST['delete'])) {
            $campusId = $_POST['campusId'];
            $selectedCampus = $campusRepo->find($campusId);
            $em->remove($selectedCampus);
            $em->flush();
            return $this->redirectToRoute('admin_campuses');
        }


        $campusForm = $this->createForm(CampusType::class);
        $campusForm->handleRequest($request);

        if ($campusForm->isSubmitted() && $campusForm->isValid()) {
            $newCampus = $campusForm->getData();
            $em->persist($newCampus);
            $em->flush();
            return $this->redirectToRoute('admin_campuses');
        }


        return $this->render('admin/campuses.html.twig', ['campuses' => $campuses, 'campusForm' => $campusForm->createView()]);
    }

    /**
     * @Route("/campuses/update/{id}", name="campus_update")
     */
    public function campusUpdate(EntityManagerInterface $em, Request $request, $id, MyServices $service)
    {
        // Redirect if participant is inactive
        if(!$service->manageInactiveParticipant()){
            dump('redirect inactive participant');
            return $this->redirectToRoute('participant_inactive');
        }

        $campusRepo = $this->getDoctrine()->getRepository(Campus::class);
        $campus = $campusRepo->find($id);


        $campusForm = $this->createForm(CampusType::class);
        $campusForm->handleRequest($request);

        if ($campusForm->isSubmitted() && $campusForm->isValid()) {
            $updatedCampus = $campusForm->getData();
            $newName = $updatedCampus->getName();
            $campus->setName($newName);
            $em->flush();
            return $this->redirectToRoute('admin_campuses');
        }

        return $this->render('admin/campusUpdate.html.twig', ['campus' => $campus, 'campusForm' => $campusForm->createView()]);
    }



}

