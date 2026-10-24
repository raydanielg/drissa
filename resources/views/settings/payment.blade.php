@extends('layouts.dashboard')

@section('title', 'Payment Gateways - ' . config('app.name', 'Laravel'))
@section('page_title', 'Payment Gateway Settings')

@section('content')
<div class="max-w-4xl mx-auto space-y-5">

    {{-- Bank Configuration --}}
    <div class="bg-slate-800 rounded-md p-6 text-white">
        <p class="text-[10px] uppercase tracking-wider text-slate-400 mb-1">Settings</p>
        <h1 class="text-xl font-bold">Payment Gateway Settings</h1>
        <p class="text-xs text-slate-400 mt-1">Choose how patients can pay for services.</p>
    </div>

    @if(session('status'))
        <div class="bg-white border-l-2 border-emerald-500 rounded-md p-3 flex items-center gap-3">
            <i class="hgi-stroke hgi-checkmark-circle-02 text-base text-emerald-600"></i>
            <p class="text-xs font-semibold text-emerald-700">{{ session('status') }}</p>
        </div>
    @endif

    {{-- Bank Configuration --}}
    <div class="bg-white rounded-md border border-gray-200 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100">
            <h2 class="text-sm font-bold text-gray-900">Payment Gateway</h2>
            <p class="text-xs text-gray-500 mt-0.5">Choose how patients can pay for services.</p>
        </div>
        <form method="POST" action="{{ route('settings.update') }}" class="p-5 space-y-5" data-ajax>
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
                            <div class="border rounded-md p-3 text-center transition-all peer-checked:border-slate-600 peer-checked:bg-slate-50 {{ $gateway === $value ? 'border-slate-600 bg-slate-50' : 'border-gray-200 hover:border-gray-300' }}">
                                <i class="hgi-stroke {{ $icon }} text-2xl mx-auto mb-1.5 {{ $gateway === $value ? 'text-slate-700' : 'text-gray-400' }}"></i>
                                <span class="text-xs font-semibold {{ $gateway === $value ? 'text-slate-800' : 'text-gray-600' }}">{{ $label }}</span>
                            </div>
                        </label>
                    @endforeach
                </div>
            </div>

            {{-- Bank Configuration --}}
            <div class="border-t border-gray-100 pt-5">
                <h3 class="text-sm font-bold text-gray-900 mb-4 flex items-center gap-2">
                    <i class="hgi-stroke hgi-smart-phone-01 text-base text-gray-400"></i>
                    Mobile Money Configuration
                </h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">API Key</label>
                        <x-ui.input type="text" name="payment_api_key" value="{{ $settings['payment_api_key']?->value ?? '' }}" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-slate-300 outline-none" placeholder="Enter API key" />
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">API Secret</label>
                        <x-ui.input type="password" name="payment_api_secret" value="{{ $settings['payment_api_secret']?->value ?? '' }}" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-slate-300 outline-none" placeholder="••••••••" />
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Shortcode / Merchant ID</label>
                        <x-ui.input type="text" name="payment_merchant_id" value="{{ $settings['payment_merchant_id']?->value ?? '' }}" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-slate-300 outline-none" placeholder="e.g. 123456" />
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Callback URL</label>
                        <x-ui.input type="text" name="payment_callback_url" value="{{ $settings['payment_callback_url']?->value ?? url('/payment/callback') }}" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-slate-300 outline-none" placeholder="https://yourclinic.co.tz/payment/callback" />
                    </div>
                </div>
            </div>

            {{-- Bank Configuration --}}
            <div class="border-t border-gray-100 pt-5">
                <h3 class="text-sm font-bold text-gray-900 mb-4 flex items-center gap-2">
                    <i class="hgi-stroke hgi-credit-card text-base text-gray-400"></i>
                    Bank Configuration
                </h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Bank Name</label>
                        <x-ui.input type="text" name="payment_bank_name" value="{{ $settings['payment_bank_name']?->value ?? '' }}" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-slate-300 outline-none" placeholder="e.g. CRDB Bank" />
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Account Number</label>
                        <x-ui.input type="text" name="payment_bank_account" value="{{ $settings['payment_bank_account']?->value ?? '' }}" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-slate-300 outline-none" placeholder="e.g. 0150-123456-001" />
                    </div>
                </div>
            </div>

            <div class="pt-4 border-t border-gray-100">
                <button type="submit" class="bg-slate-800 hover:bg-slate-700 text-white text-sm font-medium px-5 py-2 rounded flex items-center gap-2">
                    <i class="hgi-stroke hgi-tick-02 text-base"></i>
                    Save Payment Settings
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
