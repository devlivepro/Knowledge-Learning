<?php

namespace App\Controller;

use App\Repository\ThemeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class HomeController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(
        AuthenticationUtils $authenticationUtils,
        ThemeRepository $themeRepo
    ): Response {
        // Recovers connection error if any
        $error = $authenticationUtils->getLastAuthenticationError();

        // Retrieves the last user name entered
        $lastUsername = $authenticationUtils->getLastUsername();

        // Get all themes for the showcase
        $themes = $themeRepo->findAll();

        return $this->render('home/home.html.twig', [
            'last_username' => $lastUsername,
            'error'         => $error,
            'themes'        => $themes,
        ]);
    }

    #[Route('/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // Retrieve the last authentication error, if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // Retrieve the last username entered by the user (for prefilling the login form)
        $lastUsername = $authenticationUtils->getLastUsername();

        // Render the login template, passing in the last username and any login error
        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }
}
