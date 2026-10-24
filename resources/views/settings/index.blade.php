@extends('layouts.dashboard')

@section('title', 'General Settings - ' . config('app.name', 'Laravel'))
@section('page_title', 'General Settings')

@php
    use App\Models\Setting;
    $val = fn($key, $default = '') => Setting::where('key', $key)->value('value') ?? $default;
@endphp

@section('content')
<div class="max-w-4xl mx-auto space-y-5">

    <script>
    function switchTab(tab) {
        document.querySelectorAll('.tab-panel').forEach(p => p.classList.add('hidden'));
        document.querySelectorAll('.tab-btn').forEach(b => {
            b.classList.remove('bg-slate-800', 'text-white');
            b.classList.add('text-gray-600', 'hover:bg-gray-100');
        });
        const panel = document.getElementById('panel-' + tab);
        const btn = document.getElementById('tab-' + tab);
        if (panel) panel.classList.remove('hidden');
        if (btn) {
            btn.classList.add('bg-slate-800', 'text-white');
            btn.classList.remove('text-gray-600', 'hover:bg-gray-100');
        }
    }
</script>
    <div class="bg-slate-800 rounded-md p-6 text-white">
        <p class="text-[10px] uppercase tracking-wider text-slate-400 mb-1">System</p>
        <h1 class="text-xl font-bold">General Settings</h1>
        <p class="text-xs text-slate-400 mt-1">Clinic profile and financial configuration.</p>
    </div>

    <script>
    function switchTab(tab) {
        document.querySelectorAll('.tab-panel').forEach(p => p.classList.add('hidden'));
        document.querySelectorAll('.tab-btn').forEach(b => {
            b.classList.remove('bg-slate-800', 'text-white');
            b.classList.add('text-gray-600', 'hover:bg-gray-100');
        });
        const panel = document.getElementById('panel-' + tab);
        const btn = document.getElementById('tab-' + tab);
        if (panel) panel.classList.remove('hidden');
        if (btn) {
            btn.classList.add('bg-slate-800', 'text-white');
            btn.classList.remove('text-gray-600', 'hover:bg-gray-100');
        }
    }
</script>
    <div class="bg-white rounded-md border border-gray-200 p-2">
        <div class="flex flex-wrap gap-1">
            <button onclick="switchTab('clinic')" id="tab-clinic" class="tab-btn px-4 py-2 rounded text-sm font-semibold transition-all bg-slate-800 text-white">
                <i class="hgi-stroke hgi-building-03 text-base inline mr-1.5"></i>
                Clinic Info
            </button>
            <button onclick="switchTab('financial')" id="tab-financial" class="tab-btn px-4 py-2 rounded text-sm font-semibold transition-all text-gray-600 hover:bg-gray-100">
                <i class="hgi-stroke hgi-money-03 text-base inline mr-1.5"></i>
                Financial
            </button>
        </div>
    </div>

    <script>
    function switchTab(tab) {
        document.querySelectorAll('.tab-panel').forEach(p => p.classList.add('hidden'));
        document.querySelectorAll('.tab-btn').forEach(b => {
            b.classList.remove('bg-slate-800', 'text-white');
            b.classList.add('text-gray-600', 'hover:bg-gray-100');
        });
        const panel = document.getElementById('panel-' + tab);
        const btn = document.getElementById('tab-' + tab);
        if (panel) panel.classList.remove('hidden');
        if (btn) {
            btn.classList.add('bg-slate-800', 'text-white');
            btn.classList.remove('text-gray-600', 'hover:bg-gray-100');
        }
    }
</script>
    <div id="panel-clinic" class="tab-panel">
        <div class="bg-white rounded-md border border-gray-200 overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100">
                <h2 class="text-sm font-bold text-gray-900 flex items-center gap-2">
                    <i class="hgi-stroke hgi-building-03 text-base text-gray-400"></i>
                    Clinic Information
                </h2>
                <p class="text-xs text-gray-400 mt-1">Basic information about your clinic.</p>
            </div>
            <form method="POST" action="{{ route('settings.update') }}" class="p-6 space-y-5" data-ajax>
                @csrf
                @method('PUT')
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">App Name</label>
                        <x-ui.input type="text" name="app_name" value="{{ $val('app_name', config('app.name')) }}" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-slate-300 outline-none transition-all" placeholder="Uzazi Clinic" />
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Clinic Name</label>
                        <x-ui.input type="text" name="clinic_name" value="{{ $val('clinic_name', 'Uzazi Clinic') }}" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-slate-300 outline-none transition-all" placeholder="Uzazi Clinic" />
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Clinic Phone</label>
                        <x-ui.input type="text" name="clinic_phone" value="{{ $val('clinic_phone', '+255 700 000 000') }}" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-slate-300 outline-none transition-all" placeholder="+255 700 000 000" />
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Clinic Email</label>
                        <x-ui.input type="email" name="clinic_email" value="{{ $val('clinic_email', 'info@drissa.co.tz') }}" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-slate-300 outline-none transition-all" placeholder="info@yourclinic.co.tz" />
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Clinic Address</label>
                        <x-ui.textarea name="clinic_address" rows="2" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-slate-300 outline-none transition-all" placeholder="Dar es Salaam, Tanzania">{{ $val('clinic_address', 'Dar es Salaam, Tanzania') }}</x-ui.textarea>
                    </div>
                </div>
                <div class="pt-4 border-t border-gray-100">
                    <button type="submit" class="bg-slate-800 hover:bg-slate-700 text-white text-sm font-medium px-6 py-2.5 rounded transition-all flex items-center gap-2">
                        <i class="hgi-stroke hgi-tick-02 text-base"></i>
                        Save Settings
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
    function switchTab(tab) {
        document.querySelectorAll('.tab-panel').forEach(p => p.classList.add('hidden'));
        document.querySelectorAll('.tab-btn').forEach(b => {
            b.classList.remove('bg-slate-800', 'text-white');
            b.classList.add('text-gray-600', 'hover:bg-gray-100');
        });
        const panel = document.getElementById('panel-' + tab);
        const btn = document.getElementById('tab-' + tab);
        if (panel) panel.classList.remove('hidden');
        if (btn) {
            btn.classList.add('bg-slate-800', 'text-white');
            btn.classList.remove('text-gray-600', 'hover:bg-gray-100');
        }
    }
</script>
    <div id="panel-financial" class="tab-panel hidden">
        <div class="bg-white rounded-md border border-gray-200 overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100">
                <h2 class="text-sm font-bold text-gray-900 flex items-center gap-2">
                    <i class="hgi-stroke hgi-money-03 text-base text-gray-400"></i>
                    Financial Settings
                </h2>
                <p class="text-xs text-gray-400 mt-1">Currency and pricing configuration.</p>
            </div>
            <form method="POST" action="{{ route('settings.update') }}" class="p-6 space-y-5" data-ajax>
                @csrf
                @method('PUT')
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Currency Symbol</label>
                        <x-ui.input type="text" name="currency" value="{{ $val('currency', 'TSh') }}" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-slate-300 outline-none transition-all" placeholder="TSh" />
                        <p class="text-xs text-gray-400 mt-1">e.g. TSh, USD, EUR</p>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Consultation Fee (TSh)</label>
                        <x-ui.input type="number" step="0.01" min="0" name="consultation_fee" value="{{ $val('consultation_fee', '10000') }}" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-slate-300 outline-none transition-all" placeholder="10000" />
                        <p class="text-xs text-gray-400 mt-1">Default fee for general consultation</p>
                    </div>
                </div>
                <div class="pt-4 border-t border-gray-100">
                    <button type="submit" class="bg-slate-800 hover:bg-slate-700 text-white text-sm font-medium px-6 py-2.5 rounded transition-all flex items-center gap-2">
                        <i class="hgi-stroke hgi-tick-02 text-base"></i>
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
            b.classList.remove('bg-slate-800', 'text-white');
            b.classList.add('text-gray-600', 'hover:bg-gray-100');
        });
        const panel = document.getElementById('panel-' + tab);
        const btn = document.getElementById('tab-' + tab);
        if (panel) panel.classList.remove('hidden');
        if (btn) {
            btn.classList.add('bg-slate-800', 'text-white');
            btn.classList.remove('text-gray-600', 'hover:bg-gray-100');
        }
    }
</script>
@endpush
@endsection
