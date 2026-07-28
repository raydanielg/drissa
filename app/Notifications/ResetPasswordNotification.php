<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword as BaseResetPassword;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Crypt;

class ResetPasswordNotification extends BaseResetPassword
{
    public function toMail($notifiable): MailMessage
    {
        $url = url(route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ], false));

        $clinicName = config('app.name', 'Dr Issa Scientific Clinic');
        $expiry = config('auth.passwords.' . config('auth.defaults.passwords') . '.expire', 60);

        return (new MailMessage())
            ->subject('Reset Your Password - ' . $clinicName)
            ->view('emails.password-reset', [
                'url' => $url,
                'clinicName' => $clinicName,
                'userName' => $notifiable->name ?? 'User',
                'expiry' => $expiry,
            ]);
    }
}
