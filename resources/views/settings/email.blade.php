@extends('layouts.dashboard')

@section('title', 'Email Config - ' . config('app.name', 'Laravel'))
@section('page_title', 'Email Configuration')

@section('content')
<div class="max-w-4xl mx-auto space-y-5">

    {{-- Quick Reference --}}
    <div class="bg-slate-800 rounded-md p-6 text-white">
        <p class="text-[10px] uppercase tracking-wider text-slate-400 mb-1">Settings</p>
        <h1 class="text-xl font-bold">Email Configuration</h1>
        <p class="text-xs text-slate-400 mt-1">Configure your email server to send notifications and reports.</p>
    </div>

    @if(session('status'))
        <div class="bg-white border-l-2 border-emerald-500 rounded-md p-3 flex items-center gap-3">
            <i class="hgi-stroke hgi-checkmark-circle-02 text-base text-emerald-600"></i>
            <p class="text-xs font-semibold text-emerald-700">{{ session('status') }}</p>
        </div>
    @endif

    {{-- Quick Reference --}}
    <div class="bg-white rounded-md border border-gray-200 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100">
            <h2 class="text-sm font-bold text-gray-900">SMTP Configuration</h2>
            <p class="text-xs text-gray-500 mt-0.5">Configure your email server to send notifications and reports.</p>
        </div>
        <form method="POST" action="{{ route('settings.update') }}" class="p-5 space-y-5" data-ajax>
            @csrf
            @method('PUT')
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">From Email Address</label>
                    <x-ui.input type="email" name="mail_from" value="{{ $settings['mail_from']->value ?? '' }}" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-slate-300 outline-none" placeholder="noreply@yourclinic.com" />
                    <p class="text-xs text-gray-400 mt-1">The email address that appears in the "From" field.</p>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">SMTP Host</label>
                    <x-ui.input type="text" name="mail_host" value="{{ $settings['mail_host']->value ?? '' }}" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-slate-300 outline-none" placeholder="smtp.gmail.com" />
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">SMTP Port</label>
                    <x-ui.input type="text" name="mail_port" value="{{ $settings['mail_port']->value ?? '' }}" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-slate-300 outline-none" placeholder="587" />
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">SMTP Username</label>
                    <x-ui.input type="text" name="mail_username" value="{{ $settings['mail_username']->value ?? '' }}" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-slate-300 outline-none" placeholder="your@email.com" />
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">SMTP Password</label>
                    <x-ui.input type="password" name="mail_password" value="{{ $settings['mail_password']->value ?? '' }}" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-slate-300 outline-none" placeholder="••••••••" />
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Encryption</label>
                    <x-ui.select name="mail_encryption" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-slate-300 outline-none">
                        <option value="tls" {{ ($settings['mail_encryption']->value ?? 'tls') === 'tls' ? 'selected' : '' }}>TLS</option>
                        <option value="ssl" {{ ($settings['mail_encryption']->value ?? '') === 'ssl' ? 'selected' : '' }}>SSL</option>
                        <option value="none" {{ ($settings['mail_encryption']->value ?? '') === 'none' ? 'selected' : '' }}>None</option>
                    </x-ui.select>
                </div>
            </div>
            <div class="pt-4 border-t border-gray-100">
                <button type="submit" class="bg-slate-800 hover:bg-slate-700 text-white text-sm font-medium px-5 py-2 rounded flex items-center gap-2">
                    <i class="hgi-stroke hgi-tick-02 text-base"></i>
                    Save Email Settings
                </button>
            </div>
        </form>
    </div>

    {{-- Quick Reference --}}
    <div class="bg-white rounded-md border border-gray-200 p-5">
        <h3 class="text-sm font-bold text-gray-900 mb-4 flex items-center gap-2">
            <i class="hgi-stroke hgi-information-circle text-base text-amber-500"></i>
            Common SMTP Settings
        </h3>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <div class="border border-gray-200 rounded p-3">
                <p class="text-xs font-bold text-gray-700 mb-1">Gmail</p>
                <p class="text-xs text-gray-500">Host: smtp.gmail.com<br>Port: 587<br>Encryption: TLS</p>
            </div>
            <div class="border border-gray-200 rounded p-3">
                <p class="text-xs font-bold text-gray-700 mb-1">Mailtrap</p>
                <p class="text-xs text-gray-500">Host: smtp.mailtrap.io<br>Port: 2525<br>Encryption: TLS</p>
            </div>
            <div class="border border-gray-200 rounded p-3">
                <p class="text-xs font-bold text-gray-700 mb-1">Office 365</p>
                <p class="text-xs text-gray-500">Host: smtp.office365.com<br>Port: 587<br>Encryption: TLS</p>
            </div>
        </div>
    </div>
</div>
@endsection
