@extends('layouts.dashboard')

@section('title', 'My Schedule - ' . config('app.name', 'Laravel'))
@section('page_title', 'My Schedule')

@section('content')
<div class="space-y-5">

    {{-- Appointments List --}}
    <div class="bg-slate-800 rounded-md p-6 text-white">
        <p class="text-[10px] uppercase tracking-wider text-slate-400 mb-1">Doctor</p>
        <h1 class="text-xl font-bold">My Schedule</h1>
        <p class="text-xs text-slate-400 mt-1">Your upcoming appointments.</p>
    </div>

    {{-- Appointments List --}}
    <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
        <div class="bg-white rounded-md border border-gray-200 p-4">
            <p class="text-[10px] font-medium text-gray-500 uppercase tracking-wide">Today</p>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ $todayCount }}</p>
            <p class="text-[10px] text-gray-400 mt-1">appointments</p>
        </div>
        <div class="bg-white rounded-md border border-gray-200 p-4">
            <p class="text-[10px] font-medium text-gray-500 uppercase tracking-wide">This Week</p>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ $weekCount }}</p>
            <p class="text-[10px] text-gray-400 mt-1">appointments</p>
        </div>
        <div class="bg-white rounded-md border border-gray-200 p-4">
            <p class="text-[10px] font-medium text-gray-500 uppercase tracking-wide">Upcoming</p>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ $upcoming->total() }}</p>
            <p class="text-[10px] text-gray-400 mt-1">total scheduled</p>
        </div>
    </div>

    {{-- Appointments List --}}
    <div class="bg-white rounded-md border border-gray-200 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100">
            <h3 class="text-sm font-bold text-gray-900">Upcoming Appointments</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 text-[10px] uppercase text-gray-400 border-b border-gray-100">
                        <th class="px-5 py-3 font-medium text-left">Date & Time</th>
                        <th class="px-5 py-3 font-medium text-left">Patient</th>
                        <th class="px-5 py-3 font-medium text-left">Type</th>
                        <th class="px-5 py-3 font-medium text-left">Status</th>
                        <th class="px-5 py-3 font-medium text-left">Notes</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($upcoming as $appt)
                        <tr class="hover:bg-gray-50/50">
                            <td class="px-5 py-3">
                                <div class="text-xs font-medium text-gray-900">{{ \Carbon\Carbon::parse($appt->scheduled_at)->format('M j, Y') }}</div>
                                <div class="text-[10px] text-gray-500">{{ \Carbon\Carbon::parse($appt->scheduled_at)->format('g:i A') }}</div>
                            </td>
                            <td class="px-5 py-3 text-xs text-gray-600">{{ $appt->patient?->fullName() ?? 'Unknown' }}</td>
                            <td class="px-5 py-3">
                                <span class="inline-flex items-center gap-1.5 text-[10px] font-medium text-gray-600">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $appt->type === 'emergency' ? 'bg-red-500' : ($appt->type === 'followup' ? 'bg-amber-500' : 'bg-slate-400') }}"></span>
                                    {{ ucfirst($appt->type) }}
                                </span>
                            </td>
                            <td class="px-5 py-3">
                                <span class="inline-flex items-center gap-1.5 text-[10px] font-medium text-gray-600">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $appt->status === 'completed' ? 'bg-emerald-500' : ($appt->status === 'cancelled' ? 'bg-red-500' : 'bg-amber-500') }}"></span>
                                    {{ ucfirst($appt->status) }}
                                </span>
                            </td>
                            <td class="px-5 py-3 text-xs text-gray-500 max-w-xs truncate">{{ $appt->notes ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-5 py-8 text-center text-gray-400 text-xs">No upcoming appointments</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($upcoming->hasPages())
        <div class="px-5 py-3 border-t border-gray-100">
            {{ $upcoming->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
