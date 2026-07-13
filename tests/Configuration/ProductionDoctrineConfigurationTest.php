<?php

namespace App\Tests\Configuration;

use PHPUnit\Framework\TestCase;

final class ProductionDoctrineConfigurationTest extends TestCase
{
    public function testProductionDoctrineConfigurationDoesNotUseRemovedProxyOption(): void
    {
        $configuration = (string) file_get_contents(dirname(__DIR__, 2).'/config/packages/prod/doctrine.yaml');

        self::assertStringNotContainsString('auto_generate_proxy_classes', $configuration);
    }
}
