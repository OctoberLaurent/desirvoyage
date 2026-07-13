<?php

namespace App\Tests\Configuration;

use PHPUnit\Framework\TestCase;

final class TravelImageFallbackTemplateTest extends TestCase
{
    public function testTravelDetailUsesTheDefaultImageWhenNoPictureIsAvailable(): void
    {
        $template = (string) file_get_contents(dirname(__DIR__, 2).'/templates/travel/showone.html.twig');

        self::assertStringContainsString(
            '{% if travel.pictures|length < 2 %}',
            $template,
        );

        self::assertStringContainsString(
            '<img class="responsive-img travel-image-fallback" src="/data2/default.png" alt="{{ travel.name }}">',
            $template,
        );

        self::assertStringContainsString(
            '<img class="responsive-img travel-image-fallback" src="{{ image_url(picture.url) }}" alt="{{ picture.name }}">',
            $template,
        );
    }
}
