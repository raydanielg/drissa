@extends('layouts.dashboard')

@section('title', 'Sales Report - ' . config('app.name', 'Laravel'))
@section('page_title', 'Sales Report')

@section('content')
<div class="space-y-5">
{{-- Header --}}
<div class="bg-slate-800 rounded-md p-6 text-white">
    <p class="text-[10px] uppercase tracking-wider text-slate-400 mb-1">Reports</p>
    <h1 class="text-xl font-bold">Sales Report</h1>
    <p class="text-xs text-slate-400 mt-1">Payment transactions by date range.</p>
</div>

<div class="bg-white rounded-md border border-gray-200 p-5">
    <form method="GET" class="flex flex-wrap gap-3 items-end">
        <div>
            <label class="block text-xs text-gray-500 mb-1">From</label>
            <x-ui.input type="text" data-fp-date name="from" value="{{ $from }}" class="border border-gray-200 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-slate-300" />
        </div>
        <div>
            <label class="block text-xs text-gray-500 mb-1">To</label>
            <x-ui.input type="text" data-fp-date name="to" value="{{ $to }}" class="border border-gray-200 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-slate-300" />
        </div>
        <button type="submit" class="bg-slate-800 text-white text-sm font-medium px-4 py-2 rounded hover:bg-slate-700">Filter</button>
    </form>
</div>

<div class="bg-white rounded-md border border-emerald-200 p-5">
    <p class="text-[10px] font-medium text-gray-500 uppercase tracking-wide">Total Sales</p>
    <p class="text-2xl font-bold text-emerald-600 mt-1">TSh {{ number_format($total, 2) }}</p>
</div>

<div class="bg-white rounded-md border border-gray-200 overflow-hidden">
    <div class="px-5 py-4 border-b border-gray-100">
        <h3 class="text-sm font-bold text-gray-900">Transactions</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left">
            <thead class="bg-gray-50 text-[10px] uppercase text-gray-400">
                <tr>
                    <th class="px-5 py-3">Receipt #</th>
                    <th class="px-5 py-3 text-right">Amount</th>
                    <th class="px-5 py-3">Method</th>
                    <th class="px-5 py-3">Date</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse ($payments as $payment)
                    <tr class="hover:bg-gray-50/50">
                        <td class="px-5 py-3 text-xs font-medium text-gray-900">{{ $payment->receipt_number }}</td>
                        <td class="px-5 py-3 text-xs font-semibold text-gray-900 text-right">TSh {{ number_format($payment->amount, 2) }}</td>
                        <td class="px-5 py-3 text-xs text-gray-600 capitalize">{{ $payment->payment_method }}</td>
                        <td class="px-5 py-3 text-xs text-gray-500">{{ $payment->created_at->format('d M Y H:i') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="px-5 py-8 text-center text-gray-400 text-xs">No payments in this range</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-5 py-3 border-t border-gray-100">
        {{ $payments->links() }}
    </div>
</div>
</div>
@endsection
