<?php

namespace App\Controller;

use App\Entity\Reservation;
use App\Service\Payment\PaymentFailedException;
use App\Service\PaymentService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route(path: '/payment', name: 'payment')]
#[IsGranted('ROLE_USER')]
final class PaymentController extends AbstractController
{
    public function __construct(private readonly string $stripePublicKey)
    {
    }

    /**
     * Affiche le formulaire de paiement Stripe.
     */
    #[Route(path: '/{id}', name: '_create')]
    public function index(Reservation $reservation): Response
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        if ($user !== $reservation->getUser()) {
            $this->addFlash('red', 'Réservation non référencée');

            return $this->redirectToRoute('travel_list');
        }

        return $this->render('payment/index.html.twig', [
            'publicKey' => $this->stripePublicKey,
            'amount' => $reservation->getPrice(),
            'reservation' => $reservation,
        ]);
    }

    /**
     * Valide ou refuse le paiement.
     */
    #[Route(path: '/verification/{id}', name: '_charge')]
    public function charge(Request $request, Reservation $reservation, PaymentService $paymentService): RedirectResponse
    {
        // Le contrôleur ne réalise pas la logique métier : il délègue au PaymentService
        // et se contente de convertir le résultat en réponse HTTP (skill §3 Controller).
        $stripeToken = (string) $request->request->get('stripeToken', '');

        try {
            $paymentService->process($reservation, $stripeToken);
        } catch (PaymentFailedException $e) {
            $this->addFlash('red', 'Le paiement a été refusé vous pouver effectuer une nouvelle tentative.');

            return $this->redirectToRoute('reservation_list');
        }

        $this->addFlash('green', 'Votre paiement a bien été effectué');

        return $this->redirectToRoute('reservation_list');
    }
}
