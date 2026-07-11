@extends('layouts.dashboard')

@section('title', 'Appointments - ' . config('app.name', 'Laravel'))
@section('page_title', 'Master Appointments')

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-lg font-bold text-gray-900">Appointments</h2>
            <p class="text-sm text-gray-500">Manage patient appointments and schedules</p>
        </div>
        <button type="button" onclick="openAddAppointmentPanel()" class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium px-4 py-2 rounded-lg shadow-sm hover:shadow transition-all">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            New Appointment
        </button>
    </div>

    {{-- Date Filter Tabs --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-2">
        <div class="flex flex-wrap items-center gap-2">
            @php
                $tabs = [
                    'today' => 'Today',
                    'tomorrow' => 'Tomorrow',
                    'week' => 'This Week',
                    'upcoming' => 'Upcoming',
                    'past' => 'Past',
                    'all' => 'All',
                ];
            @endphp
            @foreach ($tabs as $key => $label)
                <a href="{{ route('appointments.index', array_merge(request()->except(['page', 'date']), ['filter' => $key])) }}" class="px-4 py-2 rounded-lg text-sm font-medium transition-all {{ $filter === $key ? 'bg-emerald-600 text-white shadow-sm' : 'text-gray-600 hover:bg-gray-100' }}">
                    {{ $label }}
                    @if ($key !== 'all')
                        <span class="ml-1.5 text-[10px] px-1.5 py-0.5 rounded-full {{ $filter === $key ? 'bg-white/20 text-white' : 'bg-gray-200 text-gray-600' }}">{{ $stats[$key] ?? 0 }}</span>
                    @endif
                </a>
            @endforeach
            <form method="GET" action="{{ route('appointments.index') }}" class="flex items-center gap-2 ml-auto">
                <input type="hidden" name="filter" value="date">
                <input type="date" name="date" value="{{ $date ?? '' }}" class="border border-gray-200 rounded-lg px-3 py-1.5 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                <button type="submit" class="px-3 py-1.5 bg-emerald-600 text-white text-sm rounded-lg hover:bg-emerald-700">Go</button>
            </form>
        </div>
    </div>

    {{-- Calendar-style Appointments List --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                    <tr>
                        <th class="px-6 py-3">Time</th>
                        <th class="px-6 py-3">Patient</th>
                        <th class="px-6 py-3">Doctor</th>
                        <th class="px-6 py-3">Type</th>
                        <th class="px-6 py-3">Status</th>
                        <th class="px-6 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $grouped = $appointments->groupBy(fn($a) => $a->scheduled_at->format('Y-m-d'));
                    @endphp
                    @forelse ($grouped as $dateKey => $dayAppointments)
                        <tr class="bg-emerald-50/60 border-y border-gray-100">
                            <td colspan="6" class="px-6 py-2">
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    <span class="font-semibold text-emerald-900 text-sm">
                                        @php $d = \Carbon\Carbon::parse($dateKey); @endphp
                                        {{ $d->isToday() ? 'Today' : ($d->isTomorrow() ? 'Tomorrow' : $d->format('l, d M Y')) }}
                                    </span>
                                    <span class="text-xs text-emerald-600 bg-emerald-100 px-2 py-0.5 rounded-full">{{ $dayAppointments->count() }} appointment{{ $dayAppointments->count() > 1 ? 's' : '' }}</span>
                                </div>
                            </td>
                        </tr>
                        @foreach ($dayAppointments as $appointment)
                            <tr class="group hover:bg-emerald-50/40 transition-colors border-b border-gray-50">
                                <td class="px-6 py-3.5 text-gray-700 font-medium whitespace-nowrap">
                                    {{ $appointment->scheduled_at->format('H:i') }}
                                </td>
                                <td class="px-6 py-3.5">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center text-xs font-bold">
                                            {{ strtoupper(substr($appointment->patient->first_name, 0, 1)) }}
                                        </div>
                                        <button type="button" data-popover-target="patient-popover-{{ $appointment->patient->id }}" class="font-medium text-gray-900 hover:text-emerald-600 transition-colors" onclick="event.stopPropagation()">
                                            {{ $appointment->patient->fullName() }}
                                        </button>
                                    </div>
                                    {{-- Patient Popover --}}
                                    <div data-popover id="patient-popover-{{ $appointment->patient->id }}" role="tooltip" class="absolute z-50 invisible inline-block w-80 text-sm text-gray-700 transition-opacity duration-300 bg-white border border-gray-200 rounded-xl shadow-lg opacity-0">
                                        <div class="p-4">
                                            <div class="flex items-start gap-3">
                                                <div class="w-12 h-12 rounded-full bg-gradient-to-br from-emerald-400 to-emerald-600 flex items-center justify-center text-white text-lg font-bold shrink-0">
                                                    {{ strtoupper(substr($appointment->patient->first_name, 0, 1)) }}
                                                </div>
                                                <div class="flex-1 min-w-0">
                                                    <p class="text-base font-semibold text-gray-900 truncate">{{ $appointment->patient->fullName() }}</p>
                                                    <p class="text-xs text-gray-500 mb-2">MRN: {{ $appointment->patient->mrn }}</p>
                                                    <div class="space-y-1.5">
                                                        @if ($appointment->patient->date_of_birth)
                                                            <div class="flex items-center gap-2 text-xs">
                                                                <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                                                <span>{{ $appointment->patient->date_of_birth->format('d M Y') }} ({{ $appointment->patient->date_of_birth->age }} years)</span>
                                                            </div>
                                                        @endif
                                                        @if ($appointment->patient->gender)
                                                            <div class="flex items-center gap-2 text-xs">
                                                                <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                                                <span class="capitalize">{{ $appointment->patient->gender }}</span>
                                                            </div>
                                                        @endif
                                                        @if ($appointment->patient->phone)
                                                            <div class="flex items-center gap-2 text-xs">
                                                                <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                                                <span>{{ $appointment->patient->phone }}</span>
                                                            </div>
                                                        @endif
                                                        @if ($appointment->patient->blood_group)
                                                            <div class="flex items-center gap-2 text-xs">
                                                                <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12.01 6.001C6.5 1 1 8 5.782 13.001L12.011 20l6.23-7C23 8 17.5 1 12.01 6.002Z"/></svg>
                                                                <span class="px-1.5 py-0.5 bg-red-100 text-red-700 rounded text-xs font-medium">{{ $appointment->patient->blood_group }}</span>
                                                            </div>
                                                        @endif
                                                        @if ($appointment->patient->allergies)
                                                            <div class="flex items-start gap-2 text-xs">
                                                                <svg class="w-3.5 h-3.5 text-gray-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                                                <span class="text-amber-600">{{ $appointment->patient->allergies }}</span>
                                                            </div>
                                                        @endif
                                                        @if ($appointment->patient->address)
                                                            <div class="flex items-start gap-2 text-xs">
                                                                <svg class="w-3.5 h-3.5 text-gray-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                                                <span class="text-gray-500 truncate">{{ $appointment->patient->address }}</span>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="flex gap-2 mt-4 pt-3 border-t border-gray-100">
                                                <a href="{{ route('patients.show', $appointment->patient) }}" class="flex-1 text-center text-xs font-medium text-emerald-600 bg-emerald-50 hover:bg-emerald-100 py-2 rounded-lg transition-colors">
                                                    View Profile
                                                </a>
                                                <a href="{{ route('clinical-records.create', ['patient_id' => $appointment->patient->id]) }}" class="flex-1 text-center text-xs font-medium text-blue-600 bg-blue-50 hover:bg-blue-100 py-2 rounded-lg transition-colors">
                                                    Add Record
                                                </a>
                                            </div>
                                        </div>
                                        <div data-popper-arrow></div>
                                    </div>
                                </td>
                                <td class="px-6 py-3.5 text-gray-700">{{ $appointment->doctor?->name ?? 'Not assigned' }}</td>
                                <td class="px-6 py-3.5 capitalize text-gray-700">{{ $appointment->type }}</td>
                                <td class="px-6 py-3.5">
                                    @php
                                        $statusConfig = [
                                            'scheduled' => ['bg-sky-100 text-sky-700', 'bg-sky-500'],
                                            'confirmed' => ['bg-emerald-100 text-emerald-700', 'bg-emerald-500'],
                                            'completed' => ['bg-blue-100 text-blue-700', 'bg-blue-500'],
                                            'cancelled' => ['bg-red-100 text-red-700', 'bg-red-500'],
                                            'no_show' => ['bg-gray-100 text-gray-700', 'bg-gray-500'],
                                        ];
                                        $config = $statusConfig[$appointment->status] ?? ['bg-gold-100 text-gold-700', 'bg-gold-500'];
                                    @endphp
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium capitalize {{ $config[0] }}">
                                        <span class="w-1.5 h-1.5 rounded-full {{ $config[1] }}"></span>
                                        {{ str_replace('_', ' ', $appointment->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-3.5">
                                    <div class="flex items-center justify-end gap-1">
                                        <button type="button" onclick="openEditAppointmentPanel('{{ route('appointments.edit', $appointment) }}')" class="action-icon group/icon relative p-2 text-blue-600 hover:bg-blue-100 rounded-lg transition-colors" title="Edit">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.43-9.525l-9.17 9.17a2 2 0 00-.586 1.414V17a1 1 0 001 1h2.828a2 2 0 001.414-.586l9.17-9.17a2 2 0 000-2.828l-1.414-1.414a2 2 0 00-2.828 0z"/></svg>
                                            <span class="absolute -top-8 left-1/2 -translate-x-1/2 px-2 py-1 bg-gray-900 text-white text-[10px] rounded opacity-0 group-hover/icon:opacity-100 transition-opacity whitespace-nowrap pointer-events-none">Edit</span>
                                        </button>
                                        <form method="POST" action="{{ route('appointments.destroy', $appointment) }}" data-ajax data-confirm="Cancel this appointment?" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="action-icon group/icon relative p-2 text-red-600 hover:bg-red-100 rounded-lg transition-colors" title="Cancel">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                <span class="absolute -top-8 left-1/2 -translate-x-1/2 px-2 py-1 bg-gray-900 text-white text-[10px] rounded opacity-0 group-hover/icon:opacity-100 transition-opacity whitespace-nowrap pointer-events-none">Cancel</span>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    @empty
                        <tr><td colspan="6" class="px-6 py-10 text-center text-gray-400">
                            <div class="flex flex-col items-center gap-2">
                                <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                <p>No appointments found</p>
                            </div>
                        </td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-100">
            {{ $appointments->links() }}
        </div>
    </div>
</div>

{{-- Slide-over Panel --}}
<div id="appointmentSlideOver" class="fixed inset-0 z-50 hidden" aria-hidden="true">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm transition-opacity opacity-0" id="appointmentBackdrop" onclick="closeAppointmentSlideOver()"></div>
    <div class="absolute inset-y-0 right-0 w-full max-w-lg transform translate-x-full transition-transform duration-300 ease-out" id="appointmentPanel">
        <div class="h-full bg-white shadow-2xl flex flex-col">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between bg-gray-50/50">
                <div>
                    <h3 class="text-lg font-bold text-gray-900" id="appointmentSlideTitle">Appointment</h3>
                    <p class="text-xs text-gray-500" id="appointmentSlideSubtitle">Schedule appointment</p>
                </div>
                <button onclick="closeAppointmentSlideOver()" class="text-gray-400 hover:text-gray-600 p-1 rounded-lg hover:bg-gray-100 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="flex-1 overflow-y-auto p-6" id="appointmentSlideContent"></div>
        </div>
    </div>
</div>

<template id="appointmentFormTemplate">
    <form id="appointmentForm" method="POST" action="" class="space-y-4">
        @csrf
        <input type="hidden" name="_method" id="appt_method" value="POST">
        <div class="grid grid-cols-2 gap-4">
            <div class="col-span-2">
                <label class="block text-xs font-medium text-gray-700 mb-1">Patient <span class="text-red-500">*</span></label>
                <select name="patient_id" id="appt_patient_id" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 bg-white" required>
                    <option value="">Select patient</option>
                </select>
            </div>
            <div class="col-span-2 sm:col-span-1">
                <label class="block text-xs font-medium text-gray-700 mb-1">Doctor</label>
                <select name="doctor_id" id="appt_doctor_id" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 bg-white">
                    <option value="">Select doctor</option>
                </select>
            </div>
            <div class="col-span-2 sm:col-span-1">
                <label class="block text-xs font-medium text-gray-700 mb-1">Date <span class="text-red-500">*</span></label>
                <input type="date" name="appointment_date" id="appt_date" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" required>
            </div>
            <div class="col-span-2 sm:col-span-1">
                <label class="block text-xs font-medium text-gray-700 mb-1">Time <span class="text-red-500">*</span></label>
                <input type="time" name="start_time" id="appt_time" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" required>
            </div>
            <div class="col-span-2 sm:col-span-1">
                <label class="block text-xs font-medium text-gray-700 mb-1">Type <span class="text-red-500">*</span></label>
                <select name="type" id="appt_type" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 bg-white" required>
                    <option value="general">General</option>
                    <option value="followup">Follow-up</option>
                    <option value="emergency">Emergency</option>
                </select>
            </div>
            <div class="col-span-2 sm:col-span-1">
                <label class="block text-xs font-medium text-gray-700 mb-1">Status <span class="text-red-500">*</span></label>
                <select name="status" id="appt_status" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 bg-white" required>
                    <option value="scheduled">Scheduled</option>
                    <option value="confirmed">Confirmed</option>
                    <option value="completed">Completed</option>
                    <option value="cancelled">Cancelled</option>
                    <option value="no_show">No Show</option>
                </select>
            </div>
            <div class="col-span-2">
                <label class="block text-xs font-medium text-gray-700 mb-1">Notes</label>
                <textarea name="notes" id="appt_notes" rows="3" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"></textarea>
            </div>
        </div>
        <div class="flex justify-end gap-2 pt-4 border-t border-gray-100 mt-4">
            <button type="button" onclick="closeAppointmentSlideOver()" class="px-4 py-2 text-sm text-gray-600 hover:bg-gray-100 rounded-lg">Cancel</button>
            <button type="submit" class="px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700" id="appt_submit_btn">Save Appointment</button>
        </div>
    </form>
</template>

@push('scripts')
<script>
    // Initialize Flowbite popovers
    document.addEventListener('DOMContentLoaded', function() {
        const popoverTriggers = document.querySelectorAll('[data-popover-target]');
        popoverTriggers.forEach(trigger => {
            const targetId = trigger.getAttribute('data-popover-target');
            const popover = document.getElementById(targetId);
            if (popover) {
                trigger.addEventListener('click', function(e) {
                    e.stopPropagation();
                    popover.classList.toggle('invisible');
                    popover.classList.toggle('opacity-0');
                });
            }
        });

        // Close popovers when clicking outside
        document.addEventListener('click', function(e) {
            if (!e.target.closest('[data-popover]') && !e.target.closest('[data-popover-target]')) {
                document.querySelectorAll('[data-popover]').forEach(popover => {
                    popover.classList.add('invisible', 'opacity-0');
                });
            }
        });
    });

    const apptSlide = document.getElementById('appointmentSlideOver');
    const apptBackdrop = document.getElementById('appointmentBackdrop');
    const apptPanel = document.getElementById('appointmentPanel');
    const apptContent = document.getElementById('appointmentSlideContent');

    function openAppointmentSlideOver(title, subtitle, html) {
        apptSlide.classList.remove('hidden');
        document.getElementById('appointmentSlideTitle').textContent = title;
        document.getElementById('appointmentSlideSubtitle').textContent = subtitle;
        apptContent.innerHTML = html;
        setTimeout(() => {
            apptBackdrop.classList.remove('opacity-0');
            apptPanel.classList.remove('translate-x-full');
        }, 10);
        document.body.style.overflow = 'hidden';
    }

    function closeAppointmentSlideOver() {
        apptBackdrop.classList.add('opacity-0');
        apptPanel.classList.add('translate-x-full');
        setTimeout(() => {
            apptSlide.classList.add('hidden');
            apptContent.innerHTML = '';
            document.body.style.overflow = '';
        }, 300);
    }

    function populateAppointmentSelects(patients, doctors) {
        const patientSelect = document.getElementById('appt_patient_id');
        const doctorSelect = document.getElementById('appt_doctor_id');
        patientSelect.innerHTML = '<option value="">Select patient</option>' +
            patients.map(p => `<option value="${p.id}">${p.first_name} ${p.last_name}</option>`).join('');
        doctorSelect.innerHTML = '<option value="">Select doctor</option>' +
            doctors.map(d => `<option value="${d.id}">${d.name}</option>`).join('');
    }

    function attachAppointmentForm(action, successMessage) {
        const form = document.getElementById('appointmentForm');
        form.action = action;
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(form);
            fetch(action, {
                method: 'POST',
                body: formData,
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
            })
            .then(r => r.json().catch(() => ({})))
            .then(data => {
                Swal.fire({ icon: 'success', title: 'Success', text: data.message || successMessage, timer: 1500, showConfirmButton: false });
                closeAppointmentSlideOver();
                setTimeout(() => reloadPageContent(), 1000);
            })
            .catch(err => {
                Swal.fire({ icon: 'error', title: 'Error', text: 'Failed to save appointment.' });
            });
        });
    }

    function reloadPageContent() {
        const url = window.location.href;
        fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(r => r.text())
            .then(html => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const newContent = doc.querySelector('main');
                const currentContent = document.querySelector('main');
                if (newContent && currentContent) {
                    currentContent.innerHTML = newContent.innerHTML;
                }
            })
            .catch(err => console.error('Failed to reload content:', err));
    }

    function resetAppointmentForm() {
        document.getElementById('appt_method').value = 'POST';
        document.getElementById('appt_patient_id').value = '';
        document.getElementById('appt_doctor_id').value = '';
        document.getElementById('appt_date').value = new Date().toISOString().split('T')[0];
        document.getElementById('appt_time').value = '08:00';
        document.getElementById('appt_type').value = 'general';
        document.getElementById('appt_status').value = 'scheduled';
        document.getElementById('appt_notes').value = '';
    }

    async function openAddAppointmentPanel() {
        const html = document.getElementById('appointmentFormTemplate').innerHTML;
        openAppointmentSlideOver('New Appointment', 'Schedule a new appointment', html);
        try {
            const res = await fetch('{{ route("appointments.create") }}', { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } });
            const data = await res.json();
            populateAppointmentSelects(data.patients, data.doctors);
            resetAppointmentForm();
            document.getElementById('appt_submit_btn').textContent = 'Save Appointment';
            attachAppointmentForm('{{ route("appointments.store") }}', 'Appointment scheduled successfully.');
        } catch (err) {
            apptContent.innerHTML = '<div class="text-center text-red-600 py-8">Failed to load form data.</div>';
        }
    }

    async function openEditAppointmentPanel(url) {
        const html = document.getElementById('appointmentFormTemplate').innerHTML;
        openAppointmentSlideOver('Edit Appointment', 'Update appointment details', html);
        try {
            const res = await fetch(url, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } });
            const data = await res.json();
            populateAppointmentSelects(data.patients, data.doctors);
            const a = data.appointment;
            document.getElementById('appt_method').value = 'PUT';
            document.getElementById('appt_patient_id').value = a.patient_id;
            document.getElementById('appt_doctor_id').value = a.doctor_id || '';
            document.getElementById('appt_date').value = a.scheduled_at ? a.scheduled_at.substring(0, 10) : '';
            document.getElementById('appt_time').value = a.scheduled_at ? a.scheduled_at.substring(11, 16) : '';
            document.getElementById('appt_type').value = a.type;
            document.getElementById('appt_status').value = a.status;
            document.getElementById('appt_notes').value = a.notes || '';
            document.getElementById('appt_submit_btn').textContent = 'Update Appointment';
            attachAppointmentForm(url.replace('/edit', ''), 'Appointment updated successfully.');
        } catch (err) {
            apptContent.innerHTML = '<div class="text-center text-red-600 py-8">Failed to load appointment details.</div>';
        }
    }
</script>
@endpush
@endsection
