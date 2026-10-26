@extends('layouts.dashboard')

@section('title', 'Appointments - ' . config('app.name', 'Laravel'))
@section('page_title', 'Master Appointments')

@section('content')
<div class="space-y-5">
    <div class="bg-slate-800 rounded-md p-6 text-white">
        <div class="flex flex-wrap items-end justify-between gap-3">
            <div>
                <p class="text-[10px] uppercase tracking-wider text-slate-400 mb-1">Scheduling</p>
                <h1 class="text-xl font-bold">Appointments</h1>
                <p class="text-xs text-slate-400 mt-1">Manage patient appointments and schedules.</p>
            </div>
            <button type="button" onclick="openAddAppointmentPanel()" class="inline-flex items-center gap-1.5 bg-white hover:bg-gray-100 text-slate-800 text-xs font-semibold px-3 py-1.5 rounded transition-colors">
                <i class="hgi-stroke hgi-add-01 text-sm"></i>
                New Appointment
            </button>
        </div>
    </div>

    <div class="bg-white rounded-md border border-gray-200 p-2">
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
                <a href="{{ route('appointments.index', array_merge(request()->except(['page', 'date']), ['filter' => $key])) }}" class="px-3 py-1.5 rounded text-xs font-medium transition-colors {{ $filter === $key ? 'bg-slate-800 text-white' : 'text-gray-600 hover:bg-gray-100' }}">
                    {{ $label }}
                    @if ($key !== 'all')
                        <span class="ml-1.5 text-[10px] px-1.5 py-0.5 rounded-full {{ $filter === $key ? 'bg-white/20 text-white' : 'bg-gray-100 text-gray-500' }}">{{ $stats[$key] ?? 0 }}</span>
                    @endif
                </a>
            @endforeach
            <form method="GET" action="{{ route('appointments.index') }}" class="flex items-center gap-2 ml-auto">
                <input type="hidden" name="filter" value="date">
                <x-ui.input type="text" data-fp-date name="date" value="{{ $date ?? '' }}" class="border border-gray-200 rounded px-3 py-1.5 text-sm focus:ring-2 focus:ring-slate-300 focus:outline-none" />
                <button type="submit" class="px-3 py-1.5 bg-slate-800 text-white text-sm rounded hover:bg-slate-700">Go</button>
            </form>
        </div>
    </div>

    <div class="bg-white rounded-md border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-50 text-[10px] uppercase text-gray-400">
                    <tr>
                        <th class="px-5 py-3">Time</th>
                        <th class="px-5 py-3">Patient</th>
                        <th class="px-5 py-3">Doctor</th>
                        <th class="px-5 py-3">Type</th>
                        <th class="px-5 py-3">Status</th>
                        <th class="px-5 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $grouped = $appointments->groupBy(fn($a) => $a->scheduled_at->format('Y-m-d'));
                    @endphp
                    @forelse ($grouped as $dateKey => $dayAppointments)
                        <tr class="bg-gray-50 border-y border-gray-100">
                            <td colspan="6" class="px-5 py-2">
                                <div class="flex items-center gap-2">
                                    <i class="hgi-stroke hgi-calendar-03 text-base text-gray-400"></i>
                                    <span class="font-semibold text-gray-900 text-sm">
                                        @php $d = \Carbon\Carbon::parse($dateKey); @endphp
                                        {{ $d->isToday() ? 'Today' : ($d->isTomorrow() ? 'Tomorrow' : $d->format('l, d M Y')) }}
                                    </span>
                                    <span class="text-[10px] text-gray-500 bg-gray-100 px-2 py-0.5 rounded-full">{{ $dayAppointments->count() }} appointment{{ $dayAppointments->count() > 1 ? 's' : '' }}</span>
                                    <button type="button" onclick="sendBulkSms('{{ $dateKey }}', {{ $dayAppointments->count() }})" class="ml-auto inline-flex items-center gap-1.5 text-xs font-medium text-slate-700 bg-gray-100 hover:bg-gray-200 px-3 py-1.5 rounded transition-colors">
                                        <i class="hgi-stroke hgi-message-01 text-sm"></i>
                                        Send SMS to All
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @foreach ($dayAppointments as $appointment)
                            <tr class="group hover:bg-gray-50/50 transition-colors border-b border-gray-50">
                                <td class="px-5 py-3 text-gray-700 font-medium whitespace-nowrap">
                                    {{ $appointment->scheduled_at->format('H:i') }}
                                </td>
                                <td class="px-5 py-3">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-slate-100 text-slate-600 flex items-center justify-center text-xs font-bold">
                                            {{ strtoupper(substr($appointment->patient->first_name, 0, 1)) }}
                                        </div>
                                        <button type="button" data-popover-target="patient-popover-{{ $appointment->patient->id }}" class="font-medium text-gray-900 hover:text-slate-700 transition-colors" onclick="event.stopPropagation()">
                                            {{ $appointment->patient->fullName() }}
                                        </button>
                                    </div>
                                    <div data-popover id="patient-popover-{{ $appointment->patient->id }}" role="tooltip" class="absolute z-50 invisible inline-block w-80 text-sm text-gray-700 transition-opacity duration-300 bg-white border border-gray-200 rounded-md shadow-lg opacity-0">
                                        <div class="p-4">
                                            <div class="flex items-start gap-3">
                                                <div class="w-12 h-12 rounded-full bg-slate-100 text-slate-600 flex items-center justify-center text-lg font-bold shrink-0">
                                                    {{ strtoupper(substr($appointment->patient->first_name, 0, 1)) }}
                                                </div>
                                                <div class="flex-1 min-w-0">
                                                    <p class="text-base font-semibold text-gray-900 truncate">{{ $appointment->patient->fullName() }}</p>
                                                    <p class="text-xs text-gray-500 mb-2">MRN: {{ $appointment->patient->mrn }}</p>
                                                    <div class="space-y-1.5">
                                                        @if ($appointment->patient->date_of_birth)
                                                            <div class="flex items-center gap-2 text-xs">
                                                                <i class="hgi-stroke hgi-calendar-03 text-sm text-gray-400"></i>
                                                                <span>{{ $appointment->patient->date_of_birth->format('d M Y') }} ({{ $appointment->patient->date_of_birth->age }} years)</span>
                                                            </div>
                                                        @endif
                                                        @if ($appointment->patient->gender)
                                                            <div class="flex items-center gap-2 text-xs">
                                                                <i class="hgi-stroke hgi-user text-sm text-gray-400"></i>
                                                                <span class="capitalize">{{ $appointment->patient->gender }}</span>
                                                            </div>
                                                        @endif
                                                        @if ($appointment->patient->phone)
                                                            <div class="flex items-center gap-2 text-xs">
                                                                <i class="hgi-stroke hgi-call-02 text-sm text-gray-400"></i>
                                                                <span>{{ $appointment->patient->phone }}</span>
                                                            </div>
                                                        @endif
                                                        @if ($appointment->patient->blood_group)
                                                            <div class="flex items-center gap-2 text-xs">
                                                                <i class="hgi-stroke hgi-favourite text-sm text-gray-400"></i>
                                                                <span class="px-1.5 py-0.5 bg-red-100 text-red-700 rounded text-xs font-medium">{{ $appointment->patient->blood_group }}</span>
                                                            </div>
                                                        @endif
                                                        @if ($appointment->patient->allergies)
                                                            <div class="flex items-start gap-2 text-xs">
                                                                <i class="hgi-stroke hgi-alert-02 text-sm text-gray-400 mt-0.5"></i>
                                                                <span class="text-amber-600">{{ $appointment->patient->allergies }}</span>
                                                            </div>
                                                        @endif
                                                        @if ($appointment->patient->address)
                                                            <div class="flex items-start gap-2 text-xs">
                                                                <i class="hgi-stroke hgi-location-01 text-sm text-gray-400 mt-0.5"></i>
                                                                <span class="text-gray-500 truncate">{{ $appointment->patient->address }}</span>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="flex gap-2 mt-4 pt-3 border-t border-gray-100">
                                                <a href="{{ route('patients.show', $appointment->patient) }}" class="flex-1 text-center text-xs font-medium text-white bg-slate-800 hover:bg-slate-700 py-2 rounded transition-colors">
                                                    View Profile
                                                </a>
                                                <a href="{{ route('clinical-records.create', ['patient_id' => $appointment->patient->id]) }}" class="flex-1 text-center text-xs font-medium text-gray-700 bg-gray-50 border border-gray-200 hover:bg-gray-100 py-2 rounded transition-colors">
                                                    Add Record
                                                </a>
                                            </div>
                                        </div>
                                        <div data-popper-arrow></div>
                                    </div>
                                </td>
                                <td class="px-5 py-3 text-xs text-gray-600">{{ $appointment->doctor?->name ?? 'Not assigned' }}</td>
                                <td class="px-5 py-3 capitalize text-xs text-gray-600">{{ $appointment->type }}</td>
                                <td class="px-5 py-3">
                                    @php
                                        $statusDots = [
                                            'scheduled' => 'bg-slate-400',
                                            'confirmed' => 'bg-emerald-500',
                                            'completed' => 'bg-slate-700',
                                            'cancelled' => 'bg-red-500',
                                            'no_show' => 'bg-slate-300',
                                        ];
                                        $dot = $statusDots[$appointment->status] ?? 'bg-slate-400';
                                    @endphp
                                    <span class="inline-flex items-center gap-1.5 text-[10px] font-medium capitalize text-gray-600">
                                        <span class="w-1.5 h-1.5 rounded-full {{ $dot }}"></span>
                                        {{ str_replace('_', ' ', $appointment->status) }}
                                    </span>
                                    @if($appointment->invoice)
                                        @php
                                            $invDots = [
                                                'paid' => 'bg-emerald-500',
                                                'partial' => 'bg-amber-500',
                                                'unpaid' => 'bg-red-500',
                                            ];
                                            $invDot = $invDots[$appointment->invoice->status] ?? 'bg-slate-400';
                                        @endphp
                                        <span class="inline-flex items-center gap-1.5 text-[10px] font-medium text-gray-600">
                                            <span class="w-1.5 h-1.5 rounded-full {{ $invDot }}"></span>
                                            {{ $appointment->invoice->status === 'paid' ? 'Paid' : ($appointment->invoice->status === 'partial' ? 'Partial' : 'Unpaid') }}
                                        </span>
                                    @endif
                                </td>
                                <td class="px-5 py-3">
                                    <div class="flex items-center justify-end gap-1">
                                        <button type="button" onclick="sendSingleSms('{{ route('appointments.send-sms', $appointment) }}', '{{ $appointment->patient->fullName() }}')" class="action-icon group/icon relative p-1.5 text-slate-600 hover:bg-gray-100 rounded transition-colors" title="Send SMS">
                                            <i class="hgi-stroke hgi-message-01 text-base"></i>
                                            <span class="absolute -top-8 left-1/2 -translate-x-1/2 px-2 py-1 bg-gray-900 text-white text-[10px] rounded opacity-0 group-hover/icon:opacity-100 transition-opacity whitespace-nowrap pointer-events-none">Send SMS</span>
                                        </button>
                                        <button type="button" onclick="openEditAppointmentPanel('{{ route('appointments.edit', $appointment) }}')" class="action-icon group/icon relative p-1.5 text-gray-500 hover:bg-gray-100 rounded transition-colors" title="Edit">
                                            <i class="hgi-stroke hgi-pencil-edit-01 text-base"></i>
                                            <span class="absolute -top-8 left-1/2 -translate-x-1/2 px-2 py-1 bg-gray-900 text-white text-[10px] rounded opacity-0 group-hover/icon:opacity-100 transition-opacity whitespace-nowrap pointer-events-none">Edit</span>
                                        </button>
                                        <form method="POST" action="{{ route('appointments.destroy', $appointment) }}" data-ajax data-confirm="Cancel this appointment?" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="action-icon group/icon relative p-1.5 text-red-600 hover:bg-red-50 rounded transition-colors" title="Cancel">
                                                <i class="hgi-stroke hgi-cancel-circle text-base"></i>
                                                <span class="absolute -top-8 left-1/2 -translate-x-1/2 px-2 py-1 bg-gray-900 text-white text-[10px] rounded opacity-0 group-hover/icon:opacity-100 transition-opacity whitespace-nowrap pointer-events-none">Cancel</span>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    @empty
                        <tr><td colspan="6" class="px-5 py-8 text-center text-gray-400 text-xs">
                            <div class="flex flex-col items-center gap-2">
                                <i class="hgi-stroke hgi-calendar-03 text-4xl text-gray-300"></i>
                                <p>No appointments found</p>
                            </div>
                        </td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-5 py-3 border-t border-gray-100">
            {{ $appointments->links() }}
        </div>
    </div>
</div>

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
                    <i class="hgi-stroke hgi-cancel-01 text-2xl"></i>
                </button>
            </div>
            <div class="flex-1 overflow-y-auto p-6" id="appointmentSlideContent"></div>
        </div>
    </div>
</div>


@endsection

@push('scripts')
<script>
    // Toggle payment fields based on checkbox
    document.addEventListener('change', function(e) {
        if (e.target && e.target.id === 'appt_collect_payment') {
            const fields = document.getElementById('appt_payment_fields');
            if (fields) {
                fields.style.display = e.target.checked ? 'grid' : 'none';
            }
        }
    });

    // SMS sending functions
    function sendSingleSms(url, patientName) {
        Swal.fire({
            icon: 'question',
            title: 'Send SMS?',
            text: 'Send appointment reminder SMS to ' + patientName + '?',
            showCancelButton: true,
            confirmButtonText: 'Yes, send',
            cancelButtonText: 'Cancel',
            confirmButtonColor: '#0f172a',
        }).then((result) => {
            if (!result.isConfirmed) return;
            Swal.fire({ title: 'Sending...', didOpen: () => Swal.showLoading() });
            fetch(url, {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
            })
            .then(r => r.json().then(data => ({ ok: r.ok, data })))
            .then(({ ok, data }) => {
                Swal.fire({
                    icon: ok && data.success ? 'success' : 'error',
                    title: ok && data.success ? 'Sent!' : 'Failed',
                    text: data.message || data.error || 'Unknown response',
                    timer: 3000,
                    showConfirmButton: true,
                });
            })
            .catch(err => {
                Swal.fire({ icon: 'error', title: 'Error', text: 'Failed to send SMS.' });
            });
        });
    }

    function sendBulkSms(date, count) {
        Swal.fire({
            icon: 'question',
            title: 'Send Bulk SMS?',
            text: 'Send appointment reminder SMS to all ' + count + ' patient(s) with appointments on this day?',
            showCancelButton: true,
            confirmButtonText: 'Yes, send to all',
            cancelButtonText: 'Cancel',
            confirmButtonColor: '#0f172a',
        }).then((result) => {
            if (!result.isConfirmed) return;
            Swal.fire({ title: 'Sending ' + count + ' SMS...', didOpen: () => Swal.showLoading() });
            fetch('{{ route("appointments.bulk-sms") }}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                body: JSON.stringify({ date: date }),
            })
            .then(r => r.json().then(data => ({ ok: r.ok, data })))
            .then(({ ok, data }) => {
                Swal.fire({
                    icon: ok && data.success ? 'success' : 'warning',
                    title: ok && data.success ? 'Bulk SMS Complete' : 'Issues Found',
                    text: data.message || data.error || 'Unknown response',
                    timer: 5000,
                    showConfirmButton: true,
                });
            })
            .catch(err => {
                Swal.fire({ icon: 'error', title: 'Error', text: 'Failed to send bulk SMS.' });
            });
        });
    }

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
            const submitBtn = document.getElementById('appt_submit_btn');
            submitBtn.disabled = true;
            submitBtn.textContent = 'Saving...';
            fetch(action, {
                method: 'POST',
                body: formData,
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
            })
            .then(r => {
                if (!r.ok) {
                    return r.json().then(err => { throw err; });
                }
                return r.json();
            })
            .then(data => {
                const icon = data.sms && !data.sms.success ? 'warning' : 'success';
                Swal.fire({ icon: icon, title: icon === 'success' ? 'Success' : 'SMS Issue', text: data.message || successMessage, timer: 3000, showConfirmButton: true });
                closeAppointmentSlideOver();
                setTimeout(() => reloadPageContent(), 1000);
            })
            .catch(err => {
                const msg = err?.message || (typeof err === 'string' ? err : 'Failed to save appointment.');
                Swal.fire({ icon: 'error', title: 'Error', text: msg });
            })
            .finally(() => {
                submitBtn.disabled = false;
                submitBtn.textContent = 'Save Appointment';
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
