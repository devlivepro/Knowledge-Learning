<?php

namespace App\Controller\admin;

use App\Entity\Order;
use App\Service\InvoiceManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/invoice')]
#[IsGranted('ROLE_ADMIN')]
class AdminInvoiceController extends AbstractController
{
    #[Route('/{id}/download', name: 'admin_invoice_download')]
    public function download(Order $order, InvoiceManager $invoiceManager): Response
    {
        $userId = $order->getUser()->getId();
        $orderId = $order->getId();
        $relativePath = "uploads/invoices/{$userId}/facture_{$orderId}.pdf";
        $absolutePath = $this->getParameter('kernel.project_dir') . '/public/' . $relativePath;

        if (!file_exists($absolutePath)) {
            $invoiceManager->generateInvoice($order);
        }

        return $this->redirect('/' . $relativePath);
    }
}