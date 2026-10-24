@extends('layouts.dashboard')

@section('title', 'Ultrasound Results - ' . config('app.name', 'Laravel'))
@section('page_title', 'Ultrasound Results')

@section('content')
<div class="max-w-4xl mx-auto space-y-5">
    {{-- Attachments --}}
    <div class="bg-slate-800 rounded-md p-6 text-white">
        <div class="flex flex-wrap items-end justify-between gap-3">
            <div>
                <p class="text-[10px] uppercase tracking-wider text-slate-400 mb-1">Imaging</p>
                <h1 class="text-xl font-bold">Ultrasound Results</h1>
                <p class="text-xs text-slate-400 mt-1">Order #{{ $order->id }} — {{ $order->patient?->fullName() }}</p>
            </div>
            <a href="{{ route('ultrasound.queue') }}" class="inline-flex items-center gap-1.5 bg-white hover:bg-gray-100 text-slate-800 text-xs font-semibold px-3 py-1.5 rounded transition-colors">
                <i class="hgi-stroke hgi-arrow-left-02 text-sm"></i>
                Back to Queue
            </a>
        </div>
    </div>

    {{-- Attachments --}}
    <div class="bg-white rounded-md border border-gray-200 p-5">
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

    {{-- Attachments --}}
    <div class="bg-white rounded-md border border-gray-200 p-5">
        <h3 class="text-sm font-bold text-gray-900 mb-3">Services Performed</h3>
        <div class="flex flex-wrap gap-2">
            @foreach($order->items as $item)
                <span class="inline-block px-2 py-0.5 bg-gray-100 text-gray-600 text-xs rounded">{{ $item->ultrasoundService?->name }}</span>
            @endforeach
        </div>
    </div>

    {{-- Attachments --}}
    <div class="bg-white rounded-md border border-gray-200 p-5 space-y-4">
        <h3 class="text-sm font-bold text-gray-900">Results</h3>
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
    <div class="bg-white rounded-md border border-gray-200 p-5">
        <h3 class="text-sm font-bold text-gray-900 mb-3">Attachments</h3>
        @if($order->attachments->isNotEmpty())
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                @foreach($order->attachments as $attachment)
                    <div class="border border-gray-200 rounded p-3 flex items-center gap-3">
                        <div class="w-10 h-10 rounded bg-slate-100 flex items-center justify-center flex-shrink-0">
                            <i class="hgi-stroke hgi-file-01 text-xl text-slate-600"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 truncate">{{ $attachment->file_name }}</p>
                            <p class="text-xs text-gray-500">{{ number_format($attachment->file_size / 1024, 1) }} KB</p>
                        </div>
                        <a href="{{ route('file.serve', $attachment->file_path) }}" target="_blank" class="text-slate-600 hover:text-slate-800 text-xs font-medium">View</a>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-xs text-gray-400">No attachments uploaded.</p>
        @endif
    </div>
</div>
@endsection
