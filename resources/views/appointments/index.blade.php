@extends('layouts.dashboard')

@section('title', 'Appointments - ' . config('app.name', 'Laravel'))
@section('page_title', 'Master Appointments')

@section('content')
<div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
        <h2 class="text-sm font-semibold text-gray-900">Upcoming Appointments</h2>
        <a href="{{ route('appointments.create') }}" class="bg-emerald-600 text-white text-xs font-medium px-3 py-1.5 rounded-lg hover:bg-emerald-700">+ New Appointment</a>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left">
            <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                <tr>
                    <th class="px-6 py-3">Patient</th>
                    <th class="px-6 py-3">Doctor</th>
                    <th class="px-6 py-3">Date & Time</th>
                    <th class="px-6 py-3">Type</th>
                    <th class="px-6 py-3">Status</th>
                    <th class="px-6 py-3">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($appointments as $appointment)
                    <tr class="border-t border-gray-100 hover:bg-gray-50/50">
                        <td class="px-6 py-3 font-medium">{{ $appointment->patient->fullName() }}</td>
                        <td class="px-6 py-3">{{ $appointment->doctor?->name ?? 'Not assigned' }}</td>
                        <td class="px-6 py-3">{{ $appointment->scheduled_at->format('d M Y H:i') }}</td>
                        <td class="px-6 py-3 capitalize">{{ $appointment->type }}</td>
                        <td class="px-6 py-3">
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-medium capitalize
                                @if($appointment->status === 'completed') bg-emerald-100 text-emerald-700
                                @elseif($appointment->status === 'cancelled') bg-red-100 text-red-700
                                @elseif($appointment->status === 'confirmed') bg-sky-100 text-sky-700
                                @else bg-gold-100 text-gold-700 @endif">
                                {{ $appointment->status }}
                            </span>
                        </td>
                        <td class="px-6 py-3 flex items-center gap-2">
                            <a href="{{ route('appointments.edit', $appointment) }}" class="text-emerald-600 hover:text-emerald-700 text-xs font-medium">Edit</a>
                            <form method="POST" action="{{ route('appointments.destroy', $appointment) }}" data-ajax data-confirm="Cancel this appointment?" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-700 text-xs font-medium">Cancel</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-6 py-6 text-center text-gray-400">No upcoming appointments</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-6 py-4 border-t border-gray-100">
        {{ $appointments->links() }}
    </div>
</div>
@endsection
