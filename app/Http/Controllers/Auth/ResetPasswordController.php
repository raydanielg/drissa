<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\Request;

class ResetPasswordController extends Controller
{
    use ResetsPasswords;

    protected $redirectTo = '/';

    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Configure SMTP from database settings.
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
     * Get the response for a successful password reset.
     */
    protected function sendResetResponse(Request $request, $response)
    {
        return redirect()->route('login')
            ->with('status', 'Your password has been reset successfully! You can now sign in with your new password.');
    }

    /**
     * Get the response for a failed password reset.
     */
    protected function sendResetFailedResponse(Request $request, $response)
    {
        return back()
            ->withInput($request->only('email'))
            ->withErrors(['email' => trans($response)]);
    }
}
