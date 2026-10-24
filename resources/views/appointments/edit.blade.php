@extends('layouts.dashboard')

@section('title', 'Edit Appointment - ' . config('app.name', 'Laravel'))
@section('page_title', 'Edit Appointment')

@section('content')
<div class="max-w-2xl mx-auto space-y-5">
    {{-- Actions --}}
    <div class="bg-slate-800 rounded-md p-6 text-white">
        <p class="text-[10px] uppercase tracking-wider text-slate-400 mb-1">Appointments</p>
        <h1 class="text-xl font-bold">Edit Appointment</h1>
        <p class="text-xs text-slate-400 mt-1">{{ $appointment->patient->fullName() }} — {{ $appointment->scheduled_at->format('M d, Y H:i') }}</p>
    </div>

    @if (session('status'))
        <div class="p-3 rounded-md bg-white border-l-2 border-emerald-500 text-emerald-700 text-xs">{{ session('status') }}</div>
    @endif

    {{-- Actions --}}
    <div class="bg-white rounded-md border border-gray-200 overflow-hidden">
        <form method="POST" action="{{ route('appointments.update', $appointment) }}" class="space-y-0">
            @csrf
            @method('PUT')

            {{-- Actions --}}
            <div class="px-6 py-5 space-y-4 border-b border-gray-100">
                <p class="text-xs font-bold text-gray-500 uppercase tracking-wide flex items-center gap-2">
                    <i class="hgi-stroke hgi-user-multiple-02 text-base text-gray-400"></i>
                    Patient & Doctor
                </p>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1.5">Patient <span class="text-red-500">*</span></label>
                        <x-ui.select name="patient_id" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-slate-300" required>
                            @foreach ($patients as $patient)
                                <option value="{{ $patient->id }}" {{ $appointment->patient_id == $patient->id ? 'selected' : '' }}>{{ $patient->fullName() }} ({{ $patient->mrn }})</option>
                            @endforeach
                        </x-ui.select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1.5">Doctor</label>
                        <x-ui.select name="doctor_id" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-slate-300">
                            <option value="">Any available doctor...</option>
                            @foreach ($doctors as $doctor)
                                <option value="{{ $doctor->id }}" {{ $appointment->doctor_id == $doctor->id ? 'selected' : '' }}>{{ $doctor->name }}</option>
                            @endforeach
                        </x-ui.select>
                    </div>
                </div>
            </div>

            {{-- Actions --}}
            <div class="px-6 py-5 space-y-4 border-b border-gray-100">
                <p class="text-xs font-bold text-gray-500 uppercase tracking-wide flex items-center gap-2">
                    <i class="hgi-stroke hgi-clock-01 text-base text-gray-400"></i>
                    Schedule
                </p>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1.5">Appointment Date <span class="text-red-500">*</span></label>
                        <x-ui.input type="text" data-fp-date name="appointment_date" value="{{ $appointment->scheduled_at->format('Y-m-d') }}" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-slate-300" required />
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1.5">Start Time <span class="text-red-500">*</span></label>
                        <x-ui.input type="text" data-fp-time name="start_time" value="{{ $appointment->scheduled_at->format('H:i') }}" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-slate-300" required />
                    </div>
                </div>
            </div>

            {{-- Actions --}}
            <div class="px-6 py-5 space-y-4 border-b border-gray-100">
                <p class="text-xs font-bold text-gray-500 uppercase tracking-wide flex items-center gap-2">
                    <i class="hgi-stroke hgi-file-01 text-base text-gray-400"></i>
                    Details
                </p>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1.5">Type <span class="text-red-500">*</span></label>
                        <x-ui.select name="type" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-slate-300" required>
                            <option value="general" {{ $appointment->type === 'general' ? 'selected' : '' }}>General Consultation</option>
                            <option value="followup" {{ $appointment->type === 'followup' ? 'selected' : '' }}>Follow-up</option>
                            <option value="emergency" {{ $appointment->type === 'emergency' ? 'selected' : '' }}>Emergency</option>
                        </x-ui.select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1.5">Status <span class="text-red-500">*</span></label>
                        <x-ui.select name="status" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-slate-300" required>
                            @foreach (['scheduled', 'confirmed', 'completed', 'cancelled', 'no_show'] as $status)
                                <option value="{{ $status }}" {{ $appointment->status === $status ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $status)) }}</option>
                            @endforeach
                        </x-ui.select>
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1.5">Notes</label>
                    <x-ui.textarea name="notes" rows="3" placeholder="Additional notes..." class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-slate-300">{{ $appointment->notes }}</x-ui.textarea>
                </div>
            </div>

            {{-- Actions --}}
            <div class="px-6 py-4 flex items-center justify-between bg-gray-50">
                <a href="{{ route('appointments.index') }}" class="px-4 py-2 text-sm text-gray-700 border border-gray-200 hover:bg-gray-100 rounded transition-colors">Cancel</a>
                <button type="submit" class="inline-flex items-center gap-2 px-5 py-2 bg-slate-800 hover:bg-slate-700 text-white text-sm font-medium rounded transition-colors">
                    <i class="hgi-stroke hgi-tick-02 text-base"></i>
                    Update Appointment
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
