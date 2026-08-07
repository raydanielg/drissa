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
$envPath = __DIR__.'/../.env';
$installedFile = __DIR__.'/../storage/app/installed';
if (!file_exists($envPath) || !file_exists($installedFile)) {
    // Create minimal .env so Laravel can bootstrap the installer
    // without needing a database connection
    if (!file_exists($envPath)) {
        $tempKey = 'base64:' . base64_encode(random_bytes(32));
        $minimalEnv = "APP_NAME=\"Uzazi Clinic\"\n";
        $minimalEnv .= "APP_ENV=local\n";
        $minimalEnv .= "APP_KEY={$tempKey}\n";
        $minimalEnv .= "APP_DEBUG=true\n";
        $minimalEnv .= "APP_URL=http://localhost\n\n";
        $minimalEnv .= "DB_CONNECTION=mysql\n";
        $minimalEnv .= "DB_HOST=127.0.0.1\n";
        $minimalEnv .= "DB_PORT=3306\n";
        $minimalEnv .= "DB_DATABASE=\n";
        $minimalEnv .= "DB_USERNAME=root\n";
        $minimalEnv .= "DB_PASSWORD=\n\n";
        $minimalEnv .= "SESSION_DRIVER=file\n";
        $minimalEnv .= "CACHE_STORE=file\n";
        $minimalEnv .= "QUEUE_CONNECTION=sync\n";
        $minimalEnv .= "FILESYSTEM_DISK=local\n";
        file_put_contents($envPath, $minimalEnv);
    }

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
