@extends('layouts.dashboard')

@section('title', 'Pharmacy Dashboard - ' . config('app.name', 'Laravel'))
@section('page_title', 'Pharmacy Dashboard')

@section('content')
<div class="space-y-6">

    {{-- Top Medicines --}}
    <div class="bg-slate-800 rounded-md p-6 text-white">
        <div class="flex items-center justify-between mb-4">
            <div>
                <p class="text-[10px] text-slate-400 uppercase tracking-wider mb-1">Today's Revenue</p>
                <p class="text-3xl font-bold">TSh {{ number_format($stats['today_revenue'], 0) }}</p>
            </div>
            <div class="text-right">
                <p class="text-[10px] text-slate-400 uppercase tracking-wider mb-1">Today's Profit</p>
                <p class="text-3xl font-bold">TSh {{ number_format($stats['today_profit'], 0) }}</p>
            </div>
        </div>
        <div class="flex items-center gap-6 pt-4 border-t border-slate-700">
            <div>
                <p class="text-[10px] text-slate-400 uppercase tracking-wider">Total Revenue</p>
                <p class="text-lg font-semibold">TSh {{ number_format($stats['total_revenue'], 0) }}</p>
            </div>
            <div class="border-l border-slate-700 pl-6">
                <p class="text-[10px] text-slate-400 uppercase tracking-wider">Total Profit</p>
                <p class="text-lg font-semibold">TSh {{ number_format($stats['total_profit'], 0) }}</p>
            </div>
            <div class="border-l border-slate-700 pl-6">
                <p class="text-[10px] text-slate-400 uppercase tracking-wider">Potential Profit (Stock)</p>
                <p class="text-lg font-semibold">TSh {{ number_format($stats['potential_profit'], 0) }}</p>
            </div>
        </div>
    </div>

    {{-- Top Medicines --}}
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3">
        <div class="bg-white rounded-md border border-gray-200 p-4">
            <div class="flex items-start justify-between">
                <p class="text-[10px] font-medium text-gray-500 uppercase tracking-wide">Medicines</p>
                <i class="hgi-stroke hgi-test-tube-01 text-base text-gray-400"></i>
            </div>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['total_medicines'] }}</p>
        </div>
        <div class="bg-white rounded-md border border-amber-200 p-4">
            <div class="flex items-start justify-between">
                <p class="text-[10px] font-medium text-gray-500 uppercase tracking-wide">Low Stock</p>
                <i class="hgi-stroke hgi-alert-02 text-base text-amber-500"></i>
            </div>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['low_stock'] }}</p>
            <p class="text-[10px] text-amber-600 mt-0.5">Reorder soon</p>
        </div>
        <div class="bg-white rounded-md border border-red-200 p-4">
            <div class="flex items-start justify-between">
                <p class="text-[10px] font-medium text-gray-500 uppercase tracking-wide">Out of Stock</p>
                <i class="hgi-stroke hgi-cancel-01 text-base text-red-500"></i>
            </div>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['out_of_stock'] }}</p>
            <p class="text-[10px] text-red-600 mt-0.5">Critical</p>
        </div>
        <div class="bg-white rounded-md border border-gray-200 p-4">
            <div class="flex items-start justify-between">
                <p class="text-[10px] font-medium text-gray-500 uppercase tracking-wide">Pending Rx</p>
                <i class="hgi-stroke hgi-clipboard text-base text-gray-400"></i>
            </div>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['pending_prescriptions'] }}</p>
        </div>
        <div class="bg-white rounded-md border border-emerald-200 p-4">
            <div class="flex items-start justify-between">
                <p class="text-[10px] font-medium text-gray-500 uppercase tracking-wide">Dispensed Today</p>
                <i class="hgi-stroke hgi-checkmark-circle-02 text-base text-emerald-500"></i>
            </div>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['dispensed_today'] }}</p>
        </div>
        <div class="bg-white rounded-md border border-amber-200 p-4">
            <div class="flex items-start justify-between">
                <p class="text-[10px] font-medium text-gray-500 uppercase tracking-wide">Expiring Soon</p>
                <i class="hgi-stroke hgi-clock-01 text-base text-amber-500"></i>
            </div>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['expiring_soon'] }}</p>
        </div>
    </div>

    {{-- Top Medicines --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
        <div class="bg-white rounded-md border border-gray-200 p-5">
            <p class="text-[10px] text-gray-500 uppercase tracking-wide mb-1">Stock Cost Value</p>
            <p class="text-2xl font-bold text-gray-900">TSh {{ number_format($stats['stock_value'], 0) }}</p>
            <p class="text-[10px] text-gray-400 mt-1">Total purchase value of current stock</p>
        </div>
        <div class="bg-white rounded-md border border-gray-200 p-5">
            <p class="text-[10px] text-gray-500 uppercase tracking-wide mb-1">Stock Retail Value</p>
            <p class="text-2xl font-bold text-gray-900">TSh {{ number_format($stats['retail_value'], 0) }}</p>
            <p class="text-[10px] text-gray-400 mt-1">Total selling value of current stock</p>
        </div>
        <div class="bg-white rounded-md border border-emerald-200 p-5">
            <p class="text-[10px] text-gray-500 uppercase tracking-wide mb-1">Potential Profit</p>
            <p class="text-2xl font-bold text-emerald-600">TSh {{ number_format($stats['potential_profit'], 0) }}</p>
            <p class="text-[10px] text-gray-400 mt-1">If all current stock is sold</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Top Medicines --}}
        <div class="bg-white rounded-md border border-gray-200 overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                <h3 class="text-sm font-bold text-gray-900">Recent Prescriptions</h3>
                <a href="{{ route('pharmacy.queue') }}" class="text-xs font-medium text-slate-600 hover:text-slate-800">View Queue</a>
            </div>
            <div class="p-5">
                @forelse($recentPrescriptions as $rx)
                    <div class="flex items-center gap-3 py-3 {{ !$loop->last ? 'border-b border-gray-50' : '' }}">
                        <div class="w-10 h-10 rounded-full bg-slate-100 text-slate-600 flex items-center justify-center">
                            <i class="hgi-stroke hgi-test-tube-01 text-xl"></i>
                        </div>
                        <div class="flex-1">
                            <div class="text-sm font-medium text-gray-900">{{ $rx->visit?->patient?->fullName() ?? 'Unknown' }}</div>
                            <div class="text-xs text-gray-500">{{ $rx->items->count() }} items • {{ $rx->created_at->format('M j, H:i') }}</div>
                        </div>
                        <span class="inline-flex items-center gap-1.5 text-[10px] font-medium capitalize text-gray-600">
                            <span class="w-1.5 h-1.5 rounded-full {{ $rx->status === 'pending' ? 'bg-amber-500' : 'bg-emerald-500' }}"></span>
                            {{ ucfirst($rx->status) }}
                        </span>
                    </div>
                @empty
                    <div class="text-center py-8 text-gray-400 text-sm">No prescriptions yet</div>
                @endforelse
            </div>
        </div>

        {{-- Top Medicines --}}
        <div class="bg-white rounded-md border border-gray-200 overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                <h3 class="text-sm font-bold text-gray-900">Low Stock Alerts</h3>
                <a href="{{ route('pharmacy.inventory') }}" class="text-xs font-medium text-slate-600 hover:text-slate-800">View All</a>
            </div>
            <div class="p-5">
                @forelse($lowStockMeds as $med)
                    <div class="flex items-center gap-3 py-3 {{ !$loop->last ? 'border-b border-gray-50' : '' }}">
                        <div class="flex-1">
                            <div class="text-sm font-medium text-gray-900">{{ $med->name }}</div>
                            <div class="text-xs text-gray-500">Reorder at: {{ $med->reorder_level }}</div>
                        </div>
                        <span class="inline-flex items-center gap-1.5 text-[10px] font-medium text-gray-600">
                            <span class="w-1.5 h-1.5 rounded-full {{ $med->stock_quantity == 0 ? 'bg-red-500' : 'bg-amber-500' }}"></span>
                            {{ $med->stock_quantity }} left
                        </span>
                    </div>
                @empty
                    <div class="text-center py-8 text-gray-400 text-sm">All stock levels are healthy</div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Top Medicines --}}
    @if($topMeds->isNotEmpty())
    <div class="bg-white rounded-md border border-gray-200 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100">
            <h3 class="text-sm font-bold text-gray-900">Top Dispensed Medicines</h3>
        </div>
        <div class="p-5 space-y-3">
            @foreach($topMeds as $i => $med)
                <div class="flex items-center gap-3">
                    <span class="w-6 h-6 rounded-full bg-slate-100 text-slate-600 text-[10px] font-bold flex items-center justify-center">{{ $i + 1 }}</span>
                    <div class="flex-1">
                        <div class="text-sm font-medium text-gray-900">{{ $med->medication?->name ?? 'Unknown' }}</div>
                    </div>
                    <span class="text-xs font-medium text-gray-500">{{ $med->total_qty }} units</span>
                </div>
            @endforeach
        </div>
    </div>
    @endif
</div>
@endsection
