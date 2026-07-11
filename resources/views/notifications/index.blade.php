@extends('layouts.dashboard')

@section('title', 'Send Notification - ' . config('app.name', 'Laravel'))
@section('page_title', 'Send Notification')

@section('content')
<div class="max-w-4xl mx-auto bg-white rounded-xl border border-gray-100 p-6 shadow-sm">
    <form method="POST" action="{{ route('notifications.send') }}" class="space-y-4">
        @csrf
        <div>
            <label class="block text-xs font-medium text-gray-700 mb-1">Type</label>
            <select name="type" class="w-full border rounded-lg px-3 py-2 text-sm">
                <option value="sms">SMS</option>
                <option value="email">Email</option>
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-700 mb-1">Recipients (comma separated)</label>
            <textarea name="recipients[]" class="w-full border rounded-lg px-3 py-2 text-sm" rows="3" placeholder="255700000000, 255711111111"></textarea>
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-700 mb-1">Subject (email only)</label>
            <input type="text" name="subject" class="w-full border rounded-lg px-3 py-2 text-sm">
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-700 mb-1">Message</label>
            <textarea name="message" class="w-full border rounded-lg px-3 py-2 text-sm" rows="4" required></textarea>
        </div>
        <button type="submit" class="bg-emerald-600 text-white text-sm font-medium px-5 py-2 rounded-lg hover:bg-emerald-700">Send Notification</button>
    </form>
</div>
@endsection
