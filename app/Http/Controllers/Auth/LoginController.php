<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }

    /**
     * Redirect users after login based on their role.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $user
     * @return mixed
     */
    protected function authenticated($request, $user)
    {
        if ($user->isDoctor()) {
            return redirect()->route('doctor.queue');
        }

        if ($user->isReception()) {
            return redirect()->route('reception.dashboard');
        }

        if ($user->isLab()) {
            return redirect()->route('lab.queue');
        }

        if ($user->isPharmacy()) {
            return redirect()->route('pharmacy.queue');
        }

        return redirect()->route('dashboard');
    }
}
