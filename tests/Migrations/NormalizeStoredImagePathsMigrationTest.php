<?php

namespace App\Tests\Migrations;

use PHPUnit\Framework\TestCase;

final class NormalizeStoredImagePathsMigrationTest extends TestCase
{
    public function testItNormalizesPictureAndCategoryPathsToThePublicDataDirectory(): void
    {
        $migration = \dirname(__DIR__, 2).'/src/Migrations/Version20260714090000.php';

        self::assertFileExists($migration);

        $content = (string) file_get_contents($migration);

        self::assertStringContainsString("UPDATE pictures SET url = CONCAT('/data/', SUBSTRING_INDEX(url, '/', -1))", $content);
        self::assertStringContainsString("UPDATE categories SET url = CONCAT('/data/', SUBSTRING_INDEX(url, '/', -1))", $content);
        self::assertStringContainsString('throwIrreversibleMigrationException', $content);
    }
}
