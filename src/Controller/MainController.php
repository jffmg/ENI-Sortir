<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    /**
     * @Route("/", name="main_home")
     */
    public function index(): Response
    {
        return $this->render('main/home.html.twig');
    }

    /**
     * @Route("/login", name="user_login")
     */
    public function login(): Response
    {
        return $this->render('/participant/login.html.twig');
    }

    /**
     * @Route("/logout", name="user_logout")
     */
    public function logout(): Response
    {
    }
}
