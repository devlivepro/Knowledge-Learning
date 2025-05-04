<?php

namespace App\Controller;

use App\Entity\Theme;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ThemeController extends AbstractController
{
    #[Route('/theme/{id}', name: 'theme_show')]
    public function show(Theme $theme): Response
    {
        // passing the Theme entity and its related cursus collection
        return $this->render('theme/show.html.twig', [
            'theme' => $theme,
            'cursusList' => $theme->getCursus(),
        ]);
    }
}
