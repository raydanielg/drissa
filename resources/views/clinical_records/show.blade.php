@extends('layouts.dashboard')

@section('title', 'Clinical Record - ' . config('app.name', 'Laravel'))
@section('page_title', 'Clinical Record Details')

@section('content')
<div class="max-w-3xl mx-auto space-y-5">
    <div class="bg-slate-800 rounded-md p-6 text-white flex items-center justify-between">
        <div>
            <p class="text-[10px] uppercase tracking-wider text-slate-400 mb-1">Clinical Record</p>
            <h1 class="text-xl font-bold">{{ $clinicalRecord->patient?->fullName() }}</h1>
            <p class="text-xs text-slate-400 mt-1">{{ $clinicalRecord->record_date->format('M d, Y') }}</p>
        </div>
    </div>

    <div class="bg-white rounded-md border border-gray-200 p-5 space-y-4">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pb-3 border-b border-gray-100">
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
            <p class="text-sm text-gray-600 bg-gray-50 p-3 rounded">{{ $clinicalRecord->chief_complaint ?: 'N/A' }}</p>
        </div>
        <div>
            <p class="text-xs font-semibold text-gray-700">Symptoms</p>
            <p class="text-sm text-gray-600 bg-gray-50 p-3 rounded">{{ $clinicalRecord->symptoms ?: 'N/A' }}</p>
        </div>
        <div>
            <p class="text-xs font-semibold text-gray-700">Diagnosis</p>
            <p class="text-sm text-gray-600 bg-gray-50 p-3 rounded">{{ $clinicalRecord->diagnosis ?: 'N/A' }}</p>
        </div>
        <div>
            <p class="text-xs font-semibold text-gray-700">Treatment Plan</p>
            <p class="text-sm text-gray-600 bg-gray-50 p-3 rounded">{{ $clinicalRecord->treatment_plan ?: 'N/A' }}</p>
        </div>
        <div>
            <p class="text-xs font-semibold text-gray-700">Prescription</p>
            <p class="text-sm text-gray-600 bg-gray-50 p-3 rounded whitespace-pre-line">{{ $clinicalRecord->prescription ?: 'N/A' }}</p>
        </div>
        <div>
            <p class="text-xs font-semibold text-gray-700">Notes</p>
            <p class="text-sm text-gray-600 bg-gray-50 p-3 rounded">{{ $clinicalRecord->notes ?: 'N/A' }}</p>
        </div>
    </div>

    @if($clinicalRecord->attachments->isNotEmpty())
    <div class="pt-2">
        <p class="text-xs font-semibold text-gray-700 mb-2">Attachments</p>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
            @foreach($clinicalRecord->attachments as $attachment)
                <div class="border border-gray-200 rounded p-3 flex items-center gap-3">
                    <div class="w-10 h-10 rounded bg-slate-100 flex items-center justify-center flex-shrink-0">
                        <i class="hgi-stroke hgi-file-01 text-xl text-slate-600"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900 truncate">{{ $attachment->file_name }}</p>
                        <p class="text-xs text-gray-500">{{ number_format($attachment->file_size / 1024, 1) }} KB</p>
                    </div>
                    <a href="{{ route('file.serve', $attachment->file_path) }}" target="_blank" class="text-slate-600 hover:text-slate-800 text-xs font-medium">View</a>
                </div>
            @endforeach
        </div>
    </div>
    @endif

    <div class="pt-4 flex gap-2">
        <a href="{{ route('clinical-records.edit', $clinicalRecord) }}" class="bg-slate-800 text-white text-sm font-medium px-4 py-2 rounded hover:bg-slate-700">Edit</a>
        <a href="{{ route('clinical-records.index') }}" class="border border-gray-200 text-gray-700 text-sm font-medium px-4 py-2 rounded hover:bg-gray-100">Back</a>
    </div>
    </div>
</div>
@endsection
