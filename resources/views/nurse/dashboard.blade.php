@extends('layouts.dashboard')

@section('title', 'Nurse Dashboard - ' . config('app.name', 'Laravel'))
@section('page_title', 'Nurse Dashboard')

@section('content')
<div class="space-y-5">

    {{-- Recent Vitals --}}
    <div class="bg-slate-800 rounded-md p-6 text-white">
        <div class="flex flex-wrap items-end justify-between gap-3">
            <div>
                <p class="text-[10px] uppercase tracking-wider text-slate-400 mb-1">Nursing</p>
                <h1 class="text-xl font-bold">Nurse Dashboard</h1>
                <p class="text-xs text-slate-400 mt-1">{{ today()->format('l, F j, Y') }}</p>
            </div>
            <a href="{{ route('nurse.queue') }}" class="px-3 py-1.5 bg-white text-slate-800 hover:bg-gray-100 rounded text-xs font-semibold transition-colors">Go to Queue</a>
        </div>
    </div>

    {{-- Recent Vitals --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
        <div class="bg-white rounded-md border border-gray-200 p-4">
            <div class="flex items-start justify-between">
                <p class="text-[10px] font-medium text-gray-500 uppercase tracking-wide">Today's Visits</p>
                <i class="hgi-stroke hgi-user-multiple-02 text-base text-gray-400"></i>
            </div>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ $todayVisits }}</p>
        </div>
        <div class="bg-white rounded-md border border-amber-200 p-4">
            <div class="flex items-start justify-between">
                <p class="text-[10px] font-medium text-gray-500 uppercase tracking-wide">Waiting Vitals</p>
                <i class="hgi-stroke hgi-clock-01 text-base text-amber-500"></i>
            </div>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ $waitingVitals->count() }}</p>
            <p class="text-[10px] text-amber-600 mt-0.5">Need vitals</p>
        </div>
        <div class="bg-white rounded-md border border-gray-200 p-4">
            <div class="flex items-start justify-between">
                <p class="text-[10px] font-medium text-gray-500 uppercase tracking-wide">Vitals Today</p>
                <i class="hgi-stroke hgi-analytics-02 text-base text-gray-400"></i>
            </div>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ $vitalsRecordedToday }}</p>
        </div>
        <div class="bg-white rounded-md border border-gray-200 p-4">
            <div class="flex items-start justify-between">
                <p class="text-[10px] font-medium text-gray-500 uppercase tracking-wide">Total Patients</p>
                <i class="hgi-stroke hgi-user-multiple-02 text-base text-gray-400"></i>
            </div>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ $totalPatients }}</p>
        </div>
    </div>

    {{-- Recent Vitals --}}
    <div class="bg-white rounded-md border border-gray-200 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
            <div>
                <h3 class="text-sm font-bold text-gray-900">Patients Waiting for Vitals</h3>
                <p class="text-xs text-gray-400">Take vitals before sending to doctor</p>
            </div>
            <a href="{{ route('nurse.queue') }}" class="text-xs font-medium text-slate-600 hover:text-slate-800">View All</a>
        </div>
        <div class="p-5">
            @forelse($waitingVitals as $visit)
                <div class="flex items-center gap-3 py-3 {{ !$loop->last ? 'border-b border-gray-50' : '' }}">
                    <div class="w-10 h-10 rounded-full bg-slate-100 text-slate-600 flex items-center justify-center text-sm font-bold">
                        {{ strtoupper(substr($visit->patient->fullName(), 0, 1)) }}
                    </div>
                    <div class="flex-1">
                        <div class="text-sm font-medium text-gray-900">{{ $visit->patient->fullName() }}</div>
                        <div class="text-xs text-gray-500">{{ $visit->visit_number }} • {{ $visit->registered_at->format('H:i') }}</div>
                    </div>
                    <a href="{{ route('nurse.queue') }}" class="px-3 py-1.5 bg-slate-800 hover:bg-slate-700 text-white text-xs font-medium rounded transition-colors">Take Vitals</a>
                </div>
            @empty
                <div class="text-center py-8 text-gray-400 text-sm">
                    <i class="hgi-stroke hgi-checkmark-circle-02 text-6xl mx-auto mb-2 text-gray-300"></i>
                    No patients waiting
                </div>
            @endforelse
        </div>
    </div>

    {{-- Recent Vitals --}}
    <div class="bg-white rounded-md border border-gray-200 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100">
            <h3 class="text-sm font-bold text-gray-900">Recently Recorded Vitals</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-[10px] uppercase text-gray-400 border-b border-gray-100">
                        <th class="px-5 py-3 font-medium">Patient</th>
                        <th class="px-5 py-3 font-medium">Temp</th>
                        <th class="px-5 py-3 font-medium">BP</th>
                        <th class="px-5 py-3 font-medium">Pulse</th>
                        <th class="px-5 py-3 font-medium">Weight</th>
                        <th class="px-5 py-3 font-medium">Time</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentVitals as $vital)
                        <tr class="border-b border-gray-50 hover:bg-gray-50/50">
                            <td class="px-5 py-3 font-medium text-gray-900">{{ $vital->visit?->patient?->fullName() ?? 'Unknown' }}</td>
                            <td class="px-5 py-3 text-gray-500">{{ $vital->temperature ? $vital->temperature . '°C' : '-' }}</td>
                            <td class="px-5 py-3 text-gray-500">{{ $vital->blood_pressure ?? '-' }}</td>
                            <td class="px-5 py-3 text-gray-500">{{ $vital->pulse ? $vital->pulse . ' bpm' : '-' }}</td>
                            <td class="px-5 py-3 text-gray-500">{{ $vital->weight ? $vital->weight . ' kg' : '-' }}</td>
                            <td class="px-5 py-3 text-gray-500">{{ $vital->created_at->format('M j, H:i') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-5 py-8 text-center text-gray-400">No vitals recorded yet</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
