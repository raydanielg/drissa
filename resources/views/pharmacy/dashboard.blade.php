@extends('layouts.dashboard')

@section('title', 'Pharmacy Dashboard - ' . config('app.name', 'Laravel'))
@section('page_title', 'Pharmacy Dashboard')

@section('content')
<div class="space-y-6">

    {{-- Stats Grid --}}
    <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-medium text-gray-500 uppercase tracking-wider">Total Medicines</span>
                <div class="w-8 h-8 rounded-lg bg-emerald-100 flex items-center justify-center">
                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5"/></svg>
                </div>
            </div>
            <p class="text-2xl font-bold text-gray-900">{{ $stats['total_medicines'] }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-medium text-gray-500 uppercase tracking-wider">Low Stock</span>
                <div class="w-8 h-8 rounded-lg bg-amber-100 flex items-center justify-center">
                    <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>
            </div>
            <p class="text-2xl font-bold text-amber-600">{{ $stats['low_stock'] }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-medium text-gray-500 uppercase tracking-wider">Out of Stock</span>
                <div class="w-8 h-8 rounded-lg bg-red-100 flex items-center justify-center">
                    <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </div>
            </div>
            <p class="text-2xl font-bold text-red-600">{{ $stats['out_of_stock'] }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-medium text-gray-500 uppercase tracking-wider">Pending Rx</span>
                <div class="w-8 h-8 rounded-lg bg-sky-100 flex items-center justify-center">
                    <svg class="w-4 h-4 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                </div>
            </div>
            <p class="text-2xl font-bold text-sky-600">{{ $stats['pending_prescriptions'] }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-medium text-gray-500 uppercase tracking-wider">Dispensed Today</span>
                <div class="w-8 h-8 rounded-lg bg-green-100 flex items-center justify-center">
                    <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
            <p class="text-2xl font-bold text-green-600">{{ $stats['dispensed_today'] }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-medium text-gray-500 uppercase tracking-wider">Expiring Soon</span>
                <div class="w-8 h-8 rounded-lg bg-orange-100 flex items-center justify-center">
                    <svg class="w-4 h-4 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
            <p class="text-2xl font-bold text-orange-600">{{ $stats['expiring_soon'] }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Recent Prescriptions --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                <h3 class="text-sm font-semibold text-gray-900">Recent Prescriptions</h3>
                <a href="{{ route('pharmacy.queue') }}" class="text-xs font-medium text-emerald-600 hover:text-emerald-700">View Queue</a>
            </div>
            <div class="p-5">
                @forelse($recentPrescriptions as $rx)
                    <div class="flex items-center gap-3 py-3 {{ !$loop->last ? 'border-b border-gray-50' : '' }}">
                        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-rose-500 to-rose-700 flex items-center justify-center text-white text-sm font-bold shadow-sm">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5"/></svg>
                        </div>
                        <div class="flex-1">
                            <div class="text-sm font-medium text-gray-900">{{ $rx->visit?->patient?->fullName() ?? 'Unknown' }}</div>
                            <div class="text-xs text-gray-500">{{ $rx->items->count() }} items • {{ $rx->created_at->format('M j, H:i') }}</div>
                        </div>
                        <span class="px-2 py-1 rounded-full text-[10px] font-medium {{ $rx->status === 'pending' ? 'bg-amber-100 text-amber-700' : 'bg-green-100 text-green-700' }}">
                            {{ ucfirst($rx->status) }}
                        </span>
                    </div>
                @empty
                    <div class="text-center py-8 text-gray-400 text-sm">No prescriptions yet</div>
                @endforelse
            </div>
        </div>

        {{-- Low Stock Alerts --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                <h3 class="text-sm font-semibold text-gray-900">Low Stock Alerts</h3>
                <a href="{{ route('pharmacy.inventory') }}" class="text-xs font-medium text-emerald-600 hover:text-emerald-700">View All</a>
            </div>
            <div class="p-5">
                @forelse($lowStockMeds as $med)
                    <div class="flex items-center gap-3 py-3 {{ !$loop->last ? 'border-b border-gray-50' : '' }}">
                        <div class="flex-1">
                            <div class="text-sm font-medium text-gray-900">{{ $med->name }}</div>
                            <div class="text-xs text-gray-500">Reorder at: {{ $med->reorder_level }}</div>
                        </div>
                        <span class="px-2 py-1 rounded-full text-[10px] font-medium {{ $med->stock_quantity == 0 ? 'bg-red-100 text-red-700' : 'bg-amber-100 text-amber-700' }}">
                            {{ $med->stock_quantity }} left
                        </span>
                    </div>
                @empty
                    <div class="text-center py-8 text-gray-400 text-sm">All stock levels are healthy</div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
