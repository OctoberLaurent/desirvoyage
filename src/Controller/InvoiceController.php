<?php

namespace App\Controller;

use App\Entity\Reservation;
use App\Service\InvoicePdfGenerator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * Generate invoice in html.
 */
#[Route(path: '/invoice', name: 'invoice')]
#[IsGranted('ROLE_USER')]
final class InvoiceController extends AbstractController
{
    #[Route(path: '/{id}', name: '_html')]
    public function invoiceHtml(Reservation $reservation): Response
    {
        return $this->render('invoice/index.html.twig', [
            'reservation' => $reservation,
            'root' => $this->getParameter('kernel.project_dir'),
            'document' => 'html',
        ]);
    }

    /**
     * Generate invoice in PDF.
     */
    #[Route(path: 'pdf/{id}', name: '_pdf')]
    public function invoicePdf(Reservation $reservation, InvoicePdfGenerator $pdfGenerator): Response
    {
        return new Response($pdfGenerator->generate($reservation), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="Invoice '.$reservation->getSerial().'.pdf"',
        ]);
    }
}
