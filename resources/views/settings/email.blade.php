@extends('layouts.dashboard')

@section('title', 'Email Config - ' . config('app.name', 'Laravel'))
@section('page_title', 'Email Configuration')

@section('content')
<div class="max-w-4xl mx-auto bg-white rounded-xl border border-gray-100 p-6 shadow-sm">
    <form method="POST" action="{{ route('settings.update') }}" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        @csrf
        @method('PUT')

        @foreach ($settings as $setting)
            <div class="sm:col-span-2">
                <label class="block text-xs font-medium text-gray-700 mb-1">{{ ucwords(str_replace('_', ' ', $setting->key)) }}</label>
                <input type="text" name="{{ $setting->key }}" value="{{ $setting->value }}" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
            </div>
        @endforeach

        <div class="sm:col-span-2 pt-4">
            <button type="submit" class="bg-emerald-600 text-white text-sm font-medium px-5 py-2 rounded-lg hover:bg-emerald-700">Save Email Settings</button>
        </div>
    </form>
</div>
@endsection
