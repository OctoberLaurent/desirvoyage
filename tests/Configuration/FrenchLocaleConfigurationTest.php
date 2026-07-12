<?php

namespace App\Tests\Configuration;

use PHPUnit\Framework\TestCase;

final class FrenchLocaleConfigurationTest extends TestCase
{
    public function testPhpIntlFormatsDatesInFrench(): void
    {
        $formatter = new \IntlDateFormatter('fr_FR', \IntlDateFormatter::FULL, \IntlDateFormatter::NONE);

        self::assertSame('dimanche 12 juillet 2026', $formatter->format(new \DateTimeImmutable('2026-07-12')));
    }

    public function testReservationListForcesTheFrenchLocaleForDates(): void
    {
        $projectDir = dirname(__DIR__, 2);
        $template = (string) file_get_contents($projectDir.'/templates/reservation/reservationlist.html.twig');
        $frameworkConfiguration = (string) file_get_contents($projectDir.'/config/packages/translation.yaml');

        self::assertStringContainsString("locale='fr_FR'", $template);
        self::assertStringContainsString('default_locale: fr_FR', $frameworkConfiguration);
    }
}
