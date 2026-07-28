@extends('layouts.dashboard')

@section('title', 'General Settings - ' . config('app.name', 'Laravel'))
@section('page_title', 'General Settings')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">

    {{-- Tab Navigation --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-2">
        <div class="flex flex-wrap gap-1">
            <button onclick="switchTab('clinic')" id="tab-clinic" class="tab-btn px-4 py-2 rounded-lg text-sm font-medium transition-all bg-emerald-600 text-white">
                <svg class="w-4 h-4 inline mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                Clinic Info
            </button>
            <button onclick="switchTab('financial')" id="tab-financial" class="tab-btn px-4 py-2 rounded-lg text-sm font-medium transition-all text-gray-600 hover:bg-gray-100">
                <svg class="w-4 h-4 inline mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Financial
            </button>
            @foreach ($settings as $group => $items)
                @if (!in_array($group, ['general', 'email', 'sms', 'payment']))
                    <button onclick="switchTab('{{ $group }}')" id="tab-{{ $group }}" class="tab-btn px-4 py-2 rounded-lg text-sm font-medium transition-all text-gray-600 hover:bg-gray-100">
                        <svg class="w-4 h-4 inline mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        {{ ucfirst($group) }}
                    </button>
                @endif
            @endforeach
        </div>
    </div>

    @php
        $generalSettings = $settings->get('general', collect());
        $clinicFields = ['app_name', 'clinic_name', 'clinic_phone', 'clinic_email', 'clinic_address'];
        $financialFields = ['currency', 'consultation_fee'];
        $clinicSettings = $generalSettings->filter(fn($s) => in_array($s->key, $clinicFields));
        $financialSettings = $generalSettings->filter(fn($s) => in_array($s->key, $financialFields));
        $otherGeneral = $generalSettings->filter(fn($s) => !in_array($s->key, array_merge($clinicFields, $financialFields)));
    @endphp

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
                    @foreach ($clinicSettings as $setting)
                        <div class="{{ $setting->type === 'textarea' ? 'sm:col-span-2' : '' }}">
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">{{ ucwords(str_replace('_', ' ', $setting->key)) }}</label>
                            @if ($setting->type === 'textarea')
                                <textarea name="{{ $setting->key }}" rows="2" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all">{{ $setting->value }}</textarea>
                            @else
                                <input type="text" name="{{ $setting->key }}" value="{{ $setting->value }}" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all">
                            @endif
                        </div>
                    @endforeach
                </div>
                @if($otherGeneral->count() > 0)
                <div class="border-t border-gray-100 pt-4">
                    <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-3">Other Settings</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        @foreach ($otherGeneral as $setting)
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1.5">{{ ucwords(str_replace('_', ' ', $setting->key)) }}</label>
                                <input type="text" name="{{ $setting->key }}" value="{{ $setting->value }}" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all">
                            </div>
                        @endforeach
                    </div>
                </div>
                @endif
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
                    @foreach ($financialSettings as $setting)
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">{{ ucwords(str_replace('_', ' ', $setting->key)) }}</label>
                            <input type="{{ $setting->type === 'number' ? 'number' : 'text' }}" name="{{ $setting->key }}" value="{{ $setting->value }}" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all" @if($setting->type === 'number') step="0.01" min="0" @endif>
                        </div>
                    @endforeach
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

    {{-- Other Groups --}}
    @foreach ($settings as $group => $items)
        @if (!in_array($group, ['general', 'email', 'sms', 'payment']))
            <div id="panel-{{ $group }}" class="tab-panel hidden">
                <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
                    <div class="bg-gradient-to-r from-sky-500 to-sky-600 px-6 py-4">
                        <h2 class="text-white font-bold text-lg flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            {{ ucfirst($group) }} Settings
                        </h2>
                    </div>
                    <form method="POST" action="{{ route('settings.update') }}" class="p-6 space-y-5" data-ajax>
                        @csrf
                        @method('PUT')
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            @foreach ($items as $setting)
                                <div class="{{ $setting->type === 'textarea' ? 'sm:col-span-2' : '' }}">
                                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">{{ ucwords(str_replace('_', ' ', $setting->key)) }}</label>
                                    @if ($setting->type === 'textarea')
                                        <textarea name="{{ $setting->key }}" rows="3" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all">{{ $setting->value }}</textarea>
                                    @else
                                        <input type="{{ $setting->type === 'number' ? 'number' : 'text' }}" name="{{ $setting->key }}" value="{{ $setting->value }}" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all">
                                    @endif
                                </div>
                            @endforeach
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
        @endif
    @endforeach
</div>

@push('scripts')
<script>
    function switchTab(tab) {
        document.querySelectorAll('.tab-panel').forEach(p => p.classList.add('hidden'));
        document.querySelectorAll('.tab-btn').forEach(b => {
            b.classList.remove('bg-emerald-600', 'text-white');
            b.classList.add('text-gray-600', 'hover:bg-gray-100');
        });
        const panel = document.getElementById('panel-' + tab);
        const btn = document.getElementById('tab-' + tab);
        if (panel) panel.classList.remove('hidden');
        if (btn) {
            btn.classList.add('bg-emerald-600', 'text-white');
            btn.classList.remove('text-gray-600', 'hover:bg-gray-100');
        }
    }
</script>
@endpush
@endsection
