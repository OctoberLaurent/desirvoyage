<?php

namespace App\Tests\Service;

use App\Entity\Reservation;
use App\Entity\User;
use App\Service\MailerService;
use App\Service\Payment\ChargedPayment;
use App\Service\Payment\PaymentGatewayInterface;
use App\Service\PaymentService;
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
        $gateway->expects(self::once())
            ->method('charge')
            ->with(12000, 'eur', 'commande ABC-123-456', 'tok_123')
            ->willReturn(new ChargedPayment('ch_abc', 12000));

        $em = $this->createMock(EntityManagerInterface::class);
        $em->expects(self::exactly(2))->method('persist');
        $em->expects(self::once())->method('flush');

        // MailerService est final : on utilise une vraie instance avec un
        // MailerInterface mocké et on vérifie que send() est appelée.
        $mailer = $this->createMock(MailerInterface::class);
        $mailer->expects(self::once())->method('send')->with(self::isInstanceOf(Email::class));
        $mailerService = new MailerService($this->createMock(UrlGeneratorInterface::class), $mailer);

        $service = new PaymentService($gateway, $em, $mailerService);

        $payment = $service->process($reservation, 'tok_123');

        self::assertSame('ch_abc', $payment->getPaymentId());
        self::assertSame('Stripe', $payment->getType());
        self::assertSame(120.0, $payment->getAmount());
        self::assertSame($payment, $reservation->getPayment());
    }

    private function buildReservation(float $price): Reservation
    {
        $reservation = new Reservation();
        $reservation->setPrice($price);
        $reservation->setSerial('ABC-123-456');

        $user = new User();
        $user->setEmail('user@example.com');
        $user->setPassword('x');
        $reservation->setUser($user);

        return $reservation;
    }
}
