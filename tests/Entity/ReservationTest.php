<?php

namespace App\Tests\Entity;

use App\Entity\Payment;
use App\Entity\Reservation;
use PHPUnit\Framework\TestCase;

/**
 * @covers \App\Entity\Reservation
 */
final class ReservationTest extends TestCase
{
    public function testMarkAsPaidAttachesPayment(): void
    {
        $reservation = new Reservation();
        $payment = new Payment();

        $reservation->markAsPaid($payment);

        self::assertSame($payment, $reservation->getPayment());
    }

    public function testMarkAsPaidRefusesDoublePayment(): void
    {
        $reservation = new Reservation();
        $reservation->markAsPaid(new Payment());

        $this->expectException(\DomainException::class);
        $reservation->markAsPaid(new Payment());
    }
}
