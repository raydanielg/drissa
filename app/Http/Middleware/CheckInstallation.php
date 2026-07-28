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
                       config('app.key') !== null &&
                       config('app.key') !== '';

        $isInstallerRoute = $request->is('install/*') || $request->is('install');

        if (!$isInstalled && !$isInstallerRoute) {
            if (app('router')->has('install.welcome')) {
                return redirect()->route('install.welcome');
            }
            abort(503, 'Application is not installed. Please run the installer.');
        }

        if ($isInstalled && $isInstallerRoute && !$request->is('install/complete')) {
            if (app('router')->has('public.home')) {
                return redirect()->route('public.home');
            }
            return redirect('/');
        }

        return $next($request);
    }
}
