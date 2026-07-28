@extends('layouts.dashboard')

@section('title', 'General Settings - ' . config('app.name', 'Laravel'))
@section('page_title', 'General Settings')

@php
    use App\Models\Setting;
    $val = fn($key, $default = '') => Setting::where('key', $key)->value('value') ?? $default;
@endphp

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    {{-- Tab Navigation --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-2">
        <div class="flex flex-wrap gap-1">
            <button onclick="switchTab('clinic')" id="tab-clinic" class="tab-btn px-4 py-2.5 rounded-lg text-sm font-semibold transition-all bg-emerald-600 text-white shadow-sm">
                <svg class="w-4 h-4 inline mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                Clinic Info
            </button>
            <button onclick="switchTab('financial')" id="tab-financial" class="tab-btn px-4 py-2.5 rounded-lg text-sm font-semibold transition-all text-gray-600 hover:bg-gray-100">
                <svg class="w-4 h-4 inline mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Financial
            </button>
        </div>
    </div>

    {{-- Clinic Info Tab --}}
    <div id="panel-clinic" class="tab-panel">
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="bg-gradient-to-r from-emerald-600 to-emerald-700 px-6 py-4">
                <h2 class="text-white font-bold text-lg flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    Clinic Information
                </h2>
                <p class="text-emerald-100 text-xs mt-1">Basic information about your clinic.</p>
            </div>
            <form method="POST" action="{{ route('settings.update') }}" class="p-6 space-y-5" data-ajax>
                @csrf
                @method('PUT')
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">App Name</label>
                        <input type="text" name="app_name" value="{{ $val('app_name', config('app.name')) }}" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all" placeholder="Dr Issa Scientific Clinic">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Clinic Name</label>
                        <input type="text" name="clinic_name" value="{{ $val('clinic_name', 'Dr Issa Scientific Clinic') }}" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all" placeholder="Dr Issa Scientific Clinic">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Clinic Phone</label>
                        <input type="text" name="clinic_phone" value="{{ $val('clinic_phone', '+255 700 000 000') }}" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all" placeholder="+255 700 000 000">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Clinic Email</label>
                        <input type="email" name="clinic_email" value="{{ $val('clinic_email', 'info@drissa.co.tz') }}" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all" placeholder="info@yourclinic.co.tz">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Clinic Address</label>
                        <textarea name="clinic_address" rows="2" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all" placeholder="Dar es Salaam, Tanzania">{{ $val('clinic_address', 'Dar es Salaam, Tanzania') }}</textarea>
                    </div>
                </div>
                <div class="pt-4 border-t border-gray-100">
                    <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-bold px-6 py-2.5 rounded-lg shadow-sm transition-all flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Save Settings
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Financial Tab --}}
    <div id="panel-financial" class="tab-panel hidden">
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="bg-gradient-to-r from-amber-500 to-amber-600 px-6 py-4">
                <h2 class="text-white font-bold text-lg flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Financial Settings
                </h2>
                <p class="text-amber-100 text-xs mt-1">Currency and pricing configuration.</p>
            </div>
            <form method="POST" action="{{ route('settings.update') }}" class="p-6 space-y-5" data-ajax>
                @csrf
                @method('PUT')
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Currency Symbol</label>
                        <input type="text" name="currency" value="{{ $val('currency', 'TSh') }}" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all" placeholder="TSh">
                        <p class="text-xs text-gray-400 mt-1">e.g. TSh, USD, EUR</p>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Consultation Fee (TSh)</label>
                        <input type="number" step="0.01" min="0" name="consultation_fee" value="{{ $val('consultation_fee', '10000') }}" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all" placeholder="10000">
                        <p class="text-xs text-gray-400 mt-1">Default fee for general consultation</p>
                    </div>
                </div>
                <div class="pt-4 border-t border-gray-100">
                    <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-bold px-6 py-2.5 rounded-lg shadow-sm transition-all flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Save Settings
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function switchTab(tab) {
        document.querySelectorAll('.tab-panel').forEach(p => p.classList.add('hidden'));
        document.querySelectorAll('.tab-btn').forEach(b => {
            b.classList.remove('bg-emerald-600', 'text-white', 'shadow-sm');
            b.classList.add('text-gray-600', 'hover:bg-gray-100');
        });
        const panel = document.getElementById('panel-' + tab);
        const btn = document.getElementById('tab-' + tab);
        if (panel) panel.classList.remove('hidden');
        if (btn) {
            btn.classList.add('bg-emerald-600', 'text-white', 'shadow-sm');
            btn.classList.remove('text-gray-600', 'hover:bg-gray-100');
        }
    }
</script>
@endpush
@endsection
