<?php

/**
 * Laravel - A PHP Framework For Web Artisans
 *
 * @package  Laravel
 * @author   Taylor Otwell <taylor@laravel.com>
 */

define('LARAVEL_START', microtime(true));

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = __DIR__.'/storage/framework/maintenance.php')) {
    require $maintenance;
}

// If .env doesn't exist or app not installed, redirect to installer
// before Laravel tries to bootstrap (which would cause a 500 error)
$envPath = __DIR__.'/.env';
$installedFile = __DIR__.'/storage/app/installed';
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

/*
|--------------------------------------------------------------------------
| Register The Auto Loader
|--------------------------------------------------------------------------
|
| Composer provides a convenient, automatically generated class loader for
| our application. We just need to utilize it! We'll simply require it
| into the script here so that we don't have to worry about manual
| loading any of our classes later on. It feels great to relax.
|
*/

require __DIR__.'/vendor/autoload.php';

/*
|--------------------------------------------------------------------------
| Turn On The Lights
|--------------------------------------------------------------------------
|
| We need to illuminate PHP development, so let us turn on the lights.
| This bootstraps the framework and gets it ready for use, then it
| will load up this application so that we can run it and send
| the responses back to the browser and delight our users.
|
*/

$app = require_once __DIR__.'/bootstrap/app.php';

/*
|--------------------------------------------------------------------------
| Run The Application
|--------------------------------------------------------------------------
|
| Once we have the application, we can handle the incoming request
| through the kernel, and send the associated response back to
| the client's browser allowing them to enjoy the creative
| and wonderful application we have prepared for them.
|
*/

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

$response->send();

$kernel->terminate($request, $response);
