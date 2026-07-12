<?php

namespace App\Tests\Form;

use App\Entity\Stay;
use App\Form\StayType;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use Symfony\Component\Form\Test\TypeTestCase;

#[AllowMockObjectsWithoutExpectations]
final class StayTypeTest extends TypeTestCase
{
    public function testPriceIsSubmittedInEurosAndStoredInCents(): void
    {
        $stay = new Stay();
        $form = $this->factory->create(StayType::class, $stay);
        $form->submit([
            'startDate' => '2026-08-01',
            'endDate' => '2026-08-08',
            'departure' => 'Bruxelles',
            'arrival' => 'Lisbonne',
            'stock' => '5',
            'priceAmount' => '19.99',
        ]);

        self::assertTrue($form->isSubmitted());
        self::assertTrue($form->isValid(), (string) $form->getErrors(true));
        self::assertSame(1999, $stay->getPrice()->cents());
    }
}
