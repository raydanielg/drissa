@extends('layouts.dashboard')

@section('title', 'Patient Profile - ' . config('app.name', 'Laravel'))
@section('page_title', 'Patient Profile')

@section('content')
<div class="max-w-6xl mx-auto space-y-5">
    @if (session('status'))
        <div class="p-3 rounded-md bg-white border-l-2 border-emerald-500 text-emerald-700 text-xs">{{ session('status') }}</div>
    @endif

    {{-- Recent Visits --}}
    <div class="bg-slate-800 rounded-md p-6 text-white">
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
            <div class="flex flex-col md:flex-row items-center md:items-end gap-4">
                <div class="w-20 h-20 rounded-full border-2 border-slate-600 bg-slate-700 flex items-center justify-center text-white text-2xl font-bold">
                    {{ strtoupper(substr($patient->first_name, 0, 1)) }}{{ strtoupper(substr($patient->last_name, 0, 1)) }}
                </div>
                <div class="text-center md:text-left pb-1">
                    <p class="text-[10px] uppercase tracking-wider text-slate-400 mb-1">Patient</p>
                    <h1 class="text-xl font-bold">{{ $patient->fullName() }}</h1>
                    <div class="flex flex-wrap items-center justify-center md:justify-start gap-3 text-xs text-slate-400 mt-1">
                        <span class="inline-flex items-center gap-1"><i class="hgi-stroke hgi-identity-card text-sm"></i> {{ $patient->mrn }}</span>
                        <span class="inline-flex items-center gap-1"><i class="hgi-stroke hgi-calendar-03 text-sm"></i> {{ $patient->date_of_birth?->format('M d, Y') }}</span>
                        <span class="inline-flex items-center gap-1 capitalize"><i class="hgi-stroke hgi-user text-sm"></i> {{ $patient->gender }}</span>
                    </div>
                </div>
            </div>
            <div class="flex items-center justify-center gap-2 pb-1">
                <a href="{{ route('patients.history', $patient) }}" class="inline-flex items-center gap-1.5 bg-white/10 hover:bg-white/20 border border-slate-600 text-white text-xs font-medium px-3 py-1.5 rounded transition-colors" title="History">
                    <i class="hgi-stroke hgi-clock-01 text-sm"></i>
                    History
                </a>
                <a href="{{ route('patients.documents.index', $patient) }}" class="inline-flex items-center gap-1.5 bg-white/10 hover:bg-white/20 border border-slate-600 text-white text-xs font-medium px-3 py-1.5 rounded transition-colors" title="Files">
                    <i class="hgi-stroke hgi-file-02 text-sm"></i>
                    Files
                </a>
                <a href="{{ route('patients.edit', $patient) }}" class="inline-flex items-center gap-1.5 bg-white hover:bg-gray-100 text-slate-800 text-xs font-semibold px-3 py-1.5 rounded transition-colors" title="Edit">
                    <i class="hgi-stroke hgi-pencil-edit-01 text-sm"></i>
                    Edit
                </a>
            </div>
        </div>
    </div>

    {{-- Recent Visits --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
        <div class="bg-white rounded-md border border-gray-200 p-4 text-center">
            <div class="text-2xl font-bold text-gray-900">{{ $patient->visits_count }}</div>
            <div class="text-[10px] font-medium text-gray-500 uppercase tracking-wide mt-1">Visits</div>
        </div>
        <div class="bg-white rounded-md border border-gray-200 p-4 text-center">
            <div class="text-2xl font-bold text-gray-900">{{ $patient->appointments_count }}</div>
            <div class="text-[10px] font-medium text-gray-500 uppercase tracking-wide mt-1">Appointments</div>
        </div>
        <div class="bg-white rounded-md border border-gray-200 p-4 text-center">
            <div class="text-2xl font-bold text-gray-900">{{ $patient->documents_count }}</div>
            <div class="text-[10px] font-medium text-gray-500 uppercase tracking-wide mt-1">Documents</div>
        </div>
        <div class="bg-white rounded-md border border-gray-200 p-4 text-center">
            <div class="text-2xl font-bold text-gray-900">{{ $patient->clinical_records_count }}</div>
            <div class="text-[10px] font-medium text-gray-500 uppercase tracking-wide mt-1">Records</div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
        {{-- Recent Visits --}}
        <div class="bg-white rounded-md border border-gray-200 p-5 lg:col-span-2">
            <h3 class="text-sm font-bold text-gray-900 mb-4 flex items-center gap-2">
                <i class="hgi-stroke hgi-user text-base text-gray-400"></i>
                Personal Information
            </h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-y-3 gap-x-6 text-sm">
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded">
                    <span class="text-gray-500 text-xs">Gender</span>
                    <span class="font-medium capitalize text-gray-900">{{ $patient->gender }}</span>
                </div>
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded">
                    <span class="text-gray-500 text-xs">Blood Group</span>
                    <span class="font-medium text-gray-900">{{ $patient->blood_group ?? '-' }}</span>
                </div>
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded">
                    <span class="text-gray-500 text-xs">Phone</span>
                    <span class="font-medium text-gray-900">{{ $patient->phone ?? '-' }}</span>
                </div>
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded">
                    <span class="text-gray-500 text-xs">National ID</span>
                    <span class="font-medium text-gray-900">{{ $patient->national_id ?? '-' }}</span>
                </div>
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded sm:col-span-2">
                    <span class="text-gray-500 text-xs">Address</span>
                    <span class="font-medium text-right text-gray-900">{{ $patient->address ?? '-' }}</span>
                </div>
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded sm:col-span-2">
                    <span class="text-gray-500 text-xs">Allergies</span>
                    <span class="font-medium text-right text-gray-900">{{ $patient->allergies ?? 'None recorded' }}</span>
                </div>
            </div>
        </div>

        {{-- Recent Visits --}}
        <div class="bg-white rounded-md border border-gray-200 p-5">
            <h3 class="text-sm font-bold text-gray-900 mb-4 flex items-center gap-2">
                <i class="hgi-stroke hgi-information-circle text-base text-gray-400"></i>
                Quick Info
            </h3>
            <div class="space-y-4 text-sm">
                <div>
                    <span class="text-gray-500 block text-xs">Registered On</span>
                    <span class="font-medium">{{ $patient->created_at->format('M d, Y') }}</span>
                </div>
                <div>
                    <span class="text-gray-500 block text-xs">Last Updated</span>
                    <span class="font-medium">{{ $patient->updated_at->format('M d, Y') }}</span>
                </div>
                <div>
                    <span class="text-gray-500 block text-xs">Age</span>
                    <span class="font-medium">{{ $patient->date_of_birth ? $patient->date_of_birth->diffInYears(now()) . ' years' : '-' }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Recent Visits --}}
    <div class="bg-white rounded-md border border-gray-200 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
            <h3 class="text-sm font-bold text-gray-900 flex items-center gap-2">
                <i class="hgi-stroke hgi-file-01 text-base text-gray-400"></i>
                Recent Visits
            </h3>
            <a href="{{ route('patients.history', $patient) }}" class="text-[10px] text-slate-600 hover:text-slate-800 font-medium">View all history</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-50 text-[10px] uppercase text-gray-400">
                    <tr>
                        <th class="px-5 py-3">Visit #</th>
                        <th class="px-5 py-3">Date</th>
                        <th class="px-5 py-3">Status</th>
                        <th class="px-5 py-3">Doctor</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse ($patient->visits()->latest()->limit(5)->get() as $visit)
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="px-5 py-3 text-xs font-medium text-gray-700">{{ $visit->visit_number }}</td>
                            <td class="px-5 py-3 text-xs text-gray-600">{{ $visit->registered_at?->format('M d, Y H:i') }}</td>
                            <td class="px-5 py-3">
                                @php
                                    $statusDot = match($visit->status) {
                                        'completed' => 'bg-emerald-500',
                                        'with_doctor' => 'bg-amber-500',
                                        'waiting_for_lab', 'waiting_for_payment' => 'bg-amber-500',
                                        'registered' => 'bg-slate-400',
                                        default => 'bg-slate-400',
                                    };
                                @endphp
                                <span class="inline-flex items-center gap-1.5 text-[10px] font-medium capitalize text-gray-600">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $statusDot }}"></span>
                                    {{ str_replace('_', ' ', $visit->status) }}
                                </span>
                            </td>
                            <td class="px-5 py-3 text-xs text-gray-600">{{ $visit->doctor?->name ?? 'Not assigned' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="px-5 py-8 text-center text-gray-400 text-xs">No visits recorded</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
