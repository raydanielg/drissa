@extends('layouts.dashboard')

@section('title', 'Notification Templates - ' . config('app.name', 'Laravel'))
@section('page_title', 'Email & SMS Templates')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    {{-- Create Template --}}
    <div class="bg-white rounded-xl border border-gray-100 p-6 shadow-sm">
        <h2 class="text-sm font-semibold text-gray-900 mb-4">New Template</h2>
        <form method="POST" action="{{ route('notifications.templates.store') }}" class="space-y-4">
            @csrf
            <select name="type" class="w-full border rounded-lg px-3 py-2 text-sm" required>
                <option value="email">Email</option>
                <option value="sms">SMS</option>
            </select>
            <input type="text" name="name" placeholder="Template name" class="w-full border rounded-lg px-3 py-2 text-sm" required>
            <input type="text" name="slug" placeholder="Slug (unique)" class="w-full border rounded-lg px-3 py-2 text-sm" required>
            <input type="text" name="subject" placeholder="Subject (email only)" class="w-full border rounded-lg px-3 py-2 text-sm">
            <textarea name="body" placeholder="Body" class="w-full border rounded-lg px-3 py-2 text-sm" rows="4" required></textarea>
            <button type="submit" class="bg-emerald-600 text-white text-sm font-medium px-5 py-2 rounded-lg hover:bg-emerald-700">Save Template</button>
        </form>
    </div>

    {{-- Existing Templates --}}
    <div class="bg-white rounded-xl border border-gray-100 p-6 shadow-sm space-y-6">
        <h2 class="text-sm font-semibold text-gray-900">Email Templates</h2>
        <ul class="divide-y divide-gray-100">
            @forelse ($emailTemplates as $template)
                <li class="py-3"><span class="font-medium">{{ $template->name }}</span> <span class="text-gray-400 text-xs">({{ $template->slug }})</span></li>
            @empty
                <li class="py-3 text-gray-400">No email templates</li>
            @endforelse
        </ul>

        <h2 class="text-sm font-semibold text-gray-900">SMS Templates</h2>
        <ul class="divide-y divide-gray-100">
            @forelse ($smsTemplates as $template)
                <li class="py-3"><span class="font-medium">{{ $template->name }}</span> <span class="text-gray-400 text-xs">({{ $template->slug }})</span></li>
            @empty
                <li class="py-3 text-gray-400">No SMS templates</li>
            @endforelse
        </ul>
    </div>
</div>
@endsection
