@extends('layouts.dashboard')

@section('title', 'Sales Report - ' . config('app.name', 'Laravel'))
@section('page_title', 'Sales Report')

@section('content')
@php
    $txnCount = $payments->total();
    $avgSale = $txnCount > 0 ? $total / $txnCount : 0;

    $methodStyles = [
        'cash'         => ['bg-emerald-50 text-emerald-700 ring-emerald-200', 'bg-emerald-500'],
        'card'         => ['bg-sky-50 text-sky-700 ring-sky-200', 'bg-sky-500'],
        'mobile_money' => ['bg-amber-50 text-amber-700 ring-amber-200', 'bg-amber-500'],
        'insurance'    => ['bg-violet-50 text-violet-700 ring-violet-200', 'bg-violet-500'],
    ];
    $methodLabels = ['cash' => 'Cash', 'card' => 'Card', 'mobile_money' => 'Mobile Money', 'insurance' => 'Insurance'];
@endphp

<div class="space-y-5">
    {{-- Header --}}
    <div class="brand-hero rounded-md p-6 text-white flex flex-wrap items-end justify-between gap-4">
        <div>
            <p class="text-[10px] uppercase tracking-wider text-slate-400 mb-1">Reports</p>
            <h1 class="text-xl font-bold">Sales Report</h1>
            <p class="text-xs text-slate-400 mt-1">Payment transactions by date range.</p>
        </div>
        <div class="flex items-center gap-2">
            <span class="inline-flex items-center gap-1.5 rounded-full bg-white/10 px-3 py-1.5 text-[11px] font-medium text-white ring-1 ring-white/20">
                <svg class="size-3.5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" /></svg>
                {{ \Carbon\Carbon::parse($from)->format('d M Y') }} &ndash; {{ \Carbon\Carbon::parse($to)->format('d M Y') }}
            </span>
            <button type="button" onclick="window.print()" class="brand-btn-outline inline-flex items-center gap-1.5 text-xs font-medium px-3 py-1.5 rounded-full transition-colors">
                <svg class="size-3.5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0 1 10.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0 .229 2.523a1.125 1.125 0 0 1-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0 0 21 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 0 0-1.913-.247M6.34 18H5.25A2.25 2.25 0 0 1 3 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 0 1 1.913-.247m10.5 0a48.536 48.536 0 0 0-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659" /></svg>
                Print
            </button>
        </div>
    </div>

    {{-- Filter --}}
    <div class="bg-white rounded-md border border-gray-200 p-4">
        <form method="GET" class="flex flex-wrap gap-3 items-end">
            <div class="w-40">
                <label class="block text-xs text-gray-500 mb-1">From</label>
                <x-ui.input type="text" data-fp-date name="from" value="{{ $from }}" class="w-full border border-gray-200 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-slate-300" />
            </div>
            <div class="w-40">
                <label class="block text-xs text-gray-500 mb-1">To</label>
                <x-ui.input type="text" data-fp-date name="to" value="{{ $to }}" class="w-full border border-gray-200 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-slate-300" />
            </div>
            <button type="submit" class="brand-btn text-white text-sm font-medium px-4 py-2 rounded">Filter</button>
            @if (request()->filled('from') || request()->filled('to'))
                <a href="{{ route('reports.sales') }}" class="text-xs text-gray-500 hover:text-gray-700 underline underline-offset-2 pb-2">Reset</a>
            @endif
        </form>
    </div>

    {{-- KPI cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
        <div class="bg-white rounded-md border border-emerald-200 p-5 relative overflow-hidden">
            <div class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-emerald-500 to-emerald-300"></div>
            <p class="text-[10px] font-medium text-gray-500 uppercase tracking-wide">Total Sales</p>
            <p class="text-2xl font-bold text-emerald-600 mt-1 tabular-nums">TSh {{ number_format($total, 2) }}</p>
        </div>
        <div class="bg-white rounded-md border border-gray-200 p-5">
            <p class="text-[10px] font-medium text-gray-500 uppercase tracking-wide">Transactions</p>
            <p class="text-2xl font-bold text-gray-900 mt-1 tabular-nums">{{ number_format($txnCount) }}</p>
        </div>
        <div class="bg-white rounded-md border border-gray-200 p-5">
            <p class="text-[10px] font-medium text-gray-500 uppercase tracking-wide">Average Sale</p>
            <p class="text-2xl font-bold text-gray-900 mt-1 tabular-nums">TSh {{ number_format($avgSale, 2) }}</p>
        </div>
    </div>

    {{-- Transactions --}}
    <div class="bg-white rounded-md border border-gray-200 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
            <h3 class="text-sm font-bold text-gray-900">Transactions</h3>
            <span class="text-[11px] text-gray-400">{{ number_format($txnCount) }} {{ $txnCount === 1 ? 'record' : 'records' }}</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-50 text-[10px] uppercase text-gray-400">
                    <tr>
                        <th class="px-5 py-3 font-medium">Receipt #</th>
                        <th class="px-5 py-3 font-medium text-right">Amount</th>
                        <th class="px-5 py-3 font-medium">Method</th>
                        <th class="px-5 py-3 font-medium">Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse ($payments as $payment)
                        @php [$badgeClass, $dotClass] = $methodStyles[$payment->method] ?? ['bg-gray-100 text-gray-600 ring-gray-200', 'bg-gray-400']; @endphp
                        <tr class="hover:bg-emerald-50/30 transition-colors">
                            <td class="px-5 py-3 text-xs font-mono font-medium text-gray-900">
                                {{ $payment->reference ?: '#' . str_pad($payment->id, 6, '0', STR_PAD_LEFT) }}
                            </td>
                            <td class="px-5 py-3 text-xs font-semibold text-gray-900 text-right tabular-nums">TSh {{ number_format($payment->amount, 2) }}</td>
                            <td class="px-5 py-3">
                                <span class="inline-flex items-center gap-1.5 rounded-full px-2 py-0.5 text-[10px] font-semibold ring-1 ring-inset {{ $badgeClass }}">
                                    <span class="size-1.5 rounded-full {{ $dotClass }}"></span>
                                    {{ $methodLabels[$payment->method] ?? ucfirst(str_replace('_', ' ', $payment->method)) }}
                                </span>
                            </td>
                            <td class="px-5 py-3 text-xs text-gray-500 whitespace-nowrap">
                                <span class="text-gray-700 font-medium">{{ $payment->created_at->format('d M Y') }}</span>
                                <span class="text-gray-400">{{ $payment->created_at->format('H:i') }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-5 py-10 text-center">
                                <svg class="size-8 mx-auto text-gray-300 mb-2" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm3 0h.008v.008H18V10.5Zm-12 0h.008v.008H6V10.5Z" /></svg>
                                <p class="text-gray-400 text-xs">No payments in this range</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-5 py-3 border-t border-gray-100">
            {{ $payments->withQueryString()->links() }}
        </div>
    </div>
</div>
@endsection
