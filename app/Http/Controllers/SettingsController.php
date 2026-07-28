<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Setting;
use App\Services\SmsService;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $settings = Setting::orderBy('group')->orderBy('key')->get()->groupBy('group');
        return view('settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $updated = 0;
        foreach ($request->except('_token', '_method') as $key => $value) {
            $setting = Setting::where('key', $key)->first();
            if ($setting) {
                $setting->update(['value' => $value]);
                $updated++;
            } else {
                Setting::create([
                    'key' => $key,
                    'value' => $value,
                    'group' => 'general',
                    'type' => 'text',
                ]);
                $updated++;
            }
        }

        ActivityLog::log('settings_updated', null, 'System settings updated (' . $updated . ' fields)');

        if ($request->ajax() || $request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Settings saved successfully!']);
        }

        return back()->with('status', 'Settings saved.');
    }

    public function email()
    {
        $settings = Setting::where('group', 'email')->get()->keyBy('key');
        return view('settings.email', compact('settings'));
    }

    public function sms()
    {
        $settings = Setting::where('group', 'sms')->get()->keyBy('key');
        return view('settings.sms', compact('settings'));
    }

    public function testSms(Request $request)
    {
        $data = $request->validate([
            'phone' => 'required|string|max:20',
            'message' => 'required|string|max:1600',
        ]);

        $result = SmsService::send($data['phone'], $data['message'], auth()->user(), 'Test Recipient');

        return response()->json($result);
    }

    public function payment()
    {
        $settings = Setting::where('group', 'payment')->get()->keyBy('key');
        return view('settings.payment', compact('settings'));
    }
}
