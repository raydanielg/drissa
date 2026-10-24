@extends('layouts.dashboard')

@section('title', 'Pharmacy Reports - ' . config('app.name', 'Laravel'))
@section('page_title', 'Pharmacy Reports & Profit Analysis')

@section('content')
<div class="space-y-5">

    {{-- Top Medicines with Profit --}}
    <div class="bg-slate-800 rounded-md p-6 text-white">
        <p class="text-[10px] uppercase tracking-wider text-slate-400 mb-1">Pharmacy</p>
        <h1 class="text-xl font-bold">Pharmacy Reports & Profit Analysis</h1>
        <p class="text-xs text-slate-400 mt-1">Dispensing totals, revenue, cost, and margins.</p>
    </div>

    {{-- Top Medicines with Profit --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3">
        <div class="bg-white rounded-md border border-gray-200 p-4">
            <p class="text-[10px] font-medium text-gray-500 uppercase tracking-wide">Total Dispensed</p>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ $totalDispensed }}</p>
            <p class="text-[10px] text-gray-400 mt-1">{{ $dispensedToday }} today</p>
        </div>
        <div class="bg-white rounded-md border border-emerald-200 p-4">
            <p class="text-[10px] font-medium text-gray-500 uppercase tracking-wide">Total Revenue</p>
            <p class="text-2xl font-bold text-emerald-600 mt-1">TSh {{ number_format($totalRevenue, 0) }}</p>
            <p class="text-[10px] text-gray-400 mt-1">From dispensed medicines</p>
        </div>
        <div class="bg-white rounded-md border border-gray-200 p-4">
            <p class="text-[10px] font-medium text-gray-500 uppercase tracking-wide">Total Cost</p>
            <p class="text-2xl font-bold text-gray-900 mt-1">TSh {{ number_format($totalCost, 0) }}</p>
            <p class="text-[10px] text-gray-400 mt-1">Purchase cost of dispensed</p>
        </div>
        <div class="bg-white rounded-md border border-emerald-200 p-4">
            <p class="text-[10px] font-medium text-gray-500 uppercase tracking-wide">Total Profit</p>
            <p class="text-2xl font-bold text-emerald-600 mt-1">TSh {{ number_format($totalProfit, 0) }}</p>
            <p class="text-[10px] text-gray-400 mt-1">Margin: {{ number_format($profitMargin, 1) }}%</p>
        </div>
    </div>

    {{-- Top Medicines with Profit --}}
    <div class="bg-white rounded-md border border-gray-200 p-5">
        <h3 class="text-sm font-bold text-gray-900 mb-4">Monthly Dispensing & Revenue (Last 6 Months)</h3>
        <div class="flex items-end justify-between gap-2 h-56">
            @php($maxCount = max($monthlyDispenses->max(), 1))
            @php($maxRev = max($monthlyRevenue->max(), 1))
            @foreach($monthlyDispenses as $i => $count)
                <div class="flex-1 flex flex-col items-center gap-2">
                    <div class="w-full flex flex-col items-center gap-1">
                        <span class="text-[10px] font-medium text-emerald-600">TSh {{ number_format($monthlyRevenue[$i] / 1000, 0) }}k</span>
                        <div class="w-full bg-emerald-500 rounded-t" style="height: {{ max(($monthlyRevenue[$i] / $maxRev) * 120, 4) }}px; min-height: 4px;" title="TSh {{ number_format($monthlyRevenue[$i], 0) }}"></div>
                        <div class="w-full bg-slate-400 rounded-t" style="height: {{ max(($count / $maxCount) * 60, 4) }}px; min-height: 4px;" title="{{ $count }} dispenses"></div>
                    </div>
                    <span class="text-[10px] text-gray-500 text-center">{{ $monthLabels[$i] }}</span>
                    <span class="text-xs font-bold text-gray-700">{{ $count }}</span>
                </div>
            @endforeach
        </div>
        <div class="flex items-center gap-4 mt-4 pt-4 border-t border-gray-100">
            <div class="flex items-center gap-2">
                <div class="w-3 h-3 rounded bg-slate-400"></div>
                <span class="text-xs text-gray-500">Dispenses</span>
            </div>
            <div class="flex items-center gap-2">
                <div class="w-3 h-3 rounded bg-emerald-500"></div>
                <span class="text-xs text-gray-500">Revenue (TSh)</span>
            </div>
        </div>
    </div>

    {{-- Top Medicines with Profit --}}
    <div class="bg-white rounded-md border border-gray-200 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100">
            <h3 class="text-sm font-bold text-gray-900">Top Dispensed Medicines — Profit Analysis</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 text-[10px] uppercase text-gray-400 border-b border-gray-100">
                        <th class="px-5 py-3 font-medium text-left">#</th>
                        <th class="px-5 py-3 font-medium text-left">Medicine</th>
                        <th class="px-5 py-3 font-medium text-left">Qty Dispensed</th>
                        <th class="px-5 py-3 font-medium text-right">Revenue</th>
                        <th class="px-5 py-3 font-medium text-right">Cost</th>
                        <th class="px-5 py-3 font-medium text-right">Profit</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($topMedicines as $i => $med)
                        @php $profit = $med->revenue - $med->cost; @endphp
                        <tr class="hover:bg-gray-50/50">
                            <td class="px-5 py-3 text-xs text-gray-400">{{ $i + 1 }}</td>
                            <td class="px-5 py-3 text-xs font-medium text-gray-900">{{ $med->medication?->name ?? 'Unknown' }}</td>
                            <td class="px-5 py-3 text-xs text-gray-600">{{ $med->total_qty }}</td>
                            <td class="px-5 py-3 text-xs font-semibold text-gray-900 text-right">TSh {{ number_format($med->revenue, 0) }}</td>
                            <td class="px-5 py-3 text-xs text-gray-600 text-right">TSh {{ number_format($med->cost, 0) }}</td>
                            <td class="px-5 py-3 text-xs font-semibold text-right {{ $profit > 0 ? 'text-emerald-600' : 'text-red-600' }}">TSh {{ number_format($profit, 0) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-5 py-8 text-center text-gray-400 text-xs">No data yet</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
