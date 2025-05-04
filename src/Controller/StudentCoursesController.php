<?php

namespace App\Controller;

use App\Repository\OrderRepository;
use App\Repository\LessonValidationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/student')]
#[IsGranted('ROLE_USER')]
class StudentCoursesController extends AbstractController
{
    #[Route('/courses', name: 'student_courses')]
    public function index(
        OrderRepository $orderRepository,
        LessonValidationRepository $validationRepository
    ): Response {
        $user = $this->getUser();

        // 1) Retrieves all user commands
        $orders = $orderRepository->findBy(['user' => $user]);

        // 2) Loads all validations made by the user
        $allValidations = $validationRepository->findBy(['user' => $user]);

        // 3) Builds the percentage table
        $progress = [];
        foreach ($orders as $order) {
            // only processes pack orders
            if (!$c = $order->getCursus()) {
                continue;
            }

            $totalLessons = count($c->getLessons());

            // count lessons validated for this course
            $validatedCount = 0;
            foreach ($allValidations as $val) {
                if ($val->getLesson()->getCursus()->getId() === $c->getId()) {
                    $validatedCount++;
                }
            }

            $pct = $totalLessons > 0
                ? (int) round($validatedCount / $totalLessons * 100)
                : 0;

            $progress[$c->getId()] = $pct;
        }

        // 4) Pass orders and progress to view
        return $this->render('student/courses.html.twig', [
            'orders'   => $orders,
            'progress' => $progress,
        ]);
    }
}
