<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfWrongRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = auth()->user();

        if (! $user) {
            return redirect()->route('login');
        }

        if ($user->hasAnyRole($roles)) {
            return $next($request);
        }

        return $this->redirectToRoleDashboard($user);
    }

    protected function redirectToRoleDashboard($user): Response
    {
        if ($user->isDoctor()) {
            return redirect()->route('doctor.queue')
                ->with('status', 'Redirected to your doctor dashboard.');
        }

        if ($user->isReception()) {
            return redirect()->route('reception.dashboard')
                ->with('status', 'Redirected to your reception dashboard.');
        }

        if ($user->isLab()) {
            return redirect()->route('lab.queue')
                ->with('status', 'Redirected to your lab dashboard.');
        }

        if ($user->isPharmacy()) {
            return redirect()->route('pharmacy.queue')
                ->with('status', 'Redirected to your pharmacy dashboard.');
        }

        if ($user->isNurse()) {
            return redirect()->route('nurse.dashboard')
                ->with('status', 'Redirected to your nurse dashboard.');
        }

        return redirect()->route('profile')
            ->with('status', 'Redirected to your profile.');
    }
}
