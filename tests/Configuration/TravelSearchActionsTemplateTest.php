<?php

namespace App\Tests\Configuration;

use PHPUnit\Framework\TestCase;

final class TravelSearchActionsTemplateTest extends TestCase
{
    public function testTravelSearchActionsUseAnAccessibleIconAndSharedLayout(): void
    {
        $projectDir = \dirname(__DIR__, 2);
        $template = (string) file_get_contents($projectDir.'/templates/travel/alltravels.html.twig');
        $stylesheet = (string) file_get_contents($projectDir.'/assets/css/app.scss');

        self::assertStringContainsString('<div class="travel-search-actions">', $template);
        self::assertStringContainsString('class="btn waves-effect waves-light travel-search-submit"', $template);
        self::assertStringContainsString('aria-label="{{ \'search\' | trans }}"', $template);
        self::assertStringContainsString('<i class="material-icons" aria-hidden="true">search</i>', $template);
        self::assertStringContainsString('class="btn waves-effect waves-light indigo darken-4 travel-search-reset"', $template);
        self::assertStringContainsString('.travel-search-actions {', $stylesheet);
        self::assertStringContainsString('display: flex;', $stylesheet);
        self::assertStringContainsString('flex-wrap: nowrap;', $stylesheet);
        self::assertStringContainsString('gap: 0.5rem;', $stylesheet);
        self::assertStringContainsString('min-width: 3rem;', $stylesheet);
        self::assertStringContainsString('color: #fff;', $stylesheet);
    }
}
