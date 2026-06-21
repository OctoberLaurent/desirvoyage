<?php

namespace App\Tests\Service;

use App\Service\MakeSerialService;
use PHPUnit\Framework\TestCase;

final class MakeSerialServiceTest extends TestCase
{
    public function testSerialFormatIsThreeGroupsOfThree(): void
    {
        $serial = (new MakeSerialService())->makeSerial();

        // Format attendu : XXX-XXX-XXX (11 caractères, 2 tirets)
        self::assertSame(11, strlen($serial));
        self::assertMatchesRegularExpression('/^[A-Z0-9]{3}-[A-Z0-9]{3}-[A-Z0-9]{3}$/', $serial);
    }

    public function testSerialsAreSufficientlyUniqueOverManyDraws(): void
    {
        $service = new MakeSerialService();
        $serials = [];
        for ($i = 0; $i < 1000; ++$i) {
            $serials[] = $service->makeSerial();
        }

        // Sur 1000 tirages aléatoires on tolère quelques collisions, mais la
        // grande majorité doit être unique (sanity check du générateur).
        self::assertGreaterThan(900, count(array_unique($serials)));
    }
}
