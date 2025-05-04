<?php

namespace App\Controller;

use App\Repository\ThemeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class VisitorController extends AbstractController
{
    #[Route('/themes', name: 'visitor_themes')]
    #[Route('/themes/{filter}', name: 'visitor_themes_filter')]
    public function themes(ThemeRepository $themeRepository, string $filter = null): Response
    {
        // Retrieve all themes from the database
        $themes = $themeRepository->findAll();

        // Render the themes template, passing the list and the currently selected filter
        return $this->render('home/themes.html.twig', [
            'themes' => $themes,
            'selectedTheme' => $filter,
        ]);
    }
}