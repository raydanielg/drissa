@extends('layouts.dashboard')

@section('title', 'Sales Report - ' . config('app.name', 'Laravel'))
@section('page_title', 'Sales Report')

@section('content')
<div class="bg-white rounded-xl border border-gray-100 p-5 shadow-sm mb-6">
    <form method="GET" class="flex flex-wrap gap-3 items-end">
        <div>
            <label class="block text-xs text-gray-500 mb-1">From</label>
            <input type="date" name="from" value="{{ $from }}" class="border rounded-lg px-3 py-2 text-sm">
        </div>
        <div>
            <label class="block text-xs text-gray-500 mb-1">To</label>
            <input type="date" name="to" value="{{ $to }}" class="border rounded-lg px-3 py-2 text-sm">
        </div>
        <button type="submit" class="bg-emerald-600 text-white text-sm font-medium px-4 py-2 rounded-lg hover:bg-emerald-700">Filter</button>
    </form>
</div>

<div class="bg-emerald-600 text-white rounded-xl p-6 mb-6 shadow-sm">
    <p class="text-sm opacity-90">Total Sales</p>
    <p class="text-3xl font-bold mt-1">TSh {{ number_format($total, 2) }}</p>
</div>

<div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-100">
        <h3 class="text-sm font-semibold text-gray-900">Transactions</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left">
            <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                <tr>
                    <th class="px-6 py-3">Receipt #</th>
                    <th class="px-6 py-3">Amount</th>
                    <th class="px-6 py-3">Method</th>
                    <th class="px-6 py-3">Date</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($payments as $payment)
                    <tr class="border-t border-gray-100 hover:bg-gray-50/50">
                        <td class="px-6 py-3 font-medium">{{ $payment->receipt_number }}</td>
                        <td class="px-6 py-3">TSh {{ number_format($payment->amount, 2) }}</td>
                        <td class="px-6 py-3 capitalize">{{ $payment->payment_method }}</td>
                        <td class="px-6 py-3 text-gray-500">{{ $payment->created_at->format('d M Y H:i') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="px-6 py-6 text-center text-gray-400">No payments in this range</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-6 py-4 border-t border-gray-100">
        {{ $payments->links() }}
    </div>
</div>
@endsection
