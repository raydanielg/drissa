@extends('layouts.dashboard')

@section('title', 'My Schedule - ' . config('app.name', 'Laravel'))
@section('page_title', 'My Schedule')

@section('content')
<div class="space-y-6">

    {{-- Stats --}}
    <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <p class="text-xs text-gray-500 uppercase tracking-wider mb-1">Today</p>
            <p class="text-2xl font-bold text-gray-900">{{ $todayCount }}</p>
            <p class="text-xs text-gray-400 mt-1">appointments</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <p class="text-xs text-gray-500 uppercase tracking-wider mb-1">This Week</p>
            <p class="text-2xl font-bold text-gray-900">{{ $weekCount }}</p>
            <p class="text-xs text-gray-400 mt-1">appointments</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <p class="text-xs text-gray-500 uppercase tracking-wider mb-1">Upcoming</p>
            <p class="text-2xl font-bold text-gray-900">{{ $upcoming->total() }}</p>
            <p class="text-xs text-gray-400 mt-1">total scheduled</p>
        </div>
    </div>

    {{-- Appointments List --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100">
            <h3 class="text-sm font-semibold text-gray-900">Upcoming Appointments</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-xs text-gray-500 border-b border-gray-100">
                        <th class="px-5 py-3 font-medium">Date & Time</th>
                        <th class="px-5 py-3 font-medium">Patient</th>
                        <th class="px-5 py-3 font-medium">Type</th>
                        <th class="px-5 py-3 font-medium">Status</th>
                        <th class="px-5 py-3 font-medium">Notes</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($upcoming as $appt)
                        <tr class="border-b border-gray-50 hover:bg-gray-50/50">
                            <td class="px-5 py-3">
                                <div class="font-medium text-gray-900">{{ \Carbon\Carbon::parse($appt->scheduled_at)->format('M j, Y') }}</div>
                                <div class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($appt->scheduled_at)->format('g:i A') }}</div>
                            </td>
                            <td class="px-5 py-3">{{ $appt->patient?->fullName() ?? 'Unknown' }}</td>
                            <td class="px-5 py-3">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-medium {{ $appt->type === 'emergency' ? 'bg-red-100 text-red-700' : ($appt->type === 'followup' ? 'bg-sky-100 text-sky-700' : 'bg-gray-100 text-gray-700') }}">
                                    {{ ucfirst($appt->type) }}
                                </span>
                            </td>
                            <td class="px-5 py-3">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-medium {{ $appt->status === 'completed' ? 'bg-green-100 text-green-700' : ($appt->status === 'cancelled' ? 'bg-red-100 text-red-700' : ($appt->status === 'confirmed' ? 'bg-sky-100 text-sky-700' : 'bg-amber-100 text-amber-700')) }}">
                                    {{ ucfirst($appt->status) }}
                                </span>
                            </td>
                            <td class="px-5 py-3 text-gray-500 max-w-xs truncate">{{ $appt->notes ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-5 py-8 text-center text-gray-400">No upcoming appointments</td></tr>
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
