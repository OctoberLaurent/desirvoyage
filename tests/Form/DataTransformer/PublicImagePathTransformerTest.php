<?php

namespace App\Tests\Form\DataTransformer;

use App\Form\DataTransformer\PublicImagePathTransformer;
use PHPUnit\Framework\TestCase;

final class PublicImagePathTransformerTest extends TestCase
{
    private PublicImagePathTransformer $transformer;

    protected function setUp(): void
    {
        $this->transformer = new PublicImagePathTransformer();
    }

    public function testItMakesAStoredWebPathRelativeForTheUploadField(): void
    {
        self::assertSame('data/picture.jpeg', $this->transformer->transform('/data/picture.jpeg'));
    }

    public function testItMakesAnAbsoluteLegacyPathRelativeForTheUploadField(): void
    {
        self::assertSame(
            'data/picture.jpeg',
            $this->transformer->transform('/home/ll1310/desir-voyage.site/public/data/picture.jpeg'),
        );
    }

    public function testItRestoresTheCanonicalWebPathAfterAnUpload(): void
    {
        self::assertSame('/data/picture.jpeg', $this->transformer->reverseTransform('data/picture.jpeg'));
    }
}
