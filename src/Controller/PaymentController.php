<?php

namespace App\Controller;

use App\Entity\Payment;
use App\Entity\Reservation;
use App\Service\MailerService;
use Doctrine\ORM\EntityManagerInterface;
use Stripe\Charge;
use Stripe\Stripe;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route(path: '/payment', name: 'payment')]
#[IsGranted('ROLE_USER')]
class PaymentController extends AbstractController
{
    private string $publicKey;
    private string $privateKey;

    public function __construct()
    {
        $publicKey = $_ENV['STRIPE_PUBLIC_KEY'] ?? '';
        $this->publicKey = is_string($publicKey) ? $publicKey : '';
        $privateKey = $_ENV['STRIPE_PRIVATE_KEY'] ?? '';
        $this->privateKey = is_string($privateKey) ? $privateKey : '';
    }

    /**
     * Create a payment with stripe.
     */
    #[Route(path: '/{id}', name: '_create')]
    public function index(Reservation $reservation): Response
    {
        $user = $this->getUser();
        $userReservation = $reservation->getUser();

        if ($user !== $userReservation) {
            $this->addFlash(
                'red',
                'Réservation non référencée'
            );

            return $this->redirectToRoute('travel_list');
        }

        $amount = $reservation->getPrice();

        return $this->render('payment/index.html.twig', [
            'publicKey' => $this->publicKey,
            'amount' => $amount,
            'reservation' => $reservation,
        ]);
    }

    /**
     * Validates or refuses payment.
     */
    #[Route(path: '/verification/{id}', name: '_charge')]
    public function charge(Request $request, Reservation $reservation, MailerService $mailerService, EntityManagerInterface $em): RedirectResponse
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();
        $amount = $reservation->getPrice();
        Stripe::setApiKey($this->privateKey);
        try {
            $charge = Charge::create([
                'amount' => (int) round($amount * 100.0),
                'currency' => 'eur',
                'description' => 'commande '.$reservation->getSerial(),
                'source' => (string) $request->request->get('stripeToken'),
            ]);
        } catch (\Exception $e) {
            $this->addFlash('red', 'Le paiement a été refusé vous pouver effectuer une nouvelle tentative.');

            // Go back to the reservations to redo the payment
            return $this->redirectToRoute('reservation_list');
        }

        // Create a new payment if validated.
        $payment = new Payment();
        $payment->setPayAt(new \DateTime());
        $payment->setType('Stripe');
        $payment->setPaymentId($charge->id);
        $payment->setAmount($charge->amount / 100);

        $em->persist($payment);
        $reservation->setPayment($payment);
        $em->persist($reservation);
        $em->flush();

        $mailerService->sendConfirmedPaimenent($user->getEmail());

        $this->addFlash('green', 'Votre paiement a bien été effectué');

        return $this->redirectToRoute('reservation_list');
    }
}
