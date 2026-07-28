@extends('layouts.dashboard')

@section('title', 'Analytics - ' . config('app.name', 'Laravel'))
@section('page_title', 'Analytics & Insights')

@section('content')
<div class="space-y-6">

    {{-- Key Metrics --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-gradient-to-br from-emerald-500 to-emerald-700 rounded-xl p-5 text-white shadow-lg">
            <p class="text-xs uppercase tracking-wider opacity-80 mb-1">Total Patients</p>
            <p class="text-3xl font-bold">{{ $totalPatients }}</p>
        </div>
        <div class="bg-gradient-to-br from-sky-500 to-sky-700 rounded-xl p-5 text-white shadow-lg">
            <p class="text-xs uppercase tracking-wider opacity-80 mb-1">Total Doctors</p>
            <p class="text-3xl font-bold">{{ $totalDoctors }}</p>
        </div>
        <div class="bg-gradient-to-br from-purple-500 to-purple-700 rounded-xl p-5 text-white shadow-lg">
            <p class="text-xs uppercase tracking-wider opacity-80 mb-1">Appointments</p>
            <p class="text-3xl font-bold">{{ $totalAppointments }}</p>
        </div>
        <div class="bg-gradient-to-br from-amber-500 to-amber-700 rounded-xl p-5 text-white shadow-lg">
            <p class="text-xs uppercase tracking-wider opacity-80 mb-1">Total Revenue</p>
            <p class="text-3xl font-bold">TSh {{ number_format($totalRevenue, 0) }}</p>
        </div>
    </div>

    {{-- Today's Stats --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-xl bg-emerald-100 flex items-center justify-center">
                    <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </div>
                <div>
                    <p class="text-xs text-gray-500">Today's Visits</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $todayVisits }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-xl bg-amber-100 flex items-center justify-center">
                    <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/></svg>
                </div>
                <div>
                    <p class="text-xs text-gray-500">Today's Revenue</p>
                    <p class="text-2xl font-bold text-gray-900">TSh {{ number_format($todayRevenue, 0) }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Monthly Visits Chart --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <h3 class="text-sm font-semibold text-gray-900 mb-4">Monthly Visits (Last 6 Months)</h3>
            <div class="flex items-end justify-between gap-2 h-48">
                @foreach($monthlyVisits as $i => $count)
                    @php($maxVal = max($monthlyVisits->max(), 1))
                    <div class="flex-1 flex flex-col items-center gap-2">
                        <div class="w-full bg-gradient-to-t from-emerald-500 to-emerald-400 rounded-t-lg transition-all" style="height: {{ max(($count / $maxVal) * 100, 2) }}%; min-height: 4px;" title="{{ $count }} visits"></div>
                        <span class="text-[10px] text-gray-500">{{ $visitLabels[$i] }}</span>
                        <span class="text-xs font-bold text-gray-700">{{ $count }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Monthly Revenue Chart --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <h3 class="text-sm font-semibold text-gray-900 mb-4">Monthly Revenue (Last 6 Months)</h3>
            <div class="flex items-end justify-between gap-2 h-48">
                @foreach($monthlyRevenue as $i => $rev)
                    @php($maxVal = max($monthlyRevenue->max(), 1))
                    <div class="flex-1 flex flex-col items-center gap-2">
                        <div class="w-full bg-gradient-to-t from-amber-500 to-amber-400 rounded-t-lg transition-all" style="height: {{ max(($rev / $maxVal) * 100, 2) }}%; min-height: 4px;" title="TSh {{ number_format($rev) }}"></div>
                        <span class="text-[10px] text-gray-500">{{ $revenueLabels[$i] }}</span>
                        <span class="text-xs font-bold text-gray-700">{{ number_format($rev / 1000, 0) }}k</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Gender Distribution --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <h3 class="text-sm font-semibold text-gray-900 mb-4">Patients by Gender</h3>
            <div class="space-y-3">
                @foreach($genderStats as $stat)
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-700">{{ ucfirst($stat->gender ?? 'Unknown') }}</span>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-medium bg-emerald-100 text-emerald-700">{{ $stat->count }}</span>
                    </div>
                @endforeach
                @if($genderStats->isEmpty())
                    <p class="text-sm text-gray-400 text-center py-4">No data</p>
                @endif
            </div>
        </div>

        {{-- Appointment Types --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <h3 class="text-sm font-semibold text-gray-900 mb-4">Appointments by Type</h3>
            <div class="space-y-3">
                @foreach($typeStats as $stat)
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-700">{{ ucfirst($stat->type ?? 'Unknown') }}</span>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-medium bg-sky-100 text-sky-700">{{ $stat->count }}</span>
                    </div>
                @endforeach
                @if($typeStats->isEmpty())
                    <p class="text-sm text-gray-400 text-center py-4">No data</p>
                @endif
            </div>
        </div>

        {{-- Today's Visit Status --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <h3 class="text-sm font-semibold text-gray-900 mb-4">Today's Visit Status</h3>
            <div class="space-y-3">
                @foreach($visitStatusStats as $stat)
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-700">{{ ucfirst(str_replace('_', ' ', $stat->status)) }}</span>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-medium {{ $stat->status === 'completed' ? 'bg-green-100 text-green-700' : ($stat->status === 'cancelled' ? 'bg-red-100 text-red-700' : 'bg-amber-100 text-amber-700') }}">{{ $stat->count }}</span>
                    </div>
                @endforeach
                @if($visitStatusStats->isEmpty())
                    <p class="text-sm text-gray-400 text-center py-4">No visits today</p>
                @endif
            </div>
        </div>
    </div>

    {{-- Top Doctors --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100">
            <h3 class="text-sm font-semibold text-gray-900">Top Doctors (Last 30 Days)</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-xs text-gray-500 border-b border-gray-100">
                        <th class="px-5 py-3 font-medium">#</th>
                        <th class="px-5 py-3 font-medium">Doctor</th>
                        <th class="px-5 py-3 font-medium">Visits (30 days)</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($topDoctors as $i => $doctor)
                        <tr class="border-b border-gray-50 hover:bg-gray-50/50">
                            <td class="px-5 py-3 text-gray-400">{{ $i + 1 }}</td>
                            <td class="px-5 py-3 font-medium text-gray-900">{{ $doctor->name }}</td>
                            <td class="px-5 py-3"><span class="px-2 py-0.5 rounded-full text-[10px] font-medium bg-emerald-100 text-emerald-700">{{ $doctor->visits_count }}</span></td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="px-5 py-8 text-center text-gray-400">No data</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
