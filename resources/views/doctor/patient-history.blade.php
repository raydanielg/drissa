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
<div class="space-y-6">
    @if (session('status'))
        <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm">{{ session('status') }}</div>
    @endif

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 no-print">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 rounded-full bg-gradient-to-br from-emerald-600 to-teal-700 flex items-center justify-center text-white font-bold text-xl shadow-md">
                {{ strtoupper(substr($patient->first_name ?: 'U', 0, 1)) }}
            </div>
            <div>
                <h2 class="text-lg font-bold text-gray-900">{{ $patient->fullName() }}</h2>
                <div class="flex flex-wrap items-center gap-x-3 gap-y-0.5 text-sm text-gray-500 mt-0.5">
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
                    <span class="font-semibold text-red-600">{{ $patient->blood_group }}</span>
                    @endif
                </div>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('doctor.lab-results') }}" class="inline-flex items-center gap-2 text-sm text-gray-600 hover:text-gray-900">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Back to Lab Results
            </a>
            <button onclick="window.print()" class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-lg transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                Print
            </button>
        </div>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 no-print">
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-sky-100 flex items-center justify-center">
                    <svg class="w-4 h-4 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>
                </div>
                <p class="text-xs font-medium text-gray-500 uppercase">Total Orders</p>
            </div>
            <p class="text-2xl font-bold text-gray-900 mt-2">{{ $labOrders->count() }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-violet-100 flex items-center justify-center">
                    <svg class="w-4 h-4 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                </div>
                <p class="text-xs font-medium text-gray-500 uppercase">Total Tests</p>
            </div>
            <p class="text-2xl font-bold text-gray-900 mt-2">{{ $totalTests }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-emerald-100 flex items-center justify-center">
                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <p class="text-xs font-medium text-gray-500 uppercase">Completed</p>
            </div>
            <p class="text-2xl font-bold text-emerald-600 mt-2">{{ $completedOrders->count() }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg {{ $abnormalResults > 0 ? 'bg-red-100' : 'bg-gray-100' }} flex items-center justify-center">
                    <svg class="w-4 h-4 {{ $abnormalResults > 0 ? 'text-red-600' : 'text-gray-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>
                <p class="text-xs font-medium text-gray-500 uppercase">Abnormal</p>
            </div>
            <p class="text-2xl font-bold {{ $abnormalResults > 0 ? 'text-red-600' : 'text-gray-900' }} mt-2">{{ $abnormalResults }}</p>
        </div>
    </div>

    {{-- Patient Info --}}
    @if($patient->allergies)
    <div class="bg-red-50 border border-red-200 rounded-xl p-4 flex items-center gap-3 no-print">
        <svg class="w-5 h-5 text-red-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
        <div>
            <p class="text-sm font-semibold text-red-700">Allergies</p>
            <p class="text-sm text-red-600">{{ $patient->allergies }}</p>
        </div>
    </div>
    @endif

    {{-- Lab Results History --}}
    <div class="space-y-4">
        <div class="flex items-center gap-2 no-print">
            <svg class="w-5 h-5 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>
            <h3 class="text-base font-bold text-gray-900">Lab Results History</h3>
            <span class="text-xs text-gray-400">- Majibu ya vipimo vyote</span>
        </div>

        @php
            $flagStyles = [
                'normal' => ['bg' => 'bg-emerald-50', 'text' => 'text-emerald-700', 'border' => 'border-emerald-200', 'icon' => '🟢', 'label' => 'Normal'],
                'high' => ['bg' => 'bg-amber-50', 'text' => 'text-amber-700', 'border' => 'border-amber-200', 'icon' => '🟡', 'label' => 'High'],
                'low' => ['bg' => 'bg-amber-50', 'text' => 'text-amber-700', 'border' => 'border-amber-200', 'icon' => '🟡', 'label' => 'Low'],
                'critical' => ['bg' => 'bg-red-50', 'text' => 'text-red-700', 'border' => 'border-red-200', 'icon' => '🔴', 'label' => 'Critical'],
            ];
        @endphp

        @forelse ($labOrders as $order)
            @php
                $orderResults = $order->items->flatMap->results;
                $abnormalCount = $orderResults->whereNotIn('flag', ['normal'])->count();
            @endphp
            <div class="result-card bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
                {{-- Order Header --}}
                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between {{ $order->status === 'completed' ? 'bg-gradient-to-r from-emerald-50/60 to-transparent' : 'bg-gradient-to-r from-amber-50/60 to-transparent' }}">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl {{ $order->status === 'completed' ? 'bg-emerald-100 text-emerald-600' : 'bg-amber-100 text-amber-600' }} flex items-center justify-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-gray-900">Order #{{ $order->id }} - {{ $order->visit?->visit_number ?? 'No visit' }}</p>
                            <p class="text-xs text-gray-500">
                                {{ $order->created_at->format('M d, Y H:i') }}
                                @if($order->completed_at) &bull; Completed: {{ $order->completed_at->format('M d, Y H:i') }} @endif
                                &bull; Dr. {{ $order->doctor?->name ?? 'N/A' }}
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        @if($order->status === 'completed' && $abnormalCount > 0)
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-700">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                {{ $abnormalCount }} Abnormal
                            </span>
                        @elseif($order->status === 'completed')
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-700">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                All Normal
                            </span>
                        @endif
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium {{ $order->status === 'completed' ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">
                            {{ ucfirst($order->status) }}
                        </span>
                    </div>
                </div>

                {{-- Clinical Notes --}}
                @if($order->clinical_notes)
                <div class="px-6 py-3 bg-gray-50/50 border-b border-gray-100">
                    <p class="text-xs font-medium text-gray-500 mb-0.5">Clinical Notes</p>
                    <p class="text-sm text-gray-700">{{ $order->clinical_notes }}</p>
                </div>
                @endif

                {{-- Results per test --}}
                <div class="p-5 space-y-3">
                    @foreach ($order->items as $item)
                        @php
                            $itemResults = $item->results;
                            $itemAbnormal = $itemResults->whereNotIn('flag', ['normal'])->count();
                        @endphp
                        <div class="border border-gray-200 rounded-xl overflow-hidden">
                            {{-- Test Header --}}
                            <div class="px-4 py-2.5 bg-gray-50/80 border-b border-gray-200 flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <div class="w-7 h-7 rounded-lg bg-violet-100 flex items-center justify-center">
                                        <svg class="w-4 h-4 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>
                                    </div>
                                    <span class="text-sm font-semibold text-gray-800">{{ $item->labTest?->name ?? 'Unknown Test' }}</span>
                                    @if($item->labTest?->unit)
                                    <span class="text-xs text-gray-400">({{ $item->labTest->unit }})</span>
                                    @endif
                                </div>
                                @if($itemResults->isNotEmpty())
                                    @if($itemAbnormal > 0)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-amber-100 text-amber-700">{{ $itemAbnormal }} Abnormal</span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-100 text-emerald-700">All Normal</span>
                                    @endif
                                @endif
                            </div>

                            {{-- Results Table --}}
                            @if($itemResults->isEmpty())
                                <div class="px-4 py-4 text-center text-sm text-gray-400">
                                    <svg class="w-6 h-6 mx-auto mb-1 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    No results submitted yet
                                </div>
                            @else
                                <div class="overflow-x-auto">
                                    <table class="w-full text-sm text-left">
                                        <thead class="bg-white text-xs uppercase text-gray-500">
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
                                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium {{ $style['bg'] }} {{ $style['text'] }} border {{ $style['border'] }}">
                                                            {{ $style['icon'] }} {{ $style['label'] }}
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

                {{-- Attachments --}}
                @if($order->attachments->isNotEmpty())
                <div class="px-5 pb-5 border-t border-gray-100 pt-3">
                    <p class="text-xs font-semibold text-gray-700 mb-2">Attached Reports</p>
                    <div class="flex flex-wrap gap-2">
                        @foreach($order->attachments as $attachment)
                        <a href="{{ route('file.serve', $attachment->file_path) }}" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-50 hover:bg-blue-100 text-blue-700 rounded-lg text-xs font-medium transition-colors">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                            {{ $attachment->file_name }}
                        </a>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
        @empty
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-12 text-center">
                <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>
                <p class="text-sm text-gray-400">No lab orders found for this patient</p>
            </div>
        @endforelse
    </div>

    {{-- Visit History --}}
    @if($visits->isNotEmpty())
    <div class="space-y-4 no-print">
        <div class="flex items-center gap-2">
            <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
            <h3 class="text-base font-bold text-gray-900">Visit History</h3>
            <span class="text-xs text-gray-400">- Matembezi ya mgonjwa</span>
        </div>

        <div class="space-y-3">
            @foreach ($visits as $v)
                <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center gap-2">
                            <span class="text-sm font-bold text-gray-900">{{ $v->registered_at->format('M d, Y') }}</span>
                            <span class="text-xs text-gray-400">{{ $v->visit_number }}</span>
                        </div>
                        <span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-medium {{ $v->status === 'completed' ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">{{ str_replace('_', ' ', $v->status) }}</span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        {{-- Vitals --}}
                        @if($v->vitals)
                        <div class="bg-teal-50 rounded-lg p-2.5 border border-teal-100">
                            <p class="text-[10px] font-semibold text-teal-700 mb-1">Vitals</p>
                            <div class="flex flex-wrap gap-x-3 gap-y-0.5 text-[11px] text-gray-700">
                                @if($v->vitals->temperature)<span>T: {{ $v->vitals->temperature }}°C</span>@endif
                                @if($v->vitals->blood_pressure)<span>BP: {{ $v->vitals->blood_pressure }}</span>@endif
                                @if($v->vitals->pulse)<span>P: {{ $v->vitals->pulse }}</span>@endif
                                @if($v->vitals->weight)<span>Wt: {{ $v->vitals->weight }}kg</span>@endif
                            </div>
                        </div>
                        @endif

                        {{-- Diagnosis --}}
                        @if($v->consultation?->diagnosis)
                        <div class="bg-indigo-50 rounded-lg p-2.5 border border-indigo-100">
                            <p class="text-[10px] font-semibold text-indigo-700 mb-1">Diagnosis</p>
                            <p class="text-[11px] text-gray-700">{{ $v->consultation->diagnosis }}</p>
                        </div>
                        @endif
                    </div>

                    {{-- Prescriptions --}}
                    @if($v->prescriptions->isNotEmpty())
                    <div class="mt-2 flex flex-wrap gap-1">
                        @foreach($v->prescriptions as $rx)
                            @foreach($rx->items as $item)
                                <span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-medium bg-violet-100 text-violet-700">{{ $item->medication?->name ?? 'Unknown' }}</span>
                            @endforeach
                        @endforeach
                    </div>
                    @endif

                    {{-- Ultrasound --}}
                    @if($v->ultrasoundOrders->isNotEmpty())
                    <div class="mt-2 flex flex-wrap gap-1">
                        @foreach($v->ultrasoundOrders as $usOrder)
                            @foreach($usOrder->items as $item)
                                <span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-medium bg-cyan-100 text-cyan-700">{{ $item->ultrasoundService?->name ?? 'Unknown' }}</span>
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
        <h1 style="font-size:22px; font-weight:800; color:#024938; margin:0;">{{ config('app.name', 'Clinic') }}</h1>
        <p style="font-size:11px; color:#666; margin:4px 0 0;">Patient Lab History - {{ $patient->fullName() }}</p>
        <div style="height:3px; background:#024938; border-radius:2px; margin:12px 0;"></div>
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
            <h2 style="font-size:14px; font-weight:700; color:#024938; border-bottom:2px solid #024938; padding-bottom:6px; margin-bottom:10px;">
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
