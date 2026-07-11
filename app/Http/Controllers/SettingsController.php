<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Setting;
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
        foreach ($request->except('_token') as $key => $value) {
            $setting = Setting::where('key', $key)->first();
            if ($setting) {
                $setting->update(['value' => $value]);
            }
        }

        ActivityLog::log('settings_updated', null, 'System settings updated');

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
}
