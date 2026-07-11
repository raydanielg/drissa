<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\EmailTemplate;
use App\Models\Patient;
use App\Models\SmsTemplate;
use App\Models\User;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $emailTemplates = EmailTemplate::where('is_active', true)->get();
        $smsTemplates = SmsTemplate::where('is_active', true)->get();
        return view('notifications.index', compact('emailTemplates', 'smsTemplates'));
    }

    public function send(Request $request)
    {
        $data = $request->validate([
            'type' => 'required|in:email,sms',
            'recipients' => 'required|array',
            'recipients.*' => 'required|string',
            'subject' => 'nullable|string|max:255',
            'message' => 'required|string',
        ]);

        ActivityLog::log('notification_sent', null, "Sent {$data['type']} to " . count($data['recipients']) . " recipients");

        return back()->with('status', 'Notification queued for sending.');
    }

    public function templates()
    {
        $emailTemplates = EmailTemplate::latest()->get();
        $smsTemplates = SmsTemplate::latest()->get();
        return view('notifications.templates', compact('emailTemplates', 'smsTemplates'));
    }

    public function storeTemplate(Request $request)
    {
        $data = $request->validate([
            'type' => 'required|in:email,sms',
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:email_templates,slug|unique:sms_templates,slug',
            'subject' => 'nullable|string|max:255',
            'body' => 'required|string',
        ]);

        if ($data['type'] === 'email') {
            EmailTemplate::create([
                'name' => $data['name'],
                'slug' => $data['slug'],
                'subject' => $data['subject'] ?? '',
                'body' => $data['body'],
            ]);
        } else {
            SmsTemplate::create([
                'name' => $data['name'],
                'slug' => $data['slug'],
                'body' => $data['body'],
            ]);
        }

        return back()->with('status', 'Template saved.');
    }
}
