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
 * Orchestrates the "confirm payment" use case: charges through the gateway,
 * creates the Payment entity, attaches the reservation, persists it, and
 * notifies the user (skill §3 Service, §8 Doctrine "transaction in service").
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
     * @param string $stripeToken token returned by Stripe on the client side
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

        // The network call does not occur under a database lock: Stripe is idempotent
        // through the reservation serial used as a key. The second transaction
        // reconciles state if a concurrent request already finalized the payment.
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
