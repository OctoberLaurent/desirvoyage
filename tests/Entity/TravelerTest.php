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

        // setEmail accepte le VO ; getEmail() renvoie un nouveau VO (skill §3).
        self::assertSame('voyageur@example.com', $traveler->getEmail()->value());
    }
}
