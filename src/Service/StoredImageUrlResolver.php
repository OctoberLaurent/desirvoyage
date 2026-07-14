<?php

namespace App\Service;

final readonly class StoredImageUrlResolver
{
    private const IMAGE_DIRECTORY = 'data';
    private const LEGACY_IMAGE_DIRECTORY = 'data2';
    private const DEFAULT_IMAGE_NAME = 'default.png';

    public function __construct(
        private string $projectDir,
    ) {
    }

    public function resolve(?string $storedPath): string
    {
        $filename = basename((string) $storedPath);

        if ('' === $filename) {
            return $this->defaultImageUrl();
        }

        foreach ([self::IMAGE_DIRECTORY, self::LEGACY_IMAGE_DIRECTORY] as $directory) {
            $imageUrl = '/'.$directory.'/'.$filename;

            if (is_file($this->projectDir.'/public'.$imageUrl)) {
                return $imageUrl;
            }
        }

        return $this->defaultImageUrl();
    }

    private function defaultImageUrl(): string
    {
        foreach ([self::IMAGE_DIRECTORY, self::LEGACY_IMAGE_DIRECTORY] as $directory) {
            $imageUrl = '/'.$directory.'/'.self::DEFAULT_IMAGE_NAME;

            if (is_file($this->projectDir.'/public'.$imageUrl)) {
                return $imageUrl;
            }
        }

        return '/'.self::IMAGE_DIRECTORY.'/'.self::DEFAULT_IMAGE_NAME;
    }
}
