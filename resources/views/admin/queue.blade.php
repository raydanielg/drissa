@extends('layouts.dashboard')

@section('title', 'Patient Queue - ' . config('app.name', 'Laravel'))
@section('page_title', 'Patient Queue - Admin View')

@section('content')
<div class="space-y-6">
    @if (session('status'))
        <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm">{{ session('status') }}</div>
    @endif

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-lg font-bold text-gray-900">Patient Queue</h2>
            <p class="text-sm text-gray-500">Today's patients - {{ today()->format('D, M j') }}</p>
        </div>
        <div class="flex items-center gap-2">
            <select id="doctorFilter" onchange="filterByDoctor()" class="text-xs border border-gray-200 rounded-lg px-3 py-2 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 bg-white">
                <option value="">All Doctors</option>
                @foreach($doctors as $d)
                    <option value="{{ $d->id }}">{{ $d->name }}</option>
                @endforeach
            </select>
        </div>
    </div>

    {{-- KPI Cards --}}
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl p-4 shadow-lg hover:shadow-xl transition-all hover:-translate-y-1 relative overflow-hidden">
            <div class="absolute top-0 right-0 w-16 h-16 bg-white/10 rounded-full -mr-8 -mt-8"></div>
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-2">
                    <svg class="w-6 h-6 text-blue-100" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </div>
                <div class="text-2xl font-bold text-white">{{ $stats['total'] }}</div>
                <div class="text-xs text-blue-100 font-medium">Total Patients</div>
            </div>
        </div>
        <div class="bg-gradient-to-br from-amber-500 to-amber-600 rounded-xl p-4 shadow-lg hover:shadow-xl transition-all hover:-translate-y-1 relative overflow-hidden">
            <div class="absolute top-0 right-0 w-16 h-16 bg-white/10 rounded-full -mr-8 -mt-8"></div>
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-2">
                    <svg class="w-6 h-6 text-amber-100" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div class="text-2xl font-bold text-white">{{ $stats['waiting'] }}</div>
                <div class="text-xs text-amber-100 font-medium">Waiting</div>
            </div>
        </div>
        <div class="bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-xl p-4 shadow-lg hover:shadow-xl transition-all hover:-translate-y-1 relative overflow-hidden">
            <div class="absolute top-0 right-0 w-16 h-16 bg-white/10 rounded-full -mr-8 -mt-8"></div>
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-2">
                    <svg class="w-6 h-6 text-emerald-100" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div class="text-2xl font-bold text-white">{{ $stats['with_doctor'] }}</div>
                <div class="text-xs text-emerald-100 font-medium">With Doctor</div>
            </div>
        </div>
        <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl p-4 shadow-lg hover:shadow-xl transition-all hover:-translate-y-1 relative overflow-hidden">
            <div class="absolute top-0 right-0 w-16 h-16 bg-white/10 rounded-full -mr-8 -mt-8"></div>
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-2">
                    <svg class="w-6 h-6 text-purple-100" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>
                </div>
                <div class="text-2xl font-bold text-white">{{ $stats['lab'] }}</div>
                <div class="text-xs text-purple-100 font-medium">Lab</div>
            </div>
        </div>
        <div class="bg-gradient-to-br from-cyan-500 to-cyan-600 rounded-xl p-4 shadow-lg hover:shadow-xl transition-all hover:-translate-y-1 relative overflow-hidden">
            <div class="absolute top-0 right-0 w-16 h-16 bg-white/10 rounded-full -mr-8 -mt-8"></div>
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-2">
                    <svg class="w-6 h-6 text-cyan-100" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>
                </div>
                <div class="text-2xl font-bold text-white">{{ $stats['pharmacy'] }}</div>
                <div class="text-xs text-cyan-100 font-medium">Pharmacy</div>
            </div>
        </div>
        <div class="bg-gradient-to-br from-red-500 to-red-600 rounded-xl p-4 shadow-lg hover:shadow-xl transition-all hover:-translate-y-1 relative overflow-hidden">
            <div class="absolute top-0 right-0 w-16 h-16 bg-white/10 rounded-full -mr-8 -mt-8"></div>
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-2">
                    <svg class="w-6 h-6 text-red-100" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div class="text-2xl font-bold text-white">{{ $stats['payment'] }}</div>
                <div class="text-xs text-red-100 font-medium">Payment</div>
            </div>
        </div>
    </div>

    {{-- Patients Table --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                    <tr>
                        <th class="px-6 py-3">Visit #</th>
                        <th class="px-6 py-3">Patient</th>
                        <th class="px-6 py-3">Doctor</th>
                        <th class="px-6 py-3">Status</th>
                        <th class="px-6 py-3">Registered</th>
                        <th class="px-6 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($visits as $visit)
                        <tr class="border-t border-gray-100 hover:bg-gray-50/50 transition-colors visit-row" data-doctor-id="{{ $visit->doctor_id ?? '' }}">
                            <td class="px-6 py-3 font-medium">{{ $visit->visit_number }}</td>
                            <td class="px-6 py-3">
                                <div class="flex items-center gap-2">
                                    <div class="w-8 h-8 rounded-full bg-gradient-to-br from-emerald-500 to-emerald-700 flex items-center justify-center text-white text-xs font-bold shadow-sm">
                                        {{ strtoupper(substr($visit->patient->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="text-xs text-gray-900 font-medium">{{ $visit->patient->fullName() }}</div>
                                        <div class="text-[10px] text-gray-400">{{ $visit->patient->mrn }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-3">
                                <div class="flex items-center gap-2">
                                    <span class="text-xs text-gray-700">{{ $visit->doctor?->name ?? 'Unassigned' }}</span>
                                    <button type="button" onclick="openChangeDoctorModal({{ $visit->id }}, '{{ $visit->doctor_id ?? '' }}')" class="text-emerald-600 hover:text-emerald-700">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                                    </button>
                                </div>
                            </td>
                            <td class="px-6 py-3">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-medium capitalize
                                    {{ $visit->status === 'completed' ? 'bg-emerald-100 text-emerald-700' : ($visit->status === 'waiting_for_doctor' ? 'bg-amber-100 text-amber-700' : ($visit->status === 'with_doctor' ? 'bg-emerald-100 text-emerald-700' : ($visit->status === 'waiting_for_lab' ? 'bg-purple-100 text-purple-700' : ($visit->status === 'waiting_for_pharmacy' ? 'bg-cyan-100 text-cyan-700' : 'bg-red-100 text-red-700')))) }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $visit->status === 'completed' ? 'bg-emerald-500' : ($visit->status === 'waiting_for_doctor' ? 'bg-amber-500' : ($visit->status === 'with_doctor' ? 'bg-emerald-500' : ($visit->status === 'waiting_for_lab' ? 'bg-purple-500' : ($visit->status === 'waiting_for_pharmacy' ? 'bg-cyan-500' : 'bg-red-500')))) }}"></span>
                                    {{ str_replace('_', ' ', $visit->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-3 text-gray-500 text-xs">{{ $visit->registered_at->format('H:i') }}</td>
                            <td class="px-6 py-3 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <button type="button" onclick="openStepperModal({{ $visit->id }}, '{{ $visit->patient->fullName() }}', '{{ $visit->status }}')" class="flex items-center gap-1.5 px-3 py-1.5 bg-emerald-600 text-white text-xs font-medium rounded-lg hover:bg-emerald-700 transition-colors shadow-sm">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                                        Journey
                                    </button>
                                    <button type="button" onclick="openDischargeModal({{ $visit->id }})" class="flex items-center gap-1.5 px-3 py-1.5 bg-red-600 text-white text-xs font-medium rounded-lg hover:bg-red-700 transition-colors shadow-sm">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                        Discharge
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-6 py-10 text-center text-gray-400">
                            <div class="flex flex-col items-center gap-2">
                                <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                <p>No patients in queue today</p>
                            </div>
                        </td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Change Doctor Modal --}}
<div id="changeDoctorModal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm transition-opacity" onclick="closeChangeDoctorModal()"></div>
    <div class="absolute inset-0 flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-md transform transition-all scale-95 opacity-0" id="changeDoctorPanel">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <h3 class="text-sm font-semibold text-gray-900">Change Doctor</h3>
                <button onclick="closeChangeDoctorModal()" class="p-1.5 rounded-lg hover:bg-gray-100 text-gray-500">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="p-6">
                <form id="changeDoctorForm" method="POST" action="">
                    @csrf
                    <input type="hidden" name="visit_id" id="changeDoctorVisitId">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Select Doctor</label>
                        <select name="doctor_id" id="changeDoctorSelect" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 bg-white" required>
                            <option value="">Select doctor...</option>
                            @foreach($doctors as $d)
                            <option value="{{ $d->id }}">{{ $d->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex justify-end gap-2 mt-4">
                        <button type="button" onclick="closeChangeDoctorModal()" class="px-4 py-2 text-sm text-gray-600 hover:bg-gray-100 rounded-lg">Cancel</button>
                        <button type="submit" class="px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700">Change Doctor</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Discharge Modal --}}
<div id="dischargeModal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm transition-opacity" onclick="closeDischargeModal()"></div>
    <div class="absolute inset-0 flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-md transform transition-all scale-95 opacity-0" id="dischargePanel">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <h3 class="text-sm font-semibold text-gray-900">Discharge Patient</h3>
                <button onclick="closeDischargeModal()" class="p-1.5 rounded-lg hover:bg-gray-100 text-gray-500">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="p-6">
                <form id="dischargeForm" method="POST" action="">
                    @csrf
                    <input type="hidden" name="visit_id" id="dischargeVisitId">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Discharge Notes</label>
                        <textarea name="notes" rows="3" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" placeholder="Add discharge notes..."></textarea>
                    </div>
                    <div class="flex justify-end gap-2 mt-4">
                        <button type="button" onclick="closeDischargeModal()" class="px-4 py-2 text-sm text-gray-600 hover:bg-gray-100 rounded-lg">Cancel</button>
                        <button type="submit" class="px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700">Discharge</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Patient Journey Stepper Modal --}}
<div id="stepperModal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm transition-opacity" onclick="closeStepperModal()"></div>
    <div class="absolute inset-y-0 right-0 w-full max-w-lg bg-white shadow-2xl transform transition-transform translate-x-full duration-300 ease-in-out flex flex-col" id="stepperPanel">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between bg-gradient-to-r from-emerald-500 to-emerald-600">
            <div>
                <h3 class="text-sm font-semibold text-white">Patient Journey</h3>
                <p class="text-xs text-emerald-100" id="stepperPatientName">-</p>
            </div>
            <button onclick="closeStepperModal()" class="p-1.5 rounded-lg hover:bg-white/20 text-white">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div class="flex-1 overflow-y-auto p-6">
            <div class="space-y-6" id="stepperContent">
                <!-- Steps will be populated by JavaScript -->
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function filterByDoctor() {
    const filter = document.getElementById('doctorFilter').value;
    const rows = document.querySelectorAll('.visit-row');
    rows.forEach(row => {
        const doctorId = row.dataset.doctorId || '';
        if (filter === '' || doctorId === filter) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}

function openChangeDoctorModal(visitId, currentDoctorId) {
    const modal = document.getElementById('changeDoctorModal');
    const panel = document.getElementById('changeDoctorPanel');
    document.getElementById('changeDoctorVisitId').value = visitId;
    document.getElementById('changeDoctorSelect').value = currentDoctorId;
    document.getElementById('changeDoctorForm').action = '/reception/visits/' + visitId + '/change-doctor';
    modal.classList.remove('hidden');
    setTimeout(() => {
        panel.classList.remove('scale-95', 'opacity-0');
    }, 10);
    document.body.style.overflow = 'hidden';
}

function closeChangeDoctorModal() {
    const modal = document.getElementById('changeDoctorModal');
    const panel = document.getElementById('changeDoctorPanel');
    panel.classList.add('scale-95', 'opacity-0');
    setTimeout(() => {
        modal.classList.add('hidden');
        document.body.style.overflow = '';
    }, 200);
}

function openDischargeModal(visitId) {
    const modal = document.getElementById('dischargeModal');
    const panel = document.getElementById('dischargePanel');
    document.getElementById('dischargeVisitId').value = visitId;
    document.getElementById('dischargeForm').action = '/admin/visits/' + visitId + '/discharge';
    modal.classList.remove('hidden');
    setTimeout(() => {
        panel.classList.remove('scale-95', 'opacity-0');
    }, 10);
    document.body.style.overflow = 'hidden';
}

function closeDischargeModal() {
    const modal = document.getElementById('dischargeModal');
    const panel = document.getElementById('dischargePanel');
    panel.classList.add('scale-95', 'opacity-0');
    setTimeout(() => {
        modal.classList.add('hidden');
        document.body.style.overflow = '';
    }, 200);
}

function openStepperModal(visitId, patientName, status) {
    const modal = document.getElementById('stepperModal');
    const panel = document.getElementById('stepperPanel');
    
    document.getElementById('stepperPatientName').textContent = patientName;
    
    // Generate stepper steps based on status
    const steps = [
        { name: 'Registration', icon: 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z', completed: true },
        { name: 'Waiting for Doctor', icon: 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z', completed: status.includes('waiting') || status.includes('with') || status.includes('lab') || status.includes('pharmacy') || status.includes('payment') || status.includes('completed') },
        { name: 'With Doctor', icon: 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z', completed: status.includes('with') || status.includes('lab') || status.includes('pharmacy') || status.includes('payment') || status.includes('completed') },
        { name: 'Lab Tests', icon: 'M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z', completed: status.includes('lab') || status.includes('pharmacy') || status.includes('payment') || status.includes('completed') },
        { name: 'Pharmacy', icon: 'M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z', completed: status.includes('pharmacy') || status.includes('payment') || status.includes('completed') },
        { name: 'Payment', icon: 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z', completed: status.includes('payment') || status.includes('completed') },
        { name: 'Completed', icon: 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z', completed: status.includes('completed') },
    ];
    
    // Find current step index
    const currentStepIndex = steps.findIndex(step => {
        if (status.includes('waiting_for_doctor')) return step.name === 'Waiting for Doctor';
        if (status.includes('with_doctor')) return step.name === 'With Doctor';
        if (status.includes('lab')) return step.name === 'Lab Tests';
        if (status.includes('pharmacy')) return step.name === 'Pharmacy';
        if (status.includes('payment')) return step.name === 'Payment';
        if (status.includes('completed')) return step.name === 'Completed';
        return false;
    });
    
    // Generate stepper HTML
    let stepperHTML = '';
    steps.forEach((step, index) => {
        const isCompleted = step.completed;
        const isCurrent = index === currentStepIndex;
        const isPending = !isCompleted && !isCurrent;
        
        const bgClass = isCompleted ? 'bg-emerald-500' : (isCurrent ? 'bg-emerald-500' : 'bg-gray-200');
        const textClass = isCompleted ? 'text-emerald-600' : (isCurrent ? 'text-emerald-600' : 'text-gray-400');
        const iconColor = isCompleted ? 'text-white' : (isCurrent ? 'text-white' : 'text-gray-400');
        
        // Generate action button based on step
        let actionButton = '';
        if (isCurrent) {
            if (step.name === 'Waiting for Doctor') {
                actionButton = `<button onclick="callToDoctor(${visitId})" class="mt-2 flex items-center gap-1.5 px-3 py-1.5 bg-emerald-600 text-white text-xs font-medium rounded-lg hover:bg-emerald-700 transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                    Call to Doctor
                </button>`;
            } else if (step.name === 'With Doctor') {
                actionButton = `<div class="mt-2 flex gap-2">
                    <button onclick="sendToLab(${visitId})" class="flex items-center gap-1.5 px-3 py-1.5 bg-purple-600 text-white text-xs font-medium rounded-lg hover:bg-purple-700 transition-colors">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>
                        Send to Lab
                    </button>
                    <button onclick="sendToPharmacy(${visitId})" class="flex items-center gap-1.5 px-3 py-1.5 bg-cyan-600 text-white text-xs font-medium rounded-lg hover:bg-cyan-700 transition-colors">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>
                        Send to Pharmacy
                    </button>
                </div>`;
            } else if (step.name === 'Lab Tests') {
                actionButton = `<button onclick="completeLab(${visitId})" class="mt-2 flex items-center gap-1.5 px-3 py-1.5 bg-purple-600 text-white text-xs font-medium rounded-lg hover:bg-purple-700 transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Lab Completed
                </button>`;
            } else if (step.name === 'Pharmacy') {
                actionButton = `<button onclick="completeVisit(${visitId})" class="mt-2 flex items-center gap-1.5 px-3 py-1.5 bg-emerald-600 text-white text-xs font-medium rounded-lg hover:bg-emerald-700 transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Complete Visit
                </button>`;
            }
        }
        
        stepperHTML += `
            <div class="flex items-start gap-4">
                <div class="flex flex-col items-center">
                    <div class="w-10 h-10 rounded-full ${bgClass} flex items-center justify-center shadow-md">
                        <svg class="w-5 h-5 ${iconColor}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="${step.icon}"/></svg>
                    </div>
                    ${index < steps.length - 1 ? `<div class="w-0.5 h-12 ${isCompleted ? 'bg-emerald-500' : 'bg-gray-200'} mt-2"></div>` : ''}
                </div>
                <div class="flex-1 pb-4">
                    <div class="flex items-center gap-2">
                        <h4 class="text-sm font-semibold ${textClass}">${step.name}</h4>
                        ${isCurrent ? '<span class="px-2 py-0.5 rounded-full text-[10px] font-medium bg-emerald-100 text-emerald-700">Current</span>' : ''}
                        ${isCompleted ? '<span class="px-2 py-0.5 rounded-full text-[10px] font-medium bg-emerald-100 text-emerald-700">Done</span>' : ''}
                    </div>
                    <p class="text-xs text-gray-500 mt-1">${isCompleted ? 'Step completed' : (isCurrent ? 'In progress' : 'Pending')}</p>
                    ${actionButton}
                </div>
            </div>
        `;
    });
    
    document.getElementById('stepperContent').innerHTML = stepperHTML;
    
    modal.classList.remove('hidden');
    setTimeout(() => {
        panel.classList.remove('translate-x-full');
    }, 10);
    document.body.style.overflow = 'hidden';
}

function closeStepperModal() {
    const modal = document.getElementById('stepperModal');
    const panel = document.getElementById('stepperPanel');
    panel.classList.add('translate-x-full');
    setTimeout(() => {
        modal.classList.add('hidden');
        document.body.style.overflow = '';
    }, 300);
}

function callToDoctor(visitId) {
    Swal.fire({
        title: 'Call to Doctor?',
        text: 'Send patient to doctor queue?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#10b981',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, Call',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch('/doctor/visits/' + visitId + '/call', {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
            })
            .then(r => r.json().catch(() => ({})))
            .then(data => {
                Swal.fire({ icon: 'success', title: 'Success', text: data.message || 'Patient called to doctor.', timer: 1500, showConfirmButton: false });
                closeStepperModal();
                setTimeout(() => location.reload(), 1000);
            })
            .catch(err => {
                Swal.fire({ icon: 'error', title: 'Error', text: 'Failed to call patient.' });
            });
        }
    });
}

function sendToLab(visitId) {
    Swal.fire({
        title: 'Send to Lab?',
        text: 'Send patient for lab tests?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#8b5cf6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, Send',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch('/doctor/visits/' + visitId + '/lab', {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                body: JSON.stringify({ test_ids: [], notes: 'Sent by admin' })
            })
            .then(r => r.json().catch(() => ({})))
            .then(data => {
                Swal.fire({ icon: 'success', title: 'Success', text: data.message || 'Patient sent to lab.', timer: 1500, showConfirmButton: false });
                closeStepperModal();
                setTimeout(() => location.reload(), 1000);
            })
            .catch(err => {
                Swal.fire({ icon: 'error', title: 'Error', text: 'Failed to send to lab.' });
            });
        }
    });
}

function sendToPharmacy(visitId) {
    Swal.fire({
        title: 'Send to Pharmacy?',
        text: 'Send patient to pharmacy?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#06b6d4',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, Send',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch('/doctor/visits/' + visitId + '/prescribe', {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                body: JSON.stringify({ items: [] })
            })
            .then(r => r.json().catch(() => ({})))
            .then(data => {
                Swal.fire({ icon: 'success', title: 'Success', text: data.message || 'Patient sent to pharmacy.', timer: 1500, showConfirmButton: false });
                closeStepperModal();
                setTimeout(() => location.reload(), 1000);
            })
            .catch(err => {
                Swal.fire({ icon: 'error', title: 'Error', text: 'Failed to send to pharmacy.' });
            });
        }
    });
}

function completeLab(visitId) {
    Swal.fire({
        title: 'Lab Completed?',
        text: 'Mark lab tests as completed?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#8b5cf6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, Complete',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch('/admin/visits/' + visitId + '/complete-lab', {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
            })
            .then(r => r.json().catch(() => ({})))
            .then(data => {
                Swal.fire({ icon: 'success', title: 'Success', text: data.message || 'Lab completed.', timer: 1500, showConfirmButton: false });
                closeStepperModal();
                setTimeout(() => location.reload(), 1000);
            })
            .catch(err => {
                Swal.fire({ icon: 'error', title: 'Error', text: 'Failed to complete lab.' });
            });
        }
    });
}

function completeVisit(visitId) {
    Swal.fire({
        title: 'Complete Visit?',
        text: 'Mark this visit as completed?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#10b981',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, Complete',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch('/doctor/visits/' + visitId + '/complete', {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
            })
            .then(r => r.json().catch(() => ({})))
            .then(data => {
                Swal.fire({ icon: 'success', title: 'Success', text: data.message || 'Visit completed.', timer: 1500, showConfirmButton: false });
                closeStepperModal();
                setTimeout(() => location.reload(), 1000);
            })
            .catch(err => {
                Swal.fire({ icon: 'error', title: 'Error', text: 'Failed to complete visit.' });
            });
        }
    });
}

function completePayment(visitId) {
    Swal.fire({
        title: 'Complete Payment?',
        text: 'Mark payment as completed?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, Complete',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch('/admin/visits/' + visitId + '/complete-payment', {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
            })
            .then(r => r.json().catch(() => ({})))
            .then(data => {
                Swal.fire({ icon: 'success', title: 'Success', text: data.message || 'Payment completed.', timer: 1500, showConfirmButton: false });
                closeStepperModal();
                setTimeout(() => location.reload(), 1000);
            })
            .catch(err => {
                Swal.fire({ icon: 'error', title: 'Error', text: 'Failed to complete payment.' });
            });
        }
    });
}

document.getElementById('changeDoctorForm')?.addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    fetch(this.action, {
        method: 'POST',
        body: formData,
        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
    })
    .then(r => r.json().catch(() => ({})))
    .then(data => {
        Swal.fire({ icon: 'success', title: 'Success', text: data.message || 'Doctor changed successfully.', timer: 1500, showConfirmButton: false });
        closeChangeDoctorModal();
        setTimeout(() => location.reload(), 1000);
    })
    .catch(err => {
        Swal.fire({ icon: 'error', title: 'Error', text: 'Failed to change doctor.' });
    });
});

document.getElementById('dischargeForm')?.addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    fetch(this.action, {
        method: 'POST',
        body: formData,
        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
    })
    .then(r => r.json().catch(() => ({})))
    .then(data => {
        Swal.fire({ icon: 'success', title: 'Success', text: data.message || 'Patient discharged successfully.', timer: 1500, showConfirmButton: false });
        closeDischargeModal();
        setTimeout(() => location.reload(), 1000);
    })
    .catch(err => {
        Swal.fire({ icon: 'error', title: 'Error', text: 'Failed to discharge patient.' });
    });
});
</script>
@endpush
@endsection
