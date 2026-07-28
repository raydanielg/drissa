<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\User;
use App\Notifications\ResetPasswordNotification;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;

class ForgotPasswordController extends Controller
{
    use SendsPasswordResetEmails;

    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Configure SMTP from database settings before sending.
     */
    protected function configureMail(): void
    {
        $host = Setting::where('key', 'mail_host')->value('value');
        $port = Setting::where('key', 'mail_port')->value('value');
        $username = Setting::where('key', 'mail_username')->value('value');
        $password = Setting::where('key', 'mail_password')->value('value');
        $encryption = Setting::where('key', 'mail_encryption')->value('value');
        $fromAddress = Setting::where('key', 'mail_from_address')->value('value');
        $fromName = Setting::where('key', 'mail_from_name')->value('value');

        if ($host) {
            config([
                'mail.mailers.smtp.host' => $host,
                'mail.mailers.smtp.port' => $port ?? 587,
                'mail.mailers.smtp.username' => $username,
                'mail.mailers.smtp.password' => $password,
                'mail.mailers.smtp.encryption' => $encryption ?? 'tls',
                'mail.from.address' => $fromAddress ?? config('mail.from.address'),
                'mail.from.name' => $fromName ?? config('app.name'),
                'mail.default' => 'smtp',
            ]);
        }
    }

    /**
     * Send a reset link to the given user.
     */
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $this->configureMail();

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->with('status', 'If that email exists in our system, a reset link has been sent.');
        }

        $token = Password::createToken($user);
        $user->notify(new ResetPasswordNotification($token));

        return back()->with('status', 'We have emailed your password reset link! Please check your inbox.');
    }
}
