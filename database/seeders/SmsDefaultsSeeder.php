<?php

namespace Database\Seeders;

use App\Models\Setting;
use App\Models\SmsTemplate;
use Illuminate\Database\Seeder;

class SmsDefaultsSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            ['key' => 'sms_gateway', 'value' => 'log', 'group' => 'sms', 'type' => 'text'],
            ['key' => 'sms_sender_id', 'value' => 'UZAZICLINIC', 'group' => 'sms', 'type' => 'text'],
            ['key' => 'twilio_sid', 'value' => '', 'group' => 'sms', 'type' => 'text'],
            ['key' => 'twilio_token', 'value' => '', 'group' => 'sms', 'type' => 'text'],
            ['key' => 'twilio_from', 'value' => '', 'group' => 'sms', 'type' => 'text'],
            ['key' => 'sms_http_url', 'value' => '', 'group' => 'sms', 'type' => 'text'],
            ['key' => 'sms_http_method', 'value' => 'POST', 'group' => 'sms', 'type' => 'text'],
            ['key' => 'nextsms_from', 'value' => 'UZAZICLINIC', 'group' => 'sms', 'type' => 'text'],
            ['key' => 'nextsms_username', 'value' => '', 'group' => 'sms', 'type' => 'text'],
            ['key' => 'nextsms_password', 'value' => '', 'group' => 'sms', 'type' => 'text'],
            ['key' => 'nextsms_url', 'value' => 'https://messaging-service.co.tz/api/sms/v1/text/single', 'group' => 'sms', 'type' => 'text'],
        ];

        foreach ($settings as $setting) {
            Setting::firstOrCreate(['key' => $setting['key']], $setting);
        }

        $templates = [
            ['name' => 'Appointment Reminder', 'type' => 'appointment', 'body' => 'Uzazi Clinic\nKaribu {{name}}!\nMiadi yako imewekwa kwa mafanikio.\nTarehe: {{date}}\nSaa: {{time}}\nMRN: {{mrn}}\nTafadhali fika dakika 15 kabla ya muda wako.\nKwa maswali piga: {{phone}}.\nAsante kwa kuchagua Uzazi Clinic.'],
            ['name' => 'Payment Reminder', 'type' => 'payment', 'body' => 'Hi {{name}}, this is a reminder for your outstanding payment of {{amount}} at Uzazi Clinic.'],
            ['name' => 'Lab Results Ready', 'type' => 'lab', 'body' => 'Hi {{name}}, your lab results are ready. Please visit Uzazi Clinic to collect them.'],
            ['name' => 'Welcome Patient', 'type' => 'general', 'body' => 'Uzazi Clinic\nKaribu {{name}}!\nUmesajiliwa kwa mafanikio.\nMRN yako ni: {{mrn}}\nHifadhi namba hii kwa matumizi ya baadaye.\nKwa maswali piga: {{phone}}.\nAsante kwa kuchagua Uzazi Clinic.'],
        ];

        foreach ($templates as $template) {
            SmsTemplate::firstOrCreate(
                ['name' => $template['name']],
                array_merge($template, ['is_active' => true])
            );
        }
    }
}
