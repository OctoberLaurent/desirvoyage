<?php

namespace App\Tests\Configuration;

use PHPUnit\Framework\TestCase;

final class StripeConfigurationTest extends TestCase
{
    public function testStripeSecretUsesOneEnvironmentVariableNameEverywhere(): void
    {
        $projectDir = dirname(__DIR__, 2);
        $services = (string) file_get_contents($projectDir.'/config/services.yaml');
        $readme = (string) file_get_contents($projectDir.'/README.md');
        $env = (string) file_get_contents($projectDir.'/.env');

        self::assertStringContainsString('STRIPE_SECRET_KEY', $services);
        self::assertStringContainsString('STRIPE_SECRET_KEY', $readme);
        self::assertStringContainsString('STRIPE_SECRET_KEY', $env);
        self::assertStringNotContainsString('STRIPE_PRIVATE_KEY', $services);
        self::assertStringNotContainsString('STRIPE_PRIVATE_KEY', $env);
    }
}
