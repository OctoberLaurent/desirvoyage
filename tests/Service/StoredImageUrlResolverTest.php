<?php

namespace App\Tests\Service;

use App\Service\StoredImageUrlResolver;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Filesystem;

final class StoredImageUrlResolverTest extends TestCase
{
    private string $projectDir;

    private StoredImageUrlResolver $resolver;

    protected function setUp(): void
    {
        $this->projectDir = sys_get_temp_dir().'/desir-voyage-image-resolver-'.bin2hex(random_bytes(8));
        $filesystem = new Filesystem();
        $filesystem->mkdir([
            $this->projectDir.'/public/data',
            $this->projectDir.'/public/data2',
        ]);

        file_put_contents($this->projectDir.'/public/data/default.png', 'default');
        file_put_contents($this->projectDir.'/public/data/present.jpeg', 'present');
        file_put_contents($this->projectDir.'/public/data2/legacy.jpeg', 'legacy');

        $this->resolver = new StoredImageUrlResolver($this->projectDir);
    }

    protected function tearDown(): void
    {
        (new Filesystem())->remove($this->projectDir);
    }

    public function testItReturnsTheCanonicalPublicUrlWhenTheStoredImageExists(): void
    {
        self::assertSame('/data/present.jpeg', $this->resolver->resolve('/data/present.jpeg'));
    }

    public function testItNormalizesAnAbsoluteLegacyPathToTheCanonicalPublicUrl(): void
    {
        self::assertSame(
            '/data/present.jpeg',
            $this->resolver->resolve('/home/ll1310/desir-voyage.site/public/data/present.jpeg'),
        );
    }

    public function testItUsesTheLegacyDirectoryOnlyWhenTheCanonicalFileIsNotAvailable(): void
    {
        self::assertSame('/data2/legacy.jpeg', $this->resolver->resolve('/data/legacy.jpeg'));
    }

    public function testItReturnsTheDefaultImageWhenTheStoredImageIsMissing(): void
    {
        self::assertSame('/data/default.png', $this->resolver->resolve('/data/missing.jpeg'));
    }

    public function testItReturnsTheDefaultImageWhenThereIsNoStoredImage(): void
    {
        self::assertSame('/data/default.png', $this->resolver->resolve(null));
    }
}
