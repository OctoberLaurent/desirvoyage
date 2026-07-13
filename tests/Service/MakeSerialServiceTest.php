<?php

namespace App\Tests\Service;

use App\Service\MakeSerialService;
use PHPUnit\Framework\TestCase;

final class MakeSerialServiceTest extends TestCase
{
    public function testSerialFormatIsThreeGroupsOfThree(): void
    {
        $serial = (new MakeSerialService())->makeSerial();

        // Expected format: XXX-XXX-XXX (11 characters, 2 hyphens).
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

        // A few collisions are acceptable across 1,000 random draws, but the vast
        // majority must be unique as a generator sanity check.
        self::assertGreaterThan(900, count(array_unique($serials)));
    }
}
