@extends('layouts.dashboard')

@section('title', 'Patient Profile - ' . config('app.name', 'Laravel'))
@section('page_title', 'Patient Profile')

@section('content')
<div class="max-w-6xl mx-auto space-y-6">
    @if (session('status'))
        <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm animate-fade">{{ session('status') }}</div>
    @endif

    {{-- Profile Header --}}
    <div class="bg-white rounded-2xl border border-gray-100 p-8 shadow-sm relative overflow-hidden">
        <div class="absolute top-0 left-0 right-0 h-24 bg-gradient-to-r from-emerald-600 via-emerald-700 to-emerald-800"></div>
        <div class="relative pt-10 flex flex-col md:flex-row md:items-end justify-between gap-6">
            <div class="flex flex-col md:flex-row items-center md:items-end gap-5">
                <div class="w-28 h-28 rounded-full border-4 border-white bg-gradient-to-br from-emerald-400 to-emerald-700 flex items-center justify-center text-white text-3xl font-bold shadow-lg">
                    {{ strtoupper(substr($patient->first_name, 0, 1)) }}{{ strtoupper(substr($patient->last_name, 0, 1)) }}
                </div>
                <div class="text-center md:text-left pb-1">
                    <h2 class="text-2xl font-bold text-gray-900">{{ $patient->fullName() }}</h2>
                    <div class="flex flex-wrap items-center justify-center md:justify-start gap-3 text-sm text-gray-500 mt-1">
                        <span class="inline-flex items-center gap-1"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14h10"/></svg> {{ $patient->mrn }}</span>
                        <span class="inline-flex items-center gap-1"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg> {{ $patient->date_of_birth?->format('M d, Y') }}</span>
                        <span class="inline-flex items-center gap-1 capitalize"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg> {{ $patient->gender }}</span>
                    </div>
                </div>
            </div>
            <div class="flex items-center justify-center gap-1 pb-1">
                <a href="{{ route('patients.history', $patient) }}" class="action-icon group/icon relative p-2.5 text-blue-600 hover:bg-blue-100 rounded-lg transition-colors" title="History">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span class="absolute -top-8 left-1/2 -translate-x-1/2 px-2 py-1 bg-gray-900 text-white text-[10px] rounded opacity-0 group-hover/icon:opacity-100 transition-opacity whitespace-nowrap pointer-events-none">History</span>
                </a>
                <a href="{{ route('patients.documents.index', $patient) }}" class="action-icon group/icon relative p-2.5 text-purple-600 hover:bg-purple-100 rounded-lg transition-colors" title="Files">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <span class="absolute -top-8 left-1/2 -translate-x-1/2 px-2 py-1 bg-gray-900 text-white text-[10px] rounded opacity-0 group-hover/icon:opacity-100 transition-opacity whitespace-nowrap pointer-events-none">Files</span>
                </a>
                <a href="{{ route('patients.edit', $patient) }}" class="action-icon group/icon relative p-2.5 text-emerald-600 hover:bg-emerald-100 rounded-lg transition-colors" title="Edit">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.43-9.525l-9.17 9.17a2 2 0 00-.586 1.414V17a1 1 0 001 1h2.828a2 2 0 001.414-.586l9.17-9.17a2 2 0 000-2.828l-1.414-1.414a2 2 0 00-2.828 0z"/></svg>
                    <span class="absolute -top-8 left-1/2 -translate-x-1/2 px-2 py-1 bg-gray-900 text-white text-[10px] rounded opacity-0 group-hover/icon:opacity-100 transition-opacity whitespace-nowrap pointer-events-none">Edit</span>
                </a>
            </div>
        </div>
    </div>

    {{-- Summary Cards --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl border border-gray-100 p-5 shadow-sm hover:shadow-md transition-shadow text-center">
            <div class="w-12 h-12 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center mx-auto mb-3">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6 4h6"/></svg>
            </div>
            <div class="text-2xl font-bold text-gray-900">{{ $patient->visits_count }}</div>
            <div class="text-xs text-gray-500 uppercase tracking-wide mt-1">Visits</div>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 p-5 shadow-sm hover:shadow-md transition-shadow text-center">
            <div class="w-12 h-12 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center mx-auto mb-3">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            </div>
            <div class="text-2xl font-bold text-gray-900">{{ $patient->appointments_count }}</div>
            <div class="text-xs text-gray-500 uppercase tracking-wide mt-1">Appointments</div>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 p-5 shadow-sm hover:shadow-md transition-shadow text-center">
            <div class="w-12 h-12 rounded-full bg-purple-100 text-purple-600 flex items-center justify-center mx-auto mb-3">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            </div>
            <div class="text-2xl font-bold text-gray-900">{{ $patient->documents_count }}</div>
            <div class="text-xs text-gray-500 uppercase tracking-wide mt-1">Documents</div>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 p-5 shadow-sm hover:shadow-md transition-shadow text-center">
            <div class="w-12 h-12 rounded-full bg-gold-100 text-gold-600 flex items-center justify-center mx-auto mb-3">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            </div>
            <div class="text-2xl font-bold text-gray-900">{{ $patient->clinical_records_count }}</div>
            <div class="text-xs text-gray-500 uppercase tracking-wide mt-1">Records</div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Personal Info --}}
        <div class="bg-white rounded-xl border border-gray-100 p-6 shadow-sm lg:col-span-2">
            <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wide mb-4 flex items-center gap-2">
                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                Personal Information
            </h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-y-4 gap-x-6 text-sm">
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                    <span class="text-gray-500">Gender</span>
                    <span class="font-medium capitalize">{{ $patient->gender }}</span>
                </div>
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                    <span class="text-gray-500">Blood Group</span>
                    <span class="font-medium">{{ $patient->blood_group ?? '-' }}</span>
                </div>
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                    <span class="text-gray-500">Phone</span>
                    <span class="font-medium">{{ $patient->phone ?? '-' }}</span>
                </div>
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                    <span class="text-gray-500">National ID</span>
                    <span class="font-medium">{{ $patient->national_id ?? '-' }}</span>
                </div>
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg sm:col-span-2">
                    <span class="text-gray-500">Address</span>
                    <span class="font-medium text-right">{{ $patient->address ?? '-' }}</span>
                </div>
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg sm:col-span-2">
                    <span class="text-gray-500">Allergies</span>
                    <span class="font-medium text-right">{{ $patient->allergies ?? 'None recorded' }}</span>
                </div>
            </div>
        </div>

        {{-- Quick Info --}}
        <div class="bg-white rounded-xl border border-gray-100 p-6 shadow-sm">
            <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wide mb-4 flex items-center gap-2">
                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
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
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wide flex items-center gap-2">
                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6 4h6"/></svg>
                Recent Visits
            </h3>
            <a href="{{ route('patients.history', $patient) }}" class="text-xs text-emerald-600 hover:text-emerald-700 font-medium">View all history</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                    <tr>
                        <th class="px-6 py-3">Visit #</th>
                        <th class="px-6 py-3">Date</th>
                        <th class="px-6 py-3">Status</th>
                        <th class="px-6 py-3">Doctor</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($patient->visits()->latest()->limit(5)->get() as $visit)
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="px-6 py-3 font-medium">{{ $visit->visit_number }}</td>
                            <td class="px-6 py-3">{{ $visit->registered_at?->format('M d, Y H:i') }}</td>
                            <td class="px-6 py-3">
                                @php
                                    $badgeColors = [
                                        'registered' => 'bg-gray-100 text-gray-700',
                                        'with_doctor' => 'bg-emerald-100 text-emerald-700',
                                        'waiting_for_lab' => 'bg-blue-100 text-blue-700',
                                        'waiting_for_payment' => 'bg-amber-100 text-amber-700',
                                        'completed' => 'bg-gold-100 text-gold-700',
                                    ];
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium capitalize {{ $badgeColors[$visit->status] ?? 'bg-gray-100 text-gray-700' }}">
                                    {{ str_replace('_', ' ', $visit->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-3">{{ $visit->doctor?->name ?? 'Not assigned' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="px-6 py-6 text-center text-gray-400">No visits recorded</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
