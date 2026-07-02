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
    public function testSetEmailAcceptsStringFromForm(): void
    {
        $traveler = new Traveler();
        $traveler->setEmail('voyageur@example.com');

        // getEmail() renvoie le VO Email (skill §3) même quand le setter a reçu
        // une string depuis le formulaire (TravelerType binde l'entité directement).
        self::assertSame('voyageur@example.com', $traveler->getEmail()->value());
    }

    public function testSetEmailAcceptsValueObjectFromDomain(): void
    {
        $traveler = new Traveler();
        $traveler->setEmail(new Email('domain@example.com'));

        self::assertSame('domain@example.com', $traveler->getEmail()->value());
    }
}
