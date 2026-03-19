<?php
require __DIR__.'/vendor/autoload.php';
use App\Kernel;
use Symfony\Component\HttpFoundation\Request;

$_SERVER['APP_ENV'] = 'dev';
$_SERVER['APP_DEBUG'] = true;

$kernel = new Kernel($_SERVER['APP_ENV'], (bool) $_SERVER['APP_DEBUG']);
$kernel->boot();
$packages = $kernel->getContainer()->get('assets.packages');

echo $packages->getUrl('app.css', 'easyadmin.assets.package');
