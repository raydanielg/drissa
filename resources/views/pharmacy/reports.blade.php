@extends('layouts.dashboard')

@section('title', 'Pharmacy Reports - ' . config('app.name', 'Laravel'))
@section('page_title', 'Pharmacy Reports & Profit Analysis')

@section('content')
<div class="space-y-6">

    {{-- Financial Summary --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <p class="text-xs text-gray-500 uppercase tracking-wider mb-1">Total Dispensed</p>
            <p class="text-2xl font-bold text-gray-900">{{ $totalDispensed }}</p>
            <p class="text-[10px] text-gray-400 mt-1">{{ $dispensedToday }} today</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <p class="text-xs text-gray-500 uppercase tracking-wider mb-1">Total Revenue</p>
            <p class="text-2xl font-bold text-emerald-600">TSh {{ number_format($totalRevenue, 0) }}</p>
            <p class="text-[10px] text-gray-400 mt-1">From dispensed medicines</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <p class="text-xs text-gray-500 uppercase tracking-wider mb-1">Total Cost</p>
            <p class="text-2xl font-bold text-blue-600">TSh {{ number_format($totalCost, 0) }}</p>
            <p class="text-[10px] text-gray-400 mt-1">Purchase cost of dispensed</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <p class="text-xs text-gray-500 uppercase tracking-wider mb-1">Total Profit</p>
            <p class="text-2xl font-bold text-violet-600">TSh {{ number_format($totalProfit, 0) }}</p>
            <p class="text-[10px] text-gray-400 mt-1">Margin: {{ number_format($profitMargin, 1) }}%</p>
        </div>
    </div>

    {{-- Monthly Chart --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
        <h3 class="text-sm font-semibold text-gray-900 mb-4">Monthly Dispensing & Revenue (Last 6 Months)</h3>
        <div class="flex items-end justify-between gap-2 h-56">
            @php($maxCount = max($monthlyDispenses->max(), 1))
            @php($maxRev = max($monthlyRevenue->max(), 1))
            @foreach($monthlyDispenses as $i => $count)
                <div class="flex-1 flex flex-col items-center gap-2">
                    <div class="w-full flex flex-col items-center gap-1">
                        <span class="text-[10px] font-medium text-emerald-600">TSh {{ number_format($monthlyRevenue[$i] / 1000, 0) }}k</span>
                        <div class="w-full bg-gradient-to-t from-emerald-500 to-emerald-400 rounded-t-lg transition-all hover:from-emerald-600 hover:to-emerald-500" style="height: {{ max(($monthlyRevenue[$i] / $maxRev) * 120, 4) }}px; min-height: 4px;" title="TSh {{ number_format($monthlyRevenue[$i], 0) }}"></div>
                        <div class="w-full bg-gradient-to-t from-rose-500 to-rose-400 rounded-t-lg transition-all hover:from-rose-600 hover:to-rose-500" style="height: {{ max(($count / $maxCount) * 60, 4) }}px; min-height: 4px;" title="{{ $count }} dispenses"></div>
                    </div>
                    <span class="text-[10px] text-gray-500 text-center">{{ $monthLabels[$i] }}</span>
                    <span class="text-xs font-bold text-gray-700">{{ $count }}</span>
                </div>
            @endforeach
        </div>
        <div class="flex items-center gap-4 mt-4 pt-4 border-t border-gray-50">
            <div class="flex items-center gap-2">
                <div class="w-3 h-3 rounded bg-rose-400"></div>
                <span class="text-xs text-gray-500">Dispenses</span>
            </div>
            <div class="flex items-center gap-2">
                <div class="w-3 h-3 rounded bg-emerald-400"></div>
                <span class="text-xs text-gray-500">Revenue (TSh)</span>
            </div>
        </div>
    </div>

    {{-- Top Medicines with Profit --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100">
            <h3 class="text-sm font-semibold text-gray-900">Top Dispensed Medicines - Profit Analysis</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-xs text-gray-500 border-b border-gray-100 bg-gray-50/50">
                        <th class="px-5 py-3 font-medium">#</th>
                        <th class="px-5 py-3 font-medium">Medicine</th>
                        <th class="px-5 py-3 font-medium">Qty Dispensed</th>
                        <th class="px-5 py-3 font-medium">Revenue</th>
                        <th class="px-5 py-3 font-medium">Cost</th>
                        <th class="px-5 py-3 font-medium">Profit</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($topMedicines as $i => $med)
                        @php $profit = $med->revenue - $med->cost; @endphp
                        <tr class="border-b border-gray-50 hover:bg-gray-50/50 transition-colors">
                            <td class="px-5 py-3 text-gray-400">{{ $i + 1 }}</td>
                            <td class="px-5 py-3 font-medium text-gray-900">{{ $med->medication?->name ?? 'Unknown' }}</td>
                            <td class="px-5 py-3"><span class="px-2 py-0.5 rounded-full text-[10px] font-medium bg-rose-100 text-rose-700">{{ $med->total_qty }}</span></td>
                            <td class="px-5 py-3 font-medium text-emerald-600">TSh {{ number_format($med->revenue, 0) }}</td>
                            <td class="px-5 py-3 text-blue-600">TSh {{ number_format($med->cost, 0) }}</td>
                            <td class="px-5 py-3 font-medium {{ $profit > 0 ? 'text-violet-600' : 'text-red-600' }}">TSh {{ number_format($profit, 0) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-5 py-8 text-center text-gray-400">No data yet</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
