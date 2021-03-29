<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/user")
 */
class UserController extends AbstractController
{
    /**
     * @Route("", name="user_login")
     */
    public function login(): Response
    {
        return $this->render('user/login.html.twig');
    }

    /**
     * @Route("/logout", name="user_logout")
     */
    public function logout(): Response
    {
    }
}
