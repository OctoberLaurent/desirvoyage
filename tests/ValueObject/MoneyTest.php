<?php

namespace App\Tests\ValueObject;

use App\ValueObject\Money;
use PHPUnit\Framework\TestCase;

final class MoneyTest extends TestCase
{
    public function testEurosAreStoredAsExactCents(): void
    {
        $money = Money::fromEuros(19.99);

        self::assertSame(1999, $money->cents());
        self::assertSame(19.99, $money->amount());
        self::assertSame('19.99', (string) $money);
    }

    public function testArithmeticDoesNotAccumulateFloatingPointErrors(): void
    {
        $total = Money::fromEuros(0.10)
            ->add(Money::fromEuros(0.20))
            ->multiply(3);

        self::assertSame(90, $total->cents());
        self::assertSame(0.90, $total->amount());
    }

    public function testMoneyCanBeCreatedDirectlyFromCents(): void
    {
        self::assertSame(1234, Money::fromCents(1234)->cents());
    }
}
