<?php

namespace App\Tests\Configuration;

use PHPUnit\Framework\TestCase;

final class ImageStorageConfigurationTest extends TestCase
{
    public function testNewUploadsUseTheCanonicalPublicDataDirectory(): void
    {
        $pictureType = (string) file_get_contents(\dirname(__DIR__, 2).'/src/Form/PictureType.php');
        $categoryCrudController = (string) file_get_contents(\dirname(__DIR__, 2).'/src/Controller/Admin/CategoryCrudController.php');

        self::assertStringContainsString('PublicImageUploadType::class', $pictureType);
        self::assertStringContainsString("'upload_dir' => 'public/'", $pictureType);
        self::assertStringContainsString("'upload_filename' => 'data/[uuid].[extension]'", $pictureType);
        self::assertStringContainsString("->setUploadDir('public/')", $categoryCrudController);
        self::assertStringContainsString("->setUploadedFileNamePattern('data/[uuid].[extension]')", $categoryCrudController);
    }

    public function testTemplatesDoNotContainTheLegacyImageDirectory(): void
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

            self::assertStringNotContainsString('/data2/', $content, $template.' must only use the canonical image directory.');
        }
    }

    public function testTheImageResolverDoesNotContainDebugOutput(): void
    {
        $resolver = (string) file_get_contents(\dirname(__DIR__, 2).'/src/Service/StoredImageUrlResolver.php');

        self::assertStringNotContainsString('dump(', $resolver);
    }
}
