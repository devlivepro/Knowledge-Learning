<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class HomeController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(AuthenticationUtils $authenticationUtils): Response
    {
        // Recovers connection error if any
        $error = $authenticationUtils->getLastAuthenticationError();

        // Retrieves the last user name entered
        $lastUsername = $authenticationUtils->getLastUsername();

        // Render the view with the variables required for the connection form
        return $this->render('home/home.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }
}
