<?php

namespace App\Controller;

use App\Entity\Reservation;
use App\Security\ReservationVoter;
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
     * Displays the Stripe payment form.
     */
    #[Route(path: '/{id}', name: '_create', methods: ['GET'])]
    #[IsGranted(ReservationVoter::VIEW, subject: 'reservation')]
    public function index(Reservation $reservation): Response
    {
        return $this->render('payment/index.html.twig', [
            'publicKey' => $this->stripePublicKey,
            'amount' => $reservation->getPrice(),
            'reservation' => $reservation,
        ]);
    }

    /**
     * Accepts or rejects the payment.
     */
    #[Route(path: '/verification/{id}', name: '_charge', methods: ['POST'])]
    #[IsGranted(ReservationVoter::PAY, subject: 'reservation')]
    public function charge(Request $request, Reservation $reservation, PaymentService $paymentService): RedirectResponse
    {
        // The controller delegates business logic to PaymentService and only converts
        // its result into an HTTP response (skill §3 Controller).
        $csrfTokenId = sprintf('payment-%d', $reservation->getId() ?? 0);
        if (!$this->isCsrfTokenValid($csrfTokenId, (string) $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Jeton CSRF invalide.');
        }

        $stripeToken = (string) $request->request->get('stripeToken', '');

        try {
            /** @var \App\Entity\User $buyer */
            $buyer = $this->getUser();
            $paymentService->process($reservation, $buyer, $stripeToken);
        } catch (PaymentFailedException|\DomainException) {
            $this->addFlash('red', 'Le paiement a été refusé vous pouver effectuer une nouvelle tentative.');

            return $this->redirectToRoute('reservation_list');
        }

        $this->addFlash('green', 'Votre paiement a bien été effectué');

        return $this->redirectToRoute('reservation_list');
    }
}
