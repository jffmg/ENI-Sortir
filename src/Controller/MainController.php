<?php

namespace App\Controller;

use App\Service\MyServices;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class MainController extends AbstractController
{
    /**
     * @Route("/login", name="user_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('/participant/login.html.twig', ['last_username' => $lastUsername,
            'error' => $error,
            ]);
    }

    /**
     * @Route("/logout", name="user_logout")
     */
    public function logout(): Response
    {
    }

    /**
     * @Route("/batch", name="batch")
     */
    public function executeBatch(Request $request, EntityManagerInterface $em, LoggerInterface $logger, MyServices $service)
    {
        $this->denyAccessUnlessGranted("ROLE_ADMIN");

        // Redirect if participant is inactive
        if(!$service->manageInactiveParticipant()){
            dump('redirect inactive participant');
            return $this->redirectToRoute('participant_inactive');
        }

        $logger->info('STARTING BATCH');
        set_time_limit(0);

        $interval = 10;
        $count = 0;
        while ($count < 20) {
            $now = time();

            $logger->info('TIME:'.$now);

            $service->updateState($em,$logger);

            sleep($interval);
            $count++;
        }

        $logger->info('BATCH FINISHED');

        return new Response("Batch exécuté");
    }

}
