@extends('layouts.dashboard')

@section('title', 'Edit Clinical Record - ' . config('app.name', 'Laravel'))
@section('page_title', 'Edit Clinical Record')

@section('content')
<div class="max-w-4xl mx-auto bg-white rounded-xl border border-gray-100 p-6 shadow-sm">
    <form method="POST" action="{{ route('clinical-records.update', $clinicalRecord) }}" class="grid grid-cols-1 md:grid-cols-2 gap-4">
        @csrf
        @method('PUT')
        <div class="md:col-span-2">
            <label class="block text-xs font-medium text-gray-700 mb-1">Patient</label>
            <select name="patient_id" class="w-full border rounded-lg px-3 py-2 text-sm" required>
                @foreach($patients as $patient)
                    <option value="{{ $patient->id }}" {{ $clinicalRecord->patient_id == $patient->id ? 'selected' : '' }}>{{ $patient->fullName() }} ({{ $patient->mrn }})</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-700 mb-1">Visit</label>
            <select name="visit_id" class="w-full border rounded-lg px-3 py-2 text-sm">
                <option value="">Select visit...</option>
                @foreach($visits as $visit)
                    <option value="{{ $visit->id }}" {{ $clinicalRecord->visit_id == $visit->id ? 'selected' : '' }}>{{ $visit->visit_number }} - {{ $visit->patient?->fullName() }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-700 mb-1">Appointment</label>
            <select name="appointment_id" class="w-full border rounded-lg px-3 py-2 text-sm">
                <option value="">Select appointment...</option>
                @foreach($appointments as $appointment)
                    <option value="{{ $appointment->id }}" {{ $clinicalRecord->appointment_id == $appointment->id ? 'selected' : '' }}>{{ $appointment->patient?->fullName() }} - {{ $appointment->scheduled_at?->format('M d, Y') }}</option>
                @endforeach
            </select>
        </div>
        <div class="md:col-span-2">
            <label class="block text-xs font-medium text-gray-700 mb-1">Doctor</label>
            <select name="doctor_id" class="w-full border rounded-lg px-3 py-2 text-sm" required>
                @foreach($doctors as $doctor)
                    <option value="{{ $doctor->id }}" {{ $clinicalRecord->doctor_id == $doctor->id ? 'selected' : '' }}>{{ $doctor->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="md:col-span-2">
            <label class="block text-xs font-medium text-gray-700 mb-1">Chief Complaint</label>
            <textarea name="chief_complaint" class="w-full border rounded-lg px-3 py-2 text-sm" rows="2">{{ $clinicalRecord->chief_complaint }}</textarea>
        </div>
        <div class="md:col-span-2">
            <label class="block text-xs font-medium text-gray-700 mb-1">Symptoms</label>
            <textarea name="symptoms" class="w-full border rounded-lg px-3 py-2 text-sm" rows="2">{{ $clinicalRecord->symptoms }}</textarea>
        </div>
        <div class="md:col-span-2">
            <label class="block text-xs font-medium text-gray-700 mb-1">Diagnosis</label>
            <textarea name="diagnosis" class="w-full border rounded-lg px-3 py-2 text-sm" rows="2">{{ $clinicalRecord->diagnosis }}</textarea>
        </div>
        <div class="md:col-span-2">
            <label class="block text-xs font-medium text-gray-700 mb-1">Treatment Plan</label>
            <textarea name="treatment_plan" class="w-full border rounded-lg px-3 py-2 text-sm" rows="3">{{ $clinicalRecord->treatment_plan }}</textarea>
        </div>
        <div class="md:col-span-2">
            <label class="block text-xs font-medium text-gray-700 mb-1">Prescription</label>
            <textarea name="prescription" class="w-full border rounded-lg px-3 py-2 text-sm" rows="3">{{ $clinicalRecord->prescription }}</textarea>
        </div>
        <div class="md:col-span-2">
            <label class="block text-xs font-medium text-gray-700 mb-1">Notes</label>
            <textarea name="notes" class="w-full border rounded-lg px-3 py-2 text-sm" rows="3">{{ $clinicalRecord->notes }}</textarea>
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-700 mb-1">Record Date</label>
            <input type="date" name="record_date" value="{{ $clinicalRecord->record_date->format('Y-m-d') }}" class="w-full border rounded-lg px-3 py-2 text-sm" required>
        </div>
        <div class="md:col-span-2 pt-4">
            <button type="submit" class="bg-emerald-600 text-white text-sm font-medium px-5 py-2 rounded-lg hover:bg-emerald-700">Update Clinical Record</button>
        </div>
    </form>
</div>
@endsection
