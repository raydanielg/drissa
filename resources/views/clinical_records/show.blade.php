@extends('layouts.dashboard')

@section('title', 'Clinical Record - ' . config('app.name', 'Laravel'))
@section('page_title', 'Clinical Record Details')

@section('content')
<div class="max-w-3xl mx-auto bg-white rounded-xl border border-gray-100 p-6 shadow-sm space-y-4">
    <div class="flex items-center justify-between border-b border-gray-100 pb-4">
        <div>
            <p class="text-xs text-gray-500">Patient</p>
            <h2 class="text-lg font-bold text-gray-900">{{ $clinicalRecord->patient?->fullName() }}</h2>
        </div>
        <div class="text-right">
            <p class="text-xs text-gray-500">Record Date</p>
            <p class="text-sm font-medium">{{ $clinicalRecord->record_date->format('M d, Y') }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <p class="text-xs text-gray-500">Doctor</p>
            <p class="text-sm font-medium text-gray-900">{{ $clinicalRecord->doctor?->name ?? '-' }}</p>
        </div>
        <div>
            <p class="text-xs text-gray-500">Visit</p>
            <p class="text-sm font-medium text-gray-900">{{ $clinicalRecord->visit?->visit_number ?? '-' }}</p>
        </div>
    </div>

    <div class="space-y-2">
        <div>
            <p class="text-xs font-semibold text-gray-700">Chief Complaint</p>
            <p class="text-sm text-gray-600 bg-gray-50 p-3 rounded-lg">{{ $clinicalRecord->chief_complaint ?: 'N/A' }}</p>
        </div>
        <div>
            <p class="text-xs font-semibold text-gray-700">Symptoms</p>
            <p class="text-sm text-gray-600 bg-gray-50 p-3 rounded-lg">{{ $clinicalRecord->symptoms ?: 'N/A' }}</p>
        </div>
        <div>
            <p class="text-xs font-semibold text-gray-700">Diagnosis</p>
            <p class="text-sm text-gray-600 bg-gray-50 p-3 rounded-lg">{{ $clinicalRecord->diagnosis ?: 'N/A' }}</p>
        </div>
        <div>
            <p class="text-xs font-semibold text-gray-700">Treatment Plan</p>
            <p class="text-sm text-gray-600 bg-gray-50 p-3 rounded-lg">{{ $clinicalRecord->treatment_plan ?: 'N/A' }}</p>
        </div>
        <div>
            <p class="text-xs font-semibold text-gray-700">Prescription</p>
            <p class="text-sm text-gray-600 bg-gray-50 p-3 rounded-lg whitespace-pre-line">{{ $clinicalRecord->prescription ?: 'N/A' }}</p>
        </div>
        <div>
            <p class="text-xs font-semibold text-gray-700">Notes</p>
            <p class="text-sm text-gray-600 bg-gray-50 p-3 rounded-lg">{{ $clinicalRecord->notes ?: 'N/A' }}</p>
        </div>
    </div>

    <div class="pt-4 flex gap-2">
        <a href="{{ route('clinical-records.edit', $clinicalRecord) }}" class="bg-emerald-600 text-white text-sm font-medium px-4 py-2 rounded-lg hover:bg-emerald-700">Edit</a>
        <a href="{{ route('clinical-records.index') }}" class="bg-gray-100 text-gray-700 text-sm font-medium px-4 py-2 rounded-lg hover:bg-gray-200">Back</a>
    </div>
</div>
@endsection
