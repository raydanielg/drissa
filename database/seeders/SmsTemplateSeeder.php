<?php

namespace Database\Seeders;

use App\Models\SmsTemplate;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class SmsTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $clinic = 'Uzazi Clinic';

        $templates = [
            // ── Registration ──
            [
                'name' => 'Welcome New Patient',
                'type' => 'registration',
                'subject' => 'Karibu Kliniki',
                'body' => "Karibu {{clinic}}! Umesajiliwa kwa mafanikio. MRN yako ni {{mrn}}. Hifadhi namba hii kwa matumizi ya baadaye. Kwa maswali piga {{phone}}.",
            ],
            [
                'name' => 'Patient Registration Confirmation (English)',
                'type' => 'registration',
                'subject' => 'Registration Confirmation',
                'body' => "Welcome to {{clinic}}! You have been successfully registered. Your MRN is {{mrn}}. Save this number for future visits. For inquiries call {{phone}}.",
            ],

            // ── Appointment ──
            [
                'name' => 'Appointment Reminder (Swahili)',
                'type' => 'appointment',
                'subject' => 'Kumbusho la Miadi',
                'body' => "Habari {{name}}, una miadi kesho saa {{time}} kwenye {{clinic}}. Tafadhali fika dakika 15 kabla. Kwa bahati mbaya unashindwa kuja, tujulishe mapema {{phone}}.",
            ],
            [
                'name' => 'Appointment Reminder (English)',
                'type' => 'appointment',
                'subject' => 'Appointment Reminder',
                'body' => "Hi {{name}}, this is a reminder for your appointment tomorrow at {{time}} at {{clinic}}. Please arrive 15 minutes early. If you cannot make it, please call {{phone}}.",
            ],
            [
                'name' => 'Appointment Confirmation',
                'type' => 'appointment',
                'subject' => 'Miadi Imethibitishwa',
                'body' => "Habari {{name}}, miadi yako ya {{date}} saa {{time}} imethibitishwa. Tunakutegemea. Asante kwa kuchagua {{clinic}}.",
            ],
            [
                'name' => 'Appointment Cancellation',
                'type' => 'appointment',
                'subject' => 'Miadi Imefutwa',
                'body' => "Habari {{name}}, miadi yako ya {{date}} imefutwa. Tafadhali pigia namba {{phone}} kupanga miadi mpya. Samahani kwa usumbufu.",
            ],
            [
                'name' => 'Appointment Rescheduled',
                'type' => 'appointment',
                'subject' => 'Miadi Imebadilishwa',
                'body' => "Habari {{name}}, miadi yako imebadilishwa kuwa {{date}} saa {{time}}. Kama hali hii haifai, tafadhali tupigie {{phone}}.",
            ],
            [
                'name' => 'Follow-up Appointment Reminder',
                'type' => 'appointment',
                'subject' => 'Kumbusho la Ufuatiliaji',
                'body' => "Habari {{name}}, hii ni kumbusho la miadi yako ya ufuatiliaji tarehe {{date}} saa {{time}}. Ni muhimu kufika kwa uhakika wa afya yako. {{clinic}}.",
            ],

            // ── Visit ──
            [
                'name' => 'Visit Registration Confirmation',
                'type' => 'visit',
                'subject' => 'Usajili wa Ziara',
                'body' => "Habari {{name}}, ziara yako imesajiliwa. Namba ya ziara ni {{visit_number}}. Utaitwa na muuguzi hivi karibuni. Asante kwa subiri. {{clinic}}.",
            ],
            [
                'name' => 'Visit Status Update - With Doctor',
                'type' => 'visit',
                'subject' => 'Daktari Anakuita',
                'body' => "Habari {{name}}, daktari {{doctor}} anakuita sasa. Tafadhali ingia kwenye chumba cha matibabu. {{clinic}}.",
            ],
            [
                'name' => 'Visit Completed',
                'type' => 'visit',
                'subject' => 'Ziara Imekamilika',
                'body' => "Habari {{name}}, ziara yako imekamilika. Kama una maswali au unahitaji miadi ya ufuatiliaji, tupigie {{phone}}. {{clinic}}.",
            ],
            [
                'name' => 'Visit Cancelled - No Show',
                'type' => 'visit',
                'subject' => 'Ziara Imefutwa',
                'body' => "Habari {{name}}, ziara yako ya leo imefutwa kwa sababu hukuja. Tafadhali pigia {{phone}} kupanga ziara nyingine. {{clinic}}.",
            ],

            // ── Doctor ──
            [
                'name' => 'Doctor Assignment Notification',
                'type' => 'doctor',
                'subject' => 'Daktari Wako',
                'body' => "Habari {{name}}, umekabidhiwa kwa Daktari {{doctor}}. Utaonekana hivi karibuni. Asante kwa subiri yako. {{clinic}}.",
            ],
            [
                'name' => 'Doctor Change Notification',
                'type' => 'doctor',
                'subject' => 'Mabadiliko ya Daktari',
                'body' => "Habari {{name}}, daktari wako amebadilishwa kutoka {{old_doctor}} kwenda {{new_doctor}}. Kama una swali tafadhali uliza muuguzi. {{clinic}}.",
            ],

            // ── Lab ──
            [
                'name' => 'Lab Results Ready (Swahili)',
                'type' => 'lab',
                'subject' => 'Matokeo ya Maabara',
                'body' => "Habari {{name}}, matokeo yako ya maabara yametayarishwa. Tafadhali fika kwenye {{clinic}} kuyaleta. Matokeo hayatumiwi kwa SMS kwa sababu za usiri.",
            ],
            [
                'name' => 'Lab Results Ready (English)',
                'type' => 'lab',
                'subject' => 'Lab Results Ready',
                'body' => "Hi {{name}}, your lab results are ready. Please visit {{clinic}} to collect them. Results are not sent via SMS for confidentiality reasons.",
            ],
            [
                'name' => 'Lab Order Notification',
                'type' => 'lab',
                'subject' => 'Agizo la Maabara',
                'body' => "Habari {{name}}, daktari ameagiza vipimo vya maabara. Tafadhali nenda kwenye maabara kwa ajili ya kutoa sampuli. {{clinic}}.",
            ],
            [
                'name' => 'Lab Sample Collected',
                'type' => 'lab',
                'subject' => 'Sampuli Imechukuliwa',
                'body' => "Habari {{name}}, sampuli yako imechukuliwa kwa mafanikio. Matokeo yatapatikana ndani ya masaa {{hours}}. {{clinic}}.",
            ],

            // ── Pharmacy ──
            [
                'name' => 'Prescription Ready for Dispensing',
                'type' => 'pharmacy',
                'subject' => 'Dawa Zako Zimeandaliwa',
                'body' => "Habari {{name}}, dawa zako zimeandaliwa na ziko tayari kwenye famasi. Tafadhali fika kuzichukua. {{clinic}}.",
            ],
            [
                'name' => 'Prescription Dispensed',
                'type' => 'pharmacy',
                'subject' => 'Dawa Zimekabidhiwa',
                'body' => "Habari {{name}}, dawa zako zimekabidhiwa. Tumia kama ilivyoagizwa na daktari. Kama una dalili za upungufu, rudi kwenye kliniki. {{clinic}}.",
            ],
            [
                'name' => 'Medication Out of Stock',
                'type' => 'pharmacy',
                'subject' => 'Dawa Haipo',
                'body' => "Habari {{name}}, dawa {{medication}} haipo kwa sasa. Tutaifahamu mara itakapopatikana. Samahani kwa usumbufu. {{clinic}}.",
            ],

            // ── Payment ──
            [
                'name' => 'Payment Reminder (Swahili)',
                'type' => 'payment',
                'subject' => 'Kumbusho la Malipo',
                'body' => "Habari {{name}}, hii ni kumbusho ya deni lako la TSh {{amount}} kwenye {{clinic}}. Tafadhali fika kulipa mapema. Asante.",
            ],
            [
                'name' => 'Payment Reminder (English)',
                'type' => 'payment',
                'subject' => 'Payment Reminder',
                'body' => "Hi {{name}}, this is a reminder for your outstanding payment of TSh {{amount}} at {{clinic}}. Please visit us to settle your bill. Thank you.",
            ],
            [
                'name' => 'Payment Confirmation',
                'type' => 'payment',
                'subject' => 'Malipo Yamepokelewa',
                'body' => "Habari {{name}}, tumepokea malipo yako ya TSh {{amount}}. Risiti yako ni {{receipt}}. Asante kwa kulipa kwa wakati. {{clinic}}.",
            ],
            [
                'name' => 'Invoice Generated',
                'type' => 'payment',
                'subject' => 'Ankara Imetengenezwa',
                'body' => "Habari {{name}}, ankara yako ya TSh {{amount}} imetengenezwa. Tafadhali fika kwenye {{clinic}} kulipa. Asante.",
            ],
            [
                'name' => 'Payment Overdue',
                'type' => 'payment',
                'subject' => 'Deni Limepitiliza',
                'body' => "Habari {{name}}, deni lako la TSh {{amount}} limepitiliza muda wake. Tafadhali fika kwenye {{clinic}} mapema kuliisha. Asante.",
            ],

            // ── Birthday ──
            [
                'name' => 'Birthday Wishes (Swahili)',
                'type' => 'birthday',
                'subject' => 'Heri ya Kuzaliwa',
                'body' => "Habari {{name}}, {{clinic}} inakutakia heri njema za kuzaliwa! Tunakuombea afya njema, furaha na mafanikio. Asante kwa kuwa mteja wetu.",
            ],
            [
                'name' => 'Birthday Wishes (English)',
                'type' => 'birthday',
                'subject' => 'Happy Birthday',
                'body' => "Happy Birthday {{name}}! {{clinic}} wishes you a wonderful day filled with joy and good health. Thank you for being our valued patient.",
            ],
            [
                'name' => 'Birthday with Discount Offer',
                'type' => 'birthday',
                'subject' => 'Heri ya Kuzaliwa + Punguzo',
                'body' => "Heri njema za kuzaliwa {{name}}! Kama zawadi, tunakupa punguzo la {{discount}}% kwa huduma zote wiki hii. Tumia namba {{code}}. {{clinic}}.",
            ],

            // ── Holiday ──
            [
                'name' => 'Christmas Greetings',
                'type' => 'holiday',
                'subject' => 'Heri ya Krismasi',
                'body' => "{{clinic}} inakutakia Krismasi njema na Mwaka Mpya wa Furaha! Tunaombea afya njema na amani kwa ajili yako na familia yako.",
            ],
            [
                'name' => 'Eid Mubarak Greetings',
                'type' => 'holiday',
                'subject' => 'Eid Mubarak',
                'body' => "Eid Mubarak {{name}}! {{clinic}} inakutakia Idd njema yenye baraka na furaha. Asante kwa kuwa pamoja nasi.",
            ],
            [
                'name' => 'New Year Greetings',
                'type' => 'holiday',
                'subject' => 'Mwaka Mpya Njema',
                'body' => "Mwaka Mpya Njema {{name}}! {{clinic}} inakutakia mwaka wenye afya njema, furaha na mafanikio. Tuko pamoja nawe katika safari hii.",
            ],
            [
                'name' => 'Easter Greetings',
                'type' => 'holiday',
                'subject' => 'Heri ya Pasaka',
                'body' => "Heri njema za Pasaka {{name}}! {{clinic}} inakutakia furaha na amani wakati huu mtukufu. Asante kwa kuwa mteja wetu.",
            ],
            [
                'name' => 'Independence Day Greetings',
                'type' => 'holiday',
                'subject' => 'Heri ya Uhuru',
                'body' => "Heri njema za Siku ya Uhuru {{name}}! {{clinic}} inakutakia siku yenye furaha na kumbukumbu nzuri. Tuko wajibu wako kwa afya yako.",
            ],
            [
                'name' => 'Holiday Clinic Hours Notice',
                'type' => 'holiday',
                'subject' => 'Saa za Kliniki Sikukuu',
                'body' => "Habari {{name}}, kwa ajili ya sikukuu ya {{holiday}}, {{clinic}} itafungua saa {{hours}}. Kwa dharura piga {{phone}}. Asante.",
            ],

            // ── General ──
            [
                'name' => 'Clinic Closure Notice',
                'type' => 'general',
                'subject' => 'Kliniki Imefungwa',
                'body' => "Habari {{name}}, {{clinic}} itafungwa tarehe {{date}} kwa sababu za {{reason}}. Tutafungua tena tarehe {{reopen_date}}. Kwa dharura piga {{phone}}.",
            ],
            [
                'name' => 'New Service Announcement',
                'type' => 'general',
                'subject' => 'Huduma Mpya',
                'body' => "Habari {{name}}, {{clinic}} imelaunch huduma mpya ya {{service}}! Fika kwenye kliniki kwa maelezo zaidi au piga {{phone}}.",
            ],
            [
                'name' => 'Health Tips Weekly',
                'type' => 'general',
                'subject' => 'Ushauri wa Afya',
                'body' => "Lishe bora ni msingi wa afya njema! {{clinic}} inakushauri kunywa maji mengi, kula matunda na mboga, na fanya mazoezi kila siku. Afya yako ni utajiri wako.",
            ],
            [
                'name' => 'Flu Season Reminder',
                'type' => 'general',
                'subject' => 'Kumbusho - Msimu wa Mafua',
                'body' => "Habari {{name}}, msimu wa mafua unakaribia. {{clinic}} inakushauri kulinda afya yako. Fika kwa chanjo na ushauri zaidi. Piga {{phone}}.",
            ],

            // ── Reminder ──
            [
                'name' => 'Medication Reminder',
                'type' => 'reminder',
                'subject' => 'Kumbusho la Dawa',
                'body' => "Habari {{name}}, hii ni kumbusho la kuchukua dawa yako ya {{medication}}. Dozi: {{dosage}}. Tumia kama ilivyoagizwa. {{clinic}}.",
            ],
            [
                'name' => 'Checkup Reminder',
                'type' => 'reminder',
                'subject' => 'Kumbusho la Uchunguzi',
                'body' => "Habari {{name}}, ni wakati wa uchunguzi wa mara kwa mara. Afya yako ni muhimu. Tafadhali panga miadi kwa {{clinic}}. Piga {{phone}}.",
            ],
            [
                'name' => 'Vaccination Reminder',
                'type' => 'reminder',
                'subject' => 'Kumbusho la Chanjo',
                'body' => "Habari {{name}}, chanjo yako inakaribia kufika muda wake. Tafadhali fika kwenye {{clinic}} ndani ya siku {{days}}. Piga {{phone}}.",
            ],
            [
                'name' => 'Antenatal Visit Reminder',
                'type' => 'reminder',
                'subject' => 'Kumbusho la Antenatal',
                'body' => "Habari {{name}}, hii ni kumbusho ya ziara yako ya antenatal tarehe {{date}}. Ni muhimu kwa afya yako na mtoto wako. {{clinic}}.",
            ],

            // ── Marketing ──
            [
                'name' => 'Discount Promotion',
                'type' => 'marketing',
                'subject' => 'Punguzo Maalum',
                'body' => "Habari {{name}}, {{clinic}} inatoa punguzo la {{discount}}% kwa huduma zote za {{service}} mwezi huu! Fika mapema kabla ya ofsi kumalizika. Piga {{phone}}.",
            ],
            [
                'name' => 'Free Consultation Offer',
                'type' => 'marketing',
                'subject' => 'Ushauri wa Bure',
                'body' => "Habari {{name}}, {{clinic}} inatoa ushauri wa bure kwa wiki hii! Fika kwa nafasi fupi. Piga {{phone}} kupanga miadi. Asante.",
            ],
            [
                'name' => 'Health Screening Camp',
                'type' => 'marketing',
                'subject' => 'Kampeni ya Uchunguzi',
                'body' => "Habari {{name}}, {{clinic}} itafanya kampeni ya uchunguzi wa afya tarehe {{date}}. Huduma zote ni nafuu! Fika mapema. Piga {{phone}}.",
            ],
            [
                'name' => 'Referral Program',
                'type' => 'marketing',
                'subject' => 'Programu ya Rufaa',
                'body' => "Habari {{name}}, mpe rafiki yako rufaa kwa {{clinic}} na upate punguzo la {{discount}}% kwa ziara ijayo! Piga {{phone}} kwa maelezo. Asante.",
            ],
        ];

        foreach ($templates as $template) {
            $template['body'] = str_replace('{{clinic}}', $clinic, $template['body']);
            $template['slug'] = Str::slug($template['name']);

            SmsTemplate::firstOrCreate(
                ['name' => $template['name']],
                array_merge($template, ['is_active' => true])
            );
        }
    }
}
