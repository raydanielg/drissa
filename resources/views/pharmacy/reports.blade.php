@extends('layouts.dashboard')

@section('title', 'Pharmacy Reports - ' . config('app.name', 'Laravel'))
@section('page_title', 'Pharmacy Reports')

@section('content')
<div class="space-y-6">

    {{-- Summary Stats --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <p class="text-xs text-gray-500 uppercase tracking-wider mb-1">Total Dispensed</p>
            <p class="text-2xl font-bold text-gray-900">{{ $totalDispensed }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <p class="text-xs text-gray-500 uppercase tracking-wider mb-1">Dispensed Today</p>
            <p class="text-2xl font-bold text-emerald-600">{{ $dispensedToday }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <p class="text-xs text-gray-500 uppercase tracking-wider mb-1">Total Value</p>
            <p class="text-2xl font-bold text-emerald-600">TSh {{ number_format($totalValue, 0) }}</p>
        </div>
    </div>

    {{-- Monthly Chart --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
        <h3 class="text-sm font-semibold text-gray-900 mb-4">Monthly Dispensing (Last 6 Months)</h3>
        <div class="flex items-end justify-between gap-2 h-48">
            @foreach($monthlyDispenses as $i => $count)
                @php($maxVal = max($monthlyDispenses->max(), 1))
                <div class="flex-1 flex flex-col items-center gap-2">
                    <div class="w-full bg-gradient-to-t from-rose-500 to-rose-400 rounded-t-lg transition-all hover:from-rose-600 hover:to-rose-500" style="height: {{ max(($count / $maxVal) * 100, 2) }}%; min-height: 4px;" title="{{ $count }} dispenses"></div>
                    <span class="text-[10px] text-gray-500 text-center">{{ $monthLabels[$i] }}</span>
                    <span class="text-xs font-bold text-gray-700">{{ $count }}</span>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Top Medicines --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100">
            <h3 class="text-sm font-semibold text-gray-900">Top Dispensed Medicines</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-xs text-gray-500 border-b border-gray-100">
                        <th class="px-5 py-3 font-medium">#</th>
                        <th class="px-5 py-3 font-medium">Medicine</th>
                        <th class="px-5 py-3 font-medium">Total Dispensed</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($topMedicines as $i => $med)
                        <tr class="border-b border-gray-50 hover:bg-gray-50/50">
                            <td class="px-5 py-3 text-gray-400">{{ $i + 1 }}</td>
                            <td class="px-5 py-3 font-medium text-gray-900">{{ $med->medication?->name ?? 'Unknown' }}</td>
                            <td class="px-5 py-3"><span class="px-2 py-0.5 rounded-full text-[10px] font-medium bg-rose-100 text-rose-700">{{ $med->total }}</span></td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="px-5 py-8 text-center text-gray-400">No data yet</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
