<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// If .env doesn't exist or app not installed, redirect to installer
// before Laravel tries to bootstrap (which would cause a 500 error)
if (!file_exists(__DIR__.'/../.env') || !file_exists(__DIR__.'/../storage/app/installed')) {
    $requestUri = $_SERVER['REQUEST_URI'] ?? '/';
    if (strpos($requestUri, '/install') !== 0) {
        header('Location: /install');
        exit;
    }
}

// Register the Composer autoloader...
require __DIR__.'/../vendor/autoload.php';

// Bootstrap Laravel and handle the request...
/** @var Application $app */
$app = require_once __DIR__.'/../bootstrap/app.php';

$app->handleRequest(Request::capture());
