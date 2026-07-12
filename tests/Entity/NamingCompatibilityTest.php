<?php

namespace App\Tests\Entity;

use App\Entity\Category;
use App\Entity\Formality;
use App\Entity\Reservation;
use App\Entity\Stay;
use App\Entity\Travel;
use PHPUnit\Framework\TestCase;

final class NamingCompatibilityTest extends TestCase
{
    public function testStayExposesCorrectStartDateAndDepartureNames(): void
    {
        $stay = new Stay();
        $date = new \DateTimeImmutable('+1 month');

        $stay->setStartDate($date)->setDeparture('Bruxelles');

        self::assertSame($date, $stay->getStartDate());
        self::assertSame('Bruxelles', $stay->getDeparture());
        self::assertSame($date, $stay->getStarDate());
        self::assertSame('Bruxelles', $stay->getDepature());
    }

    public function testTravelExposesSingularCategoryAndPluralFormalities(): void
    {
        $travel = new Travel();
        $category = new Category();
        $formality = new Formality();

        $travel->setCategory($category)->addFormality($formality);

        self::assertSame($category, $travel->getCategory());
        self::assertSame($category, $travel->getCategories());
        self::assertTrue($travel->getFormalities()->contains($formality));
        self::assertSame($travel->getFormalities(), $travel->getFormality());
    }

    public function testReservationExposesUpdatedAtName(): void
    {
        $reservation = new Reservation();
        $date = new \DateTimeImmutable();

        $reservation->setUpdatedAt($date);

        self::assertEquals($date, $reservation->getUpdatedAt());
        self::assertEquals($reservation->getUpdatedAt(), $reservation->getUpdateAt());
        self::assertInstanceOf(\DateTime::class, $reservation->getUpdatedAt());
    }
}
