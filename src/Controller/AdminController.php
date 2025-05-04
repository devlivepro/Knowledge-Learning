<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Repository\CommandeRepository;
use App\Repository\CertificationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin')]
#[IsGranted('ROLE_ADMIN')] // Ensure only users with ROLE_ADMIN can access any action in this controller
class AdminController extends AbstractController
{
    #[Route('', name: 'admin_dashboard')]
    public function dashboard(
        UserRepository $userRepository,
        CommandeRepository $commandeRepository,
        CertificationRepository $certificationRepository
    ): Response {
        // Fetch all users from the database
        $users = $userRepository->findAll();

        // Fetch all orders
        $commandes = $commandeRepository->findAll();

        // Fetch all certifications
        $certifications = $certificationRepository->findAll();

        // Render the dashboard template with the retrieved data
        return $this->render('admin/dashboard.html.twig', [
            'users' => $users,
            'commandes' => $commandes,
            'certifications' => $certifications,
        ]);
    }

    #[Route('/user/{id}', name: 'admin_user_detail', requirements: ['id' => '\d+'])]
    public function userDetail(
        User $user,
        CommandeRepository $commandeRepository,
        CertificationRepository $certificationRepository
    ): Response {
        // Fetch orders belonging to this user
        $commandes = $commandeRepository->findBy(['user' => $user]);

        // Fetch certifications belonging to this user
        $certifications = $certificationRepository->findBy(['user' => $user]);

        // Render the user detail template with the retrieved data
        return $this->render('admin/user_detail.html.twig', [
            'user' => $user,
            'commandes' => $commandes,
            'certifications' => $certifications,
        ]);
    }
}
