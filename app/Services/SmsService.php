<?php

namespace App\Services;

use App\Models\Setting;
use App\Models\SmsLog;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SmsService
{
    public static function send(string $phone, string $message, ?User $user = null, ?string $recipient = null): array
    {
        $gateway = Setting::get('sms_gateway', 'log');
        $phone = self::normalizePhone($phone);

        $log = SmsLog::create([
            'user_id' => $user?->id,
            'recipient' => $recipient,
            'phone' => $phone,
            'message' => $message,
            'status' => 'pending',
            'gateway' => $gateway,
        ]);

        try {
            $result = match ($gateway) {
                'twilio' => self::sendTwilio($phone, $message),
                'http' => self::sendHttp($phone, $message),
                'nextsms' => self::sendNextSms($phone, $message),
                default => self::sendLog($phone, $message),
            };

            $log->update([
                'status' => $result['success'] ? 'sent' : 'failed',
                'response' => json_encode($result),
            ]);

            return $result;
        } catch (\Throwable $e) {
            $log->update([
                'status' => 'failed',
                'response' => $e->getMessage(),
            ]);
            Log::error('SMS sending failed', ['error' => $e->getMessage(), 'phone' => $phone]);
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    protected static function sendLog(string $phone, string $message): array
    {
        Log::info('SMS logged (not sent)', ['phone' => $phone, 'message' => $message]);
        return ['success' => true, 'gateway' => 'log', 'message' => 'SMS logged for testing. Configure gateway to send real SMS.'];
    }

    protected static function sendTwilio(string $phone, string $message): array
    {
        $sid = Setting::get('twilio_sid');
        $token = Setting::get('twilio_token');
        $from = Setting::get('twilio_from');

        if (! $sid || ! $token || ! $from) {
            return ['success' => false, 'error' => 'Twilio credentials not configured.'];
        }

        $response = Http::withBasicAuth($sid, $token)
            ->asForm()
            ->post("https://api.twilio.com/2010-04-01/Accounts/{$sid}/Messages.json", [
                'From' => $from,
                'To' => $phone,
                'Body' => $message,
            ]);

        if ($response->successful()) {
            return ['success' => true, 'data' => $response->json()];
        }

        return ['success' => false, 'error' => $response->body()];
    }

    protected static function sendHttp(string $phone, string $message): array
    {
        $url = Setting::get('sms_http_url');
        $method = Setting::get('sms_http_method', 'POST');

        if (! $url) {
            return ['success' => false, 'error' => 'HTTP gateway URL not configured.'];
        }

        $payload = [
            'phone' => $phone,
            'message' => $message,
        ];

        $response = strtoupper($method) === 'GET'
            ? Http::get($url, $payload)
            : Http::post($url, $payload);

        if ($response->successful()) {
            return ['success' => true, 'data' => $response->body()];
        }

        return ['success' => false, 'error' => $response->body()];
    }

    protected static function sendNextSms(string $phone, string $message): array
    {
        $username = Setting::get('nextsms_username');
        $password = Setting::get('nextsms_password');
        $from = Setting::get('nextsms_from', Setting::get('sms_sender_id', 'UZAZICLINIC'));
        $url = Setting::get('nextsms_url', 'https://messaging-service.co.tz/api/sms/v1/text/single');

        if (! $username || ! $password) {
            return ['success' => false, 'error' => 'NextSMS credentials not configured. Go to Settings > SMS to configure.'];
        }

        $phone = self::formatForNextSms($phone);

        $response = Http::withBasicAuth($username, $password)
            ->withHeaders(['Content-Type' => 'application/json'])
            ->post($url, [
                'from' => $from,
                'to' => $phone,
                'text' => $message,
            ]);

        if ($response->successful()) {
            return ['success' => true, 'gateway' => 'nextsms', 'data' => $response->json() ?? $response->body()];
        }

        return ['success' => false, 'gateway' => 'nextsms', 'error' => $response->body(), 'status' => $response->status()];
    }

    protected static function formatForNextSms(string $phone): string
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);
        if (str_starts_with($phone, '255')) {
            return $phone;
        }
        if (str_starts_with($phone, '0')) {
            return '255' . substr($phone, 1);
        }
        if (str_starts_with($phone, '256')) {
            return $phone;
        }
        return $phone;
    }

    protected static function normalizePhone(string $phone): string
    {
        $phone = preg_replace('/[^0-9+]/', '', $phone);
        if (str_starts_with($phone, '0')) {
            $phone = '+255' . substr($phone, 1);
        }
        if (! str_starts_with($phone, '+')) {
            $phone = '+' . $phone;
        }
        return $phone;
    }
}
