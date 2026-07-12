<?php

namespace App\Tests\Configuration;

use PHPUnit\Framework\TestCase;

final class PhpUnitConfigurationTest extends TestCase
{
    public function testSymfonyBridgeTargetsTheInstalledPhpUnitMajorVersion(): void
    {
        $configuration = (string) file_get_contents(dirname(__DIR__, 2).'/phpunit.xml.dist');

        self::assertStringContainsString('name="SYMFONY_PHPUNIT_VERSION" value="13"', $configuration);
    }
}
