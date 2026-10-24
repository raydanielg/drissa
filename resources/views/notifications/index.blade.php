@extends('layouts.dashboard')

@section('title', 'Send Notification - ' . config('app.name', 'Laravel'))
@section('page_title', 'Send Notification')

@section('content')
<div class="max-w-4xl mx-auto space-y-5">
    {{-- Header --}}
    <div class="bg-slate-800 rounded-md p-6 text-white">
        <p class="text-[10px] uppercase tracking-wider text-slate-400 mb-1">Admin</p>
        <h1 class="text-xl font-bold">Send Notification</h1>
        <p class="text-xs text-slate-400 mt-1">Send an SMS or email to selected recipients.</p>
    </div>

    <div class="bg-white rounded-md border border-gray-200 p-5">
        <form method="POST" action="{{ route('notifications.send') }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Type</label>
                <x-ui.select name="type" class="w-full border border-gray-200 rounded px-3 py-2 text-sm bg-white focus:ring-2 focus:ring-slate-300">
                    <option value="sms">SMS</option>
                    <option value="email">Email</option>
                </x-ui.select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Recipients (comma separated)</label>
                <x-ui.textarea name="recipients[]" class="w-full border border-gray-200 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-slate-300" rows="3" placeholder="255700000000, 255711111111"></x-ui.textarea>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Subject (email only)</label>
                <x-ui.input type="text" name="subject" class="w-full border border-gray-200 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-slate-300" />
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Message</label>
                <x-ui.textarea name="message" class="w-full border border-gray-200 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-slate-300" rows="4" required></x-ui.textarea>
            </div>
            <button type="submit" class="bg-slate-800 text-white text-sm font-medium px-5 py-2 rounded hover:bg-slate-700">Send Notification</button>
        </form>
    </div>
</div>
@endsection
