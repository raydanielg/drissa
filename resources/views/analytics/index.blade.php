@extends('layouts.dashboard')

@section('title', 'Analytics - ' . config('app.name', 'Laravel'))
@section('page_title', 'Analytics & Insights')

@section('content')
<div class="space-y-5">

    {{-- Top Doctors --}}
    <div class="bg-slate-800 rounded-md p-6 text-white">
        <p class="text-[10px] uppercase tracking-wider text-slate-400 mb-1">Insights</p>
        <h1 class="text-xl font-bold">Analytics & Insights</h1>
        <p class="text-xs text-slate-400 mt-1">Clinic performance overview.</p>
    </div>

    {{-- Top Doctors --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
        <div class="bg-white rounded-md border border-gray-200 p-4">
            <p class="text-[10px] font-medium text-gray-500 uppercase tracking-wide">Total Patients</p>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ $totalPatients }}</p>
        </div>
        <div class="bg-white rounded-md border border-gray-200 p-4">
            <p class="text-[10px] font-medium text-gray-500 uppercase tracking-wide">Total Doctors</p>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ $totalDoctors }}</p>
        </div>
        <div class="bg-white rounded-md border border-gray-200 p-4">
            <p class="text-[10px] font-medium text-gray-500 uppercase tracking-wide">Appointments</p>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ $totalAppointments }}</p>
        </div>
        <div class="bg-white rounded-md border border-gray-200 p-4">
            <p class="text-[10px] font-medium text-gray-500 uppercase tracking-wide">Total Revenue</p>
            <p class="text-2xl font-bold text-gray-900 mt-1">TSh {{ number_format($totalRevenue, 0) }}</p>
        </div>
    </div>

    {{-- Top Doctors --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
        <div class="bg-white rounded-md border border-emerald-200 p-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded bg-slate-100 flex items-center justify-center flex-shrink-0">
                    <i class="hgi-stroke hgi-user-multiple-02 text-xl text-slate-600"></i>
                </div>
                <div>
                    <p class="text-[10px] font-medium text-gray-500 uppercase tracking-wide">Today's Visits</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $todayVisits }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-md border border-amber-200 p-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded bg-slate-100 flex items-center justify-center flex-shrink-0">
                    <i class="hgi-stroke hgi-money-03 text-xl text-slate-600"></i>
                </div>
                <div>
                    <p class="text-[10px] font-medium text-gray-500 uppercase tracking-wide">Today's Revenue</p>
                    <p class="text-2xl font-bold text-gray-900">TSh {{ number_format($todayRevenue, 0) }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
        {{-- Top Doctors --}}
        <div class="bg-white rounded-md border border-gray-200 p-5">
            <h3 class="text-sm font-bold text-gray-900 mb-4">Monthly Visits (Last 6 Months)</h3>
            <div class="flex items-end justify-between gap-2 h-48">
                @foreach($monthlyVisits as $i => $count)
                    @php($maxVal = max($monthlyVisits->max(), 1))
                    <div class="flex-1 flex flex-col items-center gap-2">
                        <div class="w-full bg-slate-700 rounded-t transition-all" style="height: {{ max(($count / $maxVal) * 100, 2) }}%; min-height: 4px;" title="{{ $count }} visits"></div>
                        <span class="text-[10px] text-gray-500">{{ $visitLabels[$i] }}</span>
                        <span class="text-xs font-bold text-gray-700">{{ $count }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Top Doctors --}}
        <div class="bg-white rounded-md border border-gray-200 p-5">
            <h3 class="text-sm font-bold text-gray-900 mb-4">Monthly Revenue (Last 6 Months)</h3>
            <div class="flex items-end justify-between gap-2 h-48">
                @foreach($monthlyRevenue as $i => $rev)
                    @php($maxVal = max($monthlyRevenue->max(), 1))
                    <div class="flex-1 flex flex-col items-center gap-2">
                        <div class="w-full bg-emerald-600 rounded-t transition-all" style="height: {{ max(($rev / $maxVal) * 100, 2) }}%; min-height: 4px;" title="TSh {{ number_format($rev) }}"></div>
                        <span class="text-[10px] text-gray-500">{{ $revenueLabels[$i] }}</span>
                        <span class="text-xs font-bold text-gray-700">{{ number_format($rev / 1000, 0) }}k</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
        {{-- Top Doctors --}}
        <div class="bg-white rounded-md border border-gray-200 p-5">
            <h3 class="text-sm font-bold text-gray-900 mb-4">Patients by Gender</h3>
            <div class="space-y-3">
                @foreach($genderStats as $stat)
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-gray-600">{{ ucfirst($stat->gender ?? 'Unknown') }}</span>
                        <span class="text-xs font-semibold text-gray-900">{{ $stat->count }}</span>
                    </div>
                @endforeach
                @if($genderStats->isEmpty())
                    <p class="text-xs text-gray-400 text-center py-4">No data</p>
                @endif
            </div>
        </div>

        {{-- Top Doctors --}}
        <div class="bg-white rounded-md border border-gray-200 p-5">
            <h3 class="text-sm font-bold text-gray-900 mb-4">Appointments by Type</h3>
            <div class="space-y-3">
                @foreach($typeStats as $stat)
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-gray-600">{{ ucfirst($stat->type ?? 'Unknown') }}</span>
                        <span class="text-xs font-semibold text-gray-900">{{ $stat->count }}</span>
                    </div>
                @endforeach
                @if($typeStats->isEmpty())
                    <p class="text-xs text-gray-400 text-center py-4">No data</p>
                @endif
            </div>
        </div>

        {{-- Top Doctors --}}
        <div class="bg-white rounded-md border border-gray-200 p-5">
            <h3 class="text-sm font-bold text-gray-900 mb-4">Today's Visit Status</h3>
            <div class="space-y-3">
                @foreach($visitStatusStats as $stat)
                    <div class="flex items-center justify-between">
                        <span class="inline-flex items-center gap-1.5 text-xs text-gray-600">
                            <span class="w-1.5 h-1.5 rounded-full {{ $stat->status === 'completed' ? 'bg-emerald-500' : ($stat->status === 'cancelled' ? 'bg-red-500' : 'bg-amber-500') }}"></span>
                            {{ ucfirst(str_replace('_', ' ', $stat->status)) }}
                        </span>
                        <span class="text-xs font-semibold text-gray-900">{{ $stat->count }}</span>
                    </div>
                @endforeach
                @if($visitStatusStats->isEmpty())
                    <p class="text-xs text-gray-400 text-center py-4">No visits today</p>
                @endif
            </div>
        </div>
    </div>

    {{-- Top Doctors --}}
    <div class="bg-white rounded-md border border-gray-200 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100">
            <h3 class="text-sm font-bold text-gray-900">Top Doctors (Last 30 Days)</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-[10px] uppercase text-gray-400 border-b border-gray-100 bg-gray-50">
                        <th class="px-5 py-3 font-medium">#</th>
                        <th class="px-5 py-3 font-medium">Doctor</th>
                        <th class="px-5 py-3 font-medium">Visits (30 days)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($topDoctors as $i => $doctor)
                        <tr class="hover:bg-gray-50/50">
                            <td class="px-5 py-3 text-gray-400 text-xs">{{ $i + 1 }}</td>
                            <td class="px-5 py-3 font-medium text-gray-900 text-xs">{{ $doctor->name }}</td>
                            <td class="px-5 py-3 text-xs font-semibold text-gray-900">{{ $doctor->visits_count }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="px-5 py-8 text-center text-gray-400 text-xs">No data</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
