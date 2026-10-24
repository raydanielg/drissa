@extends('layouts.dashboard')

@section('title', 'Notification Templates - ' . config('app.name', 'Laravel'))
@section('page_title', 'Email & SMS Templates')

@section('content')
<div class="space-y-5">
    {{-- Existing Templates --}}
    <div class="bg-slate-800 rounded-md p-6 text-white">
        <p class="text-[10px] uppercase tracking-wider text-slate-400 mb-1">Admin</p>
        <h1 class="text-xl font-bold">Email & SMS Templates</h1>
        <p class="text-xs text-slate-400 mt-1">Manage reusable notification templates.</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
        {{-- Existing Templates --}}
        <div class="bg-white rounded-md border border-gray-200 p-5">
            <h2 class="text-sm font-bold text-gray-900 mb-4">New Template</h2>
            <form method="POST" action="{{ route('notifications.templates.store') }}" class="space-y-4">
                @csrf
                <x-ui.select name="type" class="w-full border border-gray-200 rounded px-3 py-2 text-sm bg-white focus:ring-2 focus:ring-slate-300" required>
                    <option value="email">Email</option>
                    <option value="sms">SMS</option>
                </x-ui.select>
                <x-ui.input type="text" name="name" placeholder="Template name" class="w-full border border-gray-200 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-slate-300" required />
                <x-ui.input type="text" name="slug" placeholder="Slug (unique)" class="w-full border border-gray-200 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-slate-300" required />
                <x-ui.input type="text" name="subject" placeholder="Subject (email only)" class="w-full border border-gray-200 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-slate-300" />
                <x-ui.textarea name="body" placeholder="Body" class="w-full border border-gray-200 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-slate-300" rows="4" required></x-ui.textarea>
                <button type="submit" class="bg-slate-800 text-white text-sm font-medium px-5 py-2 rounded hover:bg-slate-700">Save Template</button>
            </form>
        </div>

        {{-- Existing Templates --}}
        <div class="bg-white rounded-md border border-gray-200 p-5 space-y-5">
            <h2 class="text-sm font-bold text-gray-900">Email Templates</h2>
            <ul class="divide-y divide-gray-50">
                @forelse ($emailTemplates as $template)
                    <li class="py-3 text-xs"><span class="font-medium text-gray-900">{{ $template->name }}</span> <span class="text-gray-400">({{ $template->slug }})</span></li>
                @empty
                    <li class="py-3 text-gray-400 text-xs">No email templates</li>
                @endforelse
            </ul>

            <h2 class="text-sm font-bold text-gray-900">SMS Templates</h2>
            <ul class="divide-y divide-gray-50">
                @forelse ($smsTemplates as $template)
                    <li class="py-3 text-xs"><span class="font-medium text-gray-900">{{ $template->name }}</span> <span class="text-gray-400">({{ $template->slug }})</span></li>
                @empty
                    <li class="py-3 text-gray-400 text-xs">No SMS templates</li>
                @endforelse
            </ul>
        </div>
    </div>
</div>
@endsection
