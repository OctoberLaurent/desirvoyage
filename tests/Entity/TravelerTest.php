<?php

namespace App\Tests\Entity;

use App\Entity\Traveler;
use App\ValueObject\Email;
use PHPUnit\Framework\TestCase;

/**
 * @covers \App\Entity\Traveler
 */
final class TravelerTest extends TestCase
{
    public function testSetEmailStoresValueObjectAndGetterReturnsIt(): void
    {
        $traveler = new Traveler();
        $traveler->setEmail(new Email('voyageur@example.com'));

        // setEmail accepts the value object; getEmail() returns a new value object (skill §3).
        self::assertSame('voyageur@example.com', $traveler->getEmail()->value());
    }
}
