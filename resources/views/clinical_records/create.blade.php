@extends('layouts.dashboard')

@section('title', 'Add Clinical Record - ' . config('app.name', 'Laravel'))
@section('page_title', 'Add Clinical Record')

@section('content')
<div class="max-w-4xl mx-auto space-y-5">
    <div class="bg-slate-800 rounded-md p-6 text-white flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <p class="text-[10px] uppercase tracking-wider text-slate-400 mb-1">Clinical</p>
            <h1 class="text-xl font-bold">Add Clinical Record</h1>
            <p class="text-xs text-slate-400 mt-1">Create a new diagnosis, treatment or medical note.</p>
        </div>
        <a href="{{ route('clinical-records.index') }}" class="inline-flex items-center gap-2 text-xs text-slate-300 hover:text-white">
            <i class="hgi-stroke hgi-arrow-left-02 text-base"></i>
            Back to Records
        </a>
    </div>

    <form method="POST" action="{{ route('clinical-records.store') }}" enctype="multipart/form-data" class="bg-white rounded-md border border-gray-200 overflow-hidden">
        @csrf

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
        <div class="px-6 py-5 border-b border-gray-100 bg-gray-50/50">
            <h3 class="text-sm font-semibold text-gray-900 flex items-center gap-2">
                <i class="hgi-stroke hgi-user text-base text-slate-500"></i>
                Patient & Source
            </h3>
            <p class="text-xs text-gray-500 mt-0.5">Select the patient and link to a visit or appointment if available</p>
        </div>
        <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-5">
            <div class="md:col-span-2">
                <label class="block text-xs font-medium text-gray-700 mb-1.5">Patient <span class="text-red-500">*</span></label>
                <div class="relative">
                    <x-ui.select name="patient_id" id="patient_id" class="w-full border border-gray-200 rounded-lg pl-3 pr-10 py-2.5 text-sm focus:ring-2 focus:ring-slate-300 appearance-none bg-white" required>
                        <option value="">Select patient...</option>
                        @foreach($patients as $patient)
                            <option value="{{ $patient->id }}" data-name="{{ $patient->fullName() }}" data-mrn="{{ $patient->mrn }}" {{ ($preselected['patient_id'] ?? '') == $patient->id ? 'selected' : '' }}>{{ $patient->fullName() }} ({{ $patient->mrn }})</option>
                        @endforeach
                    </x-ui.select>
                    <div class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none">
                        <i class="hgi-stroke hgi-arrow-down-01 text-base text-gray-400"></i>
                    </div>
                </div>
                <div id="patientSummary" class="mt-2 hidden">
                    <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-slate-50 border border-slate-200 rounded text-xs text-slate-700">
                        <span class="w-5 h-5 rounded-full bg-slate-700 text-white flex items-center justify-center font-bold text-[10px]" id="patientInitial">P</span>
                        <span id="patientName">Patient</span>
                        <span class="text-slate-300">|</span>
                        <span id="patientMrn">MRN</span>
                    </div>
                </div>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1.5">Linked Visit</label>
                <div class="relative">
                    <x-ui.select name="visit_id" id="visit_id" class="w-full border border-gray-200 rounded-lg pl-3 pr-10 py-2.5 text-sm focus:ring-2 focus:ring-slate-300 appearance-none bg-white">
                        <option value="">No visit</option>
                        @foreach($visits as $visit)
                            <option value="{{ $visit->id }}" data-patient-id="{{ $visit->patient_id }}" {{ ($preselected['visit_id'] ?? '') == $visit->id ? 'selected' : '' }}>{{ $visit->visit_number }} — {{ $visit->patient?->fullName() }}</option>
                        @endforeach
                    </x-ui.select>
                    <div class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none">
                        <i class="hgi-stroke hgi-arrow-down-01 text-base text-gray-400"></i>
                    </div>
                </div>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1.5">Linked Appointment</label>
                <div class="relative">
                    <x-ui.select name="appointment_id" id="appointment_id" class="w-full border border-gray-200 rounded-lg pl-3 pr-10 py-2.5 text-sm focus:ring-2 focus:ring-slate-300 appearance-none bg-white">
                        <option value="">No appointment</option>
                        @foreach($appointments as $appointment)
                            <option value="{{ $appointment->id }}" data-patient-id="{{ $appointment->patient_id }}" {{ ($preselected['appointment_id'] ?? '') == $appointment->id ? 'selected' : '' }}>{{ $appointment->patient?->fullName() }} — {{ $appointment->scheduled_at?->format('M d, Y H:i') }}</option>
                        @endforeach
                    </x-ui.select>
                    <div class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none">
                        <i class="hgi-stroke hgi-arrow-down-01 text-base text-gray-400"></i>
                    </div>
                </div>
            </div>
            <div class="md:col-span-2">
                <label class="block text-xs font-medium text-gray-700 mb-1.5">Doctor <span class="text-red-500">*</span></label>
                <div class="relative">
                    <x-ui.select name="doctor_id" class="w-full border border-gray-200 rounded-lg pl-3 pr-10 py-2.5 text-sm focus:ring-2 focus:ring-slate-300 appearance-none bg-white" required>
                        <option value="">Select doctor...</option>
                        @foreach($doctors as $doctor)
                            <option value="{{ $doctor->id }}" {{ Auth::id() == $doctor->id ? 'selected' : '' }}>{{ $doctor->name }}</option>
                        @endforeach
                    </x-ui.select>
                    <div class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none">
                        <i class="hgi-stroke hgi-arrow-down-01 text-base text-gray-400"></i>
                    </div>
                </div>
            </div>
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
        <div class="px-6 py-5 border-y border-gray-100 bg-gray-50/50">
            <h3 class="text-sm font-semibold text-gray-900 flex items-center gap-2">
                <i class="hgi-stroke hgi-file-02 text-base text-slate-500"></i>
                Clinical Details
            </h3>
        </div>
        <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-5">
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1.5">Record Date <span class="text-red-500">*</span></label>
                <x-ui.input type="text" data-fp-date name="record_date" value="{{ date('Y-m-d') }}" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-slate-300" required />
            </div>
            <div class="md:col-span-2">
                <label class="block text-xs font-medium text-gray-700 mb-1.5">Chief Complaint</label>
                <x-ui.textarea name="chief_complaint" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-slate-300" rows="2" placeholder="Patient's main complaint..."></x-ui.textarea>
            </div>
            <div class="md:col-span-2">
                <label class="block text-xs font-medium text-gray-700 mb-1.5">Symptoms</label>
                <x-ui.textarea name="symptoms" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-slate-300" rows="2" placeholder="Describe observed symptoms..."></x-ui.textarea>
            </div>
            <div class="md:col-span-2">
                <label class="block text-xs font-medium text-gray-700 mb-1.5">Diagnosis</label>
                <x-ui.textarea name="diagnosis" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-slate-300" rows="2" placeholder="Primary and secondary diagnosis..."></x-ui.textarea>
            </div>
            <div class="md:col-span-2">
                <label class="block text-xs font-medium text-gray-700 mb-1.5">Treatment Plan</label>
                <x-ui.textarea name="treatment_plan" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-slate-300" rows="3" placeholder="Recommended treatment, procedures, follow-ups..."></x-ui.textarea>
            </div>
            <div class="md:col-span-2">
                <label class="block text-xs font-medium text-gray-700 mb-1.5">Prescription</label>
                <x-ui.textarea name="prescription" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-slate-300" rows="3" placeholder="Medications, dosage and instructions..."></x-ui.textarea>
            </div>
            <div class="md:col-span-2">
                <label class="block text-xs font-medium text-gray-700 mb-1.5">Notes</label>
                <x-ui.textarea name="notes" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-slate-300" rows="3" placeholder="Additional notes..."></x-ui.textarea>
            </div>
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
        <div class="px-6 py-5 border-y border-gray-100 bg-gray-50/50">
            <h3 class="text-sm font-semibold text-gray-900 flex items-center gap-2">
                <i class="hgi-stroke hgi-attachment-01 text-base text-slate-500"></i>
                Attachments
            </h3>
            <p class="text-xs text-gray-500 mt-0.5">Upload files (PDF, Images, Documents) - Max 10MB each</p>
        </div>
        <div class="p-6">
            <x-ui.input type="file" name="attachments[]" multiple accept=".pdf,.jpg,.jpeg,.png,.doc,.docx" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-slate-300" />
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
        <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/30 flex justify-end gap-3">
            <a href="{{ route('clinical-records.index') }}" class="px-5 py-2 text-sm text-gray-700 border border-gray-200 hover:bg-gray-100 rounded transition-colors">Cancel</a>
            <button type="submit" class="inline-flex items-center gap-2 bg-slate-800 hover:bg-slate-700 text-white text-sm font-medium px-5 py-2 rounded transition-colors">
                <i class="hgi-stroke hgi-download-02 text-base"></i>
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
