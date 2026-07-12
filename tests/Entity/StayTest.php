<?php

namespace App\Tests\Entity;

use App\Entity\Stay;
use PHPUnit\Framework\TestCase;

final class StayTest extends TestCase
{
    public function testGeneratedSerialUsesReadableSecureFormat(): void
    {
        $stay = new Stay();

        $serial = $stay->generateSerial();

        self::assertMatchesRegularExpression('/^[A-Z0-9]{3}(?:-[A-Z0-9]{3}){2}$/', $serial);
    }

    public function testGeneratedSerialsAreNotRepeated(): void
    {
        $serials = [];

        for ($i = 0; $i < 500; ++$i) {
            $serials[] = (new Stay())->generateSerial();
        }

        self::assertCount(500, array_unique($serials));
    }
}
