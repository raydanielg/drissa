<?php

namespace Database\Seeders;

use App\Models\EmailTemplate;
use App\Models\Setting;
use App\Models\SmsTemplate;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SystemDefaultsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            ['key' => 'app_name', 'value' => 'Uzazi Clinic', 'group' => 'general', 'type' => 'text'],
            ['key' => 'clinic_name', 'value' => 'Uzazi Clinic', 'group' => 'general', 'type' => 'text'],
            ['key' => 'clinic_phone', 'value' => '+255 700 000 000', 'group' => 'general', 'type' => 'text'],
            ['key' => 'clinic_email', 'value' => 'info@drissa.test', 'group' => 'general', 'type' => 'text'],
            ['key' => 'clinic_address', 'value' => 'Dar es Salaam, Tanzania', 'group' => 'general', 'type' => 'textarea'],
            ['key' => 'currency', 'value' => 'TSh', 'group' => 'general', 'type' => 'text'],
            ['key' => 'consultation_fee', 'value' => '10000', 'group' => 'general', 'type' => 'number'],

            ['key' => 'mail_from', 'value' => 'noreply@drissa.test', 'group' => 'email', 'type' => 'text'],
            ['key' => 'mail_host', 'value' => 'smtp.mailtrap.io', 'group' => 'email', 'type' => 'text'],
            ['key' => 'mail_port', 'value' => '2525', 'group' => 'email', 'type' => 'text'],
            ['key' => 'mail_username', 'value' => '', 'group' => 'email', 'type' => 'text'],
            ['key' => 'mail_password', 'value' => '', 'group' => 'email', 'type' => 'text'],
            ['key' => 'mail_encryption', 'value' => 'tls', 'group' => 'email', 'type' => 'text'],

            ['key' => 'sms_gateway', 'value' => 'log', 'group' => 'sms', 'type' => 'text'],
            ['key' => 'sms_sender_id', 'value' => 'UZAZICLINIC', 'group' => 'sms', 'type' => 'text'],
            ['key' => 'sms_api_key', 'value' => '', 'group' => 'sms', 'type' => 'text'],
            ['key' => 'twilio_sid', 'value' => '', 'group' => 'sms', 'type' => 'text'],
            ['key' => 'twilio_token', 'value' => '', 'group' => 'sms', 'type' => 'text'],
            ['key' => 'twilio_from', 'value' => '', 'group' => 'sms', 'type' => 'text'],
            ['key' => 'sms_http_url', 'value' => '', 'group' => 'sms', 'type' => 'text'],
            ['key' => 'sms_http_method', 'value' => 'POST', 'group' => 'sms', 'type' => 'text'],

            ['key' => 'payment_gateway', 'value' => 'cash', 'group' => 'payment', 'type' => 'text'],
            ['key' => 'payment_api_key', 'value' => '', 'group' => 'payment', 'type' => 'text'],
            ['key' => 'payment_api_secret', 'value' => '', 'group' => 'payment', 'type' => 'text'],
            ['key' => 'payment_merchant_id', 'value' => '', 'group' => 'payment', 'type' => 'text'],
            ['key' => 'payment_callback_url', 'value' => '', 'group' => 'payment', 'type' => 'text'],
            ['key' => 'payment_bank_name', 'value' => '', 'group' => 'payment', 'type' => 'text'],
            ['key' => 'payment_bank_account', 'value' => '', 'group' => 'payment', 'type' => 'text'],
        ];

        foreach ($settings as $setting) {
            Setting::firstOrCreate(['key' => $setting['key']], $setting);
        }

        EmailTemplate::firstOrCreate(
            ['slug' => 'welcome-patient'],
            [
                'name' => 'Welcome Patient',
                'subject' => 'Welcome to Uzazi Clinic',
                'body' => 'Dear patient, welcome to our hospital. Your MRN is {{mrn}}.',
            ]
        );

        SmsTemplate::firstOrCreate(
            ['name' => 'Appointment Reminder'],
            [
                'type' => 'appointment',
                'body' => 'Uzazi Clinic\nKaribu {{name}}!\nMiadi yako imewekwa kwa mafanikio.\nTarehe: {{date}}\nSaa: {{time}}\nMRN: {{mrn}}\nTafadhali fika dakika 15 kabla ya muda wako.\nKwa maswali piga: {{phone}}.\nAsante kwa kuchagua Uzazi Clinic.',
                'is_active' => true,
            ]
        );
    }
}
