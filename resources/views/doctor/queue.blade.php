@extends('layouts.dashboard')

@section('title', 'Doctor Queue - ' . config('app.name', 'Laravel'))
@section('page_title', 'Doctor Queue')

@section('content')
<div class="space-y-6">
    @if (session('status'))
        <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm">{{ session('status') }}</div>
    @endif

    {{-- Queue Header --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h2 class="text-lg font-bold text-gray-900">My Queue</h2>
            <p class="text-sm text-gray-500 mt-1">Manage patients currently assigned to you.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('doctor.lab-results') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white text-sm font-medium rounded-lg transition-colors shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>
                Lab Results
            </a>
        </div>
    </div>

    @forelse ($visits as $visit)
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden" id="visit-{{ $visit->id }}">
            {{-- Patient Header --}}
            <div class="px-6 py-4 bg-gray-50 border-b border-gray-100 flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-full bg-gradient-to-br from-emerald-600 to-teal-700 flex items-center justify-center text-white font-bold text-lg shadow-md">
                        {{ strtoupper(substr($visit->patient->first_name, 0, 1)) }}
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-gray-900">{{ $visit->patient->fullName() }}</h3>
                        <div class="flex flex-wrap items-center gap-2 text-xs text-gray-500 mt-0.5">
                            <span class="inline-flex items-center gap-1"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0c0 .5.5 1 1 1h2c.5 0 1-.5 1-1m-4 0V5a2 2 0 114 0v1"/></svg> {{ $visit->visit_number }}</span>
                            <span>&bull;</span>
                            <span>{{ $visit->patient->gender ?? 'N/A' }} / {{ $visit->patient->date_of_birth?->age ?? 'N/A' }} yrs</span>
                            <span>&bull;</span>
                            <span>{{ $visit->patient->phone ?? 'No phone' }}</span>
                            <span>&bull;</span>
                            <span>MRN: {{ $visit->patient->mrn ?? 'N/A' }}</span>
                        </div>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    @php
                        $statusClass = match($visit->status) {
                            \App\Enums\VisitStatus::WaitingForDoctor->value => 'bg-amber-100 text-amber-700 border-amber-200',
                            \App\Enums\VisitStatus::WithDoctor->value => 'bg-emerald-100 text-emerald-700 border-emerald-200',
                            default => 'bg-gray-100 text-gray-700 border-gray-200',
                        };
                    @endphp
                    <span class="px-3 py-1 rounded-full text-xs font-semibold border {{ $statusClass }}">
                        {{ ucwords(str_replace('_', ' ', $visit->status)) }}
                    </span>
                    <span class="px-3 py-1 rounded-full text-xs font-medium bg-blue-50 text-blue-700 border border-blue-200">
                        {{ ucfirst($visit->type ?? 'walk-in') }}
                    </span>
                </div>
            </div>

            <div class="p-6">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    {{-- Left Column: Complaint + Vitals + Quick Links --}}
                    <div class="space-y-4">
                        @if ($visit->chief_complaint)
                            <div class="bg-amber-50 border border-amber-100 rounded-lg p-3">
                                <p class="text-xs font-semibold text-amber-800 uppercase tracking-wide mb-1">Chief Complaint</p>
                                <p class="text-sm text-amber-900">{{ $visit->chief_complaint }}</p>
                            </div>
                        @endif

                        {{-- Vitals --}}
                        <div class="border border-gray-100 rounded-lg overflow-hidden">
                            <div class="bg-gray-50 px-4 py-2 border-b border-gray-100">
                                <p class="text-xs font-semibold text-gray-700 uppercase tracking-wide">Vitals</p>
                            </div>
                            @if ($visit->vitals)
                                <div class="grid grid-cols-2 gap-3 p-4 text-sm">
                                    <div class="bg-red-50 rounded-lg p-2.5">
                                        <p class="text-[10px] text-red-500 uppercase font-medium">BP</p>
                                        <p class="font-semibold text-gray-900">{{ $visit->vitals->blood_pressure ?? '-' }}</p>
                                    </div>
                                    <div class="bg-orange-50 rounded-lg p-2.5">
                                        <p class="text-[10px] text-orange-500 uppercase font-medium">Temp</p>
                                        <p class="font-semibold text-gray-900">{{ $visit->vitals->temperature ?? '-' }} °C</p>
                                    </div>
                                    <div class="bg-blue-50 rounded-lg p-2.5">
                                        <p class="text-[10px] text-blue-500 uppercase font-medium">Pulse</p>
                                        <p class="font-semibold text-gray-900">{{ $visit->vitals->pulse ?? '-' }} bpm</p>
                                    </div>
                                    <div class="bg-emerald-50 rounded-lg p-2.5">
                                        <p class="text-[10px] text-emerald-500 uppercase font-medium">SpO2</p>
                                        <p class="font-semibold text-gray-900">{{ $visit->vitals->oxygen_saturation ?? '-' }}%</p>
                                    </div>
                                    <div class="bg-purple-50 rounded-lg p-2.5">
                                        <p class="text-[10px] text-purple-500 uppercase font-medium">Weight</p>
                                        <p class="font-semibold text-gray-900">{{ $visit->vitals->weight ?? '-' }} kg</p>
                                    </div>
                                    <div class="bg-teal-50 rounded-lg p-2.5">
                                        <p class="text-[10px] text-teal-500 uppercase font-medium">Height</p>
                                        <p class="font-semibold text-gray-900">{{ $visit->vitals->height ?? '-' }} cm</p>
                                    </div>
                                    <div class="bg-sky-50 rounded-lg p-2.5">
                                        <p class="text-[10px] text-sky-500 uppercase font-medium">Resp. Rate</p>
                                        <p class="font-semibold text-gray-900">{{ $visit->vitals->respiratory_rate ?? '-' }} /min</p>
                                    </div>
                                </div>
                                @if ($visit->vitals->notes)
                                    <div class="px-4 pb-3">
                                        <p class="text-xs text-gray-600 bg-gray-50 rounded-lg p-2">{{ $visit->vitals->notes }}</p>
                                    </div>
                                @endif
                            @else
                                <div class="p-4 text-sm text-gray-400">No vitals recorded.</div>
                            @endif
                        </div>

                        {{-- Patient Quick Links --}}
                        <div class="flex flex-wrap gap-2">
                            <a href="{{ route('patients.show', $visit->patient) }}" class="inline-flex items-center gap-1 px-3 py-1.5 bg-white border border-gray-200 rounded-lg text-xs font-medium text-gray-700 hover:bg-gray-50 hover:border-gray-300 transition-colors">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                Profile
                            </a>
                            <a href="{{ route('patients.history', $visit->patient) }}" class="inline-flex items-center gap-1 px-3 py-1.5 bg-white border border-gray-200 rounded-lg text-xs font-medium text-gray-700 hover:bg-gray-50 hover:border-gray-300 transition-colors">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                History
                            </a>
                            <a href="{{ route('patients.documents.index', $visit->patient) }}" class="inline-flex items-center gap-1 px-3 py-1.5 bg-white border border-gray-200 rounded-lg text-xs font-medium text-gray-700 hover:bg-gray-50 hover:border-gray-300 transition-colors">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                Documents
                            </a>
                        </div>
                    </div>

                    {{-- Middle Column: Actions --}}
                    <div class="lg:col-span-2 space-y-4">
                        @if ($visit->status === \App\Enums\VisitStatus::WaitingForDoctor->value)
                            <div class="flex flex-wrap gap-2">
                                <form method="POST" action="{{ route('doctor.visits.call', $visit) }}">
                                    @csrf
                                    <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold rounded-lg transition-colors shadow-sm">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                        Call In
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('doctor.visits.no-show', $visit) }}" onsubmit="return confirm('Mark this patient as no-show?')">
                                    @csrf
                                    <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2 bg-red-50 hover:bg-red-100 text-red-700 border border-red-200 text-xs font-semibold rounded-lg transition-colors">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        No Show
                                    </button>
                                </form>
                            </div>
                        @endif

                        @if ($visit->status === \App\Enums\VisitStatus::WithDoctor->value)
                            {{-- Consultation Form --}}
                            <form method="POST" action="{{ route('doctor.visits.consult', $visit) }}" class="w-full bg-gray-50 border border-gray-100 rounded-lg p-4 space-y-3">
                                @csrf
                                <div class="flex items-center gap-2 mb-2">
                                    <div class="w-7 h-7 rounded-lg bg-emerald-100 text-emerald-600 flex items-center justify-center">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    </div>
                                    <p class="text-sm font-semibold text-gray-900">Consultation</p>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                    <textarea name="history" placeholder="History" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-xs focus:border-emerald-500 focus:ring-emerald-500" rows="3">{{ $visit->consultation?->history }}</textarea>
                                    <textarea name="examination" placeholder="Examination" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-xs focus:border-emerald-500 focus:ring-emerald-500" rows="3">{{ $visit->consultation?->examination }}</textarea>
                                    <textarea name="diagnosis" placeholder="Diagnosis" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-xs focus:border-emerald-500 focus:ring-emerald-500" rows="3">{{ $visit->consultation?->diagnosis }}</textarea>
                                    <textarea name="notes" placeholder="Clinical Notes" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-xs focus:border-emerald-500 focus:ring-emerald-500" rows="3">{{ $visit->consultation?->notes }}</textarea>
                                </div>
                                <div class="flex justify-end">
                                    <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold rounded-lg transition-colors">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/></svg>
                                        Save Consultation
                                    </button>
                                </div>
                            </form>

                            {{-- Lab Order --}}
                            <form method="POST" action="{{ route('doctor.visits.lab', $visit) }}" class="w-full bg-sky-50 border border-sky-100 rounded-lg p-4 space-y-3">
                                @csrf
                                <div class="flex items-center gap-2 mb-2">
                                    <div class="w-7 h-7 rounded-lg bg-sky-100 text-sky-600 flex items-center justify-center">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>
                                    </div>
                                    <p class="text-sm font-semibold text-gray-900">Order Lab Tests</p>
                                </div>
                                <div class="grid grid-cols-2 md:grid-cols-3 gap-2">
                                    @foreach ($labTests as $test)
                                        <label class="flex items-center gap-2 p-2 bg-white border border-sky-100 rounded-lg cursor-pointer hover:bg-sky-50 transition-colors">
                                            <input type="checkbox" name="test_ids[]" value="{{ $test->id }}" class="rounded border-gray-300 text-sky-600 focus:ring-sky-500">
                                            <span class="text-xs text-gray-700">{{ $test->name }}</span>
                                        </label>
                                    @endforeach
                                </div>
                                <textarea name="notes" placeholder="Clinical notes for lab" class="w-full border border-sky-200 rounded-lg px-3 py-2 text-xs focus:border-sky-500 focus:ring-sky-500" rows="2"></textarea>
                                <div class="flex justify-end">
                                    <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2 bg-sky-600 hover:bg-sky-700 text-white text-xs font-semibold rounded-lg transition-colors">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        Send to Lab
                                    </button>
                                </div>
                            </form>

                            {{-- Prescription --}}
                            <form method="POST" action="{{ route('doctor.visits.prescribe', $visit) }}" class="w-full bg-violet-50 border border-violet-100 rounded-lg p-4 space-y-3">
                                @csrf
                                <div class="flex items-center gap-2 mb-2">
                                    <div class="w-7 h-7 rounded-lg bg-violet-100 text-violet-600 flex items-center justify-center">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>
                                    </div>
                                    <p class="text-sm font-semibold text-gray-900">Write Prescription</p>
                                </div>
                                <div class="space-y-2" id="prescription-items-{{ $visit->id }}">
                                    <div class="grid grid-cols-12 gap-2 prescription-row">
                                        <select name="items[0][medication_id]" class="col-span-3 border border-violet-200 rounded-lg px-2 py-2 text-xs focus:border-violet-500 focus:ring-violet-500" required>
                                            <option value="">Select drug</option>
                                            @foreach ($medications as $med)
                                                <option value="{{ $med->id }}">{{ $med->name }}</option>
                                            @endforeach
                                        </select>
                                        <input type="number" name="items[0][quantity]" placeholder="Qty" class="col-span-1 border border-violet-200 rounded-lg px-2 py-2 text-xs" required>
                                        <input type="text" name="items[0][dosage]" placeholder="Dose" class="col-span-2 border border-violet-200 rounded-lg px-2 py-2 text-xs" required>
                                        <input type="text" name="items[0][frequency]" placeholder="Frequency" class="col-span-2 border border-violet-200 rounded-lg px-2 py-2 text-xs" required>
                                        <input type="text" name="items[0][duration]" placeholder="Duration" class="col-span-2 border border-violet-200 rounded-lg px-2 py-2 text-xs" required>
                                        <input type="text" name="items[0][instructions]" placeholder="Instructions" class="col-span-2 border border-violet-200 rounded-lg px-2 py-2 text-xs">
                                    </div>
                                </div>
                                <button type="button" onclick="addPrescriptionItem({{ $visit->id }})" class="text-xs text-violet-600 hover:text-violet-700 font-medium">+ Add another drug</button>
                                <div class="flex justify-end">
                                    <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2 bg-violet-600 hover:bg-violet-700 text-white text-xs font-semibold rounded-lg transition-colors">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                        Send to Pharmacy
                                    </button>
                                </div>
                            </form>

                            {{-- Complete Visit --}}
                            <form method="POST" action="{{ route('doctor.visits.complete', $visit) }}" onsubmit="return confirm('Complete this visit? The patient will be discharged.')">
                                @csrf
                                <button type="submit" class="w-full inline-flex items-center justify-center gap-1.5 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold rounded-lg transition-colors">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    Complete Visit
                                </button>
                            </form>
                        @endif
                    </div>
                </div>

                {{-- Previous Activity Section --}}
                @if ($visit->prescriptions->isNotEmpty() || $visit->labOrders->isNotEmpty())
                    <div class="mt-6 border-t border-gray-100 pt-4">
                        <p class="text-xs font-semibold text-gray-700 uppercase tracking-wide mb-3">Previous Activity</p>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @if ($visit->labOrders->isNotEmpty())
                                <div class="bg-sky-50 rounded-lg p-3">
                                    <p class="text-xs font-semibold text-sky-700 mb-2">Lab Orders</p>
                                    <ul class="space-y-1">
                                        @foreach ($visit->labOrders as $order)
                                            <li class="text-xs text-gray-700 flex items-center gap-1">
                                                <span class="w-1.5 h-1.5 rounded-full {{ $order->status === 'completed' ? 'bg-emerald-500' : 'bg-amber-500' }}"></span>
                                                Order #{{ $order->id }} — {{ $order->items->pluck('labTest.name')->implode(', ') }}
                                                <span class="text-[10px] text-gray-500">({{ $order->status }})</span>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                            @if ($visit->prescriptions->isNotEmpty())
                                <div class="bg-violet-50 rounded-lg p-3">
                                    <p class="text-xs font-semibold text-violet-700 mb-2">Prescriptions</p>
                                    <ul class="space-y-1">
                                        @foreach ($visit->prescriptions as $prescription)
                                            <li class="text-xs text-gray-700">
                                                {{ $prescription->items->pluck('medication.name')->implode(', ') }}
                                                <span class="text-[10px] text-gray-500">— {{ $prescription->created_at->format('M d, H:i') }}</span>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        </div>
    @empty
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-12 text-center">
            <div class="w-16 h-16 bg-emerald-50 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <h3 class="text-base font-semibold text-gray-900">No patients in queue</h3>
            <p class="text-sm text-gray-500 mt-1">Your waiting list is clear. Check Lab Results for patients returning from lab.</p>
            <a href="{{ route('doctor.lab-results') }}" class="inline-flex items-center gap-2 mt-4 px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white text-sm font-medium rounded-lg transition-colors">
                View Lab Results
            </a>
        </div>
    @endforelse
</div>

<script>
function addPrescriptionItem(visitId) {
    const container = document.getElementById('prescription-items-' + visitId);
    const index = container.children.length;
    const row = document.createElement('div');
    row.className = 'grid grid-cols-12 gap-2 prescription-row';
    row.innerHTML = `
        <select name="items[${index}][medication_id]" class="col-span-3 border border-violet-200 rounded-lg px-2 py-2 text-xs focus:border-violet-500 focus:ring-violet-500" required>
            <option value="">Select drug</option>
            @foreach ($medications as $med)
                <option value="{{ $med->id }}">{{ $med->name }}</option>
            @endforeach
        </select>
        <input type="number" name="items[${index}][quantity]" placeholder="Qty" class="col-span-1 border border-violet-200 rounded-lg px-2 py-2 text-xs" required>
        <input type="text" name="items[${index}][dosage]" placeholder="Dose" class="col-span-2 border border-violet-200 rounded-lg px-2 py-2 text-xs" required>
        <input type="text" name="items[${index}][frequency]" placeholder="Frequency" class="col-span-2 border border-violet-200 rounded-lg px-2 py-2 text-xs" required>
        <input type="text" name="items[${index}][duration]" placeholder="Duration" class="col-span-2 border border-violet-200 rounded-lg px-2 py-2 text-xs" required>
        <input type="text" name="items[${index}][instructions]" placeholder="Instructions" class="col-span-2 border border-violet-200 rounded-lg px-2 py-2 text-xs">
        <button type="button" onclick="this.closest('.prescription-row').remove()" class="col-span-1 text-red-500 hover:text-red-700">
            <svg class="w-4 h-4 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
        </button>
    `;
    container.appendChild(row);
}
</script>
@endsection
