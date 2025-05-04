<?php

namespace App\Controller;

use App\Entity\Lesson;
use App\Repository\LessonValidationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/student')]
#[IsGranted('ROLE_USER')]
class StudentLessonController extends AbstractController
{
    #[Route('/lesson/{id}', name: 'student_lesson')]
    public function show(Lesson $lesson, LessonValidationRepository $validationRepo): Response
    {
        $user = $this->getUser();

        // Checks whether the lesson has been validated by the user
        $validation = $validationRepo->findOneBy([
            'user' => $user,
            'lesson' => $lesson,
        ]);

        $isValidated = $validation !== null;

        return $this->render('student/lesson.html.twig', [
            'lesson' => $lesson,
            'isValidated' => $isValidated,
        ]);
    }
}
