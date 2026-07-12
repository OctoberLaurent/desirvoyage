<?php

namespace App\Tests\Configuration;

use PHPUnit\Framework\TestCase;

final class ApiPlatformConfigurationTest extends TestCase
{
    public function testStayDoesNotUseLegacyApiPlatformAnnotation(): void
    {
        $source = (string) file_get_contents(dirname(__DIR__, 2).'/src/Entity/Stay.php');

        self::assertStringNotContainsString('ApiPlatform\\Core\\Annotation\\ApiResource', $source);
        self::assertStringNotContainsString('@ApiResource', $source);
    }
}
