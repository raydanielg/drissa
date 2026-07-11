@extends('layouts.dashboard')

@section('title', 'Reports - ' . config('app.name', 'Laravel'))
@section('page_title', 'Reports Dashboard')

@section('content')
<div class="space-y-6">
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
        @foreach ([
            ['label' => 'Total Patients', 'value' => $stats['total_patients'], 'icon' => 'users'],
            ['label' => 'Total Visits', 'value' => $stats['total_visits'], 'icon' => 'clipboard'],
            ['label' => 'Visits Today', 'value' => $stats['visits_today'], 'icon' => 'calendar'],
            ['label' => 'Doctors', 'value' => $stats['total_doctors'], 'icon' => 'stethoscope'],
            ['label' => 'Revenue Today', 'value' => 'TSh ' . number_format($stats['revenue_today']), 'icon' => 'currency'],
            ['label' => 'Revenue This Month', 'value' => 'TSh ' . number_format($stats['revenue_this_month']), 'icon' => 'chart'],
            ['label' => 'Low Stock Items', 'value' => $stats['low_stock'], 'icon' => 'box'],
            ['label' => 'Unpaid Invoices', 'value' => $stats['unpaid_invoices'], 'icon' => 'warning'],
        ] as $card)
            <div class="bg-gradient-to-br from-emerald-600 to-emerald-700 rounded-xl p-4 text-white relative overflow-hidden">
                <div class="relative z-10">
                    <p class="text-[10px] text-emerald-100">{{ $card['label'] }}</p>
                    <p class="text-lg font-bold mt-1">{{ $card['value'] }}</p>
                </div>
            </div>
        @endforeach
    </div>

    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <h2 class="text-sm font-semibold text-gray-900">Recent Payments</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                    <tr>
                        <th class="px-6 py-3">Date</th>
                        <th class="px-6 py-3">Amount</th>
                        <th class="px-6 py-3">Method</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($payments as $payment)
                        <tr class="border-t border-gray-100">
                            <td class="px-6 py-3">{{ $payment->created_at->format('d M Y H:i') }}</td>
                            <td class="px-6 py-3">TSh {{ number_format($payment->amount, 2) }}</td>
                            <td class="px-6 py-3 capitalize">{{ $payment->method }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="px-6 py-6 text-center text-gray-400">No payments yet</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
