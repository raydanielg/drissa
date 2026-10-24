@extends('layouts.dashboard')

@section('title', 'Edit Clinical Record - ' . config('app.name', 'Laravel'))
@section('page_title', 'Edit Clinical Record')

@section('content')
<div class="max-w-4xl mx-auto space-y-5">
    <div class="bg-slate-800 rounded-md p-6 text-white">
        <p class="text-[10px] uppercase tracking-wider text-slate-400 mb-1">Clinical</p>
        <h1 class="text-xl font-bold">Edit Clinical Record</h1>
        <p class="text-xs text-slate-400 mt-1">Update record for {{ $clinicalRecord->patient?->fullName() }}.</p>
    </div>

    <div class="bg-white rounded-md border border-gray-200 p-5">
    <form method="POST" action="{{ route('clinical-records.update', $clinicalRecord) }}" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-2 gap-4">
        @csrf
        @method('PUT')
        <div class="md:col-span-2">
            <label class="block text-xs font-medium text-gray-700 mb-1">Patient</label>
            <x-ui.select name="patient_id" class="w-full border border-gray-200 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-slate-300 outline-none bg-white" required>
                @foreach($patients as $patient)
                    <option value="{{ $patient->id }}" {{ $clinicalRecord->patient_id == $patient->id ? 'selected' : '' }}>{{ $patient->fullName() }} ({{ $patient->mrn }})</option>
                @endforeach
            </x-ui.select>
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-700 mb-1">Visit</label>
            <x-ui.select name="visit_id" class="w-full border border-gray-200 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-slate-300 outline-none bg-white">
                <option value="">Select visit...</option>
                @foreach($visits as $visit)
                    <option value="{{ $visit->id }}" {{ $clinicalRecord->visit_id == $visit->id ? 'selected' : '' }}>{{ $visit->visit_number }} - {{ $visit->patient?->fullName() }}</option>
                @endforeach
            </x-ui.select>
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-700 mb-1">Appointment</label>
            <x-ui.select name="appointment_id" class="w-full border border-gray-200 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-slate-300 outline-none bg-white">
                <option value="">Select appointment...</option>
                @foreach($appointments as $appointment)
                    <option value="{{ $appointment->id }}" {{ $clinicalRecord->appointment_id == $appointment->id ? 'selected' : '' }}>{{ $appointment->patient?->fullName() }} - {{ $appointment->scheduled_at?->format('M d, Y') }}</option>
                @endforeach
            </x-ui.select>
        </div>
        <div class="md:col-span-2">
            <label class="block text-xs font-medium text-gray-700 mb-1">Doctor</label>
            <x-ui.select name="doctor_id" class="w-full border border-gray-200 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-slate-300 outline-none bg-white" required>
                @foreach($doctors as $doctor)
                    <option value="{{ $doctor->id }}" {{ $clinicalRecord->doctor_id == $doctor->id ? 'selected' : '' }}>{{ $doctor->name }}</option>
                @endforeach
            </x-ui.select>
        </div>
        <div class="md:col-span-2">
            <label class="block text-xs font-medium text-gray-700 mb-1">Chief Complaint</label>
            <x-ui.textarea name="chief_complaint" class="w-full border border-gray-200 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-slate-300 outline-none bg-white" rows="2">{{ $clinicalRecord->chief_complaint }}</x-ui.textarea>
        </div>
        <div class="md:col-span-2">
            <label class="block text-xs font-medium text-gray-700 mb-1">Symptoms</label>
            <x-ui.textarea name="symptoms" class="w-full border border-gray-200 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-slate-300 outline-none bg-white" rows="2">{{ $clinicalRecord->symptoms }}</x-ui.textarea>
        </div>
        <div class="md:col-span-2">
            <label class="block text-xs font-medium text-gray-700 mb-1">Diagnosis</label>
            <x-ui.textarea name="diagnosis" class="w-full border border-gray-200 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-slate-300 outline-none bg-white" rows="2">{{ $clinicalRecord->diagnosis }}</x-ui.textarea>
        </div>
        <div class="md:col-span-2">
            <label class="block text-xs font-medium text-gray-700 mb-1">Treatment Plan</label>
            <x-ui.textarea name="treatment_plan" class="w-full border border-gray-200 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-slate-300 outline-none bg-white" rows="3">{{ $clinicalRecord->treatment_plan }}</x-ui.textarea>
        </div>
        <div class="md:col-span-2">
            <label class="block text-xs font-medium text-gray-700 mb-1">Prescription</label>
            <x-ui.textarea name="prescription" class="w-full border border-gray-200 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-slate-300 outline-none bg-white" rows="3">{{ $clinicalRecord->prescription }}</x-ui.textarea>
        </div>
        <div class="md:col-span-2">
            <label class="block text-xs font-medium text-gray-700 mb-1">Notes</label>
            <x-ui.textarea name="notes" class="w-full border border-gray-200 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-slate-300 outline-none bg-white" rows="3">{{ $clinicalRecord->notes }}</x-ui.textarea>
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-700 mb-1">Record Date</label>
            <x-ui.input type="text" data-fp-date name="record_date" value="{{ $clinicalRecord->record_date->format('Y-m-d') }}" class="w-full border border-gray-200 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-slate-300 outline-none bg-white" required />
        </div>
        <div class="md:col-span-2 pt-4">
            <label class="block text-xs font-medium text-gray-700 mb-1">Add Attachments</label>
            <x-ui.input type="file" name="attachments[]" multiple accept=".pdf,.jpg,.jpeg,.png,.doc,.docx" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm" />
            <p class="text-xs text-gray-400 mt-1">Upload additional files (PDF, Images, Documents) - Max 10MB each</p>
        </div>
        <div class="md:col-span-2 pt-4">
            <button type="submit" class="bg-slate-800 text-white text-sm font-medium px-5 py-2 rounded hover:bg-slate-700">Update Clinical Record</button>
        </div>
    </form>
    </div>
</div>
@endsection
