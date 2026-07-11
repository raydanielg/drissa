@extends('layouts.dashboard')

@section('title', 'Add Clinical Record - ' . config('app.name', 'Laravel'))
@section('page_title', 'Add Clinical Record')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-lg font-bold text-gray-900">Add Clinical Record</h2>
            <p class="text-sm text-gray-500">Create a new diagnosis, treatment or medical note</p>
        </div>
        <a href="{{ route('clinical-records.index') }}" class="inline-flex items-center gap-2 text-sm text-gray-600 hover:text-gray-900">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Back to Records
        </a>
    </div>

    <form method="POST" action="{{ route('clinical-records.store') }}" class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        @csrf

        {{-- Patient & Source Section --}}
        <div class="px-6 py-5 border-b border-gray-100 bg-gray-50/50">
            <h3 class="text-sm font-semibold text-gray-900 flex items-center gap-2">
                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                Patient & Source
            </h3>
            <p class="text-xs text-gray-500 mt-0.5">Select the patient and link to a visit or appointment if available</p>
        </div>
        <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-5">
            <div class="md:col-span-2">
                <label class="block text-xs font-medium text-gray-700 mb-1.5">Patient <span class="text-red-500">*</span></label>
                <div class="relative">
                    <select name="patient_id" id="patient_id" class="w-full border border-gray-200 rounded-lg pl-3 pr-10 py-2.5 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 appearance-none bg-white" required>
                        <option value="">Select patient...</option>
                        @foreach($patients as $patient)
                            <option value="{{ $patient->id }}" data-name="{{ $patient->fullName() }}" data-mrn="{{ $patient->mrn }}" {{ ($preselected['patient_id'] ?? '') == $patient->id ? 'selected' : '' }}>{{ $patient->fullName() }} ({{ $patient->mrn }})</option>
                        @endforeach
                    </select>
                    <div class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </div>
                </div>
                <div id="patientSummary" class="mt-2 hidden">
                    <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-emerald-50 border border-emerald-100 rounded-lg text-xs text-emerald-700">
                        <span class="w-5 h-5 rounded-full bg-emerald-600 text-white flex items-center justify-center font-bold text-[10px]" id="patientInitial">P</span>
                        <span id="patientName">Patient</span>
                        <span class="text-emerald-400">|</span>
                        <span id="patientMrn">MRN</span>
                    </div>
                </div>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1.5">Linked Visit</label>
                <div class="relative">
                    <select name="visit_id" id="visit_id" class="w-full border border-gray-200 rounded-lg pl-3 pr-10 py-2.5 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 appearance-none bg-white">
                        <option value="">No visit</option>
                        @foreach($visits as $visit)
                            <option value="{{ $visit->id }}" data-patient-id="{{ $visit->patient_id }}" {{ ($preselected['visit_id'] ?? '') == $visit->id ? 'selected' : '' }}>{{ $visit->visit_number }} — {{ $visit->patient?->fullName() }}</option>
                        @endforeach
                    </select>
                    <div class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </div>
                </div>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1.5">Linked Appointment</label>
                <div class="relative">
                    <select name="appointment_id" id="appointment_id" class="w-full border border-gray-200 rounded-lg pl-3 pr-10 py-2.5 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 appearance-none bg-white">
                        <option value="">No appointment</option>
                        @foreach($appointments as $appointment)
                            <option value="{{ $appointment->id }}" data-patient-id="{{ $appointment->patient_id }}" {{ ($preselected['appointment_id'] ?? '') == $appointment->id ? 'selected' : '' }}>{{ $appointment->patient?->fullName() }} — {{ $appointment->scheduled_at?->format('M d, Y H:i') }}</option>
                        @endforeach
                    </select>
                    <div class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </div>
                </div>
            </div>
            <div class="md:col-span-2">
                <label class="block text-xs font-medium text-gray-700 mb-1.5">Doctor <span class="text-red-500">*</span></label>
                <div class="relative">
                    <select name="doctor_id" class="w-full border border-gray-200 rounded-lg pl-3 pr-10 py-2.5 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 appearance-none bg-white" required>
                        <option value="">Select doctor...</option>
                        @foreach($doctors as $doctor)
                            <option value="{{ $doctor->id }}" {{ Auth::id() == $doctor->id ? 'selected' : '' }}>{{ $doctor->name }}</option>
                        @endforeach
                    </select>
                    <div class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </div>
                </div>
            </div>
        </div>

        {{-- Clinical Details Section --}}
        <div class="px-6 py-5 border-y border-gray-100 bg-gray-50/50">
            <h3 class="text-sm font-semibold text-gray-900 flex items-center gap-2">
                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Clinical Details
            </h3>
        </div>
        <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-5">
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1.5">Record Date <span class="text-red-500">*</span></label>
                <input type="date" name="record_date" value="{{ date('Y-m-d') }}" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" required>
            </div>
            <div class="md:col-span-2">
                <label class="block text-xs font-medium text-gray-700 mb-1.5">Chief Complaint</label>
                <textarea name="chief_complaint" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" rows="2" placeholder="Patient's main complaint..."></textarea>
            </div>
            <div class="md:col-span-2">
                <label class="block text-xs font-medium text-gray-700 mb-1.5">Symptoms</label>
                <textarea name="symptoms" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" rows="2" placeholder="Describe observed symptoms..."></textarea>
            </div>
            <div class="md:col-span-2">
                <label class="block text-xs font-medium text-gray-700 mb-1.5">Diagnosis</label>
                <textarea name="diagnosis" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" rows="2" placeholder="Primary and secondary diagnosis..."></textarea>
            </div>
            <div class="md:col-span-2">
                <label class="block text-xs font-medium text-gray-700 mb-1.5">Treatment Plan</label>
                <textarea name="treatment_plan" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" rows="3" placeholder="Recommended treatment, procedures, follow-ups..."></textarea>
            </div>
            <div class="md:col-span-2">
                <label class="block text-xs font-medium text-gray-700 mb-1.5">Prescription</label>
                <textarea name="prescription" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" rows="3" placeholder="Medications, dosage and instructions..."></textarea>
            </div>
            <div class="md:col-span-2">
                <label class="block text-xs font-medium text-gray-700 mb-1.5">Notes</label>
                <textarea name="notes" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" rows="3" placeholder="Additional notes..."></textarea>
            </div>
        </div>

        {{-- Footer Actions --}}
        <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/30 flex justify-end gap-3">
            <a href="{{ route('clinical-records.index') }}" class="px-5 py-2 text-sm text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">Cancel</a>
            <button type="submit" class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium px-5 py-2 rounded-lg shadow-sm hover:shadow transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/></svg>
                Save Clinical Record
            </button>
        </div>
    </form>
</div>

<script>
    function updatePatientSummary() {
        const select = document.getElementById('patient_id');
        const option = select.options[select.selectedIndex];
        const summary = document.getElementById('patientSummary');
        if (select.value) {
            summary.classList.remove('hidden');
            document.getElementById('patientInitial').textContent = (option.dataset.name || 'P').charAt(0).toUpperCase();
            document.getElementById('patientName').textContent = option.dataset.name || 'Patient';
            document.getElementById('patientMrn').textContent = option.dataset.mrn || 'MRN';
        } else {
            summary.classList.add('hidden');
        }
    }

    function syncPatientFromSource(sourceSelectId) {
        const source = document.getElementById(sourceSelectId);
        const option = source.options[source.selectedIndex];
        const patientId = option.dataset.patientId;
        if (patientId) {
            document.getElementById('patient_id').value = patientId;
            updatePatientSummary();
        }
    }

    document.getElementById('patient_id').addEventListener('change', updatePatientSummary);
    document.getElementById('visit_id').addEventListener('change', () => syncPatientFromSource('visit_id'));
    document.getElementById('appointment_id').addEventListener('change', () => syncPatientFromSource('appointment_id'));
    updatePatientSummary();
</script>
@endsection
