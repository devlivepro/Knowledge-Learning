<?php

namespace App\Service;

use App\Entity\Certification;
use App\Entity\Lesson;
use App\Entity\User;
use App\Repository\LessonValidationRepository;
use Doctrine\ORM\EntityManagerInterface;
use FPDF;

class CertificationManager
{
    private LessonValidationRepository $validationRepository;
    private EntityManagerInterface $em;

    public function __construct(LessonValidationRepository $validationRepository, EntityManagerInterface $em)
    {
        $this->validationRepository = $validationRepository;
        $this->em = $em;
    }

    public function checkAndGrantCertification(User $user, Lesson $lesson): void
    {
        $cursus = $lesson->getCursus();
        if (!$cursus) {
            return;
        }

        // Checks that all lessons in the curriculum have been validated by the user
        $allLessons = $cursus->getLessons();
        $validatedLessons = $this->validationRepository->findBy(['user' => $user]);
        $validatedIds = array_map(fn ($v) => $v->getLesson()->getId(), $validatedLessons);

        foreach ($allLessons as $l) {
            if (!in_array($l->getId(), $validatedIds, true)) {
                return;
            }
        }

        // Check if he already has a certificate for this course
        foreach ($user->getCertifications() as $cert) {
            if ($cert->getCursus() === $cursus) {
                return;
            }
        }

        // File name generation
        $filename = 'certificat_' . $user->getId() . '_' . $cursus->getId() . '.pdf';

        // Entity creation and registration
        $certification = new Certification();
        $certification->setUser($user);
        $certification->setCursus($cursus);
        $certification->setObtainedAt(new \DateTimeImmutable());
        $certification->setFilename($filename);
        $this->em->persist($certification);
        $this->em->flush();

        // Generate folder if none exists
        $directory = 'uploads/certifications';
        if (!is_dir($directory)) {
            @mkdir($directory, 0775, true);
        }

        // PDF file creation
        $pdf = new FPDF();
        $pdf->AddPage();

        // Centered logo
        $pdf->Image('img/logo.jpeg', 80, 10, 50);
        $pdf->Ln(40);

        // Title
        $pdf->SetFont('Helvetica', 'B', 16);
        $pdf->Cell(0, 10, iconv('UTF-8', 'ISO-8859-1//TRANSLIT', 'CERTIFICAT DE FORMATION'), 0, 1, 'C');
        $pdf->Ln(10);

        // User info
        $fullName = trim(($user->getFirstName() ?: 'Non renseigné') . ' ' . ($user->getLastName() ?: ''));
        $address = $user->getAddress() ?: 'Non renseignée';

        // Body text
        $pdf->SetFont('Helvetica', '', 12);

        $firstName = $user->getFirstName() ?: 'Non renseigné';
        $lastName  = $user->getLastName() ?: 'Non renseigné';
        $address   = $user->getAddress() ?: 'Non renseignée';
        $fullName  = trim("$firstName $lastName");

        $message = "Ce certificat est décerné à :\n\n" .
                   $fullName . "\n\n" .
                   "Adresse :\n" . $address . "\n\n" .
                   "Pour avoir complété la formation :\n\n" .
                   $cursus->getTitle();

        $pdf->MultiCell(0, 10, iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $message), 0, 'C');


        // Date
        $pdf->Ln(30);
        $pdf->SetFont('Helvetica', 'I', 10);
        $pdf->Cell(0, 10, iconv('UTF-8', 'ISO-8859-1//TRANSLIT', "Délivré le " . (new \DateTime())->format('d/m/Y')), 0, 1, 'R');

        // Saving the file
        $pdf->Output('F', "$directory/$filename");
    }
}
