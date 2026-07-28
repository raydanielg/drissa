<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckInstallation
{
    public function handle(Request $request, Closure $next): Response
    {
        $isInstalled = file_exists(base_path('.env')) &&
                       file_exists(storage_path('app/installed')) &&
                       env('APP_KEY') !== '';

        $isInstallerRoute = $request->is('install/*') || $request->is('install');

        if (!$isInstalled && !$isInstallerRoute) {
            return redirect()->route('install.welcome');
        }

        if ($isInstalled && $isInstallerRoute && !$request->is('install/complete')) {
            return redirect()->route('public.home');
        }

        return $next($request);
    }
}
