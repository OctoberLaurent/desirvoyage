<?php

namespace App\Service;

use App\Entity\Reservation;
use Dompdf\Dompdf;
use Dompdf\Options;
use Twig\Environment;

/**
 * Génère le PDF d'une facture (skill §3 Service — séparation de la génération
 * HTML (Twig) et du rendu PDF (Dompdf), hors du contrôleur).
 */
final class InvoicePdfGenerator
{
    public function __construct(
        private readonly Environment $twig,
        private readonly string $projectDir,
    ) {
    }

    /**
     * Renvoie le contenu binaire du PDF de la facture pour la réservation.
     */
    public function generate(Reservation $reservation): string
    {
        $pdfOptions = new Options();
        $pdfOptions->setIsRemoteEnabled(true);
        $pdfOptions->set('defaultFont', 'Arial');

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
