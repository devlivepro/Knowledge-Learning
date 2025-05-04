<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/student')]
#[IsGranted('ROLE_USER')]
class StudentCertificationsController extends AbstractController
{
    #[Route('/certifications', name: 'student_certifications')]
    public function index(): Response
    {
        // Retrieve the currently logged-in user
        $user = $this->getUser();

        // Fetch the certifications associated with the user
        $certifications = $user->getCertifications();

        // Render the 'student/certifications.html.twig' template,
        // passing the certifications array for display
        return $this->render('student/certifications.html.twig', [
            'certifications' => $certifications,
        ]);
    }
}
