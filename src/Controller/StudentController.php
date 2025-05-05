<?php

namespace App\Controller;

use App\Form\ProfileFormType;
use App\Repository\ThemeRepository;
use App\Repository\OrderRepository;
use App\Repository\LessonValidationRepository;
use App\Entity\Cursus;
use App\Entity\Order;
use App\Entity\Lesson;
use App\Entity\LessonValidation;
use App\Service\CertificationManager;
use App\Service\InvoiceManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class StudentController extends AbstractController
{
    #[Route('/student', name: 'student_dashboard')]
    public function index(OrderRepository $orderRepository): Response
    {
        $user = $this->getUser();
        // Fetch all orders made by this user
        $orders = $orderRepository->findBy(['user' => $user]);

        // Render the dashboard, passing order data
        return $this->render('student/dashboard.html.twig', [
            'orders' => $orders,
        ]);
    }

    #[Route('/student/courses', name: 'student_courses')]
    public function courses(OrderRepository $orderRepository): Response
    {
        $user   = $this->getUser();
        $orders = $orderRepository->findBy(['user' => $user]);

        // Collect unique cursus the user owns
        $accessible = [];
        foreach ($orders as $order) {
            if ($order->getCursus()) {
                $c = $order->getCursus();
            } elseif ($order->getLesson()) {
                $c = $order->getLesson()->getCursus();
            } else {
                continue;
            }
            $accessible[$c->getId()] = $c;
        }

        // Re-index array for Twig
        $cursusList = array_values($accessible);

        return $this->render('student/courses.html.twig', [
            'cursusList' => $cursusList,
        ]);
    }

    #[Route('/student/results', name: 'student_results')]
    public function results(): Response
    {
        return $this->render('student/results.html.twig');
    }

    #[Route('/student/certifications', name: 'student_certifications')]
    public function certifications(): Response
    {
        $user = $this->getUser();
        $certifications = $user->getCertifications();

        // Check if certificate file exists on disk
        foreach ($certifications as $certification) {
            $path = 'uploads/certifications/' . $certification->getFilename();
            $certification->hasFile = file_exists($path);
        }

        return $this->render('student/certifications.html.twig', [
            'certifications' => $certifications,
        ]);
    }

    #[Route('/student/profile', name: 'student_profile')]
    public function profile(
        Request $request,
        UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface $em
    ): Response {
        $user = $this->getUser();

        $form = $this->createForm(ProfileFormType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // If a new plain password was provided, hash and set it
            if ($plainPassword = $form->get('plainPassword')->getData()) {
                $user->setPassword(
                    $passwordHasher->hashPassword($user, $plainPassword)
                );
            }

            $em->flush();
            $this->addFlash('profile_success', 'Profil mis à jour avec succès.');

            return $this->redirectToRoute('student_profile');
        }

        return $this->render('student/profile.html.twig', [
            'form' => $form->createView(),
            'user' => $user,
        ]);
    }


    #[Route('/student/shop', name: 'student_shop')]
    public function shop(
        ThemeRepository $themeRepository,
        OrderRepository $orderRepository
    ): Response {
        $user   = $this->getUser();

        // Check if user profile is complete
        $profilComplet = $user->getFirstName() && $user->getLastName() && $user->getAddress();

        $orders = $orderRepository->findBy(['user' => $user]);

        // 1) Packs purchased
        $purchasedCursusIds = [];
        // 2) Individual lessons purchased
        $purchasedLessonIds = [];
        // 3) Identify the curricula for which at least one lesson has been purchased
        $purchasedFromLessonCursusIds = [];

        foreach ($orders as $order) {
            if ($c = $order->getCursus()) {
                $purchasedCursusIds[] = $c->getId();
            }
            if ($lesson = $order->getLesson()) {
                $purchasedLessonIds[] = $lesson->getId();
                // retrieve the curriculum ID linked to the lesson
                $purchasedFromLessonCursusIds[] = $lesson->getCursus()->getId();
            }
        }

        // Deduplication
        $purchasedCursusIds              = array_unique($purchasedCursusIds);
        $purchasedLessonIds              = array_unique($purchasedLessonIds);
        $purchasedFromLessonCursusIds    = array_unique($purchasedFromLessonCursusIds);

        $themes = $themeRepository->findAll();

        return $this->render('student/shop.html.twig', [
            'themes'                      => $themes,
            'purchasedCursusIds'          => $purchasedCursusIds,
            'purchasedLessonIds'          => $purchasedLessonIds,
            'purchasedFromLessonCursusIds' => $purchasedFromLessonCursusIds,
            'profilComplet'                => $profilComplet,
        ]);
    }


    #[Route('/student/invoice', name: 'student_invoice')]
    public function invoice(OrderRepository $orderRepository): Response
    {
        $user = $this->getUser();
        $orders = $orderRepository->findBy(['user' => $user]);

        return $this->render('student/invoice.html.twig', [
            'orders' => $orders,
        ]);
    }

    #[Route('/student/invoice/{id}/download', name: 'student_invoice_download')]
    public function downloadInvoice(Order $order, InvoiceManager $invoiceManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        // Security: check that the user is the owner of the command
        if ($order->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        // Build the expected file path
        $userId = $order->getUser()->getId();
        $orderId = $order->getId();
        $relativePath = "uploads/invoices/{$userId}/facture_{$orderId}.pdf";
        $absolutePath = $this->getParameter('kernel.project_dir') . '/public/' . $relativePath;

        // Check if invoice already exists
        if (!file_exists($absolutePath)) {
            $invoiceManager->generateInvoice($order);
        }

        // Redirects to PDF
        return $this->redirect("/" . $relativePath);
    }


    #[Route('/cursus/{id}/buy', name: 'cursus_buy')]
    public function buyCursus(Cursus $cursus, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();

        // Require profile completion before purchase
        if (!$user->getFirstName() || !$user->getLastName() || !$user->getAddress()) {
            $this->addFlash('error', 'Veuillez compléter votre profil (nom, prénom, adresse) avant de pouvoir effectuer un achat.');
            return $this->redirectToRoute('student_profile');
        }

        // Prevent duplicate purchases
        foreach ($user->getOrders() as $order) {
            if ($order->getCursus() === $cursus) {
                $this->addFlash('info', 'Vous avez déjà acheté ce cursus.');
                return $this->redirectToRoute('student_courses');
            }
        }

        // Create and persist new order
        $order = new Order();
        $order->setUser($user)
              ->setCursus($cursus)
              ->setTotal($cursus->getPrice())
              ->setStatus('Payé')
              ->setCreatedAt(new \DateTimeImmutable());
        $em->persist($order);
        $em->flush();

        $this->addFlash('success', 'Formation achetée avec succès !');
        return $this->redirectToRoute('student_courses');
    }

    #[Route('/lesson/{id}/buy', name: 'lesson_buy')]
    public function buyLesson(Lesson $lesson, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();

        // Require profile completion
        if (!$user->getFirstName() || !$user->getLastName() || !$user->getAddress()) {
            $this->addFlash('error', 'Veuillez compléter votre profil (nom, prénom, adresse) avant de pouvoir effectuer un achat.');
            return $this->redirectToRoute('student_profile');
        }

        // Prevent duplicate lesson purchases
        foreach ($user->getOrders() as $order) {
            if ($order->getLesson() === $lesson) {
                $this->addFlash('info', 'Vous avez déjà acheté cette leçon.');
                return $this->redirectToRoute('student_shop');
            }
        }

        // Create new lesson order
        $order = new Order();
        $order->setUser($user)
              ->setLesson($lesson)
              ->setTotal($lesson->getPrice())
              ->setStatus('Payé')
              ->setCreatedAt(new \DateTimeImmutable());
        $em->persist($order);
        $em->flush();

        $this->addFlash('success', 'Leçon achetée avec succès !');
        return $this->redirectToRoute('student_courses');
    }

    #[Route('/cursus/{id}/suivi', name: 'cursus_suivi')]
    public function suivreCursus(
        Cursus $cursus,
        LessonValidationRepository $lessonValidationRepository,
        OrderRepository $orderRepository
    ): Response {
        $user = $this->getUser();

        // 1) We recover ALL lessons and their IDs
        $allLessons    = $cursus->getLessons()->toArray();
        $allLessonIds  = array_map(fn ($lesson) => $lesson->getId(), $allLessons);

        // 2) See if the pack has been purchased
        $packOrder = $orderRepository->findOneBy([
            'user'   => $user,
            'cursus' => $cursus,
        ]);

        if (!$packOrder) {
            // 3) Otherwise, we check that each lesson has been purchased individually.
            $orders       = $orderRepository->findBy(['user' => $user]);
            $lessonOrders = [];
            foreach ($orders as $order) {
                if ($lesson = $order->getLesson()) {
                    if ($lesson->getCursus()->getId() === $cursus->getId()) {
                        $lessonOrders[] = $lesson->getId();
                    }
                }
            }
            $lessonOrders = array_unique($lessonOrders);
            sort($lessonOrders);
            sort($allLessonIds);

            if ($lessonOrders !== $allLessonIds) {
                throw $this->createAccessDeniedException("Vous n'avez pas acheté ce cursus.");
            }
        }

        // 4) Compute validation progress
        $validatedLessons = $lessonValidationRepository->findBy(['user' => $user]);
        $validatedIds     = array_map(fn ($v) => $v->getLesson()->getId(), $validatedLessons);

        $validatedCount   = count(array_intersect($validatedIds, $allLessonIds));
        $totalLessons     = count($allLessonIds);
        $progressPercent  = $totalLessons > 0
            ? ($validatedCount / $totalLessons) * 100
            : 0;

        return $this->render('student/cursus_suivi.html.twig', [
            'cursus'          => $cursus,
            'lessons'         => $allLessons,
            'validatedIds'    => $validatedIds,
            'validatedCount'  => $validatedCount,
            'totalLessons'    => $totalLessons,
            'progressPercent' => $progressPercent,
        ]);
    }

    #[Route('/lesson/{id}/validate', name: 'student_validate_lesson')]
    public function validateLesson(
        Lesson $lesson,
        LessonValidationRepository $lessonValidationRepository,
        EntityManagerInterface $em,
        CertificationManager $certificationManager
    ): Response {
        $user = $this->getUser();
        // Avoid duplicate validation records
        $alreadyValidated = $lessonValidationRepository->findOneBy(['user' => $user, 'lesson' => $lesson]);
        if (!$alreadyValidated) {
            $validation = new LessonValidation();
            $validation->setUser($user)->setLesson($lesson)->setValidatedAt(new \DateTimeImmutable());
            $em->persist($validation);
            $em->flush();
            $certificationManager->checkAndGrantCertification($user, $lesson);
            $this->addFlash('success', 'Leçon validée avec succès.');
        } else {
            $this->addFlash('info', 'Leçon déjà validée.');
        }
        
        // Redirect back to the cursus tracking page
        return $this->redirectToRoute('cursus_suivi', ['id' => $lesson->getCursus()->getId()]);
    }
}
