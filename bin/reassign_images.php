<?php

use App\Entity\Categories;
use App\Entity\Pictures;
use App\Kernel;
use Symfony\Component\Dotenv\Dotenv;

require dirname(__DIR__).'/vendor/autoload.php';

(new Dotenv())->bootEnv(dirname(__DIR__).'/.env');

$kernel = new Kernel($_SERVER['APP_ENV'], (bool) $_SERVER['APP_DEBUG']);
$kernel->boot();

$em = $kernel->getContainer()->get('doctrine')->getManager();

$dataDir = dirname(__DIR__).'/public/data2';
$files = scandir($dataDir);
$images = [];
foreach ($files as $file) {
    if ('.' !== $file && '..' !== $file && 'default.png' !== $file) {
        $images[] = $file;
    }
}

if (0 === count($images)) {
    echo "No matching images found.\n";
    exit(1);
}

// Update Categories
$categories = $em->getRepository(Categories::class)->findAll();
$imgIdx = 0;
foreach ($categories as $category) {
    $category->setUrl($images[$imgIdx % count($images)]);
    ++$imgIdx;
}

// Update Pictures
$pictures = $em->getRepository(Pictures::class)->findAll();
foreach ($pictures as $picture) {
    $picture->setUrl($images[$imgIdx % count($images)]);
    ++$imgIdx;
}

$em->flush();

echo 'Successfully re-assigned '.count($categories).' categories and '.count($pictures).' pictures using '.count($images)." unique images.\n";
