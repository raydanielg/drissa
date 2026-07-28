@extends('layouts.dashboard')

@section('title', 'Nurse Dashboard - ' . config('app.name', 'Laravel'))
@section('page_title', 'Nurse Dashboard')

@section('content')
<div class="space-y-6">

    {{-- Stats Grid --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-medium text-gray-500 uppercase tracking-wider">Today's Visits</span>
                <div class="w-8 h-8 rounded-lg bg-emerald-100 flex items-center justify-center">
                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </div>
            </div>
            <p class="text-2xl font-bold text-gray-900">{{ $todayVisits }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-medium text-gray-500 uppercase tracking-wider">Waiting Vitals</span>
                <div class="w-8 h-8 rounded-lg bg-amber-100 flex items-center justify-center">
                    <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
            <p class="text-2xl font-bold text-amber-600">{{ $waitingVitals->count() }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-medium text-gray-500 uppercase tracking-wider">Vitals Today</span>
                <div class="w-8 h-8 rounded-lg bg-sky-100 flex items-center justify-center">
                    <svg class="w-4 h-4 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                </div>
            </div>
            <p class="text-2xl font-bold text-sky-600">{{ $vitalsRecordedToday }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-medium text-gray-500 uppercase tracking-wider">Total Patients</span>
                <div class="w-8 h-8 rounded-lg bg-purple-100 flex items-center justify-center">
                    <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </div>
            </div>
            <p class="text-2xl font-bold text-purple-600">{{ $totalPatients }}</p>
        </div>
    </div>

    {{-- Waiting Vitals --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
            <div>
                <h3 class="text-sm font-semibold text-gray-900">Patients Waiting for Vitals</h3>
                <p class="text-xs text-gray-400">Take vitals before sending to doctor</p>
            </div>
            <a href="{{ route('nurse.queue') }}" class="text-xs font-medium text-emerald-600 hover:text-emerald-700">View All</a>
        </div>
        <div class="p-5">
            @forelse($waitingVitals as $visit)
                <div class="flex items-center gap-3 py-3 {{ !$loop->last ? 'border-b border-gray-50' : '' }}">
                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-emerald-500 to-emerald-700 flex items-center justify-center text-white text-sm font-bold shadow-sm">
                        {{ strtoupper(substr($visit->patient->fullName(), 0, 1)) }}
                    </div>
                    <div class="flex-1">
                        <div class="text-sm font-medium text-gray-900">{{ $visit->patient->fullName() }}</div>
                        <div class="text-xs text-gray-500">{{ $visit->visit_number }} • {{ $visit->registered_at->format('H:i') }}</div>
                    </div>
                    <a href="{{ route('nurse.queue') }}" class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-medium rounded-lg transition-all">Take Vitals</a>
                </div>
            @empty
                <div class="text-center py-8 text-gray-400 text-sm">
                    <svg class="w-12 h-12 mx-auto mb-2 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    No patients waiting
                </div>
            @endforelse
        </div>
    </div>

    {{-- Recent Vitals --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100">
            <h3 class="text-sm font-semibold text-gray-900">Recently Recorded Vitals</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-xs text-gray-500 border-b border-gray-100">
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
