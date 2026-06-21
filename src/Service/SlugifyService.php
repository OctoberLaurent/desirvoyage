<?php

namespace App\Service;

use Cocur\Slugify\Slugify;

class SlugifyService
{
    public function makeSlug(string $data): string
    {
        $slugify = new Slugify();

        return $slugify->slugify($data);
    }
}
