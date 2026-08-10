@extends('layouts.dashboard')

@section('title', 'Ultrasound Results - ' . config('app.name', 'Laravel'))
@section('page_title', 'Ultrasound Results')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-lg font-bold text-gray-900">Ultrasound Results</h2>
            <p class="text-sm text-gray-500">Order #{{ $order->id }} - {{ $order->patient?->fullName() }}</p>
        </div>
        <a href="{{ route('ultrasound.queue') }}" class="inline-flex items-center gap-2 text-sm text-gray-600 hover:text-gray-900">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Back to Queue
        </a>
    </div>

    {{-- Patient Info --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <p class="text-xs font-medium text-gray-500 uppercase">Patient</p>
                <p class="text-sm font-medium text-gray-900 mt-1">{{ $order->patient?->fullName() }}</p>
                <p class="text-xs text-gray-500">{{ $order->patient?->mrn }}</p>
            </div>
            <div>
                <p class="text-xs font-medium text-gray-500 uppercase">Ordered By</p>
                <p class="text-sm font-medium text-gray-900 mt-1">{{ $order->doctor?->name ?? 'N/A' }}</p>
            </div>
            <div>
                <p class="text-xs font-medium text-gray-500 uppercase">Completed</p>
                <p class="text-sm font-medium text-gray-900 mt-1">{{ $order->completed_at?->format('M d, Y H:i') }}</p>
            </div>
        </div>
    </div>

    {{-- Services --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
        <h3 class="text-sm font-semibold text-gray-900 mb-3">Services Performed</h3>
        <div class="flex flex-wrap gap-2">
            @foreach($order->items as $item)
                <span class="inline-block px-3 py-1.5 bg-purple-50 text-purple-700 text-sm rounded-lg">{{ $item->ultrasoundService?->name }}</span>
            @endforeach
        </div>
    </div>

    {{-- Findings & Impression --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 space-y-4">
        <h3 class="text-sm font-semibold text-gray-900">Results</h3>
        <div>
            <p class="text-xs font-medium text-gray-500 uppercase mb-1">Findings</p>
            <p class="text-sm text-gray-700 whitespace-pre-wrap">{{ $order->findings ?? 'No findings recorded.' }}</p>
        </div>
        <div>
            <p class="text-xs font-medium text-gray-500 uppercase mb-1">Impression</p>
            <p class="text-sm text-gray-700 whitespace-pre-wrap">{{ $order->impression ?? 'No impression recorded.' }}</p>
        </div>
    </div>

    {{-- Attachments --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
        <h3 class="text-sm font-semibold text-gray-900 mb-3">Attachments</h3>
        @if($order->attachments->isNotEmpty())
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($order->attachments as $attachment)
                    <div class="border border-gray-200 rounded-lg p-3 flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg bg-blue-50 flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 truncate">{{ $attachment->file_name }}</p>
                            <p class="text-xs text-gray-500">{{ number_format($attachment->file_size / 1024, 1) }} KB</p>
                        </div>
                        <a href="{{ Storage::url($attachment->file_path) }}" target="_blank" class="text-emerald-600 hover:text-emerald-700 text-xs font-medium">View</a>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-sm text-gray-500">No attachments uploaded.</p>
        @endif
    </div>
</div>
@endsection
