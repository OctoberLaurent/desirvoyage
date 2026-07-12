<?php

namespace App\Service;

use App\Entity\Payment;
use App\Entity\Reservation;
use App\Entity\User;
use App\Enum\ReservationStatus;
use App\Service\Payment\ChargedPayment;
use App\Service\Payment\PaymentFailedException;
use App\Service\Payment\PaymentGatewayInterface;
use App\ValueObject\Money;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Orchestre le cas d'utilisation « confirmer un paiement » :
 * débite via le gateway, crée l'entité Payment, lie la réservation, persiste
 * et notifie l'utilisateur (skill §3 Service, §8 Doctrine « transaction dans le service »).
 */
final readonly class PaymentService
{
    public function __construct(
        private PaymentGatewayInterface $gateway,
        private EntityManagerInterface $entityManager,
        private MailerService $mailer,
    ) {
    }

    /**
     * @param string $stripeToken token renvoyé par Stripe côté client
     *
     * @throws PaymentFailedException
     */
    public function process(Reservation $reservation, User $buyer, string $stripeToken): void
    {
        if (!$reservation->belongsTo($buyer)) {
            throw new \DomainException('Cette réservation appartient à un autre utilisateur.');
        }
        $existingPayment = $reservation->getPayment();
        if (ReservationStatus::Paid === $reservation->getStatus() && $existingPayment instanceof Payment) {
            return;
        }
        if (ReservationStatus::Pending !== $reservation->getStatus()) {
            throw new \DomainException('Cette réservation ne peut plus être payée.');
        }

        $existingPayment = $this->entityManager->wrapInTransaction(
            fn (): ?Payment => $this->lockAndFindExistingPayment($reservation),
        );
        if ($existingPayment instanceof Payment) {
            return;
        }

        // L'appel réseau ne se produit pas sous verrou DB : Stripe est idempotent
        // grâce au serial de réservation passé comme clé. La seconde transaction
        // réconcilie l'état si une requête concurrente a déjà finalisé le paiement.
        $charged = $this->gateway->charge(
            $reservation->getPrice()->cents(),
            'eur',
            'commande '.$reservation->getSerial(),
            $stripeToken,
            $reservation->getSerial(),
        );

        $result = $this->entityManager->wrapInTransaction(function () use ($reservation, $charged): array {
            $existingPayment = $this->lockAndFindExistingPayment($reservation);
            if ($existingPayment instanceof Payment) {
                return ['payment' => $existingPayment, 'created' => false];
            }

            $payment = $this->createPayment($charged);
            $reservation->markAsPaid($payment);
            $this->entityManager->persist($payment);
            $this->entityManager->persist($reservation);

            return ['payment' => $payment, 'created' => true];
        });

        if ($result['created']) {
            $this->mailer->sendConfirmedPayment($buyer->getEmail()->value());
        }
    }

    private function createPayment(ChargedPayment $charged): Payment
    {
        $payment = new Payment();
        $payment->setPayAt(new \DateTime());
        $payment->setType('Stripe');
        $payment->setPaymentId($charged->id);
        $payment->setAmount(Money::fromCents($charged->amountCents));

        return $payment;
    }

    private function lockAndFindExistingPayment(Reservation $reservation): ?Payment
    {
        if (null !== $reservation->getId()) {
            $this->entityManager->refresh($reservation, \Doctrine\DBAL\LockMode::PESSIMISTIC_WRITE);
        }

        $existingPayment = $reservation->getPayment();
        if (ReservationStatus::Paid === $reservation->getStatus() && $existingPayment instanceof Payment) {
            return $existingPayment;
        }

        if (ReservationStatus::Pending !== $reservation->getStatus()) {
            throw new \DomainException('Cette réservation ne peut plus être payée.');
        }

        return null;
    }
}
