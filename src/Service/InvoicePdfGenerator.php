<?php

namespace App\Service;

use App\Entity\Reservation;
use Dompdf\Dompdf;
use Dompdf\Options;
use Twig\Environment;

/**
 * Generates an invoice PDF (skill §3 Service — separating HTML generation
 * through Twig from PDF rendering through Dompdf, outside the controller).
 */
final readonly class InvoicePdfGenerator
{
    public function __construct(
        private Environment $twig,
        private string $projectDir,
    ) {
    }

    /**
     * Returns the binary invoice PDF content for the reservation.
     */
    public function generate(Reservation $reservation): string
    {
        $pdfOptions = new Options();
        $pdfOptions->setIsRemoteEnabled(true);
        $pdfOptions->set('defaultFont', 'Arial');
        // Allows Dompdf to load local project files (logo and images).
        $pdfOptions->set('chroot', $this->projectDir);

        $dompdf = new Dompdf($pdfOptions);
        $dompdf->setOptions($pdfOptions);

        $html = $this->twig->render('invoice/index.html.twig', [
            'reservation' => $reservation,
            'root' => $this->projectDir,
            'document' => 'pdf',
        ]);

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return $dompdf->output();
    }
}
