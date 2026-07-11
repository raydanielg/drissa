@extends('layouts.dashboard')

@section('title', 'SMS Gateway - ' . config('app.name', 'Laravel'))
@section('page_title', 'SMS Gateway')

@section('content')
<div class="max-w-4xl mx-auto bg-white rounded-xl border border-gray-100 p-6 shadow-sm">
    <form method="POST" action="{{ route('settings.update') }}" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        @csrf
        @method('PUT')

        <div class="sm:col-span-2">
            <label class="block text-xs font-medium text-gray-700 mb-1">SMS Gateway</label>
            <select name="sms_gateway" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
                <option value="log" {{ ($settings['sms_gateway']->value ?? 'log') === 'log' ? 'selected' : '' }}>Log Only (Testing)</option>
                <option value="twilio" {{ ($settings['sms_gateway']->value ?? '') === 'twilio' ? 'selected' : '' }}>Twilio</option>
                <option value="http" {{ ($settings['sms_gateway']->value ?? '') === 'http' ? 'selected' : '' }}>Custom HTTP Gateway</option>
            </select>
        </div>

        <div class="sm:col-span-2">
            <label class="block text-xs font-medium text-gray-700 mb-1">Sender ID</label>
            <input type="text" name="sms_sender_id" value="{{ $settings['sms_sender_id']->value ?? '' }}" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
        </div>

        <div class="sm:col-span-2 border-t border-gray-100 pt-4 mt-2">
            <h3 class="text-xs font-semibold text-gray-900 mb-2">Twilio Settings</h3>
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-700 mb-1">Twilio SID</label>
            <input type="text" name="twilio_sid" value="{{ $settings['twilio_sid']->value ?? '' }}" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-700 mb-1">Twilio Auth Token</label>
            <input type="text" name="twilio_token" value="{{ $settings['twilio_token']->value ?? '' }}" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
        </div>
        <div class="sm:col-span-2">
            <label class="block text-xs font-medium text-gray-700 mb-1">Twilio From Number</label>
            <input type="text" name="twilio_from" value="{{ $settings['twilio_from']->value ?? '' }}" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
        </div>

        <div class="sm:col-span-2 border-t border-gray-100 pt-4 mt-2">
            <h3 class="text-xs font-semibold text-gray-900 mb-2">Custom HTTP Gateway</h3>
        </div>
        <div class="sm:col-span-2">
            <label class="block text-xs font-medium text-gray-700 mb-1">HTTP URL</label>
            <input type="text" name="sms_http_url" value="{{ $settings['sms_http_url']->value ?? '' }}" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm" placeholder="https://api.example.com/send">
        </div>
        <div class="sm:col-span-2">
            <label class="block text-xs font-medium text-gray-700 mb-1">HTTP Method</label>
            <select name="sms_http_method" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
                <option value="POST" {{ ($settings['sms_http_method']->value ?? 'POST') === 'POST' ? 'selected' : '' }}>POST</option>
                <option value="GET" {{ ($settings['sms_http_method']->value ?? '') === 'GET' ? 'selected' : '' }}>GET</option>
            </select>
        </div>

        <div class="sm:col-span-2 pt-4">
            <button type="submit" class="bg-emerald-600 text-white text-sm font-medium px-5 py-2 rounded-lg hover:bg-emerald-700">Save SMS Settings</button>
        </div>
    </form>
</div>
@endsection
