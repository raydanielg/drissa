@extends('layouts.dashboard')

@section('title', 'Edit Appointment - ' . config('app.name', 'Laravel'))
@section('page_title', 'Edit Appointment')

@section('content')
<div class="max-w-xl mx-auto bg-white rounded-xl border border-gray-100 p-6 shadow-sm">
    <form method="POST" action="{{ route('appointments.update', $appointment) }}" class="space-y-4">
        @csrf
        @method('PUT')
        <div>
            <label class="block text-xs font-medium text-gray-700 mb-1">Patient</label>
            <select name="patient_id" class="w-full border rounded-lg px-3 py-2 text-sm" required>
                @foreach ($patients as $patient)
                    <option value="{{ $patient->id }}" {{ $appointment->patient_id == $patient->id ? 'selected' : '' }}>{{ $patient->fullName() }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-700 mb-1">Doctor</label>
            <select name="doctor_id" class="w-full border rounded-lg px-3 py-2 text-sm">
                <option value="">Select doctor</option>
                @foreach ($doctors as $doctor)
                    <option value="{{ $doctor->id }}" {{ $appointment->doctor_id == $doctor->id ? 'selected' : '' }}>{{ $doctor->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-700 mb-1">Date & Time</label>
            <input type="datetime-local" name="scheduled_at" value="{{ $appointment->scheduled_at->format('Y-m-d\TH:i') }}" class="w-full border rounded-lg px-3 py-2 text-sm" required>
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-700 mb-1">Status</label>
            <select name="status" class="w-full border rounded-lg px-3 py-2 text-sm" required>
                @foreach (['scheduled', 'confirmed', 'completed', 'cancelled', 'no_show'] as $status)
                    <option value="{{ $status }}" {{ $appointment->status === $status ? 'selected' : '' }}>{{ ucfirst($status) }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-700 mb-1">Type</label>
            <select name="type" class="w-full border rounded-lg px-3 py-2 text-sm" required>
                @foreach (['general', 'followup', 'emergency'] as $type)
                    <option value="{{ $type }}" {{ $appointment->type === $type ? 'selected' : '' }}>{{ ucfirst($type) }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-700 mb-1">Notes</label>
            <textarea name="notes" class="w-full border rounded-lg px-3 py-2 text-sm" rows="3">{{ $appointment->notes }}</textarea>
        </div>
        <div class="pt-4">
            <button type="submit" class="bg-emerald-600 text-white text-sm font-medium px-5 py-2 rounded-lg hover:bg-emerald-700">Update Appointment</button>
        </div>
    </form>
</div>
@endsection
