<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class InstallController extends Controller
{
    public function welcome()
    {
        if ($this->isInstalled()) {
            return redirect()->route('public.home');
        }

        $phpVersion = PHP_VERSION;
        $extensions = [
            'pdo_mysql' => extension_loaded('pdo_mysql'),
            'mbstring' => extension_loaded('mbstring'),
            'openssl' => extension_loaded('openssl'),
            'tokenizer' => extension_loaded('tokenizer'),
            'json' => extension_loaded('json'),
            'fileinfo' => extension_loaded('fileinfo'),
            'curl' => extension_loaded('curl'),
        ];

        $writablePaths = [
            'storage' => is_writable(base_path('storage')),
            'bootstrap/cache' => is_writable(base_path('bootstrap/cache')),
            '.env' => file_exists(base_path('.env')) ? is_writable(base_path('.env')) : is_writable(base_path()),
        ];

        $allGood = !in_array(false, $extensions) && !in_array(false, $writablePaths);

        return view('install.welcome', compact('phpVersion', 'extensions', 'writablePaths', 'allGood'))->with('currentStep', 1);
    }

    public function database()
    {
        if ($this->isInstalled()) {
            return redirect()->route('public.home');
        }

        return view('install.database')->with('currentStep', 2);
    }

    public function process(Request $request)
    {
        if ($this->isInstalled()) {
            return redirect()->route('public.home');
        }

        $validated = $request->validate([
            'db_host' => 'required|string',
            'db_port' => 'required|string',
            'db_name' => 'required|string',
            'db_user' => 'required|string',
            'db_pass' => 'nullable|string',
            'app_name' => 'nullable|string',
            'app_url' => 'nullable|string',
        ]);

        $dbName = $validated['db_name'];
        $dbHost = $validated['db_host'];
        $dbPort = $validated['db_port'];
        $dbUser = $validated['db_user'];
        $dbPass = $validated['db_pass'] ?? '';

        try {
            $pdo = new \PDO("mysql:host={$dbHost};port={$dbPort}", $dbUser, $dbPass, [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
            ]);

            $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$dbName}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            $pdo = null;
        } catch (\PDOException $e) {
            return back()->with('error', 'Database connection failed: ' . $e->getMessage())->withInput();
        }

        $this->writeEnvFile($validated);
        Artisan::call('config:clear');

        return view('install.process', compact('dbName'))->with('currentStep', 3);
    }

    public function runMigrations(Request $request)
    {
        if ($this->isInstalled()) {
            return response()->json(['status' => 'already_installed']);
        }

        try {
            Artisan::call('migrate:fresh', ['--force' => true, '--seed' => true]);
            $output = Artisan::output();

            Artisan::call('db:seed', ['--class' => 'UserSeeder', '--force' => true]);
            $output .= Artisan::output();

            $this->markInstalled();

            return response()->json([
                'status' => 'success',
                'output' => $output,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function complete()
    {
        if (!file_exists(storage_path('app/installed'))) {
            return redirect()->route('install.welcome');
        }

        return view('install.complete')->with('currentStep', 4);
    }

    private function isInstalled(): bool
    {
        return file_exists(base_path('.env')) &&
               file_exists(storage_path('app/installed')) &&
               config('app.key') !== null &&
               config('app.key') !== '';
    }

    private function writeEnvFile(array $data): void
    {
        $appName = $data['app_name'] ?? 'Dr Issa Scientific Clinic';
        $appUrl = $data['app_url'] ?? 'http://localhost';

        // Keep the existing APP_KEY from the temporary .env so the
        // current session and CSRF token remain valid during installation
        $key = config('app.key');
        if (empty($key)) {
            $key = 'base64:' . base64_encode(random_bytes(32));
        }

        $envContent = "APP_NAME=\"{$appName}\"\n";
        $envContent .= "APP_ENV=production\n";
        $envContent .= "APP_KEY={$key}\n";
        $envContent .= "APP_DEBUG=false\n";
        $envContent .= "APP_URL={$appUrl}\n\n";
        $envContent .= "APP_LOCALE=en\n";
        $envContent .= "APP_FALLBACK_LOCALE=en\n";
        $envContent .= "APP_FAKER_LOCALE=en_US\n\n";
        $envContent .= "APP_MAINTENANCE_DRIVER=file\n\n";
        $envContent .= "BCRYPT_ROUNDS=12\n\n";
        $envContent .= "LOG_CHANNEL=stack\n";
        $envContent .= "LOG_STACK=single\n";
        $envContent .= "LOG_DEPRECATIONS_CHANNEL=null\n";
        $envContent .= "LOG_LEVEL=error\n\n";
        $envContent .= "DB_CONNECTION=mysql\n";
        $envContent .= "DB_HOST={$data['db_host']}\n";
        $envContent .= "DB_PORT={$data['db_port']}\n";
        $envContent .= "DB_DATABASE={$data['db_name']}\n";
        $envContent .= "DB_USERNAME={$data['db_user']}\n";
        $envContent .= "DB_PASSWORD={$data['db_pass']}\n\n";
        $envContent .= "SESSION_DRIVER=file\n";
        $envContent .= "SESSION_LIFETIME=120\n";
        $envContent .= "SESSION_ENCRYPT=false\n";
        $envContent .= "SESSION_PATH=/\n";
        $envContent .= "SESSION_DOMAIN=null\n\n";
        $envContent .= "BROADCAST_CONNECTION=log\n";
        $envContent .= "FILESYSTEM_DISK=local\n";
        $envContent .= "QUEUE_CONNECTION=sync\n\n";
        $envContent .= "CACHE_STORE=file\n\n";
        $envContent .= "MAIL_MAILER=log\n";
        $envContent .= "MAIL_SCHEME=null\n";
        $envContent .= "MAIL_HOST=127.0.0.1\n";
        $envContent .= "MAIL_PORT=2525\n";
        $envContent .= "MAIL_USERNAME=null\n";
        $envContent .= "MAIL_PASSWORD=null\n";
        $envContent .= "MAIL_FROM_ADDRESS=\"info@uzaziclinic.com\"\n";
        $envContent .= "MAIL_FROM_NAME=\"\${APP_NAME}\"\n\n";
        $envContent .= "VITE_APP_NAME=\"\${APP_NAME}\"\n";

        file_put_contents(base_path('.env'), $envContent);
    }

    private function markInstalled(): void
    {
        file_put_contents(storage_path('app/installed'), json_encode([
            'installed_at' => now()->toDateTimeString(),
            'version' => '1.0.0',
        ]));
    }
}
