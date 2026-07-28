@extends('layouts.dashboard')

@section('title', 'Email Config - ' . config('app.name', 'Laravel'))
@section('page_title', 'Email Configuration')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    @if(session('status'))
        <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-4 flex items-center gap-3">
            <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <p class="text-sm font-semibold text-emerald-700">{{ session('status') }}</p>
        </div>
    @endif

    {{-- SMTP Settings --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="bg-gradient-to-r from-sky-600 to-sky-700 px-6 py-4">
            <h2 class="text-white font-bold text-lg flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                SMTP Configuration
            </h2>
            <p class="text-sky-100 text-xs mt-1">Configure your email server to send notifications and reports.</p>
        </div>
        <form method="POST" action="{{ route('settings.update') }}" class="p-6 space-y-5" data-ajax>
            @csrf
            @method('PUT')
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">From Email Address</label>
                    <input type="email" name="mail_from" value="{{ $settings['mail_from']->value ?? '' }}" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all" placeholder="noreply@yourclinic.com">
                    <p class="text-xs text-gray-400 mt-1">The email address that appears in the "From" field.</p>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">SMTP Host</label>
                    <input type="text" name="mail_host" value="{{ $settings['mail_host']->value ?? '' }}" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all" placeholder="smtp.gmail.com">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">SMTP Port</label>
                    <input type="text" name="mail_port" value="{{ $settings['mail_port']->value ?? '' }}" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all" placeholder="587">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">SMTP Username</label>
                    <input type="text" name="mail_username" value="{{ $settings['mail_username']->value ?? '' }}" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all" placeholder="your@email.com">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">SMTP Password</label>
                    <input type="password" name="mail_password" value="{{ $settings['mail_password']->value ?? '' }}" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all" placeholder="••••••••">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Encryption</label>
                    <select name="mail_encryption" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all">
                        <option value="tls" {{ ($settings['mail_encryption']->value ?? 'tls') === 'tls' ? 'selected' : '' }}>TLS</option>
                        <option value="ssl" {{ ($settings['mail_encryption']->value ?? '') === 'ssl' ? 'selected' : '' }}>SSL</option>
                        <option value="none" {{ ($settings['mail_encryption']->value ?? '') === 'none' ? 'selected' : '' }}>None</option>
                    </select>
                </div>
            </div>
            <div class="pt-4 border-t border-gray-100">
                <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-bold px-6 py-2.5 rounded-lg shadow-sm transition-all flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Save Email Settings
                </button>
            </div>
        </form>
    </div>

    {{-- Quick Reference --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
        <h3 class="text-sm font-bold text-gray-900 mb-4 flex items-center gap-2">
            <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            Common SMTP Settings
        </h3>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <div class="border border-gray-100 rounded-lg p-3">
                <p class="text-xs font-bold text-gray-700 mb-1">Gmail</p>
                <p class="text-xs text-gray-500">Host: smtp.gmail.com<br>Port: 587<br>Encryption: TLS</p>
            </div>
            <div class="border border-gray-100 rounded-lg p-3">
                <p class="text-xs font-bold text-gray-700 mb-1">Mailtrap</p>
                <p class="text-xs text-gray-500">Host: smtp.mailtrap.io<br>Port: 2525<br>Encryption: TLS</p>
            </div>
            <div class="border border-gray-100 rounded-lg p-3">
                <p class="text-xs font-bold text-gray-700 mb-1">Office 365</p>
                <p class="text-xs text-gray-500">Host: smtp.office365.com<br>Port: 587<br>Encryption: TLS</p>
            </div>
        </div>
    </div>
</div>
@endsection
