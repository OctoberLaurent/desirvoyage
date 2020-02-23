<?php

namespace App\Controller;

use App\Entity\Reservation;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Routing\Annotation\Route;
use Dompdf\Dompdf;
use Dompdf\Options;

/**
 * Generate invoice in html
 * 
 * @IsGranted("ROLE_USER")
 * 
 * @Route("/invoice", name="invoice")
 */
class InvoiceController extends AbstractController
{
	/**
	 * @Route("/{id}", name="_html")
	 */
	public function InvoiceHtml(Reservation $reservation)
	{
		$projectRoot = $this->getParameter('kernel.project_dir');
		return $this->render('invoice/index.html.twig', [
			'reservation' => $reservation,
			'root' => $projectRoot,
			"document" => "html"
		]);
	}

	/**
	 * Genrate invoice in PDF
	 * 
	 * @Route("pdf/{id}", name="_pdf")
	 */
	public function InvoicePdf(Reservation $reservation)
	{
		$projectRoot = $this->getParameter('kernel.project_dir');
		//Get reservation serial
		$serial = $reservation->getSerial();
		// Configure Dompdf according to your needs
		$pdfOptions = new Options();
		$pdfOptions->setIsRemoteEnabled(true);

		$pdfOptions->set('defaultFont', 'Arial');

		// Instantiate Dompdf with our options
		$dompdf = new Dompdf($pdfOptions);
		$dompdf->setOptions($pdfOptions);
		// Retrieve the HTML generated in our twig file
		$html = $this->renderView('invoice/index.html.twig', [
			'reservation' => $reservation,
			"root" => $projectRoot,
			"document" => "pdf"
		]);

		// Load HTML to Dompdf
		$dompdf->loadHtml($html);

		// (Optional) Setup the paper size and orientation 'portrait' or 'portrait'
		$dompdf->setPaper('A4', 'portrait');

		// Render the HTML as PDF
		$dompdf->render();

		// Output the generated PDF to Browser (inline view)
		$dompdf->stream('Invoice ' . $serial . '.pdf', [
			"Attachment" => false,
		]);
	}
}

