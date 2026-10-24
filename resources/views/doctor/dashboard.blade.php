@extends('layouts.dashboard')

@section('title', 'Doctor Dashboard - ' . config('app.name', 'Laravel'))
@section('page_title', 'Doctor Dashboard')

@section('content')
<div class="space-y-6">

    {{-- Recent Visits --}}
    <div class="bg-slate-800 rounded-md p-6 text-white">
        <div class="flex flex-wrap items-end justify-between gap-3">
            <div>
                <p class="text-[10px] uppercase tracking-wider text-slate-400 mb-1">Doctor</p>
                <h2 class="text-xl font-bold">Welcome, {{ auth()->user()->name }}</h2>
                <p class="text-xs text-slate-400 mt-1">{{ today()->format('l, F j, Y') }}</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('doctor.queue') }}" class="px-3 py-1.5 bg-white text-slate-800 hover:bg-gray-100 rounded text-xs font-semibold transition-colors flex items-center gap-1.5">
                    <i class="hgi-stroke hgi-menu-01 text-sm"></i>
                    Go to Queue
                </a>
            </div>
        </div>
    </div>

    {{-- Recent Visits --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
        <div class="bg-white rounded-md border border-gray-200 p-4">
            <div class="flex items-start justify-between">
                <p class="text-[10px] font-medium text-gray-500 uppercase tracking-wide">Today's Visits</p>
                <i class="hgi-stroke hgi-user-multiple-02 text-base text-gray-400"></i>
            </div>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['today_visits'] }}</p>
            <p class="text-[10px] text-gray-400 mt-0.5">Registered today</p>
        </div>
        <div class="bg-white rounded-md border border-amber-200 p-4">
            <div class="flex items-start justify-between">
                <p class="text-[10px] font-medium text-gray-500 uppercase tracking-wide">Waiting</p>
                <i class="hgi-stroke hgi-clock-01 text-base text-amber-500"></i>
            </div>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['waiting'] }}</p>
            <p class="text-[10px] text-amber-600 mt-0.5">In queue</p>
        </div>
        <div class="bg-white rounded-md border border-gray-200 p-4">
            <div class="flex items-start justify-between">
                <p class="text-[10px] font-medium text-gray-500 uppercase tracking-wide">With Me</p>
                <i class="hgi-stroke hgi-user text-base text-gray-400"></i>
            </div>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['with_me'] }}</p>
            <p class="text-[10px] text-gray-400 mt-0.5">In consultation</p>
        </div>
        <div class="bg-white rounded-md border border-emerald-200 p-4">
            <div class="flex items-start justify-between">
                <p class="text-[10px] font-medium text-gray-500 uppercase tracking-wide">Completed Today</p>
                <i class="hgi-stroke hgi-checkmark-circle-02 text-base text-emerald-500"></i>
            </div>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['completed_today'] }}</p>
            <p class="text-[10px] text-emerald-600 mt-0.5">Done</p>
        </div>
    </div>

    {{-- Recent Visits --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
        <div class="bg-white rounded-md border border-gray-200 p-4 flex items-center gap-3">
            <div class="w-9 h-9 rounded-md bg-gray-100 text-slate-600 flex items-center justify-center">
                <i class="hgi-stroke hgi-test-tube-01 text-base"></i>
            </div>
            <div>
                <p class="text-[10px] text-gray-500">Lab Pending</p>
                <p class="text-lg font-bold text-gray-900">{{ $stats['lab_pending'] }}</p>
            </div>
        </div>
        <div class="bg-white rounded-md border border-gray-200 p-4 flex items-center gap-3">
            <div class="w-9 h-9 rounded-md bg-gray-100 text-slate-600 flex items-center justify-center">
                <i class="hgi-stroke hgi-checkmark-circle-02 text-base"></i>
            </div>
            <div>
                <p class="text-[10px] text-gray-500">Lab Ready</p>
                <p class="text-lg font-bold text-gray-900">{{ $stats['lab_ready'] }}</p>
            </div>
        </div>
        <div class="bg-white rounded-md border border-gray-200 p-4 flex items-center gap-3">
            <div class="w-9 h-9 rounded-md bg-gray-100 text-slate-600 flex items-center justify-center">
                <i class="hgi-stroke hgi-test-tube-01 text-base"></i>
            </div>
            <div>
                <p class="text-[10px] text-gray-500">Prescriptions Today</p>
                <p class="text-lg font-bold text-gray-900">{{ $stats['prescriptions_today'] }}</p>
            </div>
        </div>
        <div class="bg-white rounded-md border border-gray-200 p-4 flex items-center gap-3">
            <div class="w-9 h-9 rounded-md bg-gray-100 text-slate-600 flex items-center justify-center">
                <i class="hgi-stroke hgi-user-multiple-02 text-base"></i>
            </div>
            <div>
                <p class="text-[10px] text-gray-500">Total Patients</p>
                <p class="text-lg font-bold text-gray-900">{{ $stats['total_patients'] }}</p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Recent Visits --}}
        <div class="bg-white rounded-md border border-gray-200 overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-bold text-gray-900">Waiting Queue</h3>
                    <p class="text-xs text-gray-400">Patients waiting to see you</p>
                </div>
                <a href="{{ route('doctor.queue') }}" class="text-xs font-medium text-slate-600 hover:text-slate-800">View All</a>
            </div>
            <div class="p-5">
                @forelse($waitingQueue as $visit)
                    <div class="flex items-center gap-3 py-3 {{ !$loop->last ? 'border-b border-gray-50' : '' }}">
                        <div class="w-10 h-10 rounded-full bg-slate-100 text-slate-600 flex items-center justify-center text-sm font-bold">
                            {{ strtoupper(substr($visit->patient->fullName(), 0, 1)) }}
                        </div>
                        <div class="flex-1">
                            <div class="text-sm font-medium text-gray-900">{{ $visit->patient->fullName() }}</div>
                            <div class="text-xs text-gray-500">{{ $visit->visit_number }} • {{ $visit->registered_at->format('H:i') }}</div>
                        </div>
                        <span class="inline-flex items-center gap-1.5 text-[10px] font-medium capitalize text-gray-600">
                            <span class="w-1.5 h-1.5 rounded-full {{ $visit->status === 'with_doctor' ? 'bg-slate-700' : 'bg-amber-500' }}"></span>
                            {{ ucfirst(str_replace('_', ' ', $visit->status)) }}
                        </span>
                    </div>
                @empty
                    <div class="text-center py-8 text-gray-400 text-sm">
                        <i class="hgi-stroke hgi-checkmark-circle-02 text-6xl mx-auto mb-2 text-gray-300"></i>
                        No patients waiting
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Recent Visits --}}
        <div class="bg-white rounded-md border border-gray-200 overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-bold text-gray-900">Today's Appointments</h3>
                    <p class="text-xs text-gray-400">{{ today()->format('D, M j') }}</p>
                </div>
                <a href="{{ route('doctor.schedule') }}" class="text-xs font-medium text-slate-600 hover:text-slate-800">View All</a>
            </div>
            <div class="p-5">
                @forelse($todayAppointments as $appt)
                    <div class="flex items-center gap-3 py-3 {{ !$loop->last ? 'border-b border-gray-50' : '' }}">
                        <div class="w-12 h-12 rounded-md bg-slate-100 text-slate-700 flex flex-col items-center justify-center">
                            <span class="text-[10px] font-bold">{{ date('H:i', strtotime($appt->scheduled_at)) }}</span>
                        </div>
                        <div class="flex-1">
                            <div class="text-sm font-medium text-gray-900">{{ $appt->patient?->fullName() ?? 'Unknown' }}</div>
                            <div class="text-xs text-gray-500">{{ ucfirst($appt->type) }} • {{ ucfirst($appt->status) }}</div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8 text-gray-400 text-sm">
                        <i class="hgi-stroke hgi-calendar-03 text-6xl mx-auto mb-2 text-gray-300"></i>
                        No appointments today
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Recent Visits --}}
    <div class="bg-white rounded-md border border-gray-200 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100">
            <h3 class="text-sm font-bold text-gray-900">Recent Visits</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-[10px] uppercase text-gray-400 border-b border-gray-100">
                        <th class="px-5 py-3 font-medium">Visit #</th>
                        <th class="px-5 py-3 font-medium">Patient</th>
                        <th class="px-5 py-3 font-medium">Status</th>
                        <th class="px-5 py-3 font-medium">Time</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentVisits as $visit)
                        <tr class="border-b border-gray-50 hover:bg-gray-50/50">
                            <td class="px-5 py-3 font-medium text-gray-900">{{ $visit->visit_number }}</td>
                            <td class="px-5 py-3">{{ $visit->patient?->fullName() ?? 'Unknown' }}</td>
                            <td class="px-5 py-3">
                                <span class="inline-flex items-center gap-1.5 text-[10px] font-medium capitalize text-gray-600">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $visit->status === 'completed' ? 'bg-emerald-500' : ($visit->status === 'cancelled' ? 'bg-red-500' : 'bg-amber-500') }}"></span>
                                    {{ ucfirst(str_replace('_', ' ', $visit->status)) }}
                                </span>
                            </td>
                            <td class="px-5 py-3 text-gray-500">{{ $visit->registered_at->format('M j, H:i') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="px-5 py-8 text-center text-gray-400">No recent visits</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
