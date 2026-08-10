@extends('layouts.dashboard')

@section('title', 'Lab History - ' . $patient->fullName())
@section('page_title', 'Lab History - ' . $patient->fullName())

@section('content')
<div class="space-y-6">
    @if (session('status'))
        <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm animate-fade">{{ session('status') }}</div>
    @endif

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center text-lg font-bold">
                {{ strtoupper(substr($patient->first_name ?: 'U', 0, 1)) }}
            </div>
            <div>
                <h2 class="text-lg font-bold text-gray-900">{{ $patient->fullName() }}</h2>
                <p class="text-sm text-gray-500">{{ $patient->mrn }} | {{ $patient->phone ?? 'No phone' }} | {{ $patient->gender ?? '-' }}</p>
            </div>
        </div>
        <a href="{{ route('lab.history') }}" class="inline-flex items-center gap-2 text-sm text-gray-600 hover:text-gray-900">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Back to History
        </a>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
            <p class="text-xs font-medium text-gray-500 uppercase">Total Orders</p>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ $orders->count() }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
            <p class="text-xs font-medium text-gray-500 uppercase">Total Tests</p>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ $totalTests }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
            <p class="text-xs font-medium text-gray-500 uppercase">Completed</p>
            <p class="text-2xl font-bold text-emerald-600 mt-1">{{ $completedOrders->count() }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
            <p class="text-xs font-medium text-gray-500 uppercase">Abnormal Results</p>
            <p class="text-2xl font-bold {{ $abnormalResults > 0 ? 'text-red-600' : 'text-gray-900' }} mt-1">{{ $abnormalResults }}</p>
        </div>
    </div>

    {{-- Lab Orders Timeline --}}
    <div class="space-y-4">
        @foreach($orders as $order)
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
            {{-- Order Header --}}
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between {{ $order->status === 'completed' ? 'bg-emerald-50/40' : 'bg-amber-50/40' }}">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg {{ $order->status === 'completed' ? 'bg-emerald-100 text-emerald-600' : 'bg-amber-100 text-amber-600' }} flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-gray-900">Order #{{ $order->id }} - {{ $order->visit?->visit_number ?? 'No visit' }}</p>
                        <p class="text-xs text-gray-500">
                            Ordered: {{ $order->created_at->format('M d, Y H:i') }}
                            @if($order->completed_at)
                            | Completed: {{ $order->completed_at->format('M d, Y H:i') }}
                            @endif
                            | By: {{ $order->doctor?->name ?? 'N/A' }}
                        </p>
                    </div>
                </div>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $order->status === 'completed' ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">
                    {{ ucfirst($order->status) }}
                </span>
            </div>

            {{-- Order Items & Results --}}
            <div class="p-6">
                @if($order->clinical_notes)
                <div class="mb-4 p-3 bg-gray-50 rounded-lg">
                    <p class="text-xs font-medium text-gray-500 mb-1">Clinical Notes</p>
                    <p class="text-sm text-gray-700">{{ $order->clinical_notes }}</p>
                </div>
                @endif

                <div class="space-y-3">
                    @foreach($order->items as $item)
                    <div class="border border-gray-200 rounded-lg p-4">
                        <div class="flex items-center justify-between mb-2">
                            <p class="text-sm font-semibold text-gray-900">{{ $item->labTest?->name ?? 'Unknown test' }}</p>
                            <a href="{{ route('lab.orders.show', $order) }}" class="text-xs text-emerald-600 hover:text-emerald-700 font-medium">View Full Report</a>
                        </div>

                        @if($item->results->isNotEmpty())
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead class="bg-gray-50/50">
                                    <tr>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500">Parameter</th>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500">Value</th>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500">Unit</th>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500">Reference</th>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500">Flag</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @foreach($item->results as $result)
                                    <tr>
                                        <td class="px-3 py-2 text-sm text-gray-700">{{ $result->parameter }}</td>
                                        <td class="px-3 py-2 text-sm font-medium text-gray-900">{{ $result->value }}</td>
                                        <td class="px-3 py-2 text-sm text-gray-500">{{ $result->unit ?? '-' }}</td>
                                        <td class="px-3 py-2 text-sm text-gray-500">{{ $result->reference_range ?? '-' }}</td>
                                        <td class="px-3 py-2">
                                            @php
                                                $flagColors = [
                                                    'normal' => 'bg-emerald-100 text-emerald-700',
                                                    'high' => 'bg-amber-100 text-amber-700',
                                                    'low' => 'bg-amber-100 text-amber-700',
                                                    'critical' => 'bg-red-100 text-red-700',
                                                ];
                                                $flagColor = $flagColors[$result->flag] ?? 'bg-gray-100 text-gray-700';
                                            @endphp
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $flagColor }}">
                                                {{ ucfirst($result->flag) }}
                                            </span>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @else
                        <p class="text-sm text-gray-400 italic">No results submitted yet</p>
                        @endif
                    </div>
                    @endforeach
                </div>

                {{-- Attachments --}}
                @if($order->attachments->isNotEmpty())
                <div class="mt-4 pt-4 border-t border-gray-100">
                    <p class="text-xs font-semibold text-gray-700 mb-2">Attachments</p>
                    <div class="flex flex-wrap gap-2">
                        @foreach($order->attachments as $attachment)
                        <a href="{{ Storage::url($attachment->file_path) }}" target="_blank" class="inline-flex items-center gap-2 px-3 py-1.5 bg-blue-50 text-blue-700 text-xs rounded-lg hover:bg-blue-100 transition-colors">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                            {{ $attachment->file_name }}
                        </a>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
        </div>
        @endforeach

        @if($orders->isEmpty())
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-12 text-center">
            <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6 4h6"/></svg>
            <p class="text-sm text-gray-400">No lab history found for this patient</p>
        </div>
        @endif
    </div>
</div>
@endsection
