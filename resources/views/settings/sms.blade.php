@extends('layouts.dashboard')

@section('title', 'SMS Gateway - ' . config('app.name', 'Laravel'))
@section('page_title', 'SMS Gateway Configuration')

@section('content')
<div class="max-w-4xl mx-auto space-y-5">

    <script>
    function toggleGatewaySections() {
        const gateway = document.getElementById('smsGateway').value;
        document.getElementById('nextsms-section').classList.toggle('hidden', gateway !== 'nextsms');
        document.getElementById('twilio-section').classList.toggle('hidden', gateway !== 'twilio');
        document.getElementById('http-section').classList.toggle('hidden', gateway !== 'http');
    }
    document.getElementById('smsGateway').addEventListener('change', toggleGatewaySections);
    toggleGatewaySections();

    document.getElementById('testSmsForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const btn = document.getElementById('testSmsBtn');
        const result = document.getElementById('testResult');
        const phone = document.getElementById('testPhone').value;
        const message = document.getElementById('testMessage').value;

        btn.disabled = true;
        btn.innerHTML = '<svg class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke-width="3" class="opacity-25"/><path d="M4 12a8 8 0 018-8" stroke-width="3" class="opacity-75"/></svg> Sending...';
        result.classList.add('hidden');

        fetch('{{ route("settings.sms.test") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
            },
            body: JSON.stringify({ phone, message }),
        })
        .then(res => res.json().then(data => ({ status: res.status, data })))
        .then(({ status, data }) => {
            result.classList.remove('hidden');
            if (data.success) {
                result.innerHTML = '<div class="bg-white border-l-2 border-emerald-500 rounded-md p-3 flex items-center gap-3"><i class="hgi-stroke hgi-checkmark-circle-02 text-base text-emerald-600"></i><div><p class="text-xs font-bold text-emerald-700">SMS Sent Successfully!</p><p class="text-[10px] text-emerald-600 mt-0.5">' + (data.message || '') + '</p></div></div>';
            } else {
                result.innerHTML = '<div class="bg-white border-l-2 border-red-500 rounded-md p-3 flex items-center gap-3"><i class="hgi-stroke hgi-alert-circle text-base text-red-600"></i><div><p class="text-xs font-bold text-red-700">SMS Failed</p><p class="text-[10px] text-red-600 mt-0.5">' + (data.error || data.message || 'Unknown error') + '</p></div></div>';
            }
        })
        .catch(err => {
            result.classList.remove('hidden');
            result.innerHTML = '<div class="bg-white border-l-2 border-red-500 rounded-md p-3"><p class="text-xs font-bold text-red-700">Network error: ' + err.message + '</p></div>';
        })
        .finally(() => {
            btn.disabled = false;
            btn.innerHTML = '<i class="hgi-stroke hgi-sent text-base"></i> Send Test SMS';
        });
    });
</script>
    <div class="bg-slate-800 rounded-md p-6 text-white">
        <p class="text-[10px] uppercase tracking-wider text-slate-400 mb-1">Settings</p>
        <h1 class="text-xl font-bold">SMS Gateway Configuration</h1>
        <p class="text-xs text-slate-400 mt-1">Configure how the system sends SMS messages to patients and staff.</p>
    </div>

    @if(session('status'))
        <div class="bg-white border-l-2 border-emerald-500 rounded-md p-3 flex items-center gap-3">
            <i class="hgi-stroke hgi-checkmark-circle-02 text-base text-emerald-600"></i>
            <p class="text-xs font-semibold text-emerald-700">{{ session('status') }}</p>
        </div>
    @endif
    @if(session('error'))
        <div class="bg-white border-l-2 border-red-500 rounded-md p-3 flex items-center gap-3">
            <i class="hgi-stroke hgi-alert-circle text-base text-red-600"></i>
            <p class="text-xs font-semibold text-red-700">{{ session('error') }}</p>
        </div>
    @endif

    <script>
    function toggleGatewaySections() {
        const gateway = document.getElementById('smsGateway').value;
        document.getElementById('nextsms-section').classList.toggle('hidden', gateway !== 'nextsms');
        document.getElementById('twilio-section').classList.toggle('hidden', gateway !== 'twilio');
        document.getElementById('http-section').classList.toggle('hidden', gateway !== 'http');
    }
    document.getElementById('smsGateway').addEventListener('change', toggleGatewaySections);
    toggleGatewaySections();

    document.getElementById('testSmsForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const btn = document.getElementById('testSmsBtn');
        const result = document.getElementById('testResult');
        const phone = document.getElementById('testPhone').value;
        const message = document.getElementById('testMessage').value;

        btn.disabled = true;
        btn.innerHTML = '<svg class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke-width="3" class="opacity-25"/><path d="M4 12a8 8 0 018-8" stroke-width="3" class="opacity-75"/></svg> Sending...';
        result.classList.add('hidden');

        fetch('{{ route("settings.sms.test") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
            },
            body: JSON.stringify({ phone, message }),
        })
        .then(res => res.json().then(data => ({ status: res.status, data })))
        .then(({ status, data }) => {
            result.classList.remove('hidden');
            if (data.success) {
                result.innerHTML = '<div class="bg-white border-l-2 border-emerald-500 rounded-md p-3 flex items-center gap-3"><i class="hgi-stroke hgi-checkmark-circle-02 text-base text-emerald-600"></i><div><p class="text-xs font-bold text-emerald-700">SMS Sent Successfully!</p><p class="text-[10px] text-emerald-600 mt-0.5">' + (data.message || '') + '</p></div></div>';
            } else {
                result.innerHTML = '<div class="bg-white border-l-2 border-red-500 rounded-md p-3 flex items-center gap-3"><i class="hgi-stroke hgi-alert-circle text-base text-red-600"></i><div><p class="text-xs font-bold text-red-700">SMS Failed</p><p class="text-[10px] text-red-600 mt-0.5">' + (data.error || data.message || 'Unknown error') + '</p></div></div>';
            }
        })
        .catch(err => {
            result.classList.remove('hidden');
            result.innerHTML = '<div class="bg-white border-l-2 border-red-500 rounded-md p-3"><p class="text-xs font-bold text-red-700">Network error: ' + err.message + '</p></div>';
        })
        .finally(() => {
            btn.disabled = false;
            btn.innerHTML = '<i class="hgi-stroke hgi-sent text-base"></i> Send Test SMS';
        });
    });
</script>
    <div class="bg-white rounded-md border border-gray-200 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100">
            <h2 class="text-sm font-bold text-gray-900">Gateway Settings</h2>
            <p class="text-xs text-gray-500 mt-0.5">Configure how the system sends SMS messages to patients and staff.</p>
        </div>

        <form method="POST" action="{{ route('settings.update') }}" class="p-5 space-y-5" data-ajax>
            @csrf
            @method('PUT')

            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1.5">SMS Gateway</label>
                <x-ui.select name="sms_gateway" id="smsGateway" class="w-full border border-gray-200 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-slate-300 outline-none">
                    <option value="log" {{ ($settings['sms_gateway']->value ?? 'log') === 'log' ? 'selected' : '' }}>Log Only (Testing - No real SMS sent)</option>
                    <option value="nextsms" {{ ($settings['sms_gateway']->value ?? '') === 'nextsms' ? 'selected' : '' }}>NextSMS (Tanzania)</option>
                    <option value="twilio" {{ ($settings['sms_gateway']->value ?? '') === 'twilio' ? 'selected' : '' }}>Twilio</option>
                    <option value="http" {{ ($settings['sms_gateway']->value ?? '') === 'http' ? 'selected' : '' }}>Custom HTTP Gateway</option>
                </x-ui.select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Sender ID</label>
                <x-ui.input type="text" name="sms_sender_id" value="{{ $settings['sms_sender_id']->value ?? 'UZAZICLINIC' }}" class="w-full border border-gray-200 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-slate-300 outline-none" placeholder="UZAZICLINIC" />
                <p class="text-[10px] text-gray-400 mt-1">The sender name that appears on recipients' phones.</p>
            </div>

            <script>
    function toggleGatewaySections() {
        const gateway = document.getElementById('smsGateway').value;
        document.getElementById('nextsms-section').classList.toggle('hidden', gateway !== 'nextsms');
        document.getElementById('twilio-section').classList.toggle('hidden', gateway !== 'twilio');
        document.getElementById('http-section').classList.toggle('hidden', gateway !== 'http');
    }
    document.getElementById('smsGateway').addEventListener('change', toggleGatewaySections);
    toggleGatewaySections();

    document.getElementById('testSmsForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const btn = document.getElementById('testSmsBtn');
        const result = document.getElementById('testResult');
        const phone = document.getElementById('testPhone').value;
        const message = document.getElementById('testMessage').value;

        btn.disabled = true;
        btn.innerHTML = '<svg class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke-width="3" class="opacity-25"/><path d="M4 12a8 8 0 018-8" stroke-width="3" class="opacity-75"/></svg> Sending...';
        result.classList.add('hidden');

        fetch('{{ route("settings.sms.test") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
            },
            body: JSON.stringify({ phone, message }),
        })
        .then(res => res.json().then(data => ({ status: res.status, data })))
        .then(({ status, data }) => {
            result.classList.remove('hidden');
            if (data.success) {
                result.innerHTML = '<div class="bg-white border-l-2 border-emerald-500 rounded-md p-3 flex items-center gap-3"><i class="hgi-stroke hgi-checkmark-circle-02 text-base text-emerald-600"></i><div><p class="text-xs font-bold text-emerald-700">SMS Sent Successfully!</p><p class="text-[10px] text-emerald-600 mt-0.5">' + (data.message || '') + '</p></div></div>';
            } else {
                result.innerHTML = '<div class="bg-white border-l-2 border-red-500 rounded-md p-3 flex items-center gap-3"><i class="hgi-stroke hgi-alert-circle text-base text-red-600"></i><div><p class="text-xs font-bold text-red-700">SMS Failed</p><p class="text-[10px] text-red-600 mt-0.5">' + (data.error || data.message || 'Unknown error') + '</p></div></div>';
            }
        })
        .catch(err => {
            result.classList.remove('hidden');
            result.innerHTML = '<div class="bg-white border-l-2 border-red-500 rounded-md p-3"><p class="text-xs font-bold text-red-700">Network error: ' + err.message + '</p></div>';
        })
        .finally(() => {
            btn.disabled = false;
            btn.innerHTML = '<i class="hgi-stroke hgi-sent text-base"></i> Send Test SMS';
        });
    });
</script>
            <div id="nextsms-section" class="border border-emerald-200 rounded-md p-4 bg-emerald-50/30 space-y-4">
                <div class="flex items-center gap-2">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                    <h3 class="text-sm font-bold text-gray-900">NextSMS Configuration</h3>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">NextSMS From (Sender ID)</label>
                        <x-ui.input type="text" name="nextsms_from" value="{{ $settings['nextsms_from']->value ?? 'UZAZICLINIC' }}" class="w-full border border-gray-200 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-slate-300 outline-none" placeholder="UZAZICLINIC" />
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">NextSMS Username</label>
                        <x-ui.input type="text" name="nextsms_username" value="{{ $settings['nextsms_username']->value ?? '' }}" class="w-full border border-gray-200 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-slate-300 outline-none" placeholder="Uzazi Clinic" />
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">NextSMS Password</label>
                        <x-ui.input type="password" name="nextsms_password" value="{{ $settings['nextsms_password']->value ?? '' }}" class="w-full border border-gray-200 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-slate-300 outline-none" placeholder="••••••••" />
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">NextSMS API URL</label>
                        <x-ui.input type="text" name="nextsms_url" value="{{ $settings['nextsms_url']->value ?? 'https://messaging-service.co.tz/api/sms/v1/text/single' }}" class="w-full border border-gray-200 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-slate-300 outline-none" placeholder="https://messaging-service.co.tz/api/sms/v1/text/single" />
                    </div>
                </div>
            </div>

            <script>
    function toggleGatewaySections() {
        const gateway = document.getElementById('smsGateway').value;
        document.getElementById('nextsms-section').classList.toggle('hidden', gateway !== 'nextsms');
        document.getElementById('twilio-section').classList.toggle('hidden', gateway !== 'twilio');
        document.getElementById('http-section').classList.toggle('hidden', gateway !== 'http');
    }
    document.getElementById('smsGateway').addEventListener('change', toggleGatewaySections);
    toggleGatewaySections();

    document.getElementById('testSmsForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const btn = document.getElementById('testSmsBtn');
        const result = document.getElementById('testResult');
        const phone = document.getElementById('testPhone').value;
        const message = document.getElementById('testMessage').value;

        btn.disabled = true;
        btn.innerHTML = '<svg class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke-width="3" class="opacity-25"/><path d="M4 12a8 8 0 018-8" stroke-width="3" class="opacity-75"/></svg> Sending...';
        result.classList.add('hidden');

        fetch('{{ route("settings.sms.test") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
            },
            body: JSON.stringify({ phone, message }),
        })
        .then(res => res.json().then(data => ({ status: res.status, data })))
        .then(({ status, data }) => {
            result.classList.remove('hidden');
            if (data.success) {
                result.innerHTML = '<div class="bg-white border-l-2 border-emerald-500 rounded-md p-3 flex items-center gap-3"><i class="hgi-stroke hgi-checkmark-circle-02 text-base text-emerald-600"></i><div><p class="text-xs font-bold text-emerald-700">SMS Sent Successfully!</p><p class="text-[10px] text-emerald-600 mt-0.5">' + (data.message || '') + '</p></div></div>';
            } else {
                result.innerHTML = '<div class="bg-white border-l-2 border-red-500 rounded-md p-3 flex items-center gap-3"><i class="hgi-stroke hgi-alert-circle text-base text-red-600"></i><div><p class="text-xs font-bold text-red-700">SMS Failed</p><p class="text-[10px] text-red-600 mt-0.5">' + (data.error || data.message || 'Unknown error') + '</p></div></div>';
            }
        })
        .catch(err => {
            result.classList.remove('hidden');
            result.innerHTML = '<div class="bg-white border-l-2 border-red-500 rounded-md p-3"><p class="text-xs font-bold text-red-700">Network error: ' + err.message + '</p></div>';
        })
        .finally(() => {
            btn.disabled = false;
            btn.innerHTML = '<i class="hgi-stroke hgi-sent text-base"></i> Send Test SMS';
        });
    });
</script>
            <div id="twilio-section" class="border border-gray-200 rounded-md p-4 bg-gray-50/50 space-y-4 hidden">
                <h3 class="text-sm font-bold text-gray-900">Twilio Settings</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Twilio SID</label>
                        <x-ui.input type="text" name="twilio_sid" placeholder="ACxxxxxxxxxxxxxxxx" value="{{ $settings['twilio_sid']->value ?? '' }}" class="w-full border border-gray-200 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-slate-300 outline-none" />
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Twilio Auth Token</label>
                        <x-ui.input type="text" name="twilio_token" placeholder="Auth token" value="{{ $settings['twilio_token']->value ?? '' }}" class="w-full border border-gray-200 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-slate-300 outline-none" />
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Twilio From Number</label>
                    <x-ui.input type="text" name="twilio_from" placeholder="e.g. +1XXXXXXXXXX" value="{{ $settings['twilio_from']->value ?? '' }}" class="w-full border border-gray-200 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-slate-300 outline-none" />
                </div>
            </div>

            <script>
    function toggleGatewaySections() {
        const gateway = document.getElementById('smsGateway').value;
        document.getElementById('nextsms-section').classList.toggle('hidden', gateway !== 'nextsms');
        document.getElementById('twilio-section').classList.toggle('hidden', gateway !== 'twilio');
        document.getElementById('http-section').classList.toggle('hidden', gateway !== 'http');
    }
    document.getElementById('smsGateway').addEventListener('change', toggleGatewaySections);
    toggleGatewaySections();

    document.getElementById('testSmsForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const btn = document.getElementById('testSmsBtn');
        const result = document.getElementById('testResult');
        const phone = document.getElementById('testPhone').value;
        const message = document.getElementById('testMessage').value;

        btn.disabled = true;
        btn.innerHTML = '<svg class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke-width="3" class="opacity-25"/><path d="M4 12a8 8 0 018-8" stroke-width="3" class="opacity-75"/></svg> Sending...';
        result.classList.add('hidden');

        fetch('{{ route("settings.sms.test") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
            },
            body: JSON.stringify({ phone, message }),
        })
        .then(res => res.json().then(data => ({ status: res.status, data })))
        .then(({ status, data }) => {
            result.classList.remove('hidden');
            if (data.success) {
                result.innerHTML = '<div class="bg-white border-l-2 border-emerald-500 rounded-md p-3 flex items-center gap-3"><i class="hgi-stroke hgi-checkmark-circle-02 text-base text-emerald-600"></i><div><p class="text-xs font-bold text-emerald-700">SMS Sent Successfully!</p><p class="text-[10px] text-emerald-600 mt-0.5">' + (data.message || '') + '</p></div></div>';
            } else {
                result.innerHTML = '<div class="bg-white border-l-2 border-red-500 rounded-md p-3 flex items-center gap-3"><i class="hgi-stroke hgi-alert-circle text-base text-red-600"></i><div><p class="text-xs font-bold text-red-700">SMS Failed</p><p class="text-[10px] text-red-600 mt-0.5">' + (data.error || data.message || 'Unknown error') + '</p></div></div>';
            }
        })
        .catch(err => {
            result.classList.remove('hidden');
            result.innerHTML = '<div class="bg-white border-l-2 border-red-500 rounded-md p-3"><p class="text-xs font-bold text-red-700">Network error: ' + err.message + '</p></div>';
        })
        .finally(() => {
            btn.disabled = false;
            btn.innerHTML = '<i class="hgi-stroke hgi-sent text-base"></i> Send Test SMS';
        });
    });
</script>
            <div id="http-section" class="border border-gray-200 rounded-md p-4 bg-gray-50/50 space-y-4 hidden">
                <h3 class="text-sm font-bold text-gray-900">Custom HTTP Gateway</h3>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">HTTP URL</label>
                    <x-ui.input type="text" name="sms_http_url" value="{{ $settings['sms_http_url']->value ?? '' }}" class="w-full border border-gray-200 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-slate-300 outline-none" placeholder="https://api.example.com/send" />
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">HTTP Method</label>
                    <x-ui.select name="sms_http_method" class="w-full border border-gray-200 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-slate-300 outline-none">
                        <option value="POST" {{ ($settings['sms_http_method']->value ?? 'POST') === 'POST' ? 'selected' : '' }}>POST</option>
                        <option value="GET" {{ ($settings['sms_http_method']->value ?? '') === 'GET' ? 'selected' : '' }}>GET</option>
                    </x-ui.select>
                </div>
            </div>

            <div class="pt-4 border-t border-gray-100">
                <button type="submit" class="bg-slate-800 text-white text-sm font-medium px-5 py-2 rounded hover:bg-slate-700">Save Settings</button>
            </div>
        </form>
    </div>

    <script>
    function toggleGatewaySections() {
        const gateway = document.getElementById('smsGateway').value;
        document.getElementById('nextsms-section').classList.toggle('hidden', gateway !== 'nextsms');
        document.getElementById('twilio-section').classList.toggle('hidden', gateway !== 'twilio');
        document.getElementById('http-section').classList.toggle('hidden', gateway !== 'http');
    }
    document.getElementById('smsGateway').addEventListener('change', toggleGatewaySections);
    toggleGatewaySections();

    document.getElementById('testSmsForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const btn = document.getElementById('testSmsBtn');
        const result = document.getElementById('testResult');
        const phone = document.getElementById('testPhone').value;
        const message = document.getElementById('testMessage').value;

        btn.disabled = true;
        btn.innerHTML = '<svg class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke-width="3" class="opacity-25"/><path d="M4 12a8 8 0 018-8" stroke-width="3" class="opacity-75"/></svg> Sending...';
        result.classList.add('hidden');

        fetch('{{ route("settings.sms.test") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
            },
            body: JSON.stringify({ phone, message }),
        })
        .then(res => res.json().then(data => ({ status: res.status, data })))
        .then(({ status, data }) => {
            result.classList.remove('hidden');
            if (data.success) {
                result.innerHTML = '<div class="bg-white border-l-2 border-emerald-500 rounded-md p-3 flex items-center gap-3"><i class="hgi-stroke hgi-checkmark-circle-02 text-base text-emerald-600"></i><div><p class="text-xs font-bold text-emerald-700">SMS Sent Successfully!</p><p class="text-[10px] text-emerald-600 mt-0.5">' + (data.message || '') + '</p></div></div>';
            } else {
                result.innerHTML = '<div class="bg-white border-l-2 border-red-500 rounded-md p-3 flex items-center gap-3"><i class="hgi-stroke hgi-alert-circle text-base text-red-600"></i><div><p class="text-xs font-bold text-red-700">SMS Failed</p><p class="text-[10px] text-red-600 mt-0.5">' + (data.error || data.message || 'Unknown error') + '</p></div></div>';
            }
        })
        .catch(err => {
            result.classList.remove('hidden');
            result.innerHTML = '<div class="bg-white border-l-2 border-red-500 rounded-md p-3"><p class="text-xs font-bold text-red-700">Network error: ' + err.message + '</p></div>';
        })
        .finally(() => {
            btn.disabled = false;
            btn.innerHTML = '<i class="hgi-stroke hgi-sent text-base"></i> Send Test SMS';
        });
    });
</script>
    <div class="bg-white rounded-md border border-gray-200 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100">
            <h2 class="text-sm font-bold text-gray-900">Test SMS</h2>
            <p class="text-xs text-gray-500 mt-0.5">Send a test SMS to verify your configuration is working.</p>
        </div>
        <form id="testSmsForm" class="p-5 space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Test Phone Number</label>
                    <x-ui.input type="text" id="testPhone" class="w-full border border-gray-200 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-slate-300 outline-none" placeholder="0678233736" required />
                    <p class="text-[10px] text-gray-400 mt-1">Tanzanian number (e.g. 0678233736 or 255678233736).</p>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Test Message</label>
                    <x-ui.input type="text" id="testMessage" value="Test SMS from Uzazi Clinic - Configuration working!" class="w-full border border-gray-200 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-slate-300 outline-none" />
                </div>
            </div>
            <button type="submit" id="testSmsBtn" class="bg-amber-500 text-white text-sm font-medium px-5 py-2 rounded hover:bg-amber-600 flex items-center gap-2">
                <i class="hgi-stroke hgi-sent text-base"></i>
                Send Test SMS
            </button>
        </form>
        <div id="testResult" class="hidden px-5 pb-5"></div>
    </div>

    <script>
    function toggleGatewaySections() {
        const gateway = document.getElementById('smsGateway').value;
        document.getElementById('nextsms-section').classList.toggle('hidden', gateway !== 'nextsms');
        document.getElementById('twilio-section').classList.toggle('hidden', gateway !== 'twilio');
        document.getElementById('http-section').classList.toggle('hidden', gateway !== 'http');
    }
    document.getElementById('smsGateway').addEventListener('change', toggleGatewaySections);
    toggleGatewaySections();

    document.getElementById('testSmsForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const btn = document.getElementById('testSmsBtn');
        const result = document.getElementById('testResult');
        const phone = document.getElementById('testPhone').value;
        const message = document.getElementById('testMessage').value;

        btn.disabled = true;
        btn.innerHTML = '<svg class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke-width="3" class="opacity-25"/><path d="M4 12a8 8 0 018-8" stroke-width="3" class="opacity-75"/></svg> Sending...';
        result.classList.add('hidden');

        fetch('{{ route("settings.sms.test") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
            },
            body: JSON.stringify({ phone, message }),
        })
        .then(res => res.json().then(data => ({ status: res.status, data })))
        .then(({ status, data }) => {
            result.classList.remove('hidden');
            if (data.success) {
                result.innerHTML = '<div class="bg-white border-l-2 border-emerald-500 rounded-md p-3 flex items-center gap-3"><i class="hgi-stroke hgi-checkmark-circle-02 text-base text-emerald-600"></i><div><p class="text-xs font-bold text-emerald-700">SMS Sent Successfully!</p><p class="text-[10px] text-emerald-600 mt-0.5">' + (data.message || '') + '</p></div></div>';
            } else {
                result.innerHTML = '<div class="bg-white border-l-2 border-red-500 rounded-md p-3 flex items-center gap-3"><i class="hgi-stroke hgi-alert-circle text-base text-red-600"></i><div><p class="text-xs font-bold text-red-700">SMS Failed</p><p class="text-[10px] text-red-600 mt-0.5">' + (data.error || data.message || 'Unknown error') + '</p></div></div>';
            }
        })
        .catch(err => {
            result.classList.remove('hidden');
            result.innerHTML = '<div class="bg-white border-l-2 border-red-500 rounded-md p-3"><p class="text-xs font-bold text-red-700">Network error: ' + err.message + '</p></div>';
        })
        .finally(() => {
            btn.disabled = false;
            btn.innerHTML = '<i class="hgi-stroke hgi-sent text-base"></i> Send Test SMS';
        });
    });
</script>
    <div class="bg-white rounded-md border border-gray-200 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
            <div>
                <h2 class="text-sm font-bold text-gray-900">SMS Audit Log</h2>
                <p class="text-xs text-gray-500 mt-0.5">Recent SMS messages sent by the system.</p>
            </div>
            <a href="{{ route('sms.logs') }}" class="text-[10px] font-medium text-slate-600 hover:text-slate-800 border border-gray-200 px-3 py-1.5 rounded hover:bg-gray-100">View All</a>
        </div>
        <div class="overflow-x-auto">
            @php $recentLogs = \App\Models\SmsLog::latest()->limit(10)->get(); @endphp
            @if($recentLogs->count() > 0)
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-[10px] uppercase text-gray-400 border-b border-gray-100">
                    <tr>
                        <th class="text-left px-5 py-3 font-medium">Phone</th>
                        <th class="text-left px-5 py-3 font-medium">Message</th>
                        <th class="text-left px-5 py-3 font-medium">Gateway</th>
                        <th class="text-left px-5 py-3 font-medium">Status</th>
                        <th class="text-left px-5 py-3 font-medium">Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($recentLogs as $log)
                    <tr class="hover:bg-gray-50/50">
                        <td class="px-5 py-3 text-xs font-mono text-gray-600">{{ $log->phone }}</td>
                        <td class="px-5 py-3 text-xs text-gray-600 max-w-xs truncate">{{ \Illuminate\Support\Str::limit($log->message, 60) }}</td>
                        <td class="px-5 py-3 text-xs text-gray-600">{{ $log->gateway }}</td>
                        <td class="px-5 py-3">
                            <span class="inline-flex items-center gap-1.5 text-[10px] font-medium text-gray-600">
                                <span class="w-1.5 h-1.5 rounded-full {{ $log->status === 'sent' ? 'bg-emerald-500' : ($log->status === 'failed' ? 'bg-red-500' : 'bg-amber-500') }}"></span>
                                {{ ucfirst($log->status) }}
                            </span>
                        </td>
                        <td class="px-5 py-3 text-xs text-gray-500">{{ $log->created_at->diffForHumans() }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @else
            <div class="p-8 text-center">
                <i class="hgi-stroke hgi-shopping-cart-01 text-4xl mx-auto text-gray-300 mb-3"></i>
                <p class="text-xs text-gray-400">No SMS messages sent yet.</p>
            </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
    function toggleGatewaySections() {
        const gateway = document.getElementById('smsGateway').value;
        document.getElementById('nextsms-section').classList.toggle('hidden', gateway !== 'nextsms');
        document.getElementById('twilio-section').classList.toggle('hidden', gateway !== 'twilio');
        document.getElementById('http-section').classList.toggle('hidden', gateway !== 'http');
    }
    document.getElementById('smsGateway').addEventListener('change', toggleGatewaySections);
    toggleGatewaySections();

    document.getElementById('testSmsForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const btn = document.getElementById('testSmsBtn');
        const result = document.getElementById('testResult');
        const phone = document.getElementById('testPhone').value;
        const message = document.getElementById('testMessage').value;

        btn.disabled = true;
        btn.innerHTML = '<svg class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke-width="3" class="opacity-25"/><path d="M4 12a8 8 0 018-8" stroke-width="3" class="opacity-75"/></svg> Sending...';
        result.classList.add('hidden');

        fetch('{{ route("settings.sms.test") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
            },
            body: JSON.stringify({ phone, message }),
        })
        .then(res => res.json().then(data => ({ status: res.status, data })))
        .then(({ status, data }) => {
            result.classList.remove('hidden');
            if (data.success) {
                result.innerHTML = '<div class="bg-white border-l-2 border-emerald-500 rounded-md p-3 flex items-center gap-3"><i class="hgi-stroke hgi-checkmark-circle-02 text-base text-emerald-600"></i><div><p class="text-xs font-bold text-emerald-700">SMS Sent Successfully!</p><p class="text-[10px] text-emerald-600 mt-0.5">' + (data.message || '') + '</p></div></div>';
            } else {
                result.innerHTML = '<div class="bg-white border-l-2 border-red-500 rounded-md p-3 flex items-center gap-3"><i class="hgi-stroke hgi-alert-circle text-base text-red-600"></i><div><p class="text-xs font-bold text-red-700">SMS Failed</p><p class="text-[10px] text-red-600 mt-0.5">' + (data.error || data.message || 'Unknown error') + '</p></div></div>';
            }
        })
        .catch(err => {
            result.classList.remove('hidden');
            result.innerHTML = '<div class="bg-white border-l-2 border-red-500 rounded-md p-3"><p class="text-xs font-bold text-red-700">Network error: ' + err.message + '</p></div>';
        })
        .finally(() => {
            btn.disabled = false;
            btn.innerHTML = '<i class="hgi-stroke hgi-sent text-base"></i> Send Test SMS';
        });
    });
</script>
@endpush
@endsection
