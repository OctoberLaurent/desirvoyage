<?php

namespace App\Twig;

use App\Service\StoredImageUrlResolver;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class ImageExtension extends AbstractExtension
{
    public function __construct(
        private readonly StoredImageUrlResolver $imageUrlResolver,
    ) {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('image_url', $this->imageUrlResolver->resolve(...)),
        ];
    }
}
