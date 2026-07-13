<?php

namespace App\Tests\Configuration;

use PHPUnit\Framework\TestCase;

final class ImageUrlTemplateTest extends TestCase
{
    public function testDynamicImagesUseTheCentralizedUrlResolver(): void
    {
        $templates = [
            'templates/travel/index.html.twig',
            'templates/travel/allcategories.html.twig',
            'templates/travel/alltravels.html.twig',
            'templates/travel/showone.html.twig',
            'templates/reservation/index.html.twig',
        ];

        foreach ($templates as $template) {
            $content = (string) file_get_contents(\dirname(__DIR__, 2).'/'.$template);

            self::assertStringContainsString('image_url(', $content, $template.' must resolve stored images centrally.');
        }
    }
}
