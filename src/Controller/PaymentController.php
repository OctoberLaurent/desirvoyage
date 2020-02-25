<?php

namespace App\Controller;

use DateTime;
use Stripe\Charge;
use Stripe\Stripe;
use App\Entity\Payment;
use App\Entity\Reservation;
use App\Service\MailerService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 *
 * @Route("/payment", name="payment")
 */
class PaymentController extends AbstractController
{

	private $publicKey;
	private $privateKey;

	public function __construct()
	{

		$this->publicKey = $_ENV['STRIPE_PUBLIC_KEY'];
		$this->privateKey = $_ENV['STRIPE_PRIVATE_KEY'];

	}

	/**
	 *
	 * Create a payment with stripe
	 *
	 * @Route("/{id}", name="_create")
	 *
	 * @IsGranted("ROLE_USER")
	 */
	public function index(Reservation $reservation)
	{
		$user = $this->getUser();
		$userReservation = $reservation->getUser();

		if ($user != $userReservation) {

			$this->addFlash(
				'red',
				"Réservation non référencée"
			);

			return $this->redirectToRoute("travel_list");

		}

		$amount = $reservation->getPrice();

		return $this->render('payment/index.html.twig', [
			'publicKey' => $this->publicKey,
			'privateKey' => $this->privateKey,
			'amount' => $amount,
			'reservation' => $reservation,
		]);
	}

	/**
	 * Validates or refuses payment
	 *
	 * @Route("/verification/{id}", name="_charge")
	 *
	 * @IsGranted("ROLE_USER")
	 *
	 * @param Request $request
	 */
	public function charge(Request $request, Reservation $reservation, MailerService $mailerService)
	{
		$user = $this->getUser();
		$amount = $reservation->getPrice();
		\Stripe\Stripe::setApiKey($this->privateKey);
		try {
			$charge = \Stripe\Charge::create([
				'amount' => $amount * 100,
				'currency' => 'eur',
				'description' => 'commande ' . $reservation->getSerial(),
				'source' => $request->request->get('stripeToken'),
			]);
		} catch (\Exception $e) {

			$this->addFlash('red', "Le paiement a été refusé vous pouver effectuer une nouvelle tentative.");

			// Go back to the reservations to redo the payment
			return $this->redirectToRoute('reservation_list');
		}

		// Create a new paiemement if valided.
		$payment = new Payment();
		$payment->setPayAt(new \DateTime());
		$payment->setType('Stripe');
		$payment->setPaymentId($charge->id);
		$payment->setAmount($charge->amount / 100);

		$em = $this->getDoctrine()->getManager();

		$em->persist($payment);
		$reservation->setPayment($payment);
		$em->persist($reservation);
		$em->flush();

		$mailerService->sendConfirmedPaimenent($user->getEmail());

		$this->addFlash('green', "Votre paiement a bien été effectué");

		return $this->redirectToRoute('reservation_list');
	}
}
