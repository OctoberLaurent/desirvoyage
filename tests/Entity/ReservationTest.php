<?php

namespace App\Tests\Entity;

use App\Entity\Payment;
use App\Entity\Reservation;
use App\Enum\ReservationStatus;
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
        self::assertSame(ReservationStatus::Paid, $reservation->getStatus());
    }

    public function testNewReservationIsPending(): void
    {
        self::assertSame(ReservationStatus::Pending, (new Reservation())->getStatus());
    }

    public function testPendingReservationCanExpireButPaidReservationCannot(): void
    {
        $reservation = new Reservation();
        $reservation->expire();

        self::assertSame(ReservationStatus::Expired, $reservation->getStatus());

        $paid = new Reservation();
        $paid->markAsPaid(new Payment());

        $this->expectException(\DomainException::class);
        $paid->expire();
    }

    public function testDoctrineDatetimeFieldsNormalizeImmutableSessionDates(): void
    {
        $reservation = new Reservation();
        $reservation->setCreatedDate(new \DateTimeImmutable('2026-07-11 10:00:00'));
        $reservation->setUpdatedAt(new \DateTimeImmutable('2026-07-11 11:00:00'));

        self::assertInstanceOf(\DateTime::class, $reservation->getCreatedDate());
        self::assertInstanceOf(\DateTime::class, $reservation->getUpdatedAt());
        self::assertSame('2026-07-11 10:00:00', $reservation->getCreatedDate()->format('Y-m-d H:i:s'));
        self::assertSame('2026-07-11 11:00:00', $reservation->getUpdatedAt()->format('Y-m-d H:i:s'));
    }

    public function testMarkAsPaidRefusesDoublePayment(): void
    {
        $reservation = new Reservation();
        $reservation->markAsPaid(new Payment());

        $this->expectException(\DomainException::class);
        $reservation->markAsPaid(new Payment());
    }
}
