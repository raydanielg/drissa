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
    .result-card { page-break-inside: avoid; }
</style>
@endpush

@section('content')
<div class="space-y-5">
    @if (session('status'))
        <div class="p-3 rounded-md bg-white border-l-2 border-emerald-500 text-emerald-700 text-xs">{{ session('status') }}</div>
    @endif

    {{-- Footer --}}
    <div class="flex items-center justify-between no-print">
        <a href="{{ route('lab.queue') }}" class="inline-flex items-center gap-2 text-xs text-slate-600 hover:text-slate-800 transition-colors">
            <i class="hgi-stroke hgi-arrow-left-02 text-sm"></i>
            Back to Lab Queue
        </a>
        <div class="flex items-center gap-2">
            <a href="{{ route('lab.patient-history', $order->visit->patient) }}" class="inline-flex items-center gap-2 px-4 py-2 border border-gray-200 hover:bg-gray-100 text-gray-700 text-xs font-medium rounded transition-colors">
                <i class="hgi-stroke hgi-clock-01 text-sm"></i>
                Patient History
            </a>
            <button onclick="window.print()" class="inline-flex items-center gap-2 px-4 py-2 bg-slate-800 hover:bg-slate-700 text-white text-xs font-semibold rounded transition-colors">
                <i class="hgi-stroke hgi-printer text-sm"></i>
                Print / Preview
            </button>
        </div>
    </div>

    {{-- Footer --}}
    <div class="bg-white rounded-md border border-gray-200 overflow-hidden">
        <div class="px-5 py-5 border-b border-gray-100">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded bg-slate-100 flex items-center justify-center">
                            <i class="hgi-stroke hgi-checkmark-circle-02 text-2xl text-slate-600"></i>
                        </div>
                        <div>
                            <h2 class="text-lg font-bold text-gray-900">Order #{{ $order->id }}</h2>
                            <p class="text-sm text-gray-500">{{ $order->visit->visit_number }} • {{ $order->visit->patient->fullName() }}</p>
                        </div>
                    </div>
                </div>
                <div class="text-right">
                    <span class="inline-flex items-center gap-1.5 text-[10px] font-medium text-gray-600">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                        Completed
                    </span>
                    <p class="text-xs text-gray-400 mt-1">{{ $order->completed_at?->format('M d, Y H:i') }}</p>
                </div>
            </div>
        </div>

        {{-- Footer --}}
        <div class="px-5 py-4 grid grid-cols-2 md:grid-cols-4 gap-4 border-b border-gray-100">
            <div>
                <p class="text-xs font-medium text-gray-400 uppercase tracking-wide">Patient</p>
                <p class="text-sm font-semibold text-gray-800 mt-0.5">{{ $order->visit->patient->fullName() }}</p>
                @if($order->visit->patient?->gender)
                <p class="text-xs text-gray-500">{{ ucfirst($order->visit->patient->gender) }}</p>
                @endif
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

    {{-- Footer --}}
    @php
        $flagStyles = [
            'normal' => ['dot' => 'bg-emerald-500', 'label' => 'Normal'],
            'high' => ['dot' => 'bg-amber-500', 'label' => 'High'],
            'low' => ['dot' => 'bg-amber-500', 'label' => 'Low'],
            'critical' => ['dot' => 'bg-red-500', 'label' => 'Critical'],
        ];
    @endphp

    @foreach ($order->items as $item)
        @php
            $itemResults = $order->results->where('lab_order_item_id', $item->id);
            $abnormalCount = $itemResults->whereNotIn('flag', ['normal'])->count();
        @endphp
        <div class="result-card bg-white rounded-md border border-gray-200 overflow-hidden">
            {{-- Footer --}}
            <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded bg-slate-100 flex items-center justify-center">
                        <i class="hgi-stroke hgi-test-tube-01 text-xl text-slate-600"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-gray-900">{{ $item->labTest?->name ?? 'Unknown Test' }}</h3>
                        <p class="text-xs text-gray-400">{{ $itemResults->count() }} parameter(s) • {{ $item->labTest?->unit ?? '' }}</p>
                    </div>
                </div>
                @if($abnormalCount > 0)
                    <span class="inline-flex items-center gap-1.5 text-[10px] font-medium text-gray-600">
                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                        {{ $abnormalCount }} Abnormal
                    </span>
                @else
                    <span class="inline-flex items-center gap-1.5 text-[10px] font-medium text-gray-600">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                        All Normal
                    </span>
                @endif
            </div>

            {{-- Footer --}}
            @if ($itemResults->isEmpty())
                <div class="px-5 py-8 text-center text-xs text-gray-400">No results recorded for this test</div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-gray-50 text-[10px] uppercase text-gray-400">
                            <tr>
                                <th class="px-5 py-3 font-medium">Parameter</th>
                                <th class="px-5 py-3 font-medium">Result</th>
                                <th class="px-5 py-3 font-medium">Unit</th>
                                <th class="px-5 py-3 font-medium">Reference Range</th>
                                <th class="px-5 py-3 font-medium">Flag</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @foreach ($itemResults as $result)
                                @php $style = $flagStyles[$result->flag] ?? $flagStyles['normal']; @endphp
                                <tr class="hover:bg-gray-50/50 transition-colors">
                                    <td class="px-5 py-3 text-xs font-medium text-gray-800">{{ $result->parameter }}</td>
                                    <td class="px-5 py-3">
                                        <span class="text-sm font-bold {{ $result->flag === 'critical' ? 'text-red-600' : ($result->flag !== 'normal' ? 'text-amber-600' : 'text-gray-900') }}">{{ $result->value }}</span>
                                    </td>
                                    <td class="px-5 py-3 text-xs text-gray-600">{{ $result->unit ?? '-' }}</td>
                                    <td class="px-5 py-3 text-xs text-gray-500">{{ $result->reference_range ?? '-' }}</td>
                                    <td class="px-5 py-3">
                                        <span class="inline-flex items-center gap-1.5 text-[10px] font-medium text-gray-600">
                                            <span class="w-1.5 h-1.5 rounded-full {{ $style['dot'] }}"></span>
                                            {{ $style['label'] }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    @endforeach

    {{-- Footer --}}
    @if ($order->attachments->isNotEmpty())
        <div class="bg-white rounded-md border border-gray-200 overflow-hidden no-print">
            <div class="px-5 py-4 border-b border-gray-100">
                <h3 class="text-sm font-bold text-gray-900">Attached Reports</h3>
            </div>
            <div class="p-5 flex flex-wrap gap-3">
                @foreach ($order->attachments as $attachment)
                    <a href="{{ route('file.serve', $attachment->file_path) }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-3 bg-gray-50 hover:bg-gray-100 border border-gray-200 rounded text-sm text-gray-700 transition-colors">
                        <i class="hgi-stroke hgi-file-01 text-xl text-gray-400"></i>
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

{{-- Footer --}}
<div id="printReport">
    <div style="text-align:center; margin-bottom: 24px;">
        <div style="display:flex; align-items:center; justify-content:center; gap:12px; margin-bottom:8px;">
            <img src="{{ asset('logo.png') }}" alt="logo" style="width:48px; height:48px; border-radius:8px;">
            <div style="text-align:left;">
                <h1 style="font-size:22px; font-weight:800; color:#0f172a; margin:0;">{{ config('app.name', 'Clinic') }}</h1>
                <p style="font-size:11px; color:#666; margin:2px 0 0;">Laboratory Report</p>
            </div>
        </div>
        <div style="height:3px; background:#0f172a; border-radius:2px; margin:12px 0;"></div>
    </div>

    {{-- Footer --}}
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

    {{-- Footer --}}
    @foreach ($order->items as $item)
        @php $itemResults = $order->results->where('lab_order_item_id', $item->id); @endphp
        <div style="margin-bottom:24px; page-break-inside:avoid;">
            <h2 style="font-size:14px; font-weight:700; color:#0f172a; border-bottom:2px solid #0f172a; padding-bottom:6px; margin-bottom:10px;">
                {{ $item->labTest?->name ?? 'Unknown Test' }}
            </h2>
            @if ($itemResults->isEmpty())
                <p style="font-size:11px; color:#999;">No results recorded</p>
            @else
                <table style="width:100%; font-size:11px; border-collapse:collapse;">
                    <thead>
                        <tr style="background:#f3f4f6;">
                            <th style="text-align:left; padding:8px 10px; border:1px solid #e5e7eb; font-weight:600;">Parameter</th>
                            <th style="text-align:left; padding:8px 10px; border:1px solid #e5e7eb; font-weight:600;">Result</th>
                            <th style="text-align:left; padding:8px 10px; border:1px solid #e5e7eb; font-weight:600;">Unit</th>
                            <th style="text-align:left; padding:8px 10px; border:1px solid #e5e7eb; font-weight:600;">Reference Range</th>
                            <th style="text-align:left; padding:8px 10px; border:1px solid #e5e7eb; font-weight:600;">Flag</th>
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
                                <td style="padding:8px 10px; border:1px solid #e5e7eb; font-weight:600;">{{ $result->parameter }}</td>
                                <td style="padding:8px 10px; border:1px solid #e5e7eb; font-weight:700; font-size:13px; color:{{ $flagColor }};">{{ $result->value }}</td>
                                <td style="padding:8px 10px; border:1px solid #e5e7eb;">{{ $result->unit ?? '-' }}</td>
                                <td style="padding:8px 10px; border:1px solid #e5e7eb; color:#666;">{{ $result->reference_range ?? '-' }}</td>
                                <td style="padding:8px 10px; border:1px solid #e5e7eb; color:{{ $flagColor }}; font-weight:700;">{{ $flagLabel }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    @endforeach

    {{-- Footer --}}
    @if ($order->attachments->isNotEmpty())
        <div style="margin-top:20px;">
            <h2 style="font-size:13px; font-weight:700; color:#0f172a; margin-bottom:8px;">Attached Reports:</h2>
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
