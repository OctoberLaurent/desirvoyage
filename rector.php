<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;

return RectorConfig::configure()
    ->withPaths([
        __DIR__.'/src',
    ])
    ->withPhpSets(php84: true)
    ->withAttributesSets(symfony: true, doctrine: true, phpunit: false)
    ->withSkip([
        // Les migrations sont auto-générées et exclues de l'analyse
        __DIR__.'/src/Migrations',
    ]);
