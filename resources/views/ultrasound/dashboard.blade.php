@extends('layouts.dashboard')

@section('title', 'Ultrasound Dashboard - ' . config('app.name', 'Laravel'))
@section('page_title', 'Ultrasound Dashboard')

@section('content')
<div class="space-y-6">
    @if (session('status'))
        <div class="p-3 rounded-md bg-white border-l-2 border-emerald-500 text-emerald-700 text-xs animate-fade">{{ session('status') }}</div>
    @endif
    @if (session('error'))
        <div class="p-3 rounded-md bg-white border-l-2 border-red-500 text-red-700 text-xs animate-fade">{{ session('error') }}</div>
    @endif

    {{-- Recent Orders --}}
    <div class="bg-slate-800 rounded-md p-6 text-white">
        <div class="flex flex-wrap items-end justify-between gap-3">
            <div>
                <p class="text-[10px] uppercase tracking-wider text-slate-400 mb-1">Imaging</p>
                <h1 class="text-xl font-bold">Ultrasound Dashboard</h1>
                <p class="text-xs text-slate-400 mt-1">{{ today()->format('l, F j, Y') }}</p>
            </div>
            <a href="{{ route('ultrasound.queue') }}" class="px-3 py-1.5 bg-white text-slate-800 hover:bg-gray-100 rounded text-xs font-semibold transition-colors inline-flex items-center gap-1.5">
                <i class="hgi-stroke hgi-menu-01 text-sm"></i>
                Go to Queue
            </a>
        </div>
    </div>

    {{-- Recent Orders --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
        <div class="bg-white rounded-md border border-amber-200 p-4 animate-fade">
            <div class="flex items-start justify-between">
                <p class="text-[10px] font-medium text-gray-500 uppercase tracking-wide">Pending</p>
                <i class="hgi-stroke hgi-clock-01 text-base text-amber-500"></i>
            </div>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['pending'] }}</p>
        </div>
        <div class="bg-white rounded-md border border-gray-200 p-4 animate-fade" style="animation-delay:0.05s">
            <div class="flex items-start justify-between">
                <p class="text-[10px] font-medium text-gray-500 uppercase tracking-wide">In Progress</p>
                <i class="hgi-stroke hgi-refresh text-base text-gray-400"></i>
            </div>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['processing'] }}</p>
        </div>
        <div class="bg-white rounded-md border border-emerald-200 p-4 animate-fade" style="animation-delay:0.1s">
            <div class="flex items-start justify-between">
                <p class="text-[10px] font-medium text-gray-500 uppercase tracking-wide">Completed Today</p>
                <i class="hgi-stroke hgi-checkmark-circle-02 text-base text-emerald-500"></i>
            </div>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['completed_today'] }}</p>
        </div>
        <div class="bg-white rounded-md border border-gray-200 p-4 animate-fade" style="animation-delay:0.15s">
            <div class="flex items-start justify-between">
                <p class="text-[10px] font-medium text-gray-500 uppercase tracking-wide">Total Today</p>
                <i class="hgi-stroke hgi-analytics-02 text-base text-gray-400"></i>
            </div>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['total_today'] }}</p>
        </div>
    </div>

    {{-- Recent Orders --}}
    <div class="flex items-center gap-2">
        <a href="{{ route('ultrasound.queue') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-slate-800 text-white text-sm font-medium rounded hover:bg-slate-700 transition-colors">
            <i class="hgi-stroke hgi-menu-01 text-base"></i>
            Go to Queue
        </a>
        <a href="{{ route('ultrasound-services.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-200 text-gray-700 text-sm font-medium rounded hover:bg-gray-100 transition-colors">
            <i class="hgi-stroke hgi-settings-02 text-base"></i>
            Manage Services
        </a>
    </div>

    {{-- Recent Orders --}}
    <div class="bg-white rounded-md border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <h3 class="text-sm font-bold text-gray-900">Recent Orders</h3>
            <a href="{{ route('ultrasound.queue') }}" class="text-xs text-slate-600 hover:text-slate-800 font-medium">View All</a>
        </div>
        @if($recentOrders->isNotEmpty())
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50/50">
                    <tr>
                        <th class="px-6 py-3 text-left text-[10px] font-medium text-gray-400 uppercase">Patient</th>
                        <th class="px-6 py-3 text-left text-[10px] font-medium text-gray-400 uppercase">Services</th>
                        <th class="px-6 py-3 text-left text-[10px] font-medium text-gray-400 uppercase">Ordered By</th>
                        <th class="px-6 py-3 text-left text-[10px] font-medium text-gray-400 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-[10px] font-medium text-gray-400 uppercase">Date</th>
                        <th class="px-6 py-3 text-right text-[10px] font-medium text-gray-400 uppercase">Action</th>
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
                                <span class="inline-block px-2 py-0.5 bg-slate-100 text-slate-700 text-[10px] rounded mr-1 mb-1">{{ $item->ultrasoundService?->name }}</span>
                            @endforeach
                        </td>
                        <td class="px-6 py-3.5 text-sm text-gray-700">{{ $order->doctor?->name ?? 'N/A' }}</td>
                        <td class="px-6 py-3.5">
                            @if($order->status === 'pending')
                                <span class="inline-flex items-center gap-1.5 text-[10px] font-medium text-gray-600"><span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>Pending</span>
                            @elseif($order->status === 'processing')
                                <span class="inline-flex items-center gap-1.5 text-[10px] font-medium text-gray-600"><span class="w-1.5 h-1.5 rounded-full bg-slate-700"></span>In Progress</span>
                            @elseif($order->status === 'completed')
                                <span class="inline-flex items-center gap-1.5 text-[10px] font-medium text-gray-600"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>Completed</span>
                            @endif
                        </td>
                        <td class="px-6 py-3.5 text-xs text-gray-500">{{ $order->created_at?->format('M d, Y H:i') }}</td>
                        <td class="px-6 py-3.5 text-right">
                            @if($order->status === 'completed')
                                <a href="{{ route('ultrasound.orders.show', $order) }}" class="text-xs text-slate-600 hover:text-slate-800 font-medium">View Results</a>
                            @else
                                <a href="{{ route('ultrasound.queue') }}" class="text-xs text-slate-600 hover:text-slate-800 font-medium">Go to Queue</a>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="text-center py-12">
            <i class="hgi-stroke hgi-analytics-02 text-base text-gray-300 mx-auto mb-4"></i>
            <p class="text-gray-500 text-sm">No ultrasound orders yet</p>
        </div>
        @endif
    </div>
</div>
@endsection
