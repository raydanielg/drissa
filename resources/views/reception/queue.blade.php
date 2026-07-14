@extends('layouts.dashboard')

@section('title', 'Reception Queue - ' . config('app.name', 'Laravel'))
@section('page_title', 'Reception Queue')

@section('content')
<div class="space-y-6">
    @if (session('status'))
        <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm animate-fade">{{ session('status') }}</div>
    @endif

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-gray-900">Reception Queue</h1>
            <p class="text-xs text-gray-500 mt-0.5">Manage patient queues and create new visits</p>
        </div>
        <div class="flex items-center gap-2">
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-emerald-50 border border-emerald-100 text-xs font-medium text-emerald-700">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span> Live Queue
            </span>
        </div>
    </div>

    {{-- Queue Stats Cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl border border-gray-100 p-4 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-[10px] font-medium text-gray-500 uppercase tracking-wide">Registered</p>
                    <p class="text-xl font-bold text-gray-900 mt-1">{{ $registeredQueue->count() }}</p>
                </div>
                <div class="w-9 h-9 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
            <div class="mt-3 h-1 bg-gray-100 rounded-full overflow-hidden">
                <div class="h-full bg-emerald-500 rounded-full" style="width: {{ min(100, $registeredQueue->count() * 10) }}%"></div>
            </div>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 p-4 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-[10px] font-medium text-gray-500 uppercase tracking-wide">Waiting Doctor</p>
                    <p class="text-xl font-bold text-gray-900 mt-1">{{ $waitingForDoctorQueue->count() }}</p>
                </div>
                <div class="w-9 h-9 rounded-full bg-amber-100 flex items-center justify-center text-amber-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
            <div class="mt-3 h-1 bg-gray-100 rounded-full overflow-hidden">
                <div class="h-full bg-amber-500 rounded-full" style="width: {{ min(100, $waitingForDoctorQueue->count() * 10) }}%"></div>
            </div>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 p-4 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-[10px] font-medium text-gray-500 uppercase tracking-wide">With Doctor</p>
                    <p class="text-xl font-bold text-gray-900 mt-1">{{ $withDoctorQueue->count() }}</p>
                </div>
                <div class="w-9 h-9 rounded-full bg-blue-100 flex items-center justify-center text-blue-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                </div>
            </div>
            <div class="mt-3 h-1 bg-gray-100 rounded-full overflow-hidden">
                <div class="h-full bg-blue-500 rounded-full" style="width: {{ min(100, $withDoctorQueue->count() * 10) }}%"></div>
            </div>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 p-4 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-[10px] font-medium text-gray-500 uppercase tracking-wide">Total Today</p>
                    <p class="text-xl font-bold text-gray-900 mt-1">{{ $allQueues->count() }}</p>
                </div>
                <div class="w-9 h-9 rounded-full bg-purple-100 flex items-center justify-center text-purple-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </div>
            </div>
            <div class="mt-3 h-1 bg-gray-100 rounded-full overflow-hidden">
                <div class="h-full bg-purple-500 rounded-full" style="width: {{ min(100, $allQueues->count() * 5) }}%"></div>
            </div>
        </div>
    </div>

    {{-- Quick Actions --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
        <button onclick="openPatientModal()" class="group flex flex-col items-center gap-2 p-4 bg-gradient-to-br from-emerald-600 to-emerald-700 rounded-xl text-white shadow-sm hover:shadow-lg hover:-translate-y-0.5 transition-all">
            <div class="w-10 h-10 rounded-lg bg-white/20 flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
            </div>
            <span class="text-xs font-semibold">Register Patient</span>
        </button>
        <button onclick="openVisitModal()" class="group flex flex-col items-center gap-2 p-4 bg-gradient-to-br from-blue-600 to-blue-700 rounded-xl text-white shadow-sm hover:shadow-lg hover:-translate-y-0.5 transition-all">
            <div class="w-10 h-10 rounded-lg bg-white/20 flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6 4h6"/></svg>
            </div>
            <span class="text-xs font-semibold">Open Visit</span>
        </button>
        <a href="{{ route('patients.index') }}" class="group flex flex-col items-center gap-2 p-4 bg-gradient-to-br from-purple-600 to-purple-700 rounded-xl text-white shadow-sm hover:shadow-lg hover:-translate-y-0.5 transition-all">
            <div class="w-10 h-10 rounded-lg bg-white/20 flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            </div>
            <span class="text-xs font-semibold">Patients</span>
        </a>
        <a href="{{ route('appointments.index') }}" class="group flex flex-col items-center gap-2 p-4 bg-gradient-to-br from-amber-500 to-amber-600 rounded-xl text-white shadow-sm hover:shadow-lg hover:-translate-y-0.5 transition-all">
            <div class="w-10 h-10 rounded-lg bg-white/20 flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            </div>
            <span class="text-xs font-semibold">Appointments</span>
        </a>
    </div>

    {{-- Queue Tabs --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="flex border-b border-gray-100 overflow-x-auto">
            <button onclick="showTab('all')" id="tab-all" class="queue-tab px-6 py-3 text-sm font-medium text-emerald-700 border-b-2 border-emerald-500 bg-emerald-50 whitespace-nowrap">
                All Queues ({{ $allQueues->count() }})
            </button>
            <button onclick="showTab('registered')" id="tab-registered" class="queue-tab px-6 py-3 text-sm font-medium text-gray-500 hover:text-gray-700 border-b-2 border-transparent hover:bg-gray-50 transition-all whitespace-nowrap">
                Registered ({{ $registeredQueue->count() }})
            </button>
            <button onclick="showTab('waiting')" id="tab-waiting" class="queue-tab px-6 py-3 text-sm font-medium text-gray-500 hover:text-gray-700 border-b-2 border-transparent hover:bg-gray-50 transition-all whitespace-nowrap">
                Waiting for Doctor ({{ $waitingForDoctorQueue->count() }})
            </button>
            <button onclick="showTab('with-doctor')" id="tab-with-doctor" class="queue-tab px-6 py-3 text-sm font-medium text-gray-500 hover:text-gray-700 border-b-2 border-transparent hover:bg-gray-50 transition-all whitespace-nowrap">
                With Doctor ({{ $withDoctorQueue->count() }})
            </button>
        </div>

        {{-- All Queues Table --}}
        <div id="queue-all" class="queue-content p-4">
            @if($allQueues->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="text-left text-gray-500 text-xs uppercase tracking-wider">
                                <th class="pb-3 font-medium">Visit #</th>
                                <th class="pb-3 font-medium">Patient</th>
                                <th class="pb-3 font-medium">MRN</th>
                                <th class="pb-3 font-medium">Status</th>
                                <th class="pb-3 font-medium">Doctor</th>
                                <th class="pb-3 font-medium">Time</th>
                                <th class="pb-3 font-medium">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($allQueues as $visit)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="py-3 text-gray-900 font-mono text-sm">{{ $visit->visit_number }}</td>
                                <td class="py-3">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-600 font-bold text-xs">
                                            {{ strtoupper(substr($visit->patient->first_name ?? 'U', 0, 1)) }}
                                        </div>
                                        <div>
                                            <p class="text-gray-900 text-sm font-medium">{{ $visit->patient->fullName() }}</p>
                                            <p class="text-gray-500 text-xs">{{ $visit->patient->phone ?? 'N/A' }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3 text-gray-600 text-sm font-mono">{{ $visit->patient->mrn }}</td>
                                <td class="py-3">
                                    @php
                                        $statusClass = match($visit->status) {
                                            'registered' => 'bg-emerald-100 text-emerald-700 border-emerald-200',
                                            'waiting_for_doctor' => 'bg-amber-100 text-amber-700 border-amber-200',
                                            'with_doctor' => 'bg-blue-100 text-blue-700 border-blue-200',
                                            default => 'bg-gray-100 text-gray-700 border-gray-200'
                                        };
                                        $statusLabel = match($visit->status) {
                                            'registered' => 'Registered',
                                            'waiting_for_doctor' => 'Waiting for Doctor',
                                            'with_doctor' => 'With Doctor',
                                            default => ucfirst(str_replace('_', ' ', $visit->status))
                                        };
                                    @endphp
                                    <span class="px-2 py-1 rounded-full text-xs font-medium border {{ $statusClass }}">
                                        {{ $statusLabel }}
                                    </span>
                                </td>
                                <td class="py-3 text-gray-600 text-sm">
                                    {{ $visit->doctor ? $visit->doctor->name : 'Not Assigned' }}
                                </td>
                                <td class="py-3 text-gray-500 text-sm">
                                    {{ $visit->registered_at->format('H:i') }}
                                </td>
                                <td class="py-3">
                                    <div class="flex items-center gap-2">
                                        @if($visit->status === 'registered')
                                            <button onclick="openAssignDoctorModal({{ $visit->id }})" class="p-2 text-gray-400 hover:text-emerald-600 hover:bg-emerald-50 rounded-lg transition-all" title="Assign Doctor">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                            </button>
                                        @endif
                                        <a href="{{ route('patients.show', $visit->patient) }}" class="p-2 text-gray-400 hover:text-emerald-600 hover:bg-emerald-50 rounded-lg transition-all" title="View Patient">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-12">
                    <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    <p class="text-gray-500">No patients in queue</p>
                    <button onclick="openVisitModal()" class="mt-4 px-4 py-2 bg-emerald-500 hover:bg-emerald-600 text-white rounded-lg text-sm font-medium transition-all">
                        Create First Visit
                    </button>
                </div>
            @endif
        </div>

        {{-- Registered Queue Table --}}
        <div id="queue-registered" class="queue-content p-4 hidden">
            @if($registeredQueue->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="text-left text-gray-500 text-xs uppercase tracking-wider">
                                <th class="pb-3 font-medium">Visit #</th>
                                <th class="pb-3 font-medium">Patient</th>
                                <th class="pb-3 font-medium">MRN</th>
                                <th class="pb-3 font-medium">Time</th>
                                <th class="pb-3 font-medium">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($registeredQueue as $visit)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="py-3 text-gray-900 font-mono text-sm">{{ $visit->visit_number }}</td>
                                <td class="py-3">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-600 font-bold text-xs">
                                            {{ strtoupper(substr($visit->patient->first_name ?? 'U', 0, 1)) }}
                                        </div>
                                        <div>
                                            <p class="text-gray-900 text-sm font-medium">{{ $visit->patient->fullName() }}</p>
                                            <p class="text-gray-500 text-xs">{{ $visit->patient->phone ?? 'N/A' }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3 text-gray-600 text-sm font-mono">{{ $visit->patient->mrn }}</td>
                                <td class="py-3 text-gray-500 text-sm">{{ $visit->registered_at->format('H:i') }}</td>
                                <td class="py-3">
                                    <div class="flex items-center gap-2">
                                        <button onclick="openAssignDoctorModal({{ $visit->id }})" class="px-3 py-1.5 bg-emerald-500 hover:bg-emerald-600 text-white rounded-lg text-xs font-medium transition-all flex items-center gap-1">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                            Assign Doctor
                                        </button>
                                        <a href="{{ route('patients.show', $visit->patient) }}" class="p-2 text-gray-400 hover:text-emerald-600 hover:bg-emerald-50 rounded-lg transition-all">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-12">
                    <p class="text-gray-500">No registered patients</p>
                </div>
            @endif
        </div>

        {{-- Waiting for Doctor Queue Table --}}
        <div id="queue-waiting" class="queue-content p-4 hidden">
            @if($waitingForDoctorQueue->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="text-left text-gray-500 text-xs uppercase tracking-wider">
                                <th class="pb-3 font-medium">Visit #</th>
                                <th class="pb-3 font-medium">Patient</th>
                                <th class="pb-3 font-medium">MRN</th>
                                <th class="pb-3 font-medium">Assigned Doctor</th>
                                <th class="pb-3 font-medium">Time</th>
                                <th class="pb-3 font-medium">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($waitingForDoctorQueue as $visit)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="py-3 text-gray-900 font-mono text-sm">{{ $visit->visit_number }}</td>
                                <td class="py-3">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-amber-100 flex items-center justify-center text-amber-600 font-bold text-xs">
                                            {{ strtoupper(substr($visit->patient->first_name ?? 'U', 0, 1)) }}
                                        </div>
                                        <div>
                                            <p class="text-gray-900 text-sm font-medium">{{ $visit->patient->fullName() }}</p>
                                            <p class="text-gray-500 text-xs">{{ $visit->patient->phone ?? 'N/A' }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3 text-gray-600 text-sm font-mono">{{ $visit->patient->mrn }}</td>
                                <td class="py-3 text-amber-700 text-sm font-medium">{{ $visit->doctor ? $visit->doctor->name : 'Not Assigned' }}</td>
                                <td class="py-3 text-gray-500 text-sm">{{ $visit->registered_at->format('H:i') }}</td>
                                <td class="py-3">
                                    <div class="flex items-center gap-2">
                                        <button onclick="openChangeDoctorModal({{ $visit->id }})" class="p-2 text-amber-600 hover:text-white hover:bg-amber-600 rounded-lg transition-all" title="Change Doctor">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                                        </button>
                                        <a href="{{ route('patients.show', $visit->patient) }}" class="p-2 text-gray-400 hover:text-emerald-600 hover:bg-emerald-50 rounded-lg transition-all">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-12">
                    <p class="text-gray-500">No patients waiting for doctor</p>
                </div>
            @endif
        </div>

        {{-- With Doctor Queue Table --}}
        <div id="queue-with-doctor" class="queue-content p-4 hidden">
            @if($withDoctorQueue->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="text-left text-gray-500 text-xs uppercase tracking-wider">
                                <th class="pb-3 font-medium">Visit #</th>
                                <th class="pb-3 font-medium">Patient</th>
                                <th class="pb-3 font-medium">MRN</th>
                                <th class="pb-3 font-medium">Doctor</th>
                                <th class="pb-3 font-medium">Time</th>
                                <th class="pb-3 font-medium">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($withDoctorQueue as $visit)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="py-3 text-gray-900 font-mono text-sm">{{ $visit->visit_number }}</td>
                                <td class="py-3">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-bold text-xs">
                                            {{ strtoupper(substr($visit->patient->first_name ?? 'U', 0, 1)) }}
                                        </div>
                                        <div>
                                            <p class="text-gray-900 text-sm font-medium">{{ $visit->patient->fullName() }}</p>
                                            <p class="text-gray-500 text-xs">{{ $visit->patient->phone ?? 'N/A' }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3 text-gray-600 text-sm font-mono">{{ $visit->patient->mrn }}</td>
                                <td class="py-3 text-blue-700 text-sm font-medium">{{ $visit->doctor ? $visit->doctor->name : 'Not Assigned' }}</td>
                                <td class="py-3 text-gray-500 text-sm">{{ $visit->registered_at->format('H:i') }}</td>
                                <td class="py-3">
                                    <a href="{{ route('patients.show', $visit->patient) }}" class="p-2 text-gray-400 hover:text-emerald-600 hover:bg-emerald-50 rounded-lg transition-all">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-12">
                    <p class="text-gray-500">No patients with doctor</p>
                </div>
            @endif
        </div>
    </div>
</div>

{{-- Patient Registration Modal --}}
<div id="patientModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white border border-gray-200 rounded-2xl w-full max-w-lg shadow-2xl">
        <div class="p-6 border-b border-gray-100">
            <div class="flex items-center justify-between">
                <h3 class="text-xl font-bold text-gray-900">Register New Patient</h3>
                <button onclick="closePatientModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        </div>
        <form action="{{ route('reception.patients.store') }}" method="POST" class="p-6 space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Full Name</label>
                <input type="text" name="name" required class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-lg text-gray-900 placeholder-gray-400 focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition-all" placeholder="John Doe">
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Date of Birth</label>
                    <input type="date" name="date_of_birth" required class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-lg text-gray-900 focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition-all">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Gender</label>
                    <select name="gender" required class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-lg text-gray-900 focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition-all">
                        <option value="">Select</option>
                        <option value="male">Male</option>
                        <option value="female">Female</option>
                        <option value="other">Other</option>
                    </select>
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Phone</label>
                <input type="text" name="phone" class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-lg text-gray-900 placeholder-gray-400 focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition-all" placeholder="+255 123 456 789">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Address</label>
                <textarea name="address" rows="2" class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-lg text-gray-900 placeholder-gray-400 focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition-all resize-none" placeholder="Patient address"></textarea>
            </div>
            <div class="flex gap-3 pt-4">
                <button type="button" onclick="closePatientModal()" class="flex-1 px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg font-medium transition-all border border-gray-200">
                    Cancel
                </button>
                <button type="submit" class="flex-1 px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg font-medium transition-all">
                    Register Patient
                </button>
            </div>
        </form>
    </div>
</div>

 {{-- New Visit Modal --}}
<div id="visitModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white border border-gray-200 rounded-2xl w-full max-w-lg shadow-2xl">
        <div class="p-6 border-b border-gray-100">
            <div class="flex items-center justify-between">
                <h3 class="text-xl font-bold text-gray-900">Create New Visit</h3>
                <button onclick="closeVisitModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        </div>
        <form action="{{ route('reception.visits.store') }}" method="POST" class="p-6 space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Select Patient</label>
                <select name="patient_id" required class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-lg text-gray-900 focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition-all">
                    <option value="">Select Patient</option>
                    @foreach($patientsList as $patient)
                        <option value="{{ $patient->id }}">{{ $patient->fullName() }} ({{ $patient->mrn }})</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Assign Doctor (Optional)</label>
                <select name="doctor_id" class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-lg text-gray-900 focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition-all">
                    <option value="">No Doctor (Register Only)</option>
                    @foreach($doctors as $doctor)
                        <option value="{{ $doctor->id }}">{{ $doctor->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Visit Type</label>
                <select name="type" required class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-lg text-gray-900 focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition-all">
                    <option value="outpatient">Outpatient</option>
                    <option value="emergency">Emergency</option>
                    <option value="followup">Follow-up</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Chief Complaint</label>
                <textarea name="chief_complaint" rows="2" class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-lg text-gray-900 placeholder-gray-400 focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition-all resize-none" placeholder="Reason for visit"></textarea>
            </div>
            <div class="flex gap-3 pt-4">
                <button type="button" onclick="closeVisitModal()" class="flex-1 px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg font-medium transition-all border border-gray-200">
                    Cancel
                </button>
                <button type="submit" class="flex-1 px-4 py-2.5 bg-amber-600 hover:bg-amber-700 text-white rounded-lg font-medium transition-all">
                    Create Visit
                </button>
            </div>
        </form>
    </div>
</div>

 {{-- Assign Doctor Modal --}}
<div id="assignDoctorModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white border border-gray-200 rounded-2xl w-full max-w-md shadow-2xl">
        <div class="p-6 border-b border-gray-100">
            <div class="flex items-center justify-between">
                <h3 class="text-xl font-bold text-gray-900">Assign Doctor</h3>
                <button onclick="closeAssignDoctorModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        </div>
        <form id="assignDoctorForm" action="" method="POST" class="p-6 space-y-4">
            @csrf
            <input type="hidden" name="doctor_id" id="assignDoctorId">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Select Doctor</label>
                <select name="doctor_id" required class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-lg text-gray-900 focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition-all">
                    <option value="">Select Doctor</option>
                    @foreach($doctors as $doctor)
                        <option value="{{ $doctor->id }}">{{ $doctor->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex gap-3 pt-4">
                <button type="button" onclick="closeAssignDoctorModal()" class="flex-1 px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg font-medium transition-all border border-gray-200">
                    Cancel
                </button>
                <button type="submit" class="flex-1 px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg font-medium transition-all">
                    Assign Doctor
                </button>
            </div>
        </form>
    </div>
</div>

 {{-- Change Doctor Modal --}}
<div id="changeDoctorModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white border border-gray-200 rounded-2xl w-full max-w-md shadow-2xl">
        <div class="p-6 border-b border-gray-100">
            <div class="flex items-center justify-between">
                <h3 class="text-xl font-bold text-gray-900">Change Doctor</h3>
                <button onclick="closeChangeDoctorModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        </div>
        <form id="changeDoctorForm" action="" method="POST" class="p-6 space-y-4">
            @csrf
            <input type="hidden" name="doctor_id" id="changeDoctorId">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Select New Doctor</label>
                <select name="doctor_id" required class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-lg text-gray-900 focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition-all">
                    <option value="">Select Doctor</option>
                    @foreach($doctors as $doctor)
                        <option value="{{ $doctor->id }}">{{ $doctor->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex gap-3 pt-4">
                <button type="button" onclick="closeChangeDoctorModal()" class="flex-1 px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg font-medium transition-all border border-gray-200">
                    Cancel
                </button>
                <button type="submit" class="flex-1 px-4 py-2.5 bg-amber-600 hover:bg-amber-700 text-white rounded-lg font-medium transition-all">
                    Change Doctor
                </button>
            </div>
        </form>
    </div>
</div>

<script>
const patientSearchData = @json($patientSearchData);

function showTab(tab) {
    document.querySelectorAll('.queue-content').forEach(el => el.classList.add('hidden'));
    document.querySelectorAll('.queue-tab').forEach(el => {
        el.classList.remove('border-emerald-500', 'bg-emerald-50', 'text-emerald-700');
        el.classList.add('border-transparent', 'text-gray-500');
    });
    
    document.getElementById('queue-' + tab).classList.remove('hidden');
    const activeTab = document.getElementById('tab-' + tab);
    activeTab.classList.remove('border-transparent', 'text-gray-500');
    activeTab.classList.add('border-emerald-500', 'bg-emerald-50', 'text-emerald-700');
}

function openPatientModal() {
    document.getElementById('patientModal').classList.remove('hidden');
}

function closePatientModal() {
    document.getElementById('patientModal').classList.add('hidden');
}

function openVisitModal() {
    document.getElementById('visitModal').classList.remove('hidden');
}

function closeVisitModal() {
    document.getElementById('visitModal').classList.add('hidden');
}

function openAssignDoctorModal(visitId) {
    document.getElementById('assignDoctorForm').action = '/reception/visits/' + visitId + '/assign';
    document.getElementById('assignDoctorModal').classList.remove('hidden');
}

function closeAssignDoctorModal() {
    document.getElementById('assignDoctorModal').classList.add('hidden');
}

function openChangeDoctorModal(visitId) {
    document.getElementById('changeDoctorForm').action = '/reception/visits/' + visitId + '/change-doctor';
    document.getElementById('changeDoctorModal').classList.remove('hidden');
}

function closeChangeDoctorModal() {
    document.getElementById('changeDoctorModal').classList.add('hidden');
}

// Close modals on escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closePatientModal();
        closeVisitModal();
        closeAssignDoctorModal();
        closeChangeDoctorModal();
    }
});

// Close modals on backdrop click
document.querySelectorAll('#patientModal, #visitModal, #assignDoctorModal, #changeDoctorModal').forEach(modal => {
    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            modal.classList.add('hidden');
        }
    });
});
</script>
@endsection
