<?php

namespace App\Tests\Service;

use App\Entity\Reservation;
use App\Entity\User;
use App\Enum\ReservationStatus;
use App\Service\MailerService;
use App\Service\Payment\ChargedPayment;
use App\Service\Payment\PaymentGatewayInterface;
use App\Service\PaymentService;
use App\ValueObject\Email as EmailAddress;
use Doctrine\DBAL\LockMode;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class PaymentServiceTest extends TestCase
{
    public function testProcessCreatesPaymentLinkedToReservationAndSendsMail(): void
    {
        $reservation = $this->buildReservation(120.0);

        $gateway = $this->createMock(PaymentGatewayInterface::class);
        $transactionState = new \stdClass();
        $transactionState->active = false;
        $gateway->expects(self::once())
            ->method('charge')
            ->with(12000, 'eur', 'commande ABC-123-456', 'tok_123', 'ABC-123-456')
            ->willReturnCallback(static function () use ($transactionState): ChargedPayment {
                self::assertFalse($transactionState->active, 'L’appel Stripe ne doit pas se produire pendant une transaction Doctrine.');

                return new ChargedPayment('ch_abc', 12000);
            });

        $em = $this->createMock(EntityManagerInterface::class);
        $em->expects(self::exactly(2))->method('persist');
        $em->expects(self::never())->method('flush');
        $em->expects(self::exactly(2))->method('refresh')->with($reservation, LockMode::PESSIMISTIC_WRITE);
        $em->expects(self::exactly(2))->method('wrapInTransaction')
            ->willReturnCallback(static function (callable $callback) use ($em, $transactionState): mixed {
                $transactionState->active = true;
                try {
                    return $callback($em);
                } finally {
                    $transactionState->active = false;
                }
            });

        // MailerService est final : on utilise une vraie instance avec un
        // MailerInterface mocké et on vérifie que send() est appelée.
        $mailer = $this->createMock(MailerInterface::class);
        $mailer->expects(self::once())->method('send')->with(self::isInstanceOf(Email::class));
        $mailerService = new MailerService(self::createStub(UrlGeneratorInterface::class), $mailer);

        $service = new PaymentService($gateway, $em, $mailerService);

        $service->process($reservation, $reservation->getUser(), 'tok_123');
        $payment = $reservation->getPayment();

        self::assertInstanceOf(\App\Entity\Payment::class, $payment);
        self::assertSame('ch_abc', $payment->getPaymentId());
        self::assertSame('Stripe', $payment->getType());
        self::assertSame(12000, $payment->getAmount()?->cents());
        self::assertSame($payment, $reservation->getPayment());
        self::assertSame(ReservationStatus::Paid, $reservation->getStatus());
    }

    public function testProcessIsIdempotentForAlreadyPaidReservation(): void
    {
        $reservation = $this->buildReservation(120.0);
        $existingPayment = new \App\Entity\Payment();
        $reservation->markAsPaid($existingPayment);

        $gateway = $this->createMock(PaymentGatewayInterface::class);
        $gateway->expects(self::never())->method('charge');
        $em = self::createStub(EntityManagerInterface::class);
        $mailer = $this->createMock(MailerInterface::class);
        $mailer->expects(self::never())->method('send');

        $service = new PaymentService(
            $gateway,
            $em,
            new MailerService(self::createStub(UrlGeneratorInterface::class), $mailer),
        );

        $service->process($reservation, $reservation->getUser(), 'tok_repeat');

        self::assertSame($existingPayment, $reservation->getPayment());
    }

    public function testProcessRefusesAnotherUserAndExpiredReservationBeforeCharging(): void
    {
        $reservation = $this->buildReservation(120.0);
        $gateway = $this->createMock(PaymentGatewayInterface::class);
        $gateway->expects(self::never())->method('charge');
        $service = new PaymentService(
            $gateway,
            self::createStub(EntityManagerInterface::class),
            new MailerService(self::createStub(UrlGeneratorInterface::class), self::createStub(MailerInterface::class)),
        );

        try {
            $service->process($reservation, new User(), 'tok_forbidden');
            self::fail('Un autre utilisateur ne doit pas pouvoir payer la réservation.');
        } catch (\DomainException) {
            self::assertSame(ReservationStatus::Pending, $reservation->getStatus());
        }

        $reservation->expire();

        $this->expectException(\DomainException::class);
        $service->process($reservation, $reservation->getUser(), 'tok_expired');
    }

    private function buildReservation(float $price): Reservation
    {
        $reservation = new Reservation();
        $id = new \ReflectionProperty($reservation, 'id');
        $id->setValue($reservation, 42);
        $reservation->setPrice($price);
        $reservation->setSerial('ABC-123-456');

        $user = new User();
        $user->setEmail(new EmailAddress('user@example.com'));
        $user->setPassword('x');
        $reservation->setUser($user);

        return $reservation;
    }
}
