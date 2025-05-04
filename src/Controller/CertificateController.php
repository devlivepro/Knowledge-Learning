<?php

namespace App\Controller;

use App\Entity\Certification;
use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CertificateController extends AbstractController
{
    #[Route('/student/certificate/{id}/download', name: 'student_download_certificate')]
    public function download(Certification $certification): Response
    {
        $user = $this->getUser();

        // Security: prevent downloading a certificate that does not belong to the owner
        if ($certification->getUser() !== $user) {
            throw $this->createAccessDeniedException("Ce certificat ne vous appartient pas.");
        }

        // Generate HTML via a Twig template
        $html = $this->renderView('pdf/certificate.html.twig', [
            'certification' => $certification
        ]);

        // Configure Dompdf
        $options = new Options();
        $options->set('defaultFont', 'Helvetica');
        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();

        return new Response(
            $dompdf->output(),
            200,
            [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="certificat.pdf"',
            ]
        );
    }
}