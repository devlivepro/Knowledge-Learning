<?php

namespace App\Controller;

use App\Entity\Lesson;
use App\Entity\LessonValidation;
use App\Repository\OrderRepository;
use App\Repository\LessonValidationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use App\Service\CertificationManager;

#[Route('/student')]
#[IsGranted('ROLE_USER')]
class StudentResultsController extends AbstractController
{
    #[Route('/results', name: 'student_results')]
    public function index(
        OrderRepository $orderRepository,
        LessonValidationRepository $lessonValidationRepository
    ): Response {
        $user = $this->getUser();

        // Retrieve all purchased lessons
        $orders = $orderRepository->findBy(['user' => $user]);
        $lessons = [];

        foreach ($orders as $order) {
            if ($order->getLesson()) {
                $lessons[] = $order->getLesson();
            } elseif ($order->getCursus()) {
                foreach ($order->getCursus()->getLessons() as $lesson) {
                    $lessons[] = $lesson;
                }
            }
        }

        // Retrieve validated lessons
        $validations = $lessonValidationRepository->findBy(['user' => $user]);
        $validatedIds = array_map(fn ($val) => $val->getLesson()->getId(), $validations);

        return $this->render('student/results.html.twig', [
            'lessons' => $lessons,
            'validatedIds' => $validatedIds,
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

        $alreadyValidated = $lessonValidationRepository->findOneBy([
            'user' => $user,
            'lesson' => $lesson
        ]);

        if (!$alreadyValidated) {
            $validation = new LessonValidation();
            $validation->setUser($user);
            $validation->setLesson($lesson);
            $validation->setValidatedAt(new \DateTimeImmutable());

            $em->persist($validation);
            $em->flush();

            // Automatic call to the certification service
            $certificationManager->checkAndGrantCertification($user, $lesson);

            $this->addFlash('success', 'Leçon validée avec succès !');
        } else {
            $this->addFlash('info', 'Leçon déjà validée.');
        }

        return $this->redirectToRoute('student_results');
    }
}
