<?php

namespace App\Tests\Service;

use App\Service\StoredImageUrlResolver;
use PHPUnit\Framework\TestCase;

final class StoredImageUrlResolverTest extends TestCase
{
    private StoredImageUrlResolver $resolver;

    protected function setUp(): void
    {
        $this->resolver = new StoredImageUrlResolver(\dirname(__DIR__, 2));
    }

    public function testItReturnsThePublicUrlWhenTheStoredImageExists(): void
    {
        self::assertSame(
            '/data2/c2f93ece-ccb0-4b4a-af9e-f42e97074b87.jpeg',
            $this->resolver->resolve('/legacy/path/c2f93ece-ccb0-4b4a-af9e-f42e97074b87.jpeg'),
        );
    }

    public function testItReturnsTheDefaultImageWhenTheStoredImageIsMissing(): void
    {
        self::assertSame('/data2/default.png', $this->resolver->resolve('/legacy/path/missing.jpeg'));
    }

    public function testItReturnsTheDefaultImageWhenThereIsNoStoredImage(): void
    {
        self::assertSame('/data2/default.png', $this->resolver->resolve(null));
    }
}
