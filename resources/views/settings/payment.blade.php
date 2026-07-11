@extends('layouts.dashboard')

@section('title', 'Payment Gateways - ' . config('app.name', 'Laravel'))
@section('page_title', 'Payment Gateway Settings')

@section('content')
<div class="max-w-3xl mx-auto bg-white rounded-xl border border-gray-100 p-6 shadow-sm">
    <form method="POST" action="{{ route('settings.update') }}" class="space-y-6">
        @csrf
        @method('PUT')

        @php
            $gateway = $settings['payment_gateway']?->value ?? 'cash';
        @endphp

        <div>
            <label class="block text-xs font-medium text-gray-700 mb-1">Default Payment Gateway</label>
            <select name="payment_gateway" class="w-full border rounded-lg px-3 py-2 text-sm">
                <option value="cash" {{ $gateway === 'cash' ? 'selected' : '' }}>Cash / Manual</option>
                <option value="mpesa" {{ $gateway === 'mpesa' ? 'selected' : '' }}>M-Pesa</option>
                <option value="tigo_pesa" {{ $gateway === 'tigo_pesa' ? 'selected' : '' }}>Tigo Pesa</option>
                <option value="airtel_money" {{ $gateway === 'airtel_money' ? 'selected' : '' }}>Airtel Money</option>
                <option value="bank" {{ $gateway === 'bank' ? 'selected' : '' }}>Bank Transfer</option>
            </select>
        </div>

        <div class="border-t border-gray-100 pt-4">
            <h3 class="text-sm font-semibold text-gray-900 mb-4">Mobile Money Configuration</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">API Key</label>
                    <input type="text" name="payment_api_key" value="{{ $settings['payment_api_key']?->value ?? '' }}" class="w-full border rounded-lg px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">API Secret</label>
                    <input type="password" name="payment_api_secret" value="{{ $settings['payment_api_secret']?->value ?? '' }}" class="w-full border rounded-lg px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Shortcode / Merchant ID</label>
                    <input type="text" name="payment_merchant_id" value="{{ $settings['payment_merchant_id']?->value ?? '' }}" class="w-full border rounded-lg px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Callback URL</label>
                    <input type="text" name="payment_callback_url" value="{{ $settings['payment_callback_url']?->value ?? url('/payment/callback') }}" class="w-full border rounded-lg px-3 py-2 text-sm">
                </div>
            </div>
        </div>

        <div class="border-t border-gray-100 pt-4">
            <h3 class="text-sm font-semibold text-gray-900 mb-4">Bank Configuration</h3>
            <div class="space-y-3">
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Bank Name</label>
                    <input type="text" name="payment_bank_name" value="{{ $settings['payment_bank_name']?->value ?? '' }}" class="w-full border rounded-lg px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Account Number</label>
                    <input type="text" name="payment_bank_account" value="{{ $settings['payment_bank_account']?->value ?? '' }}" class="w-full border rounded-lg px-3 py-2 text-sm">
                </div>
            </div>
        </div>

        <div class="pt-4">
            <button type="submit" class="bg-emerald-600 text-white text-sm font-medium px-5 py-2 rounded-lg hover:bg-emerald-700">Save Payment Settings</button>
        </div>
    </form>
</div>
@endsection
