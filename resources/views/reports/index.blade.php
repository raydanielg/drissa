@extends('layouts.dashboard')

@section('title', 'Reports - ' . config('app.name', 'Laravel'))
@section('page_title', 'Reports Dashboard')

@section('content')
<div class="space-y-5">
    {{-- Header --}}
    <div class="bg-slate-800 rounded-md p-6 text-white">
        <p class="text-[10px] uppercase tracking-wider text-slate-400 mb-1">Admin</p>
        <h1 class="text-xl font-bold">Reports Dashboard</h1>
        <p class="text-xs text-slate-400 mt-1">Clinic performance at a glance.</p>
    </div>

    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
        @foreach ([
            ['label' => 'Total Patients', 'value' => $stats['total_patients']],
            ['label' => 'Total Visits', 'value' => $stats['total_visits']],
            ['label' => 'Visits Today', 'value' => $stats['visits_today']],
            ['label' => 'Doctors', 'value' => $stats['total_doctors']],
            ['label' => 'Revenue Today', 'value' => 'TSh ' . number_format($stats['revenue_today'])],
            ['label' => 'Revenue This Month', 'value' => 'TSh ' . number_format($stats['revenue_this_month'])],
            ['label' => 'Low Stock Items', 'value' => $stats['low_stock']],
            ['label' => 'Unpaid Invoices', 'value' => $stats['unpaid_invoices']],
        ] as $card)
            <div class="bg-white rounded-md border border-gray-200 p-4">
                <p class="text-[10px] font-medium text-gray-500 uppercase tracking-wide">{{ $card['label'] }}</p>
                <p class="text-lg font-bold text-gray-900 mt-1">{{ $card['value'] }}</p>
            </div>
        @endforeach
    </div>

    <div class="bg-white rounded-md border border-gray-200 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100">
            <h2 class="text-sm font-bold text-gray-900">Recent Payments</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-50 text-[10px] uppercase text-gray-400">
                    <tr>
                        <th class="px-5 py-3">Date</th>
                        <th class="px-5 py-3 text-right">Amount</th>
                        <th class="px-5 py-3">Method</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse ($payments as $payment)
                        <tr class="hover:bg-gray-50/50">
                            <td class="px-5 py-3 text-xs text-gray-600">{{ $payment->created_at->format('d M Y H:i') }}</td>
                            <td class="px-5 py-3 text-xs font-semibold text-gray-900 text-right">TSh {{ number_format($payment->amount, 2) }}</td>
                            <td class="px-5 py-3 text-xs text-gray-600 capitalize">{{ $payment->method }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="px-5 py-8 text-center text-gray-400 text-xs">No payments yet</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
