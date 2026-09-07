@extends('layouts.dashboard')

@section('title', 'Ultrasound Dashboard - ' . config('app.name', 'Laravel'))
@section('page_title', 'Ultrasound Dashboard')

@section('content')
<div class="space-y-6">
    @if (session('status'))
        <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm animate-fade">{{ session('status') }}</div>
    @endif
    @if (session('error'))
        <div class="p-4 rounded-xl bg-red-50 border border-red-200 text-red-700 text-sm animate-fade">{{ session('error') }}</div>
    @endif

    {{-- Stats Cards --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5 animate-fade">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Pending</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['pending'] }}</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-amber-100 flex items-center justify-center">
                    <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5 animate-fade" style="animation-delay:0.05s">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">In Progress</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['processing'] }}</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-blue-100 flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="animation-duration:3s"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5 animate-fade" style="animation-delay:0.1s">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Completed Today</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['completed_today'] }}</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-emerald-100 flex items-center justify-center">
                    <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5 animate-fade" style="animation-delay:0.15s">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Total Today</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['total_today'] }}</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-purple-100 flex items-center justify-center">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                </div>
            </div>
        </div>
    </div>

    {{-- Quick Actions --}}
    <div class="flex items-center gap-3">
        <a href="{{ route('ultrasound.queue') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700 transition-all">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
            Go to Queue
        </a>
        <a href="{{ route('ultrasound-services.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-gray-200 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 transition-all">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            Manage Services
        </a>
    </div>

    {{-- Recent Orders --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <h3 class="text-sm font-semibold text-gray-900">Recent Orders</h3>
            <a href="{{ route('ultrasound.queue') }}" class="text-xs text-emerald-600 hover:text-emerald-700 font-medium">View All</a>
        </div>
        @if($recentOrders->isNotEmpty())
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50/50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Patient</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Services</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ordered By</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($recentOrders as $order)
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="px-6 py-3.5">
                            <div class="text-sm font-medium text-gray-900">{{ $order->patient?->fullName() }}</div>
                            <div class="text-xs text-gray-500">{{ $order->patient?->mrn }}</div>
                        </td>
                        <td class="px-6 py-3.5">
                            @foreach($order->items as $item)
                                <span class="inline-block px-2 py-0.5 bg-purple-50 text-purple-700 text-xs rounded-full mr-1 mb-1">{{ $item->ultrasoundService?->name }}</span>
                            @endforeach
                        </td>
                        <td class="px-6 py-3.5 text-sm text-gray-700">{{ $order->doctor?->name ?? 'N/A' }}</td>
                        <td class="px-6 py-3.5">
                            @if($order->status === 'pending')
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-700">Pending</span>
                            @elseif($order->status === 'processing')
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-700">In Progress</span>
                            @elseif($order->status === 'completed')
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700">Completed</span>
                            @endif
                        </td>
                        <td class="px-6 py-3.5 text-xs text-gray-500">{{ $order->created_at?->format('M d, Y H:i') }}</td>
                        <td class="px-6 py-3.5 text-right">
                            @if($order->status === 'completed')
                                <a href="{{ route('ultrasound.orders.show', $order) }}" class="text-xs text-emerald-600 hover:text-emerald-700 font-medium">View Results</a>
                            @else
                                <a href="{{ route('ultrasound.queue') }}" class="text-xs text-blue-600 hover:text-blue-700 font-medium">Go to Queue</a>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="text-center py-12">
            <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
            <p class="text-gray-500 text-sm">No ultrasound orders yet</p>
        </div>
        @endif
    </div>
</div>
@endsection
