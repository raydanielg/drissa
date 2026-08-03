@extends('layouts.dashboard')

@section('title', 'Edit Appointment - ' . config('app.name', 'Laravel'))
@section('page_title', 'Edit Appointment')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    @if (session('status'))
        <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm animate-fade">{{ session('status') }}</div>
    @endif

    {{-- Header --}}
    <div class="flex items-center gap-4">
        <div class="w-12 h-12 rounded-xl bg-sky-100 flex items-center justify-center">
            <svg class="w-6 h-6 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
        </div>
        <div>
            <h2 class="text-lg font-bold text-gray-900">Edit Appointment</h2>
            <p class="text-sm text-gray-500">{{ $appointment->patient->fullName() }} • {{ $appointment->scheduled_at->format('M d, Y H:i') }}</p>
        </div>
    </div>

    {{-- Form --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        <form method="POST" action="{{ route('appointments.update', $appointment) }}" class="space-y-0">
            @csrf
            @method('PUT')

            {{-- Patient & Doctor Section --}}
            <div class="px-6 py-5 space-y-4 border-b border-gray-100">
                <p class="text-xs font-bold text-gray-500 uppercase tracking-wide flex items-center gap-2">
                    <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    Patient & Doctor
                </p>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1.5">Patient <span class="text-red-500">*</span></label>
                        <select name="patient_id" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" required>
                            @foreach ($patients as $patient)
                                <option value="{{ $patient->id }}" {{ $appointment->patient_id == $patient->id ? 'selected' : '' }}>{{ $patient->fullName() }} ({{ $patient->mrn }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1.5">Doctor</label>
                        <select name="doctor_id" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                            <option value="">Any available doctor...</option>
                            @foreach ($doctors as $doctor)
                                <option value="{{ $doctor->id }}" {{ $appointment->doctor_id == $doctor->id ? 'selected' : '' }}>{{ $doctor->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            {{-- Date & Time Section --}}
            <div class="px-6 py-5 space-y-4 border-b border-gray-100">
                <p class="text-xs font-bold text-gray-500 uppercase tracking-wide flex items-center gap-2">
                    <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Schedule
                </p>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1.5">Appointment Date <span class="text-red-500">*</span></label>
                        <input type="date" name="appointment_date" value="{{ $appointment->scheduled_at->format('Y-m-d') }}" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" required>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1.5">Start Time <span class="text-red-500">*</span></label>
                        <input type="time" name="start_time" value="{{ $appointment->scheduled_at->format('H:i') }}" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" required>
                    </div>
                </div>
            </div>

            {{-- Type & Status Section --}}
            <div class="px-6 py-5 space-y-4 border-b border-gray-100">
                <p class="text-xs font-bold text-gray-500 uppercase tracking-wide flex items-center gap-2">
                    <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6 4h6"/></svg>
                    Details
                </p>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1.5">Type <span class="text-red-500">*</span></label>
                        <select name="type" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" required>
                            <option value="general" {{ $appointment->type === 'general' ? 'selected' : '' }}>🩺 General Consultation</option>
                            <option value="followup" {{ $appointment->type === 'followup' ? 'selected' : '' }}>🔄 Follow-up</option>
                            <option value="emergency" {{ $appointment->type === 'emergency' ? 'selected' : '' }}>🚨 Emergency</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1.5">Status <span class="text-red-500">*</span></label>
                        <select name="status" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" required>
                            @foreach (['scheduled', 'confirmed', 'completed', 'cancelled', 'no_show'] as $status)
                                <option value="{{ $status }}" {{ $appointment->status === $status ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $status)) }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1.5">Notes</label>
                    <textarea name="notes" rows="3" placeholder="Additional notes..." class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">{{ $appointment->notes }}</textarea>
                </div>
            </div>

            {{-- Actions --}}
            <div class="px-6 py-4 flex items-center justify-between bg-gray-50">
                <a href="{{ route('appointments.index') }}" class="text-sm text-gray-500 hover:text-gray-700 font-medium transition-colors">Cancel</a>
                <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-lg transition-colors shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Update Appointment
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
