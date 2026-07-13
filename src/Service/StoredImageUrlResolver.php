<?php

namespace App\Service;

final readonly class StoredImageUrlResolver
{
    private const DEFAULT_IMAGE_URL = '/data2/default.png';

    public function __construct(
        private string $projectDir,
    ) {
    }

    public function resolve(?string $storedPath): string
    {
        $filename = basename((string) $storedPath);

        if ('' === $filename) {
            return self::DEFAULT_IMAGE_URL;
        }

        $relativePath = 'data2/'.$filename;
        $absolutePath = $this->projectDir.'/public/'.$relativePath;

        return is_file($absolutePath) ? '/'.$relativePath : self::DEFAULT_IMAGE_URL;
    }
}
