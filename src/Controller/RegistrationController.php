<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Security\LoginFormAuthenticator;
use App\Service\MailerService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class RegistrationController extends AbstractController
{
    private MailerService $mailerService;

    public function __construct(MailerService $mailerService)
    {
        $this->mailerService = $mailerService;
    }

    #[Route('/register', name: 'app_register')]
    public function register(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher, UserAuthenticatorInterface $userAuthenticator, LoginFormAuthenticator $authenticator): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Générer un token de validation
            $verificationToken = Uuid::v4()->toRfc4122();
            $user->setActivated(false);
            $user->setVerificationToken($verificationToken);

            // Add the default “ROLE_USER” role
            $user->setRoles(['ROLE_USER']);

            // Encode password
            $hashedPassword = $passwordHasher->hashPassword(
                $user,
                $form->get('plainPassword')->getData()
            );
            $user->setPassword($hashedPassword);

            // Save user
            $entityManager->persist($user);
            $entityManager->flush();

            // Generate validation link
            $verificationLink = $this->generateUrl('app_verify_email', [
                'token' => $verificationToken
            ], UrlGeneratorInterface::ABSOLUTE_URL);

            // Send validation email
            $this->mailerService->sendVerificationEmail($user->getEmail(), $verificationLink);

            // Redirect user to login page
            return $this->redirectToRoute('app_login');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    #[Route('/verify-email/{token}', name: 'app_verify_email')]
    public function verifyEmail(string $token, EntityManagerInterface $entityManager): Response
    {
        $user = $entityManager->getRepository(User::class)->findOneBy(['verificationToken' => $token]);

        if ($user) {
            // Validate user
            $user->setActivated(true);
            $user->setVerificationToken(null);  // Delete token after validation
            $entityManager->flush();

            // Redirect to login page with success message
            $this->addFlash('success', 'Votre compte a été activé avec succès.');
            return $this->redirectToRoute('app_login');
        }

        // Redirect with error message if token is invalid
        $this->addFlash('error', 'Le token de validation est invalide.');
        return $this->redirectToRoute('app_register');
    }
}