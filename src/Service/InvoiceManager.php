<?php

namespace App\Service;

use App\Entity\Order;
use FPDF;
use Symfony\Component\Filesystem\Filesystem;

class InvoiceManager
{
    private string $invoiceDirectory;

    public function __construct(string $projectDir)
    {
        $this->invoiceDirectory = $projectDir . '/public/uploads/invoices';
    }

    public function generateInvoice(Order $order): string
    {
        $user = $order->getUser();
        $userId = $user->getId();
        $orderId = $order->getId();

        // Create folder if necessary
        $userDir = $this->invoiceDirectory . '/' . $userId;
        (new Filesystem())->mkdir($userDir, 0777);

        $filePath = "$userDir/facture_$orderId.pdf";

        $pdf = new FPDF();
        $pdf->AddPage();

        // Centered logo
        $pdf->Image('img/logo.jpeg', 80, 10, 50);
        $pdf->Ln(40);

        // Title
        $pdf->SetFont('Helvetica', 'B', 16);
        $pdf->Cell(0, 10, $this->toLatin1('Facture'), 0, 1, 'C');
        $pdf->Ln(10);

        // User data
        $firstName = $user->getFirstName() ?: 'Non renseigné';
        $lastName  = $user->getLastName() ?: 'Non renseigné';
        $address   = $user->getAddress() ?: 'Non renseignée';
        $amount = number_format($order->getTotal(), 2, ',', ' ') . ' ' . chr(128);

        // Determining the training title
        if ($order->getCursus()) {
            $formationTitle = $order->getCursus()->getTitle();
        } elseif ($order->getLesson()) {
            $formationTitle = 'Leçon : ' . $order->getLesson()->getTitle();
        } else {
            $formationTitle = '—';
        }

        // Body
        $pdf->SetFont('Helvetica', '', 12);
        $pdf->Cell(0, 10, $this->toLatin1("Date d'achat : ") . $order->getCreatedAt()->format('d/m/Y'), 0, 1, 'C');
        $pdf->Cell(0, 10, $this->toLatin1('Client : ') . $this->toLatin1("$firstName $lastName"), 0, 1, 'C');
        $pdf->Cell(0, 10, $this->toLatin1('Adresse : ') . $this->toLatin1($address), 0, 1, 'C');
        $pdf->Cell(0, 10, $this->toLatin1('Formation : ') . $this->toLatin1($formationTitle), 0, 1, 'C');
        $pdf->Cell(0, 10, $this->toLatin1('Montant : ') . $amount, 0, 1, 'C');
        $pdf->Cell(0, 10, $this->toLatin1('Statut : ') . $this->toLatin1($order->getStatus()), 0, 1, 'C');

        $pdf->Output('F', $filePath);

        return "uploads/invoices/$userId/facture_$orderId.pdf";
    }

    private function toLatin1(string $text): string
    {
        return iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $text);
    }
}