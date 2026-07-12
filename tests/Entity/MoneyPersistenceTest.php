<?php

namespace App\Tests\Entity;

use App\Entity\Option;
use App\Entity\Payment;
use App\Entity\Reservation;
use App\Entity\Stay;
use App\ValueObject\Money;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class MoneyPersistenceTest extends TestCase
{
    public function testPricesExposeExactCentsWhileAcceptingEuros(): void
    {
        $stay = new Stay();
        $stay->setPrice(19.99);
        $option = new Option();
        $option->setPrice(Money::fromEuros(3.50));
        $reservation = new Reservation();
        $reservation->setPrice(23.49);

        self::assertSame(1999, $stay->getPrice()->cents());
        self::assertSame(350, $option->getPrice()->cents());
        self::assertSame(2349, $reservation->getPrice()->cents());
    }

    public function testPaymentAmountIsStoredAsMoney(): void
    {
        $payment = new Payment();
        $payment->setAmount(Money::fromCents(12000));
        $amount = $payment->getAmount();
        self::assertNotNull($amount);

        self::assertSame(12000, $amount->cents());
        self::assertSame(120.0, $amount->amount());
    }

    /** @param class-string $class */
    #[DataProvider('priceEntityProvider')]
    public function testDoctrineMapsMonetaryColumnsAsIntegers(string $class, string $property): void
    {
        $reflection = new \ReflectionProperty($class, $property);
        $column = $reflection->getAttributes(\Doctrine\ORM\Mapping\Column::class)[0]->newInstance();

        self::assertSame('integer', $column->type);
    }

    /** @return iterable<string, array{class-string, string}> */
    public static function priceEntityProvider(): iterable
    {
        yield 'stay price' => [Stay::class, 'price'];
        yield 'option price' => [Option::class, 'price'];
        yield 'reservation price' => [Reservation::class, 'price'];
        yield 'payment amount' => [Payment::class, 'amount'];
    }
}
