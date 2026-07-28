@extends('layouts.dashboard')

@section('title', 'SMS Gateway - ' . config('app.name', 'Laravel'))
@section('page_title', 'SMS Gateway Configuration')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    @if(session('status'))
        <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-4 flex items-center gap-3">
            <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <p class="text-sm font-semibold text-emerald-700">{{ session('status') }}</p>
        </div>
    @endif
    @if(session('error'))
        <div class="bg-red-50 border border-red-200 rounded-xl p-4 flex items-center gap-3">
            <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <p class="text-sm font-semibold text-red-700">{{ session('error') }}</p>
        </div>
    @endif

    {{-- Settings Form --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="bg-gradient-to-r from-emerald-600 to-emerald-700 px-6 py-4">
            <h2 class="text-white font-bold text-lg flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h2l3.6 7.59-1.35 2.45c-.16.28-.25.61-.25.96 0 1.1.9 2 2 2h12v-2H9.42c-.14 0-.25-.11-.25-.25l.03-.12L10.1 13h7.45c.75 0 1.41-.41 1.75-1.03L21.7 4H5.21l-.94-2H1v2z"/></svg>
                SMS Gateway Configuration
            </h2>
            <p class="text-emerald-100 text-xs mt-1">Configure how the system sends SMS messages to patients and staff.</p>
        </div>

        <form method="POST" action="{{ route('settings.update') }}" class="p-6 space-y-5">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">SMS Gateway</label>
                <select name="sms_gateway" id="smsGateway" class="w-full border-2 border-gray-200 rounded-lg px-4 py-2.5 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all">
                    <option value="log" {{ ($settings['sms_gateway']->value ?? 'log') === 'log' ? 'selected' : '' }}>Log Only (Testing - No real SMS sent)</option>
                    <option value="nextsms" {{ ($settings['sms_gateway']->value ?? '') === 'nextsms' ? 'selected' : '' }}>NextSMS (Tanzania)</option>
                    <option value="twilio" {{ ($settings['sms_gateway']->value ?? '') === 'twilio' ? 'selected' : '' }}>Twilio</option>
                    <option value="http" {{ ($settings['sms_gateway']->value ?? '') === 'http' ? 'selected' : '' }}>Custom HTTP Gateway</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Sender ID</label>
                <input type="text" name="sms_sender_id" value="{{ $settings['sms_sender_id']->value ?? 'UZAZICLINIC' }}" class="w-full border-2 border-gray-200 rounded-lg px-4 py-2.5 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all" placeholder="UZAZICLINIC">
                <p class="text-xs text-gray-400 mt-1">The sender name that appears on recipients' phones.</p>
            </div>

            {{-- NextSMS Section --}}
            <div id="nextsms-section" class="border-2 border-emerald-100 rounded-xl p-5 bg-emerald-50/30 space-y-4">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg bg-emerald-600 flex items-center justify-center">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h2l3.6 7.59-1.35 2.45c-.16.28-.25.61-.25.96 0 1.1.9 2 2 2h12v-2H9.42c-.14 0-.25-.11-.25-.25l.03-.12L10.1 13h7.45c.75 0 1.41-.41 1.75-1.03L21.7 4H5.21l-.94-2H1v2z"/></svg>
                    </div>
                    <h3 class="text-sm font-bold text-emerald-800">NextSMS Configuration</h3>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">NextSMS From (Sender ID)</label>
                        <input type="text" name="nextsms_from" value="{{ $settings['nextsms_from']->value ?? 'UZAZICLINIC' }}" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:border-emerald-500 outline-none" placeholder="UZAZICLINIC">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">NextSMS Username</label>
                        <input type="text" name="nextsms_username" value="{{ $settings['nextsms_username']->value ?? '' }}" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:border-emerald-500 outline-none" placeholder="Issa Scientist">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">NextSMS Password</label>
                        <input type="password" name="nextsms_password" value="{{ $settings['nextsms_password']->value ?? '' }}" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:border-emerald-500 outline-none" placeholder="••••••••">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">NextSMS API URL</label>
                        <input type="text" name="nextsms_url" value="{{ $settings['nextsms_url']->value ?? 'https://messaging-service.co.tz/api/sms/v1/text/single' }}" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:border-emerald-500 outline-none" placeholder="https://messaging-service.co.tz/api/sms/v1/text/single">
                    </div>
                </div>
            </div>

            {{-- Twilio Section --}}
            <div id="twilio-section" class="border-2 border-gray-100 rounded-xl p-5 bg-gray-50/30 space-y-4 hidden">
                <h3 class="text-sm font-bold text-gray-800">Twilio Settings</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Twilio SID</label>
                        <input type="text" name="twilio_sid" value="{{ $settings['twilio_sid']->value ?? '' }}" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:border-emerald-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Twilio Auth Token</label>
                        <input type="text" name="twilio_token" value="{{ $settings['twilio_token']->value ?? '' }}" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:border-emerald-500 outline-none">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Twilio From Number</label>
                    <input type="text" name="twilio_from" value="{{ $settings['twilio_from']->value ?? '' }}" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:border-emerald-500 outline-none">
                </div>
            </div>

            {{-- HTTP Gateway Section --}}
            <div id="http-section" class="border-2 border-gray-100 rounded-xl p-5 bg-gray-50/30 space-y-4 hidden">
                <h3 class="text-sm font-bold text-gray-800">Custom HTTP Gateway</h3>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">HTTP URL</label>
                    <input type="text" name="sms_http_url" value="{{ $settings['sms_http_url']->value ?? '' }}" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:border-emerald-500 outline-none" placeholder="https://api.example.com/send">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">HTTP Method</label>
                    <select name="sms_http_method" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
                        <option value="POST" {{ ($settings['sms_http_method']->value ?? 'POST') === 'POST' ? 'selected' : '' }}>POST</option>
                        <option value="GET" {{ ($settings['sms_http_method']->value ?? '') === 'GET' ? 'selected' : '' }}>GET</option>
                    </select>
                </div>
            </div>

            <div class="pt-4 border-t border-gray-100">
                <button type="submit" class="bg-emerald-600 text-white text-sm font-bold px-6 py-2.5 rounded-lg hover:bg-emerald-700 shadow-sm transition-all">Save Settings</button>
            </div>
        </form>
    </div>

    {{-- Test SMS --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="bg-gradient-to-r from-gold-400 to-gold-500 px-6 py-4">
            <h2 class="text-white font-bold text-lg flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                Test SMS
            </h2>
            <p class="text-white/80 text-xs mt-1">Send a test SMS to verify your configuration is working.</p>
        </div>
        <form id="testSmsForm" class="p-6 space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Test Phone Number</label>
                    <input type="text" id="testPhone" class="w-full border-2 border-gray-200 rounded-lg px-4 py-2.5 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all" placeholder="0678233736" required>
                    <p class="text-xs text-gray-400 mt-1">Tanzanian number (e.g. 0678233736 or 255678233736).</p>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Test Message</label>
                    <input type="text" id="testMessage" value="Test SMS from Issa Scientist Clinic - Configuration working!" class="w-full border-2 border-gray-200 rounded-lg px-4 py-2.5 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all">
                </div>
            </div>
            <button type="submit" id="testSmsBtn" class="bg-gold-500 text-white text-sm font-bold px-6 py-2.5 rounded-lg hover:bg-gold-600 shadow-sm transition-all flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                Send Test SMS
            </button>
        </form>
        <div id="testResult" class="hidden px-6 pb-6"></div>
    </div>

    {{-- SMS Audit Log --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="bg-gradient-to-r from-slate-700 to-slate-800 px-6 py-4 flex items-center justify-between">
            <div>
                <h2 class="text-white font-bold text-lg flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    SMS Audit Log
                </h2>
                <p class="text-slate-300 text-xs mt-1">Recent SMS messages sent by the system.</p>
            </div>
            <a href="{{ route('sms.logs') }}" class="text-xs text-white/80 hover:text-white bg-white/10 px-3 py-1.5 rounded-lg hover:bg-white/20 transition-all">View All</a>
        </div>
        <div class="overflow-x-auto">
            @php $recentLogs = \App\Models\SmsLog::latest()->limit(10)->get(); @endphp
            @if($recentLogs->count() > 0)
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="text-left px-4 py-3 text-xs font-bold text-gray-600 uppercase">Phone</th>
                        <th class="text-left px-4 py-3 text-xs font-bold text-gray-600 uppercase">Message</th>
                        <th class="text-left px-4 py-3 text-xs font-bold text-gray-600 uppercase">Gateway</th>
                        <th class="text-left px-4 py-3 text-xs font-bold text-gray-600 uppercase">Status</th>
                        <th class="text-left px-4 py-3 text-xs font-bold text-gray-600 uppercase">Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($recentLogs as $log)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-xs font-mono text-gray-700">{{ $log->phone }}</td>
                        <td class="px-4 py-3 text-xs text-gray-600 max-w-xs truncate">{{ \Illuminate\Support\Str::limit($log->message, 60) }}</td>
                        <td class="px-4 py-3"><span class="text-xs font-semibold px-2 py-0.5 rounded {{ $log->gateway === 'nextsms' ? 'bg-emerald-50 text-emerald-700' : ($log->gateway === 'log' ? 'bg-gray-100 text-gray-600' : 'bg-blue-50 text-blue-700') }}">{{ $log->gateway }}</span></td>
                        <td class="px-4 py-3">
                            @if($log->status === 'sent')
                                <span class="text-xs font-bold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded">SENT</span>
                            @elseif($log->status === 'failed')
                                <span class="text-xs font-bold text-red-600 bg-red-50 px-2 py-0.5 rounded">FAILED</span>
                            @else
                                <span class="text-xs font-bold text-amber-600 bg-amber-50 px-2 py-0.5 rounded">{{ strtoupper($log->status) }}</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-xs text-gray-500">{{ $log->created_at->diffForHumans() }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @else
            <div class="p-8 text-center">
                <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 5h2l3.6 7.59-1.35 2.45c-.16.28-.25.61-.25.96 0 1.1.9 2 2 2h12v-2H9.42c-.14 0-.25-.11-.25-.25l.03-.12L10.1 13h7.45c.75 0 1.41-.41 1.75-1.03L21.7 4H5.21l-.94-2H1v2z"/></svg>
                <p class="text-sm text-gray-400">No SMS messages sent yet.</p>
            </div>
            @endif
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
                result.innerHTML = '<div class="bg-emerald-50 border border-emerald-200 rounded-xl p-4 flex items-center gap-3"><svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg><div><p class="text-sm font-bold text-emerald-700">SMS Sent Successfully!</p><p class="text-xs text-emerald-600 mt-0.5">' + (data.message || '') + '</p></div></div>';
            } else {
                result.innerHTML = '<div class="bg-red-50 border border-red-200 rounded-xl p-4 flex items-center gap-3"><svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg><div><p class="text-sm font-bold text-red-700">SMS Failed</p><p class="text-xs text-red-600 mt-0.5">' + (data.error || data.message || 'Unknown error') + '</p></div></div>';
            }
        })
        .catch(err => {
            result.classList.remove('hidden');
            result.innerHTML = '<div class="bg-red-50 border border-red-200 rounded-xl p-4"><p class="text-sm font-bold text-red-700">Network error: ' + err.message + '</p></div>';
        })
        .finally(() => {
            btn.disabled = false;
            btn.innerHTML = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg> Send Test SMS';
        });
    });
</script>
@endsection
