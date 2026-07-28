@extends('layouts.dashboard')

@section('title', 'Payment Gateways - ' . config('app.name', 'Laravel'))
@section('page_title', 'Payment Gateway Settings')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    @if(session('status'))
        <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-4 flex items-center gap-3">
            <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <p class="text-sm font-semibold text-emerald-700">{{ session('status') }}</p>
        </div>
    @endif

    {{-- Gateway Selection --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="bg-gradient-to-r from-violet-600 to-violet-700 px-6 py-4">
            <h2 class="text-white font-bold text-lg flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                Payment Gateway
            </h2>
            <p class="text-violet-100 text-xs mt-1">Choose how patients can pay for services.</p>
        </div>
        <form method="POST" action="{{ route('settings.update') }}" class="p-6 space-y-5" data-ajax>
            @csrf
            @method('PUT')
            @php
                $gateway = $settings['payment_gateway']?->value ?? 'cash';
            @endphp
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-2">Default Payment Method</label>
                <div class="grid grid-cols-2 sm:grid-cols-5 gap-3">
                    @php
                        $gateways = [
                            'cash' => ['Cash', 'bg-emerald-100 text-emerald-700 border-emerald-300', 'M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z'],
                            'mpesa' => ['M-Pesa', 'bg-red-100 text-red-700 border-red-300', 'M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z'],
                            'tigo_pesa' => ['Tigo Pesa', 'bg-blue-100 text-blue-700 border-blue-300', 'M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z'],
                            'airtel_money' => ['Airtel Money', 'bg-rose-100 text-rose-700 border-rose-300', 'M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z'],
                            'bank' => ['Bank Transfer', 'bg-amber-100 text-amber-700 border-amber-300', 'M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z'],
                        ];
                    @endphp
                    @foreach ($gateways as $value => [$label, $color, $icon])
                        <label class="cursor-pointer">
                            <input type="radio" name="payment_gateway" value="{{ $value }}" class="peer sr-only" {{ $gateway === $value ? 'checked' : '' }}>
                            <div class="border-2 rounded-xl p-3 text-center transition-all peer-checked:border-emerald-500 peer-checked:bg-emerald-50 {{ $gateway === $value ? 'border-emerald-500 bg-emerald-50' : 'border-gray-200 hover:border-gray-300' }}">
                                <svg class="w-6 h-6 mx-auto mb-1.5 {{ $gateway === $value ? 'text-emerald-600' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $icon }}"/></svg>
                                <span class="text-xs font-semibold {{ $gateway === $value ? 'text-emerald-700' : 'text-gray-600' }}">{{ $label }}</span>
                            </div>
                        </label>
                    @endforeach
                </div>
            </div>

            {{-- Mobile Money Configuration --}}
            <div class="border-t border-gray-100 pt-5">
                <h3 class="text-sm font-bold text-gray-900 mb-4 flex items-center gap-2">
                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                    Mobile Money Configuration
                </h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">API Key</label>
                        <input type="text" name="payment_api_key" value="{{ $settings['payment_api_key']?->value ?? '' }}" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all" placeholder="Enter API key">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">API Secret</label>
                        <input type="password" name="payment_api_secret" value="{{ $settings['payment_api_secret']?->value ?? '' }}" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all" placeholder="••••••••">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Shortcode / Merchant ID</label>
                        <input type="text" name="payment_merchant_id" value="{{ $settings['payment_merchant_id']?->value ?? '' }}" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all" placeholder="e.g. 123456">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Callback URL</label>
                        <input type="text" name="payment_callback_url" value="{{ $settings['payment_callback_url']?->value ?? url('/payment/callback') }}" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all" placeholder="https://yourclinic.co.tz/payment/callback">
                    </div>
                </div>
            </div>

            {{-- Bank Configuration --}}
            <div class="border-t border-gray-100 pt-5">
                <h3 class="text-sm font-bold text-gray-900 mb-4 flex items-center gap-2">
                    <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                    Bank Configuration
                </h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Bank Name</label>
                        <input type="text" name="payment_bank_name" value="{{ $settings['payment_bank_name']?->value ?? '' }}" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all" placeholder="e.g. CRDB Bank">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Account Number</label>
                        <input type="text" name="payment_bank_account" value="{{ $settings['payment_bank_account']?->value ?? '' }}" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all" placeholder="e.g. 0150-123456-001">
                    </div>
                </div>
            </div>

            <div class="pt-4 border-t border-gray-100">
                <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-bold px-6 py-2.5 rounded-lg shadow-sm transition-all flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Save Payment Settings
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
