<?php

namespace App\Tests\Configuration;

use PHPUnit\Framework\TestCase;

final class DependencyConstraintTest extends TestCase
{
    public function testApplicationDependenciesUseBoundedVersionConstraints(): void
    {
        $composer = json_decode((string) file_get_contents(dirname(__DIR__, 2).'/composer.json'), true, 512, \JSON_THROW_ON_ERROR);
        self::assertIsArray($composer);
        self::assertArrayHasKey('require', $composer);
        self::assertArrayHasKey('require-dev', $composer);
        self::assertIsArray($composer['require']);
        self::assertIsArray($composer['require-dev']);

        foreach (array_merge($composer['require'], $composer['require-dev']) as $package => $constraint) {
            if (str_starts_with($package, 'ext-')) {
                continue;
            }

            self::assertNotSame('*', $constraint, sprintf('%s doit utiliser une contrainte de version bornée.', $package));
        }
    }
}
