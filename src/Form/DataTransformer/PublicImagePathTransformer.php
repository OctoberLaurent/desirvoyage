<?php

namespace App\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;

/** @implements DataTransformerInterface<mixed, mixed> */
final class PublicImagePathTransformer implements DataTransformerInterface
{
    public function transform(mixed $value): mixed
    {
        if (!is_string($value)) {
            return $value;
        }

        $path = str_replace('\\', '/', $value);
        $publicDirectoryMarker = '/public/';
        $publicDirectoryPosition = strpos($path, $publicDirectoryMarker);

        if (false === $publicDirectoryPosition) {
            return ltrim($path, '/');
        }

        return substr($path, $publicDirectoryPosition + strlen($publicDirectoryMarker));
    }

    public function reverseTransform(mixed $value): mixed
    {
        if (!is_string($value) || '' === $value) {
            return $value;
        }

        return '/'.ltrim($value, '/');
    }
}
