@extends('layouts.dashboard')

@section('title', 'New Appointment - ' . config('app.name', 'Laravel'))
@section('page_title', 'Book Appointment')

@section('content')
<div class="max-w-xl mx-auto bg-white rounded-xl border border-gray-100 p-6 shadow-sm">
    <form method="POST" action="{{ route('appointments.store') }}" class="space-y-4">
        @csrf
        <div>
            <label class="block text-xs font-medium text-gray-700 mb-1">Patient</label>
            <select name="patient_id" class="w-full border rounded-lg px-3 py-2 text-sm" required>
                <option value="">Select patient</option>
                @foreach ($patients as $patient)
                    <option value="{{ $patient->id }}">{{ $patient->fullName() }} ({{ $patient->mrn }})</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-700 mb-1">Doctor</label>
            <select name="doctor_id" class="w-full border rounded-lg px-3 py-2 text-sm">
                <option value="">Select doctor</option>
                @foreach ($doctors as $doctor)
                    <option value="{{ $doctor->id }}">{{ $doctor->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-700 mb-1">Date & Time</label>
            <input type="datetime-local" name="scheduled_at" class="w-full border rounded-lg px-3 py-2 text-sm" required>
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-700 mb-1">Type</label>
            <select name="type" class="w-full border rounded-lg px-3 py-2 text-sm" required>
                <option value="general">General</option>
                <option value="followup">Follow-up</option>
                <option value="emergency">Emergency</option>
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-700 mb-1">Notes</label>
            <textarea name="notes" class="w-full border rounded-lg px-3 py-2 text-sm" rows="3"></textarea>
        </div>
        <div class="pt-4">
            <button type="submit" class="bg-emerald-600 text-white text-sm font-medium px-5 py-2 rounded-lg hover:bg-emerald-700">Book Appointment</button>
        </div>
    </form>
</div>
@endsection
