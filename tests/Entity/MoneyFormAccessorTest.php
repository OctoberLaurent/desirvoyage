<?php

namespace App\Tests\Entity;

use App\Entity\Option;
use App\Entity\Reservation;
use App\Entity\Stay;
use PHPUnit\Framework\TestCase;

final class MoneyFormAccessorTest extends TestCase
{
    public function testEntitiesExposeNumericEuroAccessorsForFormsAndAdmin(): void
    {
        $stay = (new Stay())->setPriceAmount(19.99);
        $option = (new Option())->setPriceAmount(3.50);
        $reservation = (new Reservation())->setPriceAmount(23.49);

        self::assertSame(19.99, $stay->getPriceAmount());
        self::assertSame(1999, $stay->getPrice()->cents());
        self::assertSame(3.50, $option->getPriceAmount());
        self::assertSame(350, $option->getPrice()->cents());
        self::assertSame(23.49, $reservation->getPriceAmount());
        self::assertSame(2349, $reservation->getPrice()->cents());
    }
}
