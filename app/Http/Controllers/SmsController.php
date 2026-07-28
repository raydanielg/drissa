<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\SmsLog;
use App\Models\SmsTemplate;
use App\Models\User;
use App\Services\SmsService;
use Illuminate\Http\Request;

class SmsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $templates = SmsTemplate::where('is_active', true)->get();
        $users = User::whereNotNull('phone')->get();
        $patients = Patient::whereNotNull('phone')->get();
        return view('sms.index', compact('templates', 'users', 'patients'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'recipient_type' => 'required|in:user,patient,manual',
            'user_id' => 'nullable|exists:users,id',
            'patient_id' => 'nullable|exists:patients,id',
            'phone' => 'nullable|string|max:20',
            'message' => 'required|string|max:1600',
        ]);

        $phone = null;
        $recipient = null;

        if ($data['recipient_type'] === 'user' && $data['user_id']) {
            $user = User::find($data['user_id']);
            $phone = $user?->phone;
            $recipient = $user?->name;
        } elseif ($data['recipient_type'] === 'patient' && $data['patient_id']) {
            $patient = Patient::find($data['patient_id']);
            $phone = $patient?->phone;
            $recipient = $patient?->fullName();
        } else {
            $phone = $data['phone'];
        }

        if (! $phone) {
            return back()->withErrors(['phone' => 'No phone number found for recipient.'])->withInput();
        }

        $result = SmsService::send($phone, $data['message'], auth()->user(), $recipient);

        if ($result['success']) {
            return back()->with('status', 'SMS sent successfully.');
        }

        return back()->with('error', 'Failed to send SMS: ' . ($result['error'] ?? 'Unknown error'))->withInput();
    }

    public function logs()
    {
        $logs = SmsLog::with('user')->latest()->paginate(25);
        return view('sms.logs', compact('logs'));
    }

    public function templates()
    {
        $templates = SmsTemplate::latest()->paginate(20);
        return view('sms.templates', compact('templates'));
    }

    public function storeTemplate(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'subject' => 'nullable|string|max:255',
            'body' => 'required|string|max:1600',
            'type' => 'required|in:appointment,payment,lab,general,birthday,holiday,pharmacy,visit,doctor,registration,reminder,marketing',
        ]);
        $data['is_active'] = true;
        SmsTemplate::create($data);
        return back()->with('status', 'Template saved.');
    }

    public function updateTemplate(Request $request, SmsTemplate $template)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'subject' => 'nullable|string|max:255',
            'body' => 'required|string|max:1600',
            'type' => 'required|in:appointment,payment,lab,general,birthday,holiday,pharmacy,visit,doctor,registration,reminder,marketing',
            'is_active' => 'boolean',
        ]);
        $data['is_active'] = $request->boolean('is_active', true);
        $template->update($data);
        return back()->with('status', 'Template updated.');
    }

    public function destroyTemplate(SmsTemplate $template)
    {
        $template->delete();
        return back()->with('status', 'Template deleted.');
    }
}
