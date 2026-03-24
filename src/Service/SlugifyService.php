<?php

namespace App\Service;

use Cocur\Slugify\Slugify;

class SlugifyService
{
    public function makeSlug($data): string
    {
        $slugify = new Slugify();

        return $slugify->slugify($data);
    }
}
