<?php

namespace App\Service;

use App\Entity\Payment;
use App\Entity\Reservation;
use App\Service\Payment\ChargedPayment;
use App\Service\Payment\PaymentFailedException;
use App\Service\Payment\PaymentGatewayInterface;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Orchestre le cas d'utilisation « confirmer un paiement » :
 * débite via le gateway, crée l'entité Payment, lie la réservation, persiste
 * et notifie l'utilisateur (skill §3 Service, §8 Doctrine « transaction dans le service »).
 */
final class PaymentService
{
    public function __construct(
        private readonly PaymentGatewayInterface $gateway,
        private readonly EntityManagerInterface $entityManager,
        private readonly MailerService $mailer,
    ) {
    }

    /**
     * @param string $stripeToken token renvoyé par Stripe côté client
     *
     * @throws PaymentFailedException
     */
    public function process(Reservation $reservation, string $stripeToken): Payment
    {
        $amountCents = (int) round($reservation->getPrice() * 100.0);
        $charged = $this->gateway->charge(
            $amountCents,
            'eur',
            'commande '.$reservation->getSerial(),
            $stripeToken,
        );

        $payment = $this->createPayment($charged);
        $reservation->setPayment($payment);

        $this->entityManager->persist($payment);
        $this->entityManager->persist($reservation);
        $this->entityManager->flush();

        $user = $reservation->getUser();
        $this->mailer->sendConfirmedPaimenent($user->getEmail());

        return $payment;
    }

    private function createPayment(ChargedPayment $charged): Payment
    {
        $payment = new Payment();
        $payment->setPayAt(new \DateTime());
        $payment->setType('Stripe');
        $payment->setPaymentId($charged->id);
        $payment->setAmount($charged->amountCents / 100);

        return $payment;
    }
}
