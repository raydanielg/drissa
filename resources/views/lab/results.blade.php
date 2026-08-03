@extends('layouts.dashboard')

@section('title', 'Lab Results - ' . config('app.name', 'Laravel'))
@section('page_title', 'Lab Results Detail')

@push('styles')
<style>
    @media print {
        body * { visibility: hidden; }
        #printReport, #printReport * { visibility: visible; }
        #printReport { position: absolute; left: 0; top: 0; width: 100%; }
        .no-print { display: none !important; }
        @page { margin: 1.5cm; }
    }
    #printReport { display: none; }
    @media print {
        #printReport { display: block; }
    }
</style>
@endpush

@section('content')
<div class="space-y-6">
    @if (session('status'))
        <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm animate-fade">{{ session('status') }}</div>
    @endif

    {{-- Action Bar --}}
    <div class="flex items-center justify-between no-print">
        <a href="{{ route('lab.queue') }}" class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-gray-700 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Back to Lab Queue
        </a>
        <button onclick="window.print()" class="inline-flex items-center gap-2 px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-lg transition-colors shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
            Print / Preview
        </button>
    </div>

    {{-- Order Header Card --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-6 py-5 bg-gradient-to-r from-emerald-50 to-sky-50 border-b border-gray-100">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-xl bg-emerald-100 flex items-center justify-center">
                            <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <div>
                            <h2 class="text-lg font-bold text-gray-900">Order #{{ $order->id }}</h2>
                            <p class="text-sm text-gray-500">{{ $order->visit->visit_number }} • {{ $order->visit->patient->fullName() }}</p>
                        </div>
                    </div>
                </div>
                <div class="text-right">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-700">
                        <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Completed
                    </span>
                    <p class="text-xs text-gray-400 mt-1">{{ $order->completed_at?->format('M d, Y H:i') }}</p>
                </div>
            </div>
        </div>

        {{-- Patient & Order Info --}}
        <div class="px-6 py-4 grid grid-cols-2 md:grid-cols-4 gap-4 border-b border-gray-100">
            <div>
                <p class="text-xs font-medium text-gray-400 uppercase tracking-wide">Patient</p>
                <p class="text-sm font-semibold text-gray-800 mt-0.5">{{ $order->visit->patient->fullName() }}</p>
            </div>
            <div>
                <p class="text-xs font-medium text-gray-400 uppercase tracking-wide">Ordered By</p>
                <p class="text-sm font-semibold text-gray-800 mt-0.5">Dr. {{ $order->doctor?->name ?? 'Unknown' }}</p>
            </div>
            <div>
                <p class="text-xs font-medium text-gray-400 uppercase tracking-wide">Processed By</p>
                <p class="text-sm font-semibold text-gray-800 mt-0.5">{{ $order->labTech?->name ?? 'Unknown' }}</p>
            </div>
            <div>
                <p class="text-xs font-medium text-gray-400 uppercase tracking-wide">Order Date</p>
                <p class="text-sm font-semibold text-gray-800 mt-0.5">{{ $order->created_at->format('M d, Y') }}</p>
            </div>
        </div>
    </div>

    {{-- Results per Test --}}
    @php
        $flagStyles = [
            'normal' => ['bg' => 'bg-emerald-50', 'text' => 'text-emerald-700', 'border' => 'border-emerald-200', 'icon' => '🟢', 'label' => 'Normal'],
            'high' => ['bg' => 'bg-amber-50', 'text' => 'text-amber-700', 'border' => 'border-amber-200', 'icon' => '🟡', 'label' => 'High'],
            'low' => ['bg' => 'bg-amber-50', 'text' => 'text-amber-700', 'border' => 'border-amber-200', 'icon' => '🟡', 'label' => 'Low'],
            'critical' => ['bg' => 'bg-red-50', 'text' => 'text-red-700', 'border' => 'border-red-200', 'icon' => '🔴', 'label' => 'Critical'],
        ];
    @endphp

    @foreach ($order->items as $item)
        @php
            $itemResults = $order->results->where('lab_order_item_id', $item->id);
        @endphp
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-3">
                <div class="w-9 h-9 rounded-lg bg-violet-100 flex items-center justify-center">
                    <svg class="w-5 h-5 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-gray-900">{{ $item->labTest?->name ?? 'Unknown Test' }}</h3>
                    <p class="text-xs text-gray-400">{{ $itemResults->count() }} parameter(s) measured</p>
                </div>
            </div>
            <div class="overflow-x-auto">
                @if ($itemResults->isEmpty())
                    <div class="px-6 py-6 text-center text-sm text-gray-400">No results recorded for this test</div>
                @else
                    <table class="w-full text-sm text-left">
                        <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                            <tr>
                                <th class="px-6 py-3">Parameter</th>
                                <th class="px-6 py-3">Value</th>
                                <th class="px-6 py-3">Unit</th>
                                <th class="px-6 py-3">Reference Range</th>
                                <th class="px-6 py-3">Flag</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($itemResults as $result)
                                @php $style = $flagStyles[$result->flag] ?? $flagStyles['normal']; @endphp
                                <tr class="border-t border-gray-100 hover:bg-gray-50/50 transition-colors">
                                    <td class="px-6 py-3 font-medium text-gray-800">{{ $result->parameter }}</td>
                                    <td class="px-6 py-3 font-bold text-gray-900">{{ $result->value }}</td>
                                    <td class="px-6 py-3 text-gray-600">{{ $result->unit ?? '-' }}</td>
                                    <td class="px-6 py-3 text-gray-500 text-xs">{{ $result->reference_range ?? '-' }}</td>
                                    <td class="px-6 py-3">
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium {{ $style['bg'] }} {{ $style['text'] }} border {{ $style['border'] }}">
                                            {{ $style['icon'] }} {{ $style['label'] }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>
    @endforeach

    {{-- Attachments --}}
    @if ($order->attachments->isNotEmpty())
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden no-print">
            <div class="px-6 py-4 border-b border-gray-100">
                <h3 class="text-sm font-bold text-gray-900">Attached Reports</h3>
            </div>
            <div class="p-5 flex flex-wrap gap-3">
                @foreach ($order->attachments as $attachment)
                    <a href="{{ asset('storage/' . $attachment->file_path) }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-3 bg-gray-50 hover:bg-gray-100 border border-gray-200 rounded-lg text-sm text-gray-700 transition-colors">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                        <div>
                            <p class="font-medium">{{ $attachment->file_name }}</p>
                            <p class="text-xs text-gray-400">{{ number_format($attachment->file_size / 1024, 1) }} KB</p>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    @endif
</div>

{{-- Print Report --}}
<div id="printReport">
    <div style="text-align:center; margin-bottom: 24px;">
        <div style="display:flex; align-items:center; justify-content:center; gap:12px; margin-bottom:8px;">
            <img src="{{ asset('logo.png') }}" alt="logo" style="width:48px; height:48px; border-radius:8px;">
            <div style="text-align:left;">
                <h1 style="font-size:22px; font-weight:800; color:#024938; margin:0;">{{ config('app.name', 'Clinic') }}</h1>
                <p style="font-size:11px; color:#666; margin:2px 0 0;">Laboratory Report</p>
            </div>
        </div>
        <div style="height:3px; background:#024938; border-radius:2px; margin:12px 0;"></div>
    </div>

    {{-- Report Meta --}}
    <table style="width:100%; font-size:11px; margin-bottom:20px; border-collapse:collapse;">
        <tr>
            <td style="padding:4px 0;"><strong>Report #:</strong> {{ $order->id }}</td>
            <td style="padding:4px 0;"><strong>Date:</strong> {{ $order->completed_at?->format('d M Y H:i') }}</td>
        </tr>
        <tr>
            <td style="padding:4px 0;"><strong>Patient:</strong> {{ $order->visit->patient->fullName() }}</td>
            <td style="padding:4px 0;"><strong>Visit #:</strong> {{ $order->visit->visit_number }}</td>
        </tr>
        <tr>
            <td style="padding:4px 0;"><strong>Ordered By:</strong> Dr. {{ $order->doctor?->name ?? 'Unknown' }}</td>
            <td style="padding:4px 0;"><strong>Processed By:</strong> {{ $order->labTech?->name ?? 'Unknown' }}</td>
        </tr>
    </table>

    {{-- Results per Test --}}
    @foreach ($order->items as $item)
        @php $itemResults = $order->results->where('lab_order_item_id', $item->id); @endphp
        <div style="margin-bottom:20px;">
            <h2 style="font-size:13px; font-weight:700; color:#024938; border-bottom:2px solid #024938; padding-bottom:4px; margin-bottom:8px;">
                {{ $item->labTest?->name ?? 'Unknown Test' }}
            </h2>
            @if ($itemResults->isEmpty())
                <p style="font-size:11px; color:#999;">No results recorded</p>
            @else
                <table style="width:100%; font-size:11px; border-collapse:collapse;">
                    <thead>
                        <tr style="background:#f3f4f6;">
                            <th style="text-align:left; padding:6px 8px; border:1px solid #e5e7eb;">Parameter</th>
                            <th style="text-align:left; padding:6px 8px; border:1px solid #e5e7eb;">Value</th>
                            <th style="text-align:left; padding:6px 8px; border:1px solid #e5e7eb;">Unit</th>
                            <th style="text-align:left; padding:6px 8px; border:1px solid #e5e7eb;">Reference Range</th>
                            <th style="text-align:left; padding:6px 8px; border:1px solid #e5e7eb;">Flag</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($itemResults as $result)
                            @php
                                $flagLabel = match($result->flag) {
                                    'normal' => 'Normal',
                                    'high' => 'High',
                                    'low' => 'Low',
                                    'critical' => 'Critical',
                                    default => ucfirst($result->flag),
                                };
                                $flagColor = match($result->flag) {
                                    'normal' => '#059669',
                                    'high' => '#D97706',
                                    'low' => '#D97706',
                                    'critical' => '#DC2626',
                                    default => '#666',
                                };
                            @endphp
                            <tr>
                                <td style="padding:6px 8px; border:1px solid #e5e7eb; font-weight:600;">{{ $result->parameter }}</td>
                                <td style="padding:6px 8px; border:1px solid #e5e7eb; font-weight:700;">{{ $result->value }}</td>
                                <td style="padding:6px 8px; border:1px solid #e5e7eb;">{{ $result->unit ?? '-' }}</td>
                                <td style="padding:6px 8px; border:1px solid #e5e7eb; color:#666;">{{ $result->reference_range ?? '-' }}</td>
                                <td style="padding:6px 8px; border:1px solid #e5e7eb; color:{{ $flagColor }}; font-weight:700;">{{ $flagLabel }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    @endforeach

    {{-- Attachments --}}
    @if ($order->attachments->isNotEmpty())
        <div style="margin-top:20px;">
            <h2 style="font-size:13px; font-weight:700; color:#024938; margin-bottom:8px;">Attached Reports:</h2>
            <ul style="font-size:11px; color:#666; padding-left:16px;">
                @foreach ($order->attachments as $attachment)
                    <li>{{ $attachment->file_name }} ({{ number_format($attachment->file_size / 1024, 1) }} KB)</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Footer --}}
    <div style="margin-top:32px; padding-top:12px; border-top:1px solid #e5e7eb; text-align:center;">
        <p style="font-size:10px; color:#999;">This report is computer-generated from {{ config('app.name', 'Clinic') }} Laboratory System. • Generated on {{ now()->format('d M Y H:i') }}</p>
    </div>
</div>
@endsection
