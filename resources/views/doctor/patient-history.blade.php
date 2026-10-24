@extends('layouts.dashboard')

@section('title', 'Patient History - ' . $patient->fullName())
@section('page_title', 'Patient History')

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

    {{-- Print Report --}}
    <div class="bg-slate-800 rounded-md p-6 text-white no-print">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-full border-2 border-slate-600 bg-slate-700 flex items-center justify-center text-white font-bold text-xl">
                    {{ strtoupper(substr($patient->first_name ?: 'U', 0, 1)) }}
                </div>
                <div>
                    <p class="text-[10px] uppercase tracking-wider text-slate-400 mb-1">Patient History</p>
                    <h1 class="text-xl font-bold">{{ $patient->fullName() }}</h1>
                    <div class="flex flex-wrap items-center gap-x-3 gap-y-0.5 text-xs text-slate-400 mt-1">
                        <span>{{ $patient->mrn ?? 'No MRN' }}</span>
                        <span>&bull;</span>
                        <span>{{ $patient->phone ?? 'No phone' }}</span>
                        <span>&bull;</span>
                        <span>{{ ucfirst($patient->gender ?? 'N/A') }}</span>
                        @if($patient->date_of_birth)
                        <span>&bull;</span>
                        <span>{{ $patient->date_of_birth->age }} yrs</span>
                        @endif
                        @if($patient->blood_group)
                        <span>&bull;</span>
                        <span class="font-semibold text-red-400">{{ $patient->blood_group }}</span>
                        @endif
                    </div>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('doctor.lab-results') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white/10 hover:bg-white/20 border border-slate-600 text-white text-xs font-medium rounded transition-colors">
                    <i class="hgi-stroke hgi-arrow-left-02 text-sm"></i>
                    Back
                </a>
                <button onclick="window.print()" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white hover:bg-gray-100 text-slate-800 text-xs font-semibold rounded transition-colors">
                    <i class="hgi-stroke hgi-printer text-sm"></i>
                    Print
                </button>
            </div>
        </div>
    </div>

    {{-- Print Report --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3 no-print">
        <div class="bg-white rounded-md border border-gray-200 p-4">
            <div class="flex items-start justify-between">
                <p class="text-[10px] font-medium text-gray-500 uppercase tracking-wide">Total Orders</p>
                <i class="hgi-stroke hgi-test-tube-01 text-base text-gray-400"></i>
            </div>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ $labOrders->count() }}</p>
        </div>
        <div class="bg-white rounded-md border border-gray-200 p-4">
            <div class="flex items-start justify-between">
                <p class="text-[10px] font-medium text-gray-500 uppercase tracking-wide">Total Tests</p>
                <i class="hgi-stroke hgi-clipboard text-base text-gray-400"></i>
            </div>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ $totalTests }}</p>
        </div>
        <div class="bg-white rounded-md border border-emerald-200 p-4">
            <div class="flex items-start justify-between">
                <p class="text-[10px] font-medium text-gray-500 uppercase tracking-wide">Completed</p>
                <i class="hgi-stroke hgi-checkmark-circle-02 text-base text-emerald-600"></i>
            </div>
            <p class="text-2xl font-bold text-emerald-600 mt-1">{{ $completedOrders->count() }}</p>
        </div>
        <div class="bg-white rounded-md border {{ $abnormalResults > 0 ? 'border-red-200' : 'border-gray-200' }} p-4">
            <div class="flex items-start justify-between">
                <p class="text-[10px] font-medium text-gray-500 uppercase tracking-wide">Abnormal</p>
                <i class="hgi-stroke hgi-alert-02 text-base {{ $abnormalResults > 0 ? 'text-red-600' : 'text-gray-400' }}"></i>
            </div>
            <p class="text-2xl font-bold {{ $abnormalResults > 0 ? 'text-red-600' : 'text-gray-900' }} mt-1">{{ $abnormalResults }}</p>
        </div>
    </div>

    {{-- Print Report --}}
    @if($patient->allergies)
    <div class="bg-white border-l-2 border-red-500 rounded-md p-3 flex items-center gap-2 no-print">
        <i class="hgi-stroke hgi-alert-02 text-xl text-red-600 flex-shrink-0"></i>
        <div>
            <p class="text-xs font-semibold text-red-700">Allergies</p>
            <p class="text-xs text-red-600">{{ $patient->allergies }}</p>
        </div>
    </div>
    @endif

    {{-- Print Report --}}
    <div class="space-y-4">
        <div class="flex items-center gap-2 no-print">
            <i class="hgi-stroke hgi-test-tube-01 text-base text-gray-400"></i>
            <h3 class="text-sm font-bold text-gray-900">Lab Results History</h3>
            <span class="text-xs text-gray-400">— Majibu ya vipimo vyote</span>
        </div>

        @php
            $flagStyles = [
                'normal' => ['dot' => 'bg-emerald-500', 'label' => 'Normal'],
                'high' => ['dot' => 'bg-amber-500', 'label' => 'High'],
                'low' => ['dot' => 'bg-amber-500', 'label' => 'Low'],
                'critical' => ['dot' => 'bg-red-500', 'label' => 'Critical'],
            ];
        @endphp

        @forelse ($labOrders as $order)
            @php
                $orderResults = $order->items->flatMap->results;
                $abnormalCount = $orderResults->whereNotIn('flag', ['normal'])->count();
            @endphp
            <div class="result-card bg-white rounded-md border border-gray-200 overflow-hidden">
                {{-- Print Report --}}
                <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded bg-slate-100 flex items-center justify-center">
                            <i class="hgi-stroke hgi-test-tube-01 text-xl text-slate-600"></i>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-gray-900">Order #{{ $order->id }} — {{ $order->visit?->visit_number ?? 'No visit' }}</p>
                            <p class="text-xs text-gray-500">
                                {{ $order->created_at->format('M d, Y H:i') }}
                                @if($order->completed_at) &bull; Completed: {{ $order->completed_at->format('M d, Y H:i') }} @endif
                                &bull; Dr. {{ $order->doctor?->name ?? 'N/A' }}
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        @if($order->status === 'completed' && $abnormalCount > 0)
                            <span class="inline-flex items-center gap-1.5 text-[10px] font-medium text-gray-600">
                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                {{ $abnormalCount }} Abnormal
                            </span>
                        @elseif($order->status === 'completed')
                            <span class="inline-flex items-center gap-1.5 text-[10px] font-medium text-gray-600">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                All Normal
                            </span>
                        @endif
                        <span class="inline-flex items-center gap-1.5 text-[10px] font-medium text-gray-600">
                            <span class="w-1.5 h-1.5 rounded-full {{ $order->status === 'completed' ? 'bg-emerald-500' : 'bg-amber-500' }}"></span>
                            {{ ucfirst($order->status) }}
                        </span>
                    </div>
                </div>

                {{-- Print Report --}}
                @if($order->clinical_notes)
                <div class="px-5 py-3 bg-gray-50 border-b border-gray-100">
                    <p class="text-xs font-medium text-gray-500 mb-0.5">Clinical Notes</p>
                    <p class="text-sm text-gray-700">{{ $order->clinical_notes }}</p>
                </div>
                @endif

                {{-- Print Report --}}
                <div class="p-5 space-y-3">
                    @foreach ($order->items as $item)
                        @php
                            $itemResults = $item->results;
                            $itemAbnormal = $itemResults->whereNotIn('flag', ['normal'])->count();
                        @endphp
                        <div class="border border-gray-200 rounded overflow-hidden">
                            {{-- Print Report --}}
                            <div class="px-4 py-2.5 bg-gray-50 border-b border-gray-200 flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <div class="w-7 h-7 rounded bg-slate-100 flex items-center justify-center">
                                        <i class="hgi-stroke hgi-test-tube-01 text-base text-slate-600"></i>
                                    </div>
                                    <span class="text-xs font-semibold text-gray-800">{{ $item->labTest?->name ?? 'Unknown Test' }}</span>
                                    @if($item->labTest?->unit)
                                    <span class="text-xs text-gray-400">({{ $item->labTest->unit }})</span>
                                    @endif
                                </div>
                                @if($itemResults->isNotEmpty())
                                    @if($itemAbnormal > 0)
                                        <span class="inline-flex items-center gap-1 text-[10px] font-medium text-gray-600"><span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>{{ $itemAbnormal }} Abnormal</span>
                                    @else
                                        <span class="inline-flex items-center gap-1 text-[10px] font-medium text-gray-600"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>All Normal</span>
                                    @endif
                                @endif
                            </div>

                            {{-- Print Report --}}
                            @if($itemResults->isEmpty())
                                <div class="px-4 py-4 text-center text-sm text-gray-400">
                                    <i class="hgi-stroke hgi-clock-01 text-2xl mx-auto mb-1 text-gray-300"></i>
                                    No results submitted yet
                                </div>
                            @else
                                <div class="overflow-x-auto">
                                    <table class="w-full text-sm text-left">
                                        <thead class="bg-white text-[10px] uppercase text-gray-400">
                                            <tr>
                                                <th class="px-4 py-2.5 font-semibold">Parameter</th>
                                                <th class="px-4 py-2.5 font-semibold">Result</th>
                                                <th class="px-4 py-2.5 font-semibold">Unit</th>
                                                <th class="px-4 py-2.5 font-semibold">Reference Range</th>
                                                <th class="px-4 py-2.5 font-semibold">Flag</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-100">
                                            @foreach ($itemResults as $result)
                                                @php $style = $flagStyles[$result->flag] ?? $flagStyles['normal']; @endphp
                                                <tr class="hover:bg-gray-50/30 transition-colors">
                                                    <td class="px-4 py-3 font-medium text-gray-800">{{ $result->parameter }}</td>
                                                    <td class="px-4 py-3">
                                                        <span class="text-base font-bold {{ $result->flag === 'critical' ? 'text-red-600' : ($result->flag !== 'normal' ? 'text-amber-600' : 'text-gray-900') }}">{{ $result->value }}</span>
                                                    </td>
                                                    <td class="px-4 py-3 text-gray-600">{{ $result->unit ?? '-' }}</td>
                                                    <td class="px-4 py-3 text-gray-500 text-xs">{{ $result->reference_range ?? '-' }}</td>
                                                    <td class="px-4 py-3">
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
                </div>

                {{-- Print Report --}}
                @if($order->attachments->isNotEmpty())
                <div class="px-5 pb-5 border-t border-gray-100 pt-3">
                    <p class="text-xs font-semibold text-gray-700 mb-2">Attached Reports</p>
                    <div class="flex flex-wrap gap-2">
                        @foreach($order->attachments as $attachment)
                        <a href="{{ route('file.serve', $attachment->file_path) }}" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded text-xs font-medium transition-colors">
                            <i class="hgi-stroke hgi-file-01 text-sm"></i>
                            {{ $attachment->file_name }}
                        </a>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
        @empty
            <div class="bg-white rounded-md border border-gray-200 p-12 text-center">
                <i class="hgi-stroke hgi-test-tube-01 text-6xl mx-auto text-gray-300 mb-3"></i>
                <p class="text-xs text-gray-400">No lab orders found for this patient</p>
            </div>
        @endforelse
    </div>

    {{-- Print Report --}}
    @if($visits->isNotEmpty())
    <div class="space-y-4 no-print">
        <div class="flex items-center gap-2">
            <i class="hgi-stroke hgi-clipboard text-base text-gray-400"></i>
            <h3 class="text-sm font-bold text-gray-900">Visit History</h3>
            <span class="text-xs text-gray-400">— Matembezi ya mgonjwa</span>
        </div>

        <div class="space-y-3">
            @foreach ($visits as $v)
                <div class="bg-white rounded-md border border-gray-200 p-4">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-bold text-gray-900">{{ $v->registered_at->format('M d, Y') }}</span>
                            <span class="text-xs text-gray-400">{{ $v->visit_number }}</span>
                        </div>
                        <span class="inline-flex items-center gap-1.5 text-[10px] font-medium text-gray-600">
                            <span class="w-1.5 h-1.5 rounded-full {{ $v->status === 'completed' ? 'bg-emerald-500' : 'bg-amber-500' }}"></span>
                            {{ str_replace('_', ' ', $v->status) }}
                        </span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        {{-- Print Report --}}
                        @if($v->vitals)
                        <div class="bg-gray-50 rounded p-2.5 border border-gray-200">
                            <p class="text-[10px] font-semibold text-gray-500 uppercase mb-1">Vitals</p>
                            <div class="flex flex-wrap gap-x-3 gap-y-0.5 text-[11px] text-gray-700">
                                @if($v->vitals->temperature)<span>T: {{ $v->vitals->temperature }}°C</span>@endif
                                @if($v->vitals->blood_pressure)<span>BP: {{ $v->vitals->blood_pressure }}</span>@endif
                                @if($v->vitals->pulse)<span>P: {{ $v->vitals->pulse }}</span>@endif
                                @if($v->vitals->weight)<span>Wt: {{ $v->vitals->weight }}kg</span>@endif
                            </div>
                        </div>
                        @endif

                        {{-- Print Report --}}
                        @if($v->consultation?->diagnosis)
                        <div class="bg-gray-50 rounded p-2.5 border border-gray-200">
                            <p class="text-[10px] font-semibold text-gray-500 uppercase mb-1">Diagnosis</p>
                            <p class="text-[11px] text-gray-700">{{ $v->consultation->diagnosis }}</p>
                        </div>
                        @endif
                    </div>

                    {{-- Print Report --}}
                    @if($v->prescriptions->isNotEmpty())
                    <div class="mt-2 flex flex-wrap gap-1">
                        @foreach($v->prescriptions as $rx)
                            @foreach($rx->items as $item)
                                <span class="inline-flex px-2 py-0.5 rounded text-[10px] font-medium bg-gray-100 text-gray-700 border border-gray-200">{{ $item->medication?->name ?? 'Unknown' }}</span>
                            @endforeach
                        @endforeach
                    </div>
                    @endif

                    {{-- Print Report --}}
                    @if($v->ultrasoundOrders->isNotEmpty())
                    <div class="mt-2 flex flex-wrap gap-1">
                        @foreach($v->ultrasoundOrders as $usOrder)
                            @foreach($usOrder->items as $item)
                                <span class="inline-flex px-2 py-0.5 rounded text-[10px] font-medium bg-gray-100 text-gray-700 border border-gray-200">{{ $item->ultrasoundService?->name ?? 'Unknown' }}</span>
                            @endforeach
                        @endforeach
                    </div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
    @endif
</div>

{{-- Print Report --}}
<div id="printReport">
    <div style="text-align:center; margin-bottom: 24px;">
        <h1 style="font-size:22px; font-weight:800; color:#0f172a; margin:0;">{{ config('app.name', 'Clinic') }}</h1>
        <p style="font-size:11px; color:#666; margin:4px 0 0;">Patient Lab History - {{ $patient->fullName() }}</p>
        <div style="height:3px; background:#0f172a; border-radius:2px; margin:12px 0;"></div>
    </div>

    <table style="width:100%; font-size:11px; margin-bottom:20px; border-collapse:collapse;">
        <tr>
            <td style="padding:4px 0;"><strong>Patient:</strong> {{ $patient->fullName() }}</td>
            <td style="padding:4px 0;"><strong>MRN:</strong> {{ $patient->mrn ?? 'N/A' }}</td>
            <td style="padding:4px 0;"><strong>Gender:</strong> {{ ucfirst($patient->gender ?? 'N/A') }}</td>
        </tr>
        <tr>
            <td style="padding:4px 0;"><strong>Phone:</strong> {{ $patient->phone ?? 'N/A' }}</td>
            <td style="padding:4px 0;"><strong>Age:</strong> {{ $patient->date_of_birth?->age ?? 'N/A' }}</td>
            <td style="padding:4px 0;"><strong>Blood:</strong> {{ $patient->blood_group ?? 'N/A' }}</td>
        </tr>
    </table>

    @foreach ($labOrders as $order)
        @php $orderResults = $order->items->flatMap->results; @endphp
        <div style="margin-bottom:24px; page-break-inside:avoid;">
            <h2 style="font-size:14px; font-weight:700; color:#0f172a; border-bottom:2px solid #0f172a; padding-bottom:6px; margin-bottom:10px;">
                Order #{{ $order->id }} - {{ $order->created_at->format('d M Y') }} - {{ ucfirst($order->status) }}
            </h2>
            @foreach ($order->items as $item)
                @php $itemResults = $item->results; @endphp
                <div style="margin-bottom:12px;">
                    <p style="font-size:12px; font-weight:700; color:#333; margin-bottom:6px;">{{ $item->labTest?->name ?? 'Unknown' }}</p>
                    @if($itemResults->isEmpty())
                        <p style="font-size:11px; color:#999;">No results</p>
                    @else
                        <table style="width:100%; font-size:11px; border-collapse:collapse;">
                            <thead>
                                <tr style="background:#f3f4f6;">
                                    <th style="text-align:left; padding:6px 8px; border:1px solid #e5e7eb;">Parameter</th>
                                    <th style="text-align:left; padding:6px 8px; border:1px solid #e5e7eb;">Result</th>
                                    <th style="text-align:left; padding:6px 8px; border:1px solid #e5e7eb;">Unit</th>
                                    <th style="text-align:left; padding:6px 8px; border:1px solid #e5e7eb;">Reference</th>
                                    <th style="text-align:left; padding:6px 8px; border:1px solid #e5e7eb;">Flag</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($itemResults as $result)
                                    @php
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
                                        <td style="padding:6px 8px; border:1px solid #e5e7eb; font-weight:700; color:{{ $flagColor }};">{{ $result->value }}</td>
                                        <td style="padding:6px 8px; border:1px solid #e5e7eb;">{{ $result->unit ?? '-' }}</td>
                                        <td style="padding:6px 8px; border:1px solid #e5e7eb; color:#666;">{{ $result->reference_range ?? '-' }}</td>
                                        <td style="padding:6px 8px; border:1px solid #e5e7eb; color:{{ $flagColor }}; font-weight:700;">{{ ucfirst($result->flag) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            @endforeach
        </div>
    @endforeach

    <div style="margin-top:32px; padding-top:12px; border-top:1px solid #e5e7eb; text-align:center;">
        <p style="font-size:10px; color:#999;">Generated on {{ now()->format('d M Y H:i') }} - {{ config('app.name', 'Clinic') }}</p>
    </div>
</div>
@endsection
