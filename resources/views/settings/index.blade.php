@extends('layouts.dashboard')

@section('title', 'General Settings - ' . config('app.name', 'Laravel'))
@section('page_title', 'General Settings')

@section('content')
<div class="max-w-4xl mx-auto bg-white rounded-xl border border-gray-100 p-6 shadow-sm">
    <form method="POST" action="{{ route('settings.update') }}" class="space-y-6">
        @csrf
        @method('PUT')

        @foreach ($settings as $group => $items)
            <div>
                <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wider mb-3">{{ ucfirst($group) }}</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    @foreach ($items as $setting)
                        <div class="sm:col-span-2">
                            <label class="block text-xs font-medium text-gray-700 mb-1">{{ ucwords(str_replace('_', ' ', $setting->key)) }}</label>
                            @if ($setting->type === 'textarea')
                                <textarea name="{{ $setting->key }}" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm" rows="3">{{ $setting->value }}</textarea>
                            @else
                                <input type="{{ $setting->type === 'number' ? 'number' : 'text' }}" name="{{ $setting->key }}" value="{{ $setting->value }}" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach

        <div class="pt-4 border-t border-gray-100">
            <button type="submit" class="bg-emerald-600 text-white text-sm font-medium px-5 py-2 rounded-lg hover:bg-emerald-700">Save Settings</button>
        </div>
    </form>
</div>
@endsection
