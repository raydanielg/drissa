@extends('layouts.dashboard')

@section('title', 'Doctor Queue - ' . config('app.name', 'Laravel'))
@section('page_title', 'Doctor Queue')

@section('content')
<div class="space-y-5">
    @if (session('status'))
        <div class="p-3 rounded-md bg-white border-l-2 border-emerald-500 text-emerald-700 text-xs">{{ session('status') }}</div>
    @endif

    <script>
function swalConfirmSubmit(event, message) {
    event.preventDefault();
    const form = event.target;
    Swal.fire({
        icon: 'question',
        title: 'Confirm',
        text: message,
        showCancelButton: true,
        confirmButtonText: 'Yes',
        cancelButtonText: 'Cancel',
        confirmButtonColor: '#0f172a',
    }).then((result) => {
        if (result.isConfirmed) form.submit();
    });
    return false;
}

function addPrescriptionItem(visitId) {
    const container = document.getElementById('prescription-items-' + visitId);
    const index = container.children.length;
    const row = document.createElement('div');
    row.className = 'grid grid-cols-12 gap-2 prescription-row';
    row.innerHTML = `
        <div class="col-span-3 relative">
            <select name="items[${index}][medication_id]" class="h-9 w-full appearance-none rounded-md border border-gray-200 bg-white px-3 pr-8 py-1 text-xs transition-shadow outline-none focus-visible:ring-2 focus-visible:ring-slate-300" required>
            <option value="">Select drug</option>
            @foreach ($medications as $med)
                <option value="{{ $med->id }}">{{ $med->name }}</option>
            @endforeach
            </select>
            <i class="hgi-stroke hgi-arrow-down-01 pointer-events-none absolute right-2.5 top-1/2 -translate-y-1/2 text-sm text-gray-400"></i>
        </div>
        <input type="number" name="items[${index}][quantity]" placeholder="Qty" class="col-span-1 h-9 w-full rounded-md border border-gray-200 bg-white px-3 py-1 text-xs transition-shadow outline-none focus-visible:ring-2 focus-visible:ring-slate-300" required>
        <input type="text" name="items[${index}][dosage]" placeholder="Dose" class="col-span-2 h-9 w-full rounded-md border border-gray-200 bg-white px-3 py-1 text-xs transition-shadow outline-none focus-visible:ring-2 focus-visible:ring-slate-300" required>
        <input type="text" name="items[${index}][frequency]" placeholder="Frequency" class="col-span-2 h-9 w-full rounded-md border border-gray-200 bg-white px-3 py-1 text-xs transition-shadow outline-none focus-visible:ring-2 focus-visible:ring-slate-300" required>
        <input type="text" name="items[${index}][duration]" placeholder="Duration" class="col-span-2 h-9 w-full rounded-md border border-gray-200 bg-white px-3 py-1 text-xs transition-shadow outline-none focus-visible:ring-2 focus-visible:ring-slate-300" required>
        <input type="text" name="items[${index}][instructions]" placeholder="Instructions" class="col-span-2 h-9 w-full rounded-md border border-gray-200 bg-white px-3 py-1 text-xs transition-shadow outline-none focus-visible:ring-2 focus-visible:ring-slate-300">
        <button type="button" onclick="this.closest('.prescription-row').remove()" class="col-span-1 text-red-500 hover:text-red-700">
            <i class="hgi-stroke hgi-delete-02 text-base mx-auto"></i>
        </button>
    `;
    container.appendChild(row);
}

function toggleVitalsForm(visitId) {
    const display = document.getElementById('vitals-display-' + visitId);
    const form = document.getElementById('vitals-form-' + visitId);
    display.classList.toggle('hidden');
    form.classList.toggle('hidden');
}
</script>
    <div class="bg-slate-800 rounded-md p-6 text-white">
        <div class="flex flex-wrap items-end justify-between gap-3">
            <div>
                <p class="text-[10px] uppercase tracking-wider text-slate-400 mb-1">Doctor</p>
                <h1 class="text-xl font-bold">My Queue</h1>
                <p class="text-xs text-slate-400 mt-1">Manage patients currently assigned to you.</p>
            </div>
            <a href="{{ route('doctor.lab-results') }}" class="relative inline-flex items-center gap-1.5 px-3 py-1.5 bg-white hover:bg-gray-100 text-slate-800 text-xs font-semibold rounded transition-colors">
                <i class="hgi-stroke hgi-test-tube-01 text-sm"></i>
                Results
                @if($pendingResultsCount > 0)
                    <span class="absolute -top-2 -right-2 w-5 h-5 bg-red-500 text-white text-[10px] font-bold rounded-full flex items-center justify-center shadow-sm">{{ $pendingResultsCount }}</span>
                @endif
            </a>
        </div>
    </div>

    @forelse ($visits as $visit)
        <div class="bg-white rounded-md border border-gray-200 overflow-hidden" id="visit-{{ $visit->id }}">
            <script>
function swalConfirmSubmit(event, message) {
    event.preventDefault();
    const form = event.target;
    Swal.fire({
        icon: 'question',
        title: 'Confirm',
        text: message,
        showCancelButton: true,
        confirmButtonText: 'Yes',
        cancelButtonText: 'Cancel',
        confirmButtonColor: '#0f172a',
    }).then((result) => {
        if (result.isConfirmed) form.submit();
    });
    return false;
}

function addPrescriptionItem(visitId) {
    const container = document.getElementById('prescription-items-' + visitId);
    const index = container.children.length;
    const row = document.createElement('div');
    row.className = 'grid grid-cols-12 gap-2 prescription-row';
    row.innerHTML = `
        <div class="col-span-3 relative">
            <select name="items[${index}][medication_id]" class="h-9 w-full appearance-none rounded-md border border-gray-200 bg-white px-3 pr-8 py-1 text-xs transition-shadow outline-none focus-visible:ring-2 focus-visible:ring-slate-300" required>
            <option value="">Select drug</option>
            @foreach ($medications as $med)
                <option value="{{ $med->id }}">{{ $med->name }}</option>
            @endforeach
            </select>
            <i class="hgi-stroke hgi-arrow-down-01 pointer-events-none absolute right-2.5 top-1/2 -translate-y-1/2 text-sm text-gray-400"></i>
        </div>
        <input type="number" name="items[${index}][quantity]" placeholder="Qty" class="col-span-1 h-9 w-full rounded-md border border-gray-200 bg-white px-3 py-1 text-xs transition-shadow outline-none focus-visible:ring-2 focus-visible:ring-slate-300" required>
        <input type="text" name="items[${index}][dosage]" placeholder="Dose" class="col-span-2 h-9 w-full rounded-md border border-gray-200 bg-white px-3 py-1 text-xs transition-shadow outline-none focus-visible:ring-2 focus-visible:ring-slate-300" required>
        <input type="text" name="items[${index}][frequency]" placeholder="Frequency" class="col-span-2 h-9 w-full rounded-md border border-gray-200 bg-white px-3 py-1 text-xs transition-shadow outline-none focus-visible:ring-2 focus-visible:ring-slate-300" required>
        <input type="text" name="items[${index}][duration]" placeholder="Duration" class="col-span-2 h-9 w-full rounded-md border border-gray-200 bg-white px-3 py-1 text-xs transition-shadow outline-none focus-visible:ring-2 focus-visible:ring-slate-300" required>
        <input type="text" name="items[${index}][instructions]" placeholder="Instructions" class="col-span-2 h-9 w-full rounded-md border border-gray-200 bg-white px-3 py-1 text-xs transition-shadow outline-none focus-visible:ring-2 focus-visible:ring-slate-300">
        <button type="button" onclick="this.closest('.prescription-row').remove()" class="col-span-1 text-red-500 hover:text-red-700">
            <i class="hgi-stroke hgi-delete-02 text-base mx-auto"></i>
        </button>
    `;
    container.appendChild(row);
}

function toggleVitalsForm(visitId) {
    const display = document.getElementById('vitals-display-' + visitId);
    const form = document.getElementById('vitals-form-' + visitId);
    display.classList.toggle('hidden');
    form.classList.toggle('hidden');
}
</script>
            <div class="px-5 py-4 bg-gray-50 border-b border-gray-100 flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-full bg-slate-100 text-slate-600 flex items-center justify-center font-bold text-lg">
                        {{ strtoupper(substr($visit->patient->first_name, 0, 1)) }}
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-gray-900">{{ $visit->patient->fullName() }}</h3>
                        <div class="flex flex-wrap items-center gap-2 text-xs text-gray-500 mt-0.5">
                            <span class="inline-flex items-center gap-1"><i class="hgi-stroke hgi-identity-card text-sm"></i> {{ $visit->visit_number }}</span>
                            <span>&bull;</span>
                            <span>{{ $visit->patient->gender ?? 'N/A' }} / {{ $visit->patient->date_of_birth?->age ?? 'N/A' }} yrs</span>
                            <span>&bull;</span>
                            <span>{{ $visit->patient->phone ?? 'No phone' }}</span>
                            <span>&bull;</span>
                            <span>MRN: {{ $visit->patient->mrn ?? 'N/A' }}</span>
                        </div>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    @php
                        $statusDot = match($visit->status) {
                            \App\Enums\VisitStatus::WaitingForDoctor->value => 'bg-amber-500',
                            \App\Enums\VisitStatus::WithDoctor->value => 'bg-emerald-500',
                            default => 'bg-slate-400',
                        };
                    @endphp
                    <span class="inline-flex items-center gap-1.5 text-xs font-medium text-gray-600">
                        <span class="w-1.5 h-1.5 rounded-full {{ $statusDot }}"></span>
                        {{ ucwords(str_replace('_', ' ', $visit->status)) }}
                    </span>
                    <span class="inline-flex items-center gap-1.5 text-xs font-medium text-gray-500">
                        <span class="w-1.5 h-1.5 rounded-full bg-slate-300"></span>
                        {{ ucfirst($visit->type ?? 'walk-in') }}
                    </span>
                </div>
            </div>

            <div class="p-6">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <script>
function swalConfirmSubmit(event, message) {
    event.preventDefault();
    const form = event.target;
    Swal.fire({
        icon: 'question',
        title: 'Confirm',
        text: message,
        showCancelButton: true,
        confirmButtonText: 'Yes',
        cancelButtonText: 'Cancel',
        confirmButtonColor: '#0f172a',
    }).then((result) => {
        if (result.isConfirmed) form.submit();
    });
    return false;
}

function addPrescriptionItem(visitId) {
    const container = document.getElementById('prescription-items-' + visitId);
    const index = container.children.length;
    const row = document.createElement('div');
    row.className = 'grid grid-cols-12 gap-2 prescription-row';
    row.innerHTML = `
        <div class="col-span-3 relative">
            <select name="items[${index}][medication_id]" class="h-9 w-full appearance-none rounded-md border border-gray-200 bg-white px-3 pr-8 py-1 text-xs transition-shadow outline-none focus-visible:ring-2 focus-visible:ring-slate-300" required>
            <option value="">Select drug</option>
            @foreach ($medications as $med)
                <option value="{{ $med->id }}">{{ $med->name }}</option>
            @endforeach
            </select>
            <i class="hgi-stroke hgi-arrow-down-01 pointer-events-none absolute right-2.5 top-1/2 -translate-y-1/2 text-sm text-gray-400"></i>
        </div>
        <input type="number" name="items[${index}][quantity]" placeholder="Qty" class="col-span-1 h-9 w-full rounded-md border border-gray-200 bg-white px-3 py-1 text-xs transition-shadow outline-none focus-visible:ring-2 focus-visible:ring-slate-300" required>
        <input type="text" name="items[${index}][dosage]" placeholder="Dose" class="col-span-2 h-9 w-full rounded-md border border-gray-200 bg-white px-3 py-1 text-xs transition-shadow outline-none focus-visible:ring-2 focus-visible:ring-slate-300" required>
        <input type="text" name="items[${index}][frequency]" placeholder="Frequency" class="col-span-2 h-9 w-full rounded-md border border-gray-200 bg-white px-3 py-1 text-xs transition-shadow outline-none focus-visible:ring-2 focus-visible:ring-slate-300" required>
        <input type="text" name="items[${index}][duration]" placeholder="Duration" class="col-span-2 h-9 w-full rounded-md border border-gray-200 bg-white px-3 py-1 text-xs transition-shadow outline-none focus-visible:ring-2 focus-visible:ring-slate-300" required>
        <input type="text" name="items[${index}][instructions]" placeholder="Instructions" class="col-span-2 h-9 w-full rounded-md border border-gray-200 bg-white px-3 py-1 text-xs transition-shadow outline-none focus-visible:ring-2 focus-visible:ring-slate-300">
        <button type="button" onclick="this.closest('.prescription-row').remove()" class="col-span-1 text-red-500 hover:text-red-700">
            <i class="hgi-stroke hgi-delete-02 text-base mx-auto"></i>
        </button>
    `;
    container.appendChild(row);
}

function toggleVitalsForm(visitId) {
    const display = document.getElementById('vitals-display-' + visitId);
    const form = document.getElementById('vitals-form-' + visitId);
    display.classList.toggle('hidden');
    form.classList.toggle('hidden');
}
</script>
                    <div class="space-y-4">
                        @if ($visit->chief_complaint)
                            <div class="bg-white border-l-2 border-amber-500 rounded-md p-3 border border-gray-200">
                                <p class="text-[10px] font-semibold text-gray-500 uppercase tracking-wide mb-1">Chief Complaint</p>
                                <p class="text-sm text-gray-700">{{ $visit->chief_complaint }}</p>
                            </div>
                        @endif

                        <script>
function swalConfirmSubmit(event, message) {
    event.preventDefault();
    const form = event.target;
    Swal.fire({
        icon: 'question',
        title: 'Confirm',
        text: message,
        showCancelButton: true,
        confirmButtonText: 'Yes',
        cancelButtonText: 'Cancel',
        confirmButtonColor: '#0f172a',
    }).then((result) => {
        if (result.isConfirmed) form.submit();
    });
    return false;
}

function addPrescriptionItem(visitId) {
    const container = document.getElementById('prescription-items-' + visitId);
    const index = container.children.length;
    const row = document.createElement('div');
    row.className = 'grid grid-cols-12 gap-2 prescription-row';
    row.innerHTML = `
        <div class="col-span-3 relative">
            <select name="items[${index}][medication_id]" class="h-9 w-full appearance-none rounded-md border border-gray-200 bg-white px-3 pr-8 py-1 text-xs transition-shadow outline-none focus-visible:ring-2 focus-visible:ring-slate-300" required>
            <option value="">Select drug</option>
            @foreach ($medications as $med)
                <option value="{{ $med->id }}">{{ $med->name }}</option>
            @endforeach
            </select>
            <i class="hgi-stroke hgi-arrow-down-01 pointer-events-none absolute right-2.5 top-1/2 -translate-y-1/2 text-sm text-gray-400"></i>
        </div>
        <input type="number" name="items[${index}][quantity]" placeholder="Qty" class="col-span-1 h-9 w-full rounded-md border border-gray-200 bg-white px-3 py-1 text-xs transition-shadow outline-none focus-visible:ring-2 focus-visible:ring-slate-300" required>
        <input type="text" name="items[${index}][dosage]" placeholder="Dose" class="col-span-2 h-9 w-full rounded-md border border-gray-200 bg-white px-3 py-1 text-xs transition-shadow outline-none focus-visible:ring-2 focus-visible:ring-slate-300" required>
        <input type="text" name="items[${index}][frequency]" placeholder="Frequency" class="col-span-2 h-9 w-full rounded-md border border-gray-200 bg-white px-3 py-1 text-xs transition-shadow outline-none focus-visible:ring-2 focus-visible:ring-slate-300" required>
        <input type="text" name="items[${index}][duration]" placeholder="Duration" class="col-span-2 h-9 w-full rounded-md border border-gray-200 bg-white px-3 py-1 text-xs transition-shadow outline-none focus-visible:ring-2 focus-visible:ring-slate-300" required>
        <input type="text" name="items[${index}][instructions]" placeholder="Instructions" class="col-span-2 h-9 w-full rounded-md border border-gray-200 bg-white px-3 py-1 text-xs transition-shadow outline-none focus-visible:ring-2 focus-visible:ring-slate-300">
        <button type="button" onclick="this.closest('.prescription-row').remove()" class="col-span-1 text-red-500 hover:text-red-700">
            <i class="hgi-stroke hgi-delete-02 text-base mx-auto"></i>
        </button>
    `;
    container.appendChild(row);
}

function toggleVitalsForm(visitId) {
    const display = document.getElementById('vitals-display-' + visitId);
    const form = document.getElementById('vitals-form-' + visitId);
    display.classList.toggle('hidden');
    form.classList.toggle('hidden');
}
</script>
                        <div class="border border-gray-100 rounded-lg overflow-hidden">
                            <div class="bg-gray-50 px-4 py-2 border-b border-gray-100 flex items-center justify-between">
                                <p class="text-xs font-semibold text-gray-700 uppercase tracking-wide">Vitals</p>
                                <button type="button" onclick="toggleVitalsForm('{{ $visit->id }}')" class="text-xs font-medium text-slate-600 hover:text-slate-800 flex items-center gap-1">
                                    <i class="hgi-stroke hgi-pencil-edit-01 text-sm"></i>
                                    {{ $visit->vitals ? 'Edit' : 'Record' }}
                                </button>
                            </div>

                            <script>
function swalConfirmSubmit(event, message) {
    event.preventDefault();
    const form = event.target;
    Swal.fire({
        icon: 'question',
        title: 'Confirm',
        text: message,
        showCancelButton: true,
        confirmButtonText: 'Yes',
        cancelButtonText: 'Cancel',
        confirmButtonColor: '#0f172a',
    }).then((result) => {
        if (result.isConfirmed) form.submit();
    });
    return false;
}

function addPrescriptionItem(visitId) {
    const container = document.getElementById('prescription-items-' + visitId);
    const index = container.children.length;
    const row = document.createElement('div');
    row.className = 'grid grid-cols-12 gap-2 prescription-row';
    row.innerHTML = `
        <div class="col-span-3 relative">
            <select name="items[${index}][medication_id]" class="h-9 w-full appearance-none rounded-md border border-gray-200 bg-white px-3 pr-8 py-1 text-xs transition-shadow outline-none focus-visible:ring-2 focus-visible:ring-slate-300" required>
            <option value="">Select drug</option>
            @foreach ($medications as $med)
                <option value="{{ $med->id }}">{{ $med->name }}</option>
            @endforeach
            </select>
            <i class="hgi-stroke hgi-arrow-down-01 pointer-events-none absolute right-2.5 top-1/2 -translate-y-1/2 text-sm text-gray-400"></i>
        </div>
        <input type="number" name="items[${index}][quantity]" placeholder="Qty" class="col-span-1 h-9 w-full rounded-md border border-gray-200 bg-white px-3 py-1 text-xs transition-shadow outline-none focus-visible:ring-2 focus-visible:ring-slate-300" required>
        <input type="text" name="items[${index}][dosage]" placeholder="Dose" class="col-span-2 h-9 w-full rounded-md border border-gray-200 bg-white px-3 py-1 text-xs transition-shadow outline-none focus-visible:ring-2 focus-visible:ring-slate-300" required>
        <input type="text" name="items[${index}][frequency]" placeholder="Frequency" class="col-span-2 h-9 w-full rounded-md border border-gray-200 bg-white px-3 py-1 text-xs transition-shadow outline-none focus-visible:ring-2 focus-visible:ring-slate-300" required>
        <input type="text" name="items[${index}][duration]" placeholder="Duration" class="col-span-2 h-9 w-full rounded-md border border-gray-200 bg-white px-3 py-1 text-xs transition-shadow outline-none focus-visible:ring-2 focus-visible:ring-slate-300" required>
        <input type="text" name="items[${index}][instructions]" placeholder="Instructions" class="col-span-2 h-9 w-full rounded-md border border-gray-200 bg-white px-3 py-1 text-xs transition-shadow outline-none focus-visible:ring-2 focus-visible:ring-slate-300">
        <button type="button" onclick="this.closest('.prescription-row').remove()" class="col-span-1 text-red-500 hover:text-red-700">
            <i class="hgi-stroke hgi-delete-02 text-base mx-auto"></i>
        </button>
    `;
    container.appendChild(row);
}

function toggleVitalsForm(visitId) {
    const display = document.getElementById('vitals-display-' + visitId);
    const form = document.getElementById('vitals-form-' + visitId);
    display.classList.toggle('hidden');
    form.classList.toggle('hidden');
}
</script>
                            <div id="vitals-display-{{ $visit->id }}">
                                @if ($visit->vitals)
                                    <div class="grid grid-cols-2 gap-2 p-3 text-sm">
                                        <div class="bg-gray-50 rounded p-2.5">
                                            <p class="text-[10px] text-gray-400 uppercase font-medium">BP</p>
                                            <p class="font-semibold text-gray-900">{{ $visit->vitals->blood_pressure ?? '-' }}</p>
                                        </div>
                                        <div class="bg-gray-50 rounded p-2.5">
                                            <p class="text-[10px] text-gray-400 uppercase font-medium">Temp</p>
                                            <p class="font-semibold text-gray-900">{{ $visit->vitals->temperature ?? '-' }} °C</p>
                                        </div>
                                        <div class="bg-gray-50 rounded p-2.5">
                                            <p class="text-[10px] text-gray-400 uppercase font-medium">Pulse</p>
                                            <p class="font-semibold text-gray-900">{{ $visit->vitals->pulse ?? '-' }} bpm</p>
                                        </div>
                                        <div class="bg-gray-50 rounded p-2.5">
                                            <p class="text-[10px] text-gray-400 uppercase font-medium">SpO2</p>
                                            <p class="font-semibold text-gray-900">{{ $visit->vitals->oxygen_saturation ?? '-' }}%</p>
                                        </div>
                                        <div class="bg-gray-50 rounded p-2.5">
                                            <p class="text-[10px] text-gray-400 uppercase font-medium">Weight</p>
                                            <p class="font-semibold text-gray-900">{{ $visit->vitals->weight ?? '-' }} kg</p>
                                        </div>
                                        <div class="bg-gray-50 rounded p-2.5">
                                            <p class="text-[10px] text-gray-400 uppercase font-medium">Height</p>
                                            <p class="font-semibold text-gray-900">{{ $visit->vitals->height ?? '-' }} cm</p>
                                        </div>
                                        <div class="bg-gray-50 rounded p-2.5">
                                            <p class="text-[10px] text-gray-400 uppercase font-medium">Resp. Rate</p>
                                            <p class="font-semibold text-gray-900">{{ $visit->vitals->respiratory_rate ?? '-' }} /min</p>
                                        </div>
                                    </div>
                                    @if ($visit->vitals->notes)
                                        <div class="px-4 pb-3">
                                            <p class="text-xs text-gray-600 bg-gray-50 rounded-lg p-2">{{ $visit->vitals->notes }}</p>
                                        </div>
                                    @endif
                                @else
                                    <div class="p-4 text-sm text-gray-400">No vitals recorded yet. Click "Record" to add.</div>
                                @endif
                            </div>

                            <script>
function swalConfirmSubmit(event, message) {
    event.preventDefault();
    const form = event.target;
    Swal.fire({
        icon: 'question',
        title: 'Confirm',
        text: message,
        showCancelButton: true,
        confirmButtonText: 'Yes',
        cancelButtonText: 'Cancel',
        confirmButtonColor: '#0f172a',
    }).then((result) => {
        if (result.isConfirmed) form.submit();
    });
    return false;
}

function addPrescriptionItem(visitId) {
    const container = document.getElementById('prescription-items-' + visitId);
    const index = container.children.length;
    const row = document.createElement('div');
    row.className = 'grid grid-cols-12 gap-2 prescription-row';
    row.innerHTML = `
        <div class="col-span-3 relative">
            <select name="items[${index}][medication_id]" class="h-9 w-full appearance-none rounded-md border border-gray-200 bg-white px-3 pr-8 py-1 text-xs transition-shadow outline-none focus-visible:ring-2 focus-visible:ring-slate-300" required>
            <option value="">Select drug</option>
            @foreach ($medications as $med)
                <option value="{{ $med->id }}">{{ $med->name }}</option>
            @endforeach
            </select>
            <i class="hgi-stroke hgi-arrow-down-01 pointer-events-none absolute right-2.5 top-1/2 -translate-y-1/2 text-sm text-gray-400"></i>
        </div>
        <input type="number" name="items[${index}][quantity]" placeholder="Qty" class="col-span-1 h-9 w-full rounded-md border border-gray-200 bg-white px-3 py-1 text-xs transition-shadow outline-none focus-visible:ring-2 focus-visible:ring-slate-300" required>
        <input type="text" name="items[${index}][dosage]" placeholder="Dose" class="col-span-2 h-9 w-full rounded-md border border-gray-200 bg-white px-3 py-1 text-xs transition-shadow outline-none focus-visible:ring-2 focus-visible:ring-slate-300" required>
        <input type="text" name="items[${index}][frequency]" placeholder="Frequency" class="col-span-2 h-9 w-full rounded-md border border-gray-200 bg-white px-3 py-1 text-xs transition-shadow outline-none focus-visible:ring-2 focus-visible:ring-slate-300" required>
        <input type="text" name="items[${index}][duration]" placeholder="Duration" class="col-span-2 h-9 w-full rounded-md border border-gray-200 bg-white px-3 py-1 text-xs transition-shadow outline-none focus-visible:ring-2 focus-visible:ring-slate-300" required>
        <input type="text" name="items[${index}][instructions]" placeholder="Instructions" class="col-span-2 h-9 w-full rounded-md border border-gray-200 bg-white px-3 py-1 text-xs transition-shadow outline-none focus-visible:ring-2 focus-visible:ring-slate-300">
        <button type="button" onclick="this.closest('.prescription-row').remove()" class="col-span-1 text-red-500 hover:text-red-700">
            <i class="hgi-stroke hgi-delete-02 text-base mx-auto"></i>
        </button>
    `;
    container.appendChild(row);
}

function toggleVitalsForm(visitId) {
    const display = document.getElementById('vitals-display-' + visitId);
    const form = document.getElementById('vitals-form-' + visitId);
    display.classList.toggle('hidden');
    form.classList.toggle('hidden');
}
</script>
                            <div id="vitals-form-{{ $visit->id }}" class="hidden p-4 bg-gray-50 border-t border-gray-100">
                                <form method="POST" action="{{ route('doctor.visits.vitals', $visit) }}" class="space-y-3">
                                    @csrf
                                    <div class="grid grid-cols-2 gap-3">
                                        <div>
                                            <label class="text-[10px] font-medium text-gray-500 uppercase">Blood Pressure</label>
                                            <x-ui.input type="text" name="blood_pressure" value="{{ $visit->vitals?->blood_pressure ?? '' }}" placeholder="120/80" class="w-full mt-1 border border-gray-200 rounded-lg px-2.5 py-1.5 text-xs focus:ring-2 focus:ring-slate-300" />
                                        </div>
                                        <div>
                                            <label class="text-[10px] font-medium text-gray-500 uppercase">Temperature (°C)</label>
                                            <x-ui.input type="number" name="temperature" step="0.1" value="{{ $visit->vitals?->temperature ?? '' }}" placeholder="36.5" class="w-full mt-1 border border-gray-200 rounded-lg px-2.5 py-1.5 text-xs focus:ring-2 focus:ring-slate-300" />
                                        </div>
                                        <div>
                                            <label class="text-[10px] font-medium text-gray-500 uppercase">Pulse (bpm)</label>
                                            <x-ui.input type="number" name="pulse" value="{{ $visit->vitals?->pulse ?? '' }}" placeholder="72" class="w-full mt-1 border border-gray-200 rounded-lg px-2.5 py-1.5 text-xs focus:ring-2 focus:ring-slate-300" />
                                        </div>
                                        <div>
                                            <label class="text-[10px] font-medium text-gray-500 uppercase">SpO2 (%)</label>
                                            <x-ui.input type="number" name="oxygen_saturation" step="0.1" value="{{ $visit->vitals?->oxygen_saturation ?? '' }}" placeholder="98" class="w-full mt-1 border border-gray-200 rounded-lg px-2.5 py-1.5 text-xs focus:ring-2 focus:ring-slate-300" />
                                        </div>
                                        <div>
                                            <label class="text-[10px] font-medium text-gray-500 uppercase">Weight (kg)</label>
                                            <x-ui.input type="number" name="weight" step="0.1" value="{{ $visit->vitals?->weight ?? '' }}" placeholder="65" class="w-full mt-1 border border-gray-200 rounded-lg px-2.5 py-1.5 text-xs focus:ring-2 focus:ring-slate-300" />
                                        </div>
                                        <div>
                                            <label class="text-[10px] font-medium text-gray-500 uppercase">Height (cm)</label>
                                            <x-ui.input type="number" name="height" step="0.1" value="{{ $visit->vitals?->height ?? '' }}" placeholder="170" class="w-full mt-1 border border-gray-200 rounded-lg px-2.5 py-1.5 text-xs focus:ring-2 focus:ring-slate-300" />
                                        </div>
                                        <div>
                                            <label class="text-[10px] font-medium text-gray-500 uppercase">Resp. Rate (/min)</label>
                                            <x-ui.input type="number" name="respiratory_rate" value="{{ $visit->vitals?->respiratory_rate ?? '' }}" placeholder="16" class="w-full mt-1 border border-gray-200 rounded-lg px-2.5 py-1.5 text-xs focus:ring-2 focus:ring-slate-300" />
                                        </div>
                                    </div>
                                    <div>
                                        <label class="text-[10px] font-medium text-gray-500 uppercase">Notes</label>
                                        <x-ui.textarea name="notes" rows="2" placeholder="Additional observations..." class="w-full mt-1 border border-gray-200 rounded-lg px-2.5 py-1.5 text-xs focus:ring-2 focus:ring-slate-300">{{ $visit->vitals?->notes ?? '' }}</x-ui.textarea>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2 bg-slate-800 hover:bg-slate-700 text-white text-xs font-semibold rounded transition-colors">
                                            <i class="hgi-stroke hgi-tick-02 text-sm"></i>
                                            Save Vitals
                                        </button>
                                        <button type="button" onclick="toggleVitalsForm('{{ $visit->id }}')" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 text-xs font-semibold rounded-lg transition-colors">
                                            Cancel
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <script>
function swalConfirmSubmit(event, message) {
    event.preventDefault();
    const form = event.target;
    Swal.fire({
        icon: 'question',
        title: 'Confirm',
        text: message,
        showCancelButton: true,
        confirmButtonText: 'Yes',
        cancelButtonText: 'Cancel',
        confirmButtonColor: '#0f172a',
    }).then((result) => {
        if (result.isConfirmed) form.submit();
    });
    return false;
}

function addPrescriptionItem(visitId) {
    const container = document.getElementById('prescription-items-' + visitId);
    const index = container.children.length;
    const row = document.createElement('div');
    row.className = 'grid grid-cols-12 gap-2 prescription-row';
    row.innerHTML = `
        <div class="col-span-3 relative">
            <select name="items[${index}][medication_id]" class="h-9 w-full appearance-none rounded-md border border-gray-200 bg-white px-3 pr-8 py-1 text-xs transition-shadow outline-none focus-visible:ring-2 focus-visible:ring-slate-300" required>
            <option value="">Select drug</option>
            @foreach ($medications as $med)
                <option value="{{ $med->id }}">{{ $med->name }}</option>
            @endforeach
            </select>
            <i class="hgi-stroke hgi-arrow-down-01 pointer-events-none absolute right-2.5 top-1/2 -translate-y-1/2 text-sm text-gray-400"></i>
        </div>
        <input type="number" name="items[${index}][quantity]" placeholder="Qty" class="col-span-1 h-9 w-full rounded-md border border-gray-200 bg-white px-3 py-1 text-xs transition-shadow outline-none focus-visible:ring-2 focus-visible:ring-slate-300" required>
        <input type="text" name="items[${index}][dosage]" placeholder="Dose" class="col-span-2 h-9 w-full rounded-md border border-gray-200 bg-white px-3 py-1 text-xs transition-shadow outline-none focus-visible:ring-2 focus-visible:ring-slate-300" required>
        <input type="text" name="items[${index}][frequency]" placeholder="Frequency" class="col-span-2 h-9 w-full rounded-md border border-gray-200 bg-white px-3 py-1 text-xs transition-shadow outline-none focus-visible:ring-2 focus-visible:ring-slate-300" required>
        <input type="text" name="items[${index}][duration]" placeholder="Duration" class="col-span-2 h-9 w-full rounded-md border border-gray-200 bg-white px-3 py-1 text-xs transition-shadow outline-none focus-visible:ring-2 focus-visible:ring-slate-300" required>
        <input type="text" name="items[${index}][instructions]" placeholder="Instructions" class="col-span-2 h-9 w-full rounded-md border border-gray-200 bg-white px-3 py-1 text-xs transition-shadow outline-none focus-visible:ring-2 focus-visible:ring-slate-300">
        <button type="button" onclick="this.closest('.prescription-row').remove()" class="col-span-1 text-red-500 hover:text-red-700">
            <i class="hgi-stroke hgi-delete-02 text-base mx-auto"></i>
        </button>
    `;
    container.appendChild(row);
}

function toggleVitalsForm(visitId) {
    const display = document.getElementById('vitals-display-' + visitId);
    const form = document.getElementById('vitals-form-' + visitId);
    display.classList.toggle('hidden');
    form.classList.toggle('hidden');
}
</script>
                        <div class="flex flex-wrap gap-2">
                            <a href="{{ route('patients.show', $visit->patient) }}" class="inline-flex items-center gap-1 px-3 py-1.5 bg-white border border-gray-200 rounded-lg text-xs font-medium text-gray-700 hover:bg-gray-50 hover:border-gray-300 transition-colors">
                                <i class="hgi-stroke hgi-view text-sm"></i>
                                Profile
                            </a>
                            <a href="{{ route('patients.history', $visit->patient) }}" class="inline-flex items-center gap-1 px-3 py-1.5 bg-white border border-gray-200 rounded-lg text-xs font-medium text-gray-700 hover:bg-gray-50 hover:border-gray-300 transition-colors">
                                <i class="hgi-stroke hgi-clock-01 text-sm"></i>
                                History
                            </a>
                            <a href="{{ route('patients.documents.index', $visit->patient) }}" class="inline-flex items-center gap-1 px-3 py-1.5 bg-white border border-gray-200 rounded-lg text-xs font-medium text-gray-700 hover:bg-gray-50 hover:border-gray-300 transition-colors">
                                <i class="hgi-stroke hgi-file-02 text-sm"></i>
                                Documents
                            </a>
                        </div>
                    </div>

                    <script>
function swalConfirmSubmit(event, message) {
    event.preventDefault();
    const form = event.target;
    Swal.fire({
        icon: 'question',
        title: 'Confirm',
        text: message,
        showCancelButton: true,
        confirmButtonText: 'Yes',
        cancelButtonText: 'Cancel',
        confirmButtonColor: '#0f172a',
    }).then((result) => {
        if (result.isConfirmed) form.submit();
    });
    return false;
}

function addPrescriptionItem(visitId) {
    const container = document.getElementById('prescription-items-' + visitId);
    const index = container.children.length;
    const row = document.createElement('div');
    row.className = 'grid grid-cols-12 gap-2 prescription-row';
    row.innerHTML = `
        <div class="col-span-3 relative">
            <select name="items[${index}][medication_id]" class="h-9 w-full appearance-none rounded-md border border-gray-200 bg-white px-3 pr-8 py-1 text-xs transition-shadow outline-none focus-visible:ring-2 focus-visible:ring-slate-300" required>
            <option value="">Select drug</option>
            @foreach ($medications as $med)
                <option value="{{ $med->id }}">{{ $med->name }}</option>
            @endforeach
            </select>
            <i class="hgi-stroke hgi-arrow-down-01 pointer-events-none absolute right-2.5 top-1/2 -translate-y-1/2 text-sm text-gray-400"></i>
        </div>
        <input type="number" name="items[${index}][quantity]" placeholder="Qty" class="col-span-1 h-9 w-full rounded-md border border-gray-200 bg-white px-3 py-1 text-xs transition-shadow outline-none focus-visible:ring-2 focus-visible:ring-slate-300" required>
        <input type="text" name="items[${index}][dosage]" placeholder="Dose" class="col-span-2 h-9 w-full rounded-md border border-gray-200 bg-white px-3 py-1 text-xs transition-shadow outline-none focus-visible:ring-2 focus-visible:ring-slate-300" required>
        <input type="text" name="items[${index}][frequency]" placeholder="Frequency" class="col-span-2 h-9 w-full rounded-md border border-gray-200 bg-white px-3 py-1 text-xs transition-shadow outline-none focus-visible:ring-2 focus-visible:ring-slate-300" required>
        <input type="text" name="items[${index}][duration]" placeholder="Duration" class="col-span-2 h-9 w-full rounded-md border border-gray-200 bg-white px-3 py-1 text-xs transition-shadow outline-none focus-visible:ring-2 focus-visible:ring-slate-300" required>
        <input type="text" name="items[${index}][instructions]" placeholder="Instructions" class="col-span-2 h-9 w-full rounded-md border border-gray-200 bg-white px-3 py-1 text-xs transition-shadow outline-none focus-visible:ring-2 focus-visible:ring-slate-300">
        <button type="button" onclick="this.closest('.prescription-row').remove()" class="col-span-1 text-red-500 hover:text-red-700">
            <i class="hgi-stroke hgi-delete-02 text-base mx-auto"></i>
        </button>
    `;
    container.appendChild(row);
}

function toggleVitalsForm(visitId) {
    const display = document.getElementById('vitals-display-' + visitId);
    const form = document.getElementById('vitals-form-' + visitId);
    display.classList.toggle('hidden');
    form.classList.toggle('hidden');
}
</script>
                    <div class="lg:col-span-2 space-y-4">
                        @if ($visit->status === \App\Enums\VisitStatus::WaitingForDoctor->value)
                            <div class="flex flex-wrap gap-2">
                                <form method="POST" action="{{ route('doctor.visits.call', $visit) }}">
                                    @csrf
                                    <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2 bg-slate-800 hover:bg-slate-700 text-white text-xs font-semibold rounded transition-colors">
                                        <i class="hgi-stroke hgi-arrow-right-02 text-sm"></i>
                                        Call In
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('doctor.visits.no-show', $visit) }}" onsubmit="return swalConfirmSubmit(event, 'Mark this patient as no-show?')">
                                    @csrf
                                    <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2 bg-red-50 hover:bg-red-100 text-red-600 border border-red-200 text-xs font-semibold rounded transition-colors">
                                        <i class="hgi-stroke hgi-cancel-circle text-sm"></i>
                                        No Show
                                    </button>
                                </form>
                            </div>
                        @endif

                        @if ($visit->status === \App\Enums\VisitStatus::WithDoctor->value)
                            <script>
function swalConfirmSubmit(event, message) {
    event.preventDefault();
    const form = event.target;
    Swal.fire({
        icon: 'question',
        title: 'Confirm',
        text: message,
        showCancelButton: true,
        confirmButtonText: 'Yes',
        cancelButtonText: 'Cancel',
        confirmButtonColor: '#0f172a',
    }).then((result) => {
        if (result.isConfirmed) form.submit();
    });
    return false;
}

function addPrescriptionItem(visitId) {
    const container = document.getElementById('prescription-items-' + visitId);
    const index = container.children.length;
    const row = document.createElement('div');
    row.className = 'grid grid-cols-12 gap-2 prescription-row';
    row.innerHTML = `
        <div class="col-span-3 relative">
            <select name="items[${index}][medication_id]" class="h-9 w-full appearance-none rounded-md border border-gray-200 bg-white px-3 pr-8 py-1 text-xs transition-shadow outline-none focus-visible:ring-2 focus-visible:ring-slate-300" required>
            <option value="">Select drug</option>
            @foreach ($medications as $med)
                <option value="{{ $med->id }}">{{ $med->name }}</option>
            @endforeach
            </select>
            <i class="hgi-stroke hgi-arrow-down-01 pointer-events-none absolute right-2.5 top-1/2 -translate-y-1/2 text-sm text-gray-400"></i>
        </div>
        <input type="number" name="items[${index}][quantity]" placeholder="Qty" class="col-span-1 h-9 w-full rounded-md border border-gray-200 bg-white px-3 py-1 text-xs transition-shadow outline-none focus-visible:ring-2 focus-visible:ring-slate-300" required>
        <input type="text" name="items[${index}][dosage]" placeholder="Dose" class="col-span-2 h-9 w-full rounded-md border border-gray-200 bg-white px-3 py-1 text-xs transition-shadow outline-none focus-visible:ring-2 focus-visible:ring-slate-300" required>
        <input type="text" name="items[${index}][frequency]" placeholder="Frequency" class="col-span-2 h-9 w-full rounded-md border border-gray-200 bg-white px-3 py-1 text-xs transition-shadow outline-none focus-visible:ring-2 focus-visible:ring-slate-300" required>
        <input type="text" name="items[${index}][duration]" placeholder="Duration" class="col-span-2 h-9 w-full rounded-md border border-gray-200 bg-white px-3 py-1 text-xs transition-shadow outline-none focus-visible:ring-2 focus-visible:ring-slate-300" required>
        <input type="text" name="items[${index}][instructions]" placeholder="Instructions" class="col-span-2 h-9 w-full rounded-md border border-gray-200 bg-white px-3 py-1 text-xs transition-shadow outline-none focus-visible:ring-2 focus-visible:ring-slate-300">
        <button type="button" onclick="this.closest('.prescription-row').remove()" class="col-span-1 text-red-500 hover:text-red-700">
            <i class="hgi-stroke hgi-delete-02 text-base mx-auto"></i>
        </button>
    `;
    container.appendChild(row);
}

function toggleVitalsForm(visitId) {
    const display = document.getElementById('vitals-display-' + visitId);
    const form = document.getElementById('vitals-form-' + visitId);
    display.classList.toggle('hidden');
    form.classList.toggle('hidden');
}
</script>
                            <form method="POST" action="{{ route('doctor.visits.consult', $visit) }}" class="w-full bg-gray-50 border border-gray-200 rounded-md p-4 space-y-3">
                                @csrf
                                <div class="flex items-center gap-2 mb-2">
                                    <div class="w-7 h-7 rounded bg-slate-100 text-slate-600 flex items-center justify-center">
                                        <i class="hgi-stroke hgi-file-02 text-base"></i>
                                    </div>
                                    <p class="text-sm font-semibold text-gray-900">Consultation</p>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                    <x-ui.textarea name="history" placeholder="History" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-xs focus:ring-2 focus:ring-slate-300" rows="3">{{ $visit->consultation?->history }}</x-ui.textarea>
                                    <x-ui.textarea name="examination" placeholder="Examination" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-xs focus:ring-2 focus:ring-slate-300" rows="3">{{ $visit->consultation?->examination }}</x-ui.textarea>
                                    <x-ui.textarea name="diagnosis" placeholder="Diagnosis" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-xs focus:ring-2 focus:ring-slate-300" rows="3">{{ $visit->consultation?->diagnosis }}</x-ui.textarea>
                                    <x-ui.textarea name="notes" placeholder="Clinical Notes" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-xs focus:ring-2 focus:ring-slate-300" rows="3">{{ $visit->consultation?->notes }}</x-ui.textarea>
                                </div>
                                <div class="flex justify-end">
                                    <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2 bg-slate-800 hover:bg-slate-700 text-white text-xs font-semibold rounded transition-colors">
                                        <i class="hgi-stroke hgi-download-02 text-sm"></i>
                                        Save Consultation
                                    </button>
                                </div>
                            </form>

                            <script>
function swalConfirmSubmit(event, message) {
    event.preventDefault();
    const form = event.target;
    Swal.fire({
        icon: 'question',
        title: 'Confirm',
        text: message,
        showCancelButton: true,
        confirmButtonText: 'Yes',
        cancelButtonText: 'Cancel',
        confirmButtonColor: '#0f172a',
    }).then((result) => {
        if (result.isConfirmed) form.submit();
    });
    return false;
}

function addPrescriptionItem(visitId) {
    const container = document.getElementById('prescription-items-' + visitId);
    const index = container.children.length;
    const row = document.createElement('div');
    row.className = 'grid grid-cols-12 gap-2 prescription-row';
    row.innerHTML = `
        <div class="col-span-3 relative">
            <select name="items[${index}][medication_id]" class="h-9 w-full appearance-none rounded-md border border-gray-200 bg-white px-3 pr-8 py-1 text-xs transition-shadow outline-none focus-visible:ring-2 focus-visible:ring-slate-300" required>
            <option value="">Select drug</option>
            @foreach ($medications as $med)
                <option value="{{ $med->id }}">{{ $med->name }}</option>
            @endforeach
            </select>
            <i class="hgi-stroke hgi-arrow-down-01 pointer-events-none absolute right-2.5 top-1/2 -translate-y-1/2 text-sm text-gray-400"></i>
        </div>
        <input type="number" name="items[${index}][quantity]" placeholder="Qty" class="col-span-1 h-9 w-full rounded-md border border-gray-200 bg-white px-3 py-1 text-xs transition-shadow outline-none focus-visible:ring-2 focus-visible:ring-slate-300" required>
        <input type="text" name="items[${index}][dosage]" placeholder="Dose" class="col-span-2 h-9 w-full rounded-md border border-gray-200 bg-white px-3 py-1 text-xs transition-shadow outline-none focus-visible:ring-2 focus-visible:ring-slate-300" required>
        <input type="text" name="items[${index}][frequency]" placeholder="Frequency" class="col-span-2 h-9 w-full rounded-md border border-gray-200 bg-white px-3 py-1 text-xs transition-shadow outline-none focus-visible:ring-2 focus-visible:ring-slate-300" required>
        <input type="text" name="items[${index}][duration]" placeholder="Duration" class="col-span-2 h-9 w-full rounded-md border border-gray-200 bg-white px-3 py-1 text-xs transition-shadow outline-none focus-visible:ring-2 focus-visible:ring-slate-300" required>
        <input type="text" name="items[${index}][instructions]" placeholder="Instructions" class="col-span-2 h-9 w-full rounded-md border border-gray-200 bg-white px-3 py-1 text-xs transition-shadow outline-none focus-visible:ring-2 focus-visible:ring-slate-300">
        <button type="button" onclick="this.closest('.prescription-row').remove()" class="col-span-1 text-red-500 hover:text-red-700">
            <i class="hgi-stroke hgi-delete-02 text-base mx-auto"></i>
        </button>
    `;
    container.appendChild(row);
}

function toggleVitalsForm(visitId) {
    const display = document.getElementById('vitals-display-' + visitId);
    const form = document.getElementById('vitals-form-' + visitId);
    display.classList.toggle('hidden');
    form.classList.toggle('hidden');
}
</script>
                            <form method="POST" action="{{ route('doctor.visits.lab', $visit) }}" class="w-full bg-gray-50 border border-gray-200 rounded-md p-4 space-y-3">
                                @csrf
                                <div class="flex items-center gap-2 mb-2">
                                    <div class="w-7 h-7 rounded bg-slate-100 text-slate-600 flex items-center justify-center">
                                        <i class="hgi-stroke hgi-test-tube-01 text-base"></i>
                                    </div>
                                    <p class="text-sm font-semibold text-gray-900">Order Lab Tests</p>
                                </div>
                                <div class="grid grid-cols-2 md:grid-cols-3 gap-2">
                                    @foreach ($labTests as $test)
                                        <label class="flex items-center gap-2 p-2 bg-white border border-gray-200 rounded cursor-pointer hover:bg-gray-50 transition-colors">
                                            <input type="checkbox" name="test_ids[]" value="{{ $test->id }}" class="rounded border-gray-300 text-slate-700 focus:ring-slate-400">
                                            <span class="text-xs text-gray-700">{{ $test->name }}</span>
                                        </label>
                                    @endforeach
                                </div>
                                <x-ui.textarea name="notes" placeholder="Clinical notes for lab" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-xs focus:ring-2 focus:ring-slate-300" rows="2"></x-ui.textarea>
                                <div class="flex justify-end">
                                    <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2 bg-slate-800 hover:bg-slate-700 text-white text-xs font-semibold rounded transition-colors">
                                        <i class="hgi-stroke hgi-plus-sign text-sm"></i>
                                        Send to Lab
                                    </button>
                                </div>
                            </form>

                            <script>
function swalConfirmSubmit(event, message) {
    event.preventDefault();
    const form = event.target;
    Swal.fire({
        icon: 'question',
        title: 'Confirm',
        text: message,
        showCancelButton: true,
        confirmButtonText: 'Yes',
        cancelButtonText: 'Cancel',
        confirmButtonColor: '#0f172a',
    }).then((result) => {
        if (result.isConfirmed) form.submit();
    });
    return false;
}

function addPrescriptionItem(visitId) {
    const container = document.getElementById('prescription-items-' + visitId);
    const index = container.children.length;
    const row = document.createElement('div');
    row.className = 'grid grid-cols-12 gap-2 prescription-row';
    row.innerHTML = `
        <div class="col-span-3 relative">
            <select name="items[${index}][medication_id]" class="h-9 w-full appearance-none rounded-md border border-gray-200 bg-white px-3 pr-8 py-1 text-xs transition-shadow outline-none focus-visible:ring-2 focus-visible:ring-slate-300" required>
            <option value="">Select drug</option>
            @foreach ($medications as $med)
                <option value="{{ $med->id }}">{{ $med->name }}</option>
            @endforeach
            </select>
            <i class="hgi-stroke hgi-arrow-down-01 pointer-events-none absolute right-2.5 top-1/2 -translate-y-1/2 text-sm text-gray-400"></i>
        </div>
        <input type="number" name="items[${index}][quantity]" placeholder="Qty" class="col-span-1 h-9 w-full rounded-md border border-gray-200 bg-white px-3 py-1 text-xs transition-shadow outline-none focus-visible:ring-2 focus-visible:ring-slate-300" required>
        <input type="text" name="items[${index}][dosage]" placeholder="Dose" class="col-span-2 h-9 w-full rounded-md border border-gray-200 bg-white px-3 py-1 text-xs transition-shadow outline-none focus-visible:ring-2 focus-visible:ring-slate-300" required>
        <input type="text" name="items[${index}][frequency]" placeholder="Frequency" class="col-span-2 h-9 w-full rounded-md border border-gray-200 bg-white px-3 py-1 text-xs transition-shadow outline-none focus-visible:ring-2 focus-visible:ring-slate-300" required>
        <input type="text" name="items[${index}][duration]" placeholder="Duration" class="col-span-2 h-9 w-full rounded-md border border-gray-200 bg-white px-3 py-1 text-xs transition-shadow outline-none focus-visible:ring-2 focus-visible:ring-slate-300" required>
        <input type="text" name="items[${index}][instructions]" placeholder="Instructions" class="col-span-2 h-9 w-full rounded-md border border-gray-200 bg-white px-3 py-1 text-xs transition-shadow outline-none focus-visible:ring-2 focus-visible:ring-slate-300">
        <button type="button" onclick="this.closest('.prescription-row').remove()" class="col-span-1 text-red-500 hover:text-red-700">
            <i class="hgi-stroke hgi-delete-02 text-base mx-auto"></i>
        </button>
    `;
    container.appendChild(row);
}

function toggleVitalsForm(visitId) {
    const display = document.getElementById('vitals-display-' + visitId);
    const form = document.getElementById('vitals-form-' + visitId);
    display.classList.toggle('hidden');
    form.classList.toggle('hidden');
}
</script>
                            <form method="POST" action="{{ route('doctor.visits.ultrasound', $visit) }}" class="w-full bg-gray-50 border border-gray-200 rounded-md p-4 space-y-3">
                                @csrf
                                <div class="flex items-center gap-2 mb-2">
                                    <div class="w-7 h-7 rounded bg-slate-100 text-slate-600 flex items-center justify-center">
                                        <i class="hgi-stroke hgi-analytics-02 text-base"></i>
                                    </div>
                                    <p class="text-sm font-semibold text-gray-900">Order Ultrasound</p>
                                </div>
                                @if($ultrasoundServices->isNotEmpty())
                                <div class="grid grid-cols-2 md:grid-cols-3 gap-2">
                                    @foreach($ultrasoundServices as $service)
                                        <label class="flex items-center gap-2 p-2 bg-white border border-gray-200 rounded cursor-pointer hover:bg-gray-50 transition-colors">
                                            <input type="checkbox" name="service_ids[]" value="{{ $service->id }}" class="rounded border-gray-300 text-slate-700 focus:ring-slate-400">
                                            <span class="text-xs text-gray-700">{{ $service->name }}</span>
                                        </label>
                                    @endforeach
                                </div>
                                <x-ui.textarea name="notes" placeholder="Clinical notes for ultrasound" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-xs focus:ring-2 focus:ring-slate-300" rows="2"></x-ui.textarea>
                                <div class="flex justify-end">
                                    <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2 bg-slate-800 hover:bg-slate-700 text-white text-xs font-semibold rounded transition-colors">
                                        <i class="hgi-stroke hgi-plus-sign text-sm"></i>
                                        Send to Ultrasound
                                    </button>
                                </div>
                                @else
                                <p class="text-xs text-gray-500 py-2">No ultrasound services configured. <a href="{{ route('ultrasound-services.create') }}" class="text-slate-600 hover:text-slate-800">Add services</a></p>
                                @endif
                            </form>

                            <script>
function swalConfirmSubmit(event, message) {
    event.preventDefault();
    const form = event.target;
    Swal.fire({
        icon: 'question',
        title: 'Confirm',
        text: message,
        showCancelButton: true,
        confirmButtonText: 'Yes',
        cancelButtonText: 'Cancel',
        confirmButtonColor: '#0f172a',
    }).then((result) => {
        if (result.isConfirmed) form.submit();
    });
    return false;
}

function addPrescriptionItem(visitId) {
    const container = document.getElementById('prescription-items-' + visitId);
    const index = container.children.length;
    const row = document.createElement('div');
    row.className = 'grid grid-cols-12 gap-2 prescription-row';
    row.innerHTML = `
        <div class="col-span-3 relative">
            <select name="items[${index}][medication_id]" class="h-9 w-full appearance-none rounded-md border border-gray-200 bg-white px-3 pr-8 py-1 text-xs transition-shadow outline-none focus-visible:ring-2 focus-visible:ring-slate-300" required>
            <option value="">Select drug</option>
            @foreach ($medications as $med)
                <option value="{{ $med->id }}">{{ $med->name }}</option>
            @endforeach
            </select>
            <i class="hgi-stroke hgi-arrow-down-01 pointer-events-none absolute right-2.5 top-1/2 -translate-y-1/2 text-sm text-gray-400"></i>
        </div>
        <input type="number" name="items[${index}][quantity]" placeholder="Qty" class="col-span-1 h-9 w-full rounded-md border border-gray-200 bg-white px-3 py-1 text-xs transition-shadow outline-none focus-visible:ring-2 focus-visible:ring-slate-300" required>
        <input type="text" name="items[${index}][dosage]" placeholder="Dose" class="col-span-2 h-9 w-full rounded-md border border-gray-200 bg-white px-3 py-1 text-xs transition-shadow outline-none focus-visible:ring-2 focus-visible:ring-slate-300" required>
        <input type="text" name="items[${index}][frequency]" placeholder="Frequency" class="col-span-2 h-9 w-full rounded-md border border-gray-200 bg-white px-3 py-1 text-xs transition-shadow outline-none focus-visible:ring-2 focus-visible:ring-slate-300" required>
        <input type="text" name="items[${index}][duration]" placeholder="Duration" class="col-span-2 h-9 w-full rounded-md border border-gray-200 bg-white px-3 py-1 text-xs transition-shadow outline-none focus-visible:ring-2 focus-visible:ring-slate-300" required>
        <input type="text" name="items[${index}][instructions]" placeholder="Instructions" class="col-span-2 h-9 w-full rounded-md border border-gray-200 bg-white px-3 py-1 text-xs transition-shadow outline-none focus-visible:ring-2 focus-visible:ring-slate-300">
        <button type="button" onclick="this.closest('.prescription-row').remove()" class="col-span-1 text-red-500 hover:text-red-700">
            <i class="hgi-stroke hgi-delete-02 text-base mx-auto"></i>
        </button>
    `;
    container.appendChild(row);
}

function toggleVitalsForm(visitId) {
    const display = document.getElementById('vitals-display-' + visitId);
    const form = document.getElementById('vitals-form-' + visitId);
    display.classList.toggle('hidden');
    form.classList.toggle('hidden');
}
</script>
                            <form method="POST" action="{{ route('doctor.visits.prescribe', $visit) }}" class="w-full bg-gray-50 border border-gray-200 rounded-md p-4 space-y-3">
                                @csrf
                                <div class="flex items-center gap-2 mb-2">
                                    <div class="w-7 h-7 rounded bg-slate-100 text-slate-600 flex items-center justify-center">
                                        <i class="hgi-stroke hgi-test-tube-01 text-base"></i>
                                    </div>
                                    <p class="text-sm font-semibold text-gray-900">Write Prescription</p>
                                </div>
                                <div class="space-y-2" id="prescription-items-{{ $visit->id }}">
                                    <div class="grid grid-cols-12 gap-2 prescription-row">
                                        <x-ui.select name="items[0][medication_id]" class="col-span-3 border border-gray-200 rounded px-2 py-2 text-xs focus:ring-2 focus:ring-slate-300" required>
                                            <option value="">Select drug</option>
                                            @foreach ($medications as $med)
                                                <option value="{{ $med->id }}">{{ $med->name }}</option>
                                            @endforeach
                                        </x-ui.select>
                                        <x-ui.input type="number" name="items[0][quantity]" placeholder="Qty" class="col-span-1 border border-gray-200 rounded px-2 py-2 text-xs" required />
                                        <x-ui.input type="text" name="items[0][dosage]" placeholder="Dose" class="col-span-2 border border-gray-200 rounded px-2 py-2 text-xs" required />
                                        <x-ui.input type="text" name="items[0][frequency]" placeholder="Frequency" class="col-span-2 border border-gray-200 rounded px-2 py-2 text-xs" required />
                                        <x-ui.input type="text" name="items[0][duration]" placeholder="Duration" class="col-span-2 border border-gray-200 rounded px-2 py-2 text-xs" required />
                                        <x-ui.input type="text" name="items[0][instructions]" placeholder="Instructions" class="col-span-2 border border-gray-200 rounded px-2 py-2 text-xs" />
                                    </div>
                                </div>
                                <button type="button" onclick="addPrescriptionItem({{ $visit->id }})" class="text-xs text-slate-600 hover:text-slate-800 font-medium">+ Add another drug</button>
                                <div class="flex justify-end">
                                    <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2 bg-slate-800 hover:bg-slate-700 text-white text-xs font-semibold rounded transition-colors">
                                        <i class="hgi-stroke hgi-flash text-sm"></i>
                                        Send to Pharmacy
                                    </button>
                                </div>
                            </form>

                            <script>
function swalConfirmSubmit(event, message) {
    event.preventDefault();
    const form = event.target;
    Swal.fire({
        icon: 'question',
        title: 'Confirm',
        text: message,
        showCancelButton: true,
        confirmButtonText: 'Yes',
        cancelButtonText: 'Cancel',
        confirmButtonColor: '#0f172a',
    }).then((result) => {
        if (result.isConfirmed) form.submit();
    });
    return false;
}

function addPrescriptionItem(visitId) {
    const container = document.getElementById('prescription-items-' + visitId);
    const index = container.children.length;
    const row = document.createElement('div');
    row.className = 'grid grid-cols-12 gap-2 prescription-row';
    row.innerHTML = `
        <div class="col-span-3 relative">
            <select name="items[${index}][medication_id]" class="h-9 w-full appearance-none rounded-md border border-gray-200 bg-white px-3 pr-8 py-1 text-xs transition-shadow outline-none focus-visible:ring-2 focus-visible:ring-slate-300" required>
            <option value="">Select drug</option>
            @foreach ($medications as $med)
                <option value="{{ $med->id }}">{{ $med->name }}</option>
            @endforeach
            </select>
            <i class="hgi-stroke hgi-arrow-down-01 pointer-events-none absolute right-2.5 top-1/2 -translate-y-1/2 text-sm text-gray-400"></i>
        </div>
        <input type="number" name="items[${index}][quantity]" placeholder="Qty" class="col-span-1 h-9 w-full rounded-md border border-gray-200 bg-white px-3 py-1 text-xs transition-shadow outline-none focus-visible:ring-2 focus-visible:ring-slate-300" required>
        <input type="text" name="items[${index}][dosage]" placeholder="Dose" class="col-span-2 h-9 w-full rounded-md border border-gray-200 bg-white px-3 py-1 text-xs transition-shadow outline-none focus-visible:ring-2 focus-visible:ring-slate-300" required>
        <input type="text" name="items[${index}][frequency]" placeholder="Frequency" class="col-span-2 h-9 w-full rounded-md border border-gray-200 bg-white px-3 py-1 text-xs transition-shadow outline-none focus-visible:ring-2 focus-visible:ring-slate-300" required>
        <input type="text" name="items[${index}][duration]" placeholder="Duration" class="col-span-2 h-9 w-full rounded-md border border-gray-200 bg-white px-3 py-1 text-xs transition-shadow outline-none focus-visible:ring-2 focus-visible:ring-slate-300" required>
        <input type="text" name="items[${index}][instructions]" placeholder="Instructions" class="col-span-2 h-9 w-full rounded-md border border-gray-200 bg-white px-3 py-1 text-xs transition-shadow outline-none focus-visible:ring-2 focus-visible:ring-slate-300">
        <button type="button" onclick="this.closest('.prescription-row').remove()" class="col-span-1 text-red-500 hover:text-red-700">
            <i class="hgi-stroke hgi-delete-02 text-base mx-auto"></i>
        </button>
    `;
    container.appendChild(row);
}

function toggleVitalsForm(visitId) {
    const display = document.getElementById('vitals-display-' + visitId);
    const form = document.getElementById('vitals-form-' + visitId);
    display.classList.toggle('hidden');
    form.classList.toggle('hidden');
}
</script>
                            <form method="POST" action="{{ route('doctor.visits.complete', $visit) }}" onsubmit="return swalConfirmSubmit(event, 'Complete this visit? The patient will be discharged.')">
                                @csrf
                                <button type="submit" class="w-full inline-flex items-center justify-center gap-1.5 px-4 py-2 bg-slate-800 hover:bg-slate-700 text-white text-xs font-semibold rounded transition-colors">
                                    <i class="hgi-stroke hgi-checkmark-circle-02 text-sm"></i>
                                    Complete Visit
                                </button>
                            </form>
                        @endif
                    </div>
                </div>

                <script>
function swalConfirmSubmit(event, message) {
    event.preventDefault();
    const form = event.target;
    Swal.fire({
        icon: 'question',
        title: 'Confirm',
        text: message,
        showCancelButton: true,
        confirmButtonText: 'Yes',
        cancelButtonText: 'Cancel',
        confirmButtonColor: '#0f172a',
    }).then((result) => {
        if (result.isConfirmed) form.submit();
    });
    return false;
}

function addPrescriptionItem(visitId) {
    const container = document.getElementById('prescription-items-' + visitId);
    const index = container.children.length;
    const row = document.createElement('div');
    row.className = 'grid grid-cols-12 gap-2 prescription-row';
    row.innerHTML = `
        <div class="col-span-3 relative">
            <select name="items[${index}][medication_id]" class="h-9 w-full appearance-none rounded-md border border-gray-200 bg-white px-3 pr-8 py-1 text-xs transition-shadow outline-none focus-visible:ring-2 focus-visible:ring-slate-300" required>
            <option value="">Select drug</option>
            @foreach ($medications as $med)
                <option value="{{ $med->id }}">{{ $med->name }}</option>
            @endforeach
            </select>
            <i class="hgi-stroke hgi-arrow-down-01 pointer-events-none absolute right-2.5 top-1/2 -translate-y-1/2 text-sm text-gray-400"></i>
        </div>
        <input type="number" name="items[${index}][quantity]" placeholder="Qty" class="col-span-1 h-9 w-full rounded-md border border-gray-200 bg-white px-3 py-1 text-xs transition-shadow outline-none focus-visible:ring-2 focus-visible:ring-slate-300" required>
        <input type="text" name="items[${index}][dosage]" placeholder="Dose" class="col-span-2 h-9 w-full rounded-md border border-gray-200 bg-white px-3 py-1 text-xs transition-shadow outline-none focus-visible:ring-2 focus-visible:ring-slate-300" required>
        <input type="text" name="items[${index}][frequency]" placeholder="Frequency" class="col-span-2 h-9 w-full rounded-md border border-gray-200 bg-white px-3 py-1 text-xs transition-shadow outline-none focus-visible:ring-2 focus-visible:ring-slate-300" required>
        <input type="text" name="items[${index}][duration]" placeholder="Duration" class="col-span-2 h-9 w-full rounded-md border border-gray-200 bg-white px-3 py-1 text-xs transition-shadow outline-none focus-visible:ring-2 focus-visible:ring-slate-300" required>
        <input type="text" name="items[${index}][instructions]" placeholder="Instructions" class="col-span-2 h-9 w-full rounded-md border border-gray-200 bg-white px-3 py-1 text-xs transition-shadow outline-none focus-visible:ring-2 focus-visible:ring-slate-300">
        <button type="button" onclick="this.closest('.prescription-row').remove()" class="col-span-1 text-red-500 hover:text-red-700">
            <i class="hgi-stroke hgi-delete-02 text-base mx-auto"></i>
        </button>
    `;
    container.appendChild(row);
}

function toggleVitalsForm(visitId) {
    const display = document.getElementById('vitals-display-' + visitId);
    const form = document.getElementById('vitals-form-' + visitId);
    display.classList.toggle('hidden');
    form.classList.toggle('hidden');
}
</script>
                @php
                    $hasActivity = $visit->prescriptions->isNotEmpty()
                        || $visit->labOrders->isNotEmpty()
                        || $visit->ultrasoundOrders->isNotEmpty()
                        || $visit->consultation
                        || $visit->vitals;
                    $previousVisits = $visit->patient->visits->filter(fn($v) => $v->id !== $visit->id);
                @endphp
                @if ($hasActivity || $previousVisits->isNotEmpty())
                    <div class="mt-6 border-t-2 border-gray-100 pt-5">
                        <div class="flex items-center gap-2 mb-4">
                            <i class="hgi-stroke hgi-clock-01 text-base text-gray-500"></i>
                            <p class="text-sm font-bold text-gray-800">Previous Activity</p>
                            <span class="text-xs text-gray-400">- History ya mgonjwa</span>
                            <a href="{{ route('patients.history', $visit->patient) }}" class="ml-auto inline-flex items-center gap-1 px-2.5 py-1 bg-gray-50 hover:bg-gray-100 text-slate-600 text-xs font-semibold rounded transition-colors">
                                <i class="hgi-stroke hgi-clipboard text-sm"></i>
                                Full History
                            </a>
                        </div>

                        <script>
function swalConfirmSubmit(event, message) {
    event.preventDefault();
    const form = event.target;
    Swal.fire({
        icon: 'question',
        title: 'Confirm',
        text: message,
        showCancelButton: true,
        confirmButtonText: 'Yes',
        cancelButtonText: 'Cancel',
        confirmButtonColor: '#0f172a',
    }).then((result) => {
        if (result.isConfirmed) form.submit();
    });
    return false;
}

function addPrescriptionItem(visitId) {
    const container = document.getElementById('prescription-items-' + visitId);
    const index = container.children.length;
    const row = document.createElement('div');
    row.className = 'grid grid-cols-12 gap-2 prescription-row';
    row.innerHTML = `
        <div class="col-span-3 relative">
            <select name="items[${index}][medication_id]" class="h-9 w-full appearance-none rounded-md border border-gray-200 bg-white px-3 pr-8 py-1 text-xs transition-shadow outline-none focus-visible:ring-2 focus-visible:ring-slate-300" required>
            <option value="">Select drug</option>
            @foreach ($medications as $med)
                <option value="{{ $med->id }}">{{ $med->name }}</option>
            @endforeach
            </select>
            <i class="hgi-stroke hgi-arrow-down-01 pointer-events-none absolute right-2.5 top-1/2 -translate-y-1/2 text-sm text-gray-400"></i>
        </div>
        <input type="number" name="items[${index}][quantity]" placeholder="Qty" class="col-span-1 h-9 w-full rounded-md border border-gray-200 bg-white px-3 py-1 text-xs transition-shadow outline-none focus-visible:ring-2 focus-visible:ring-slate-300" required>
        <input type="text" name="items[${index}][dosage]" placeholder="Dose" class="col-span-2 h-9 w-full rounded-md border border-gray-200 bg-white px-3 py-1 text-xs transition-shadow outline-none focus-visible:ring-2 focus-visible:ring-slate-300" required>
        <input type="text" name="items[${index}][frequency]" placeholder="Frequency" class="col-span-2 h-9 w-full rounded-md border border-gray-200 bg-white px-3 py-1 text-xs transition-shadow outline-none focus-visible:ring-2 focus-visible:ring-slate-300" required>
        <input type="text" name="items[${index}][duration]" placeholder="Duration" class="col-span-2 h-9 w-full rounded-md border border-gray-200 bg-white px-3 py-1 text-xs transition-shadow outline-none focus-visible:ring-2 focus-visible:ring-slate-300" required>
        <input type="text" name="items[${index}][instructions]" placeholder="Instructions" class="col-span-2 h-9 w-full rounded-md border border-gray-200 bg-white px-3 py-1 text-xs transition-shadow outline-none focus-visible:ring-2 focus-visible:ring-slate-300">
        <button type="button" onclick="this.closest('.prescription-row').remove()" class="col-span-1 text-red-500 hover:text-red-700">
            <i class="hgi-stroke hgi-delete-02 text-base mx-auto"></i>
        </button>
    `;
    container.appendChild(row);
}

function toggleVitalsForm(visitId) {
    const display = document.getElementById('vitals-display-' + visitId);
    const form = document.getElementById('vitals-form-' + visitId);
    display.classList.toggle('hidden');
    form.classList.toggle('hidden');
}
</script>
                        @if ($hasActivity)
                        <div class="mb-5">
                            <p class="text-xs font-semibold text-gray-700 mb-3 flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full bg-slate-500"></span>
                                Current Visit - {{ $visit->registered_at->format('M d, Y H:i') }}
                            </p>
                            <div class="space-y-3 ml-3 border-l-2 border-gray-200 pl-4">
                                <script>
function swalConfirmSubmit(event, message) {
    event.preventDefault();
    const form = event.target;
    Swal.fire({
        icon: 'question',
        title: 'Confirm',
        text: message,
        showCancelButton: true,
        confirmButtonText: 'Yes',
        cancelButtonText: 'Cancel',
        confirmButtonColor: '#0f172a',
    }).then((result) => {
        if (result.isConfirmed) form.submit();
    });
    return false;
}

function addPrescriptionItem(visitId) {
    const container = document.getElementById('prescription-items-' + visitId);
    const index = container.children.length;
    const row = document.createElement('div');
    row.className = 'grid grid-cols-12 gap-2 prescription-row';
    row.innerHTML = `
        <div class="col-span-3 relative">
            <select name="items[${index}][medication_id]" class="h-9 w-full appearance-none rounded-md border border-gray-200 bg-white px-3 pr-8 py-1 text-xs transition-shadow outline-none focus-visible:ring-2 focus-visible:ring-slate-300" required>
            <option value="">Select drug</option>
            @foreach ($medications as $med)
                <option value="{{ $med->id }}">{{ $med->name }}</option>
            @endforeach
            </select>
            <i class="hgi-stroke hgi-arrow-down-01 pointer-events-none absolute right-2.5 top-1/2 -translate-y-1/2 text-sm text-gray-400"></i>
        </div>
        <input type="number" name="items[${index}][quantity]" placeholder="Qty" class="col-span-1 h-9 w-full rounded-md border border-gray-200 bg-white px-3 py-1 text-xs transition-shadow outline-none focus-visible:ring-2 focus-visible:ring-slate-300" required>
        <input type="text" name="items[${index}][dosage]" placeholder="Dose" class="col-span-2 h-9 w-full rounded-md border border-gray-200 bg-white px-3 py-1 text-xs transition-shadow outline-none focus-visible:ring-2 focus-visible:ring-slate-300" required>
        <input type="text" name="items[${index}][frequency]" placeholder="Frequency" class="col-span-2 h-9 w-full rounded-md border border-gray-200 bg-white px-3 py-1 text-xs transition-shadow outline-none focus-visible:ring-2 focus-visible:ring-slate-300" required>
        <input type="text" name="items[${index}][duration]" placeholder="Duration" class="col-span-2 h-9 w-full rounded-md border border-gray-200 bg-white px-3 py-1 text-xs transition-shadow outline-none focus-visible:ring-2 focus-visible:ring-slate-300" required>
        <input type="text" name="items[${index}][instructions]" placeholder="Instructions" class="col-span-2 h-9 w-full rounded-md border border-gray-200 bg-white px-3 py-1 text-xs transition-shadow outline-none focus-visible:ring-2 focus-visible:ring-slate-300">
        <button type="button" onclick="this.closest('.prescription-row').remove()" class="col-span-1 text-red-500 hover:text-red-700">
            <i class="hgi-stroke hgi-delete-02 text-base mx-auto"></i>
        </button>
    `;
    container.appendChild(row);
}

function toggleVitalsForm(visitId) {
    const display = document.getElementById('vitals-display-' + visitId);
    const form = document.getElementById('vitals-form-' + visitId);
    display.classList.toggle('hidden');
    form.classList.toggle('hidden');
}
</script>
                                @if ($visit->vitals)
                                    <div class="relative">
                                        <span class="absolute -left-[21px] top-1 w-3 h-3 rounded-full bg-slate-400 border-2 border-white"></span>
                                        <div class="bg-gray-50 rounded p-3 border border-gray-100">
                                            <p class="text-xs font-semibold text-gray-700 mb-1.5 flex items-center gap-1.5">
                                                <i class="hgi-stroke hgi-flash text-sm"></i>
                                                Vitals
                                            </p>
                                            <div class="flex flex-wrap gap-x-4 gap-y-1 text-xs text-gray-700">
                                                @if($visit->vitals->temperature)<span><strong>Temp:</strong> {{ $visit->vitals->temperature }}°C</span>@endif
                                                @if($visit->vitals->blood_pressure)<span><strong>BP:</strong> {{ $visit->vitals->blood_pressure }}</span>@endif
                                                @if($visit->vitals->pulse)<span><strong>Pulse:</strong> {{ $visit->vitals->pulse }} bpm</span>@endif
                                                @if($visit->vitals->weight)<span><strong>Wt:</strong> {{ $visit->vitals->weight }} kg</span>@endif
                                                @if($visit->vitals->height)<span><strong>Ht:</strong> {{ $visit->vitals->height }} cm</span>@endif
                                                @if($visit->vitals->respiratory_rate)<span><strong>RR:</strong> {{ $visit->vitals->respiratory_rate }}</span>@endif
                                                @if($visit->vitals->oxygen_saturation)<span><strong>SpO2:</strong> {{ $visit->vitals->oxygen_saturation }}%</span>@endif
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                <script>
function swalConfirmSubmit(event, message) {
    event.preventDefault();
    const form = event.target;
    Swal.fire({
        icon: 'question',
        title: 'Confirm',
        text: message,
        showCancelButton: true,
        confirmButtonText: 'Yes',
        cancelButtonText: 'Cancel',
        confirmButtonColor: '#0f172a',
    }).then((result) => {
        if (result.isConfirmed) form.submit();
    });
    return false;
}

function addPrescriptionItem(visitId) {
    const container = document.getElementById('prescription-items-' + visitId);
    const index = container.children.length;
    const row = document.createElement('div');
    row.className = 'grid grid-cols-12 gap-2 prescription-row';
    row.innerHTML = `
        <div class="col-span-3 relative">
            <select name="items[${index}][medication_id]" class="h-9 w-full appearance-none rounded-md border border-gray-200 bg-white px-3 pr-8 py-1 text-xs transition-shadow outline-none focus-visible:ring-2 focus-visible:ring-slate-300" required>
            <option value="">Select drug</option>
            @foreach ($medications as $med)
                <option value="{{ $med->id }}">{{ $med->name }}</option>
            @endforeach
            </select>
            <i class="hgi-stroke hgi-arrow-down-01 pointer-events-none absolute right-2.5 top-1/2 -translate-y-1/2 text-sm text-gray-400"></i>
        </div>
        <input type="number" name="items[${index}][quantity]" placeholder="Qty" class="col-span-1 h-9 w-full rounded-md border border-gray-200 bg-white px-3 py-1 text-xs transition-shadow outline-none focus-visible:ring-2 focus-visible:ring-slate-300" required>
        <input type="text" name="items[${index}][dosage]" placeholder="Dose" class="col-span-2 h-9 w-full rounded-md border border-gray-200 bg-white px-3 py-1 text-xs transition-shadow outline-none focus-visible:ring-2 focus-visible:ring-slate-300" required>
        <input type="text" name="items[${index}][frequency]" placeholder="Frequency" class="col-span-2 h-9 w-full rounded-md border border-gray-200 bg-white px-3 py-1 text-xs transition-shadow outline-none focus-visible:ring-2 focus-visible:ring-slate-300" required>
        <input type="text" name="items[${index}][duration]" placeholder="Duration" class="col-span-2 h-9 w-full rounded-md border border-gray-200 bg-white px-3 py-1 text-xs transition-shadow outline-none focus-visible:ring-2 focus-visible:ring-slate-300" required>
        <input type="text" name="items[${index}][instructions]" placeholder="Instructions" class="col-span-2 h-9 w-full rounded-md border border-gray-200 bg-white px-3 py-1 text-xs transition-shadow outline-none focus-visible:ring-2 focus-visible:ring-slate-300">
        <button type="button" onclick="this.closest('.prescription-row').remove()" class="col-span-1 text-red-500 hover:text-red-700">
            <i class="hgi-stroke hgi-delete-02 text-base mx-auto"></i>
        </button>
    `;
    container.appendChild(row);
}

function toggleVitalsForm(visitId) {
    const display = document.getElementById('vitals-display-' + visitId);
    const form = document.getElementById('vitals-form-' + visitId);
    display.classList.toggle('hidden');
    form.classList.toggle('hidden');
}
</script>
                                @if ($visit->consultation)
                                    <div class="relative">
                                        <span class="absolute -left-[21px] top-1 w-3 h-3 rounded-full bg-slate-500 border-2 border-white"></span>
                                        <div class="bg-gray-50 rounded p-3 border border-gray-100">
                                            <p class="text-xs font-semibold text-gray-700 mb-1.5 flex items-center gap-1.5">
                                                <i class="hgi-stroke hgi-file-02 text-sm"></i>
                                                Consultation
                                            </p>
                                            <div class="space-y-1.5 text-xs text-gray-700">
                                                @if($visit->consultation->history)<p><strong>History:</strong> {{ $visit->consultation->history }}</p>@endif
                                                @if($visit->consultation->examination)<p><strong>Examination:</strong> {{ $visit->consultation->examination }}</p>@endif
                                                @if($visit->consultation->diagnosis)<p><strong>Diagnosis:</strong> {{ $visit->consultation->diagnosis }}</p>@endif
                                                @if($visit->consultation->notes)<p><strong>Notes:</strong> {{ $visit->consultation->notes }}</p>@endif
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                <script>
function swalConfirmSubmit(event, message) {
    event.preventDefault();
    const form = event.target;
    Swal.fire({
        icon: 'question',
        title: 'Confirm',
        text: message,
        showCancelButton: true,
        confirmButtonText: 'Yes',
        cancelButtonText: 'Cancel',
        confirmButtonColor: '#0f172a',
    }).then((result) => {
        if (result.isConfirmed) form.submit();
    });
    return false;
}

function addPrescriptionItem(visitId) {
    const container = document.getElementById('prescription-items-' + visitId);
    const index = container.children.length;
    const row = document.createElement('div');
    row.className = 'grid grid-cols-12 gap-2 prescription-row';
    row.innerHTML = `
        <div class="col-span-3 relative">
            <select name="items[${index}][medication_id]" class="h-9 w-full appearance-none rounded-md border border-gray-200 bg-white px-3 pr-8 py-1 text-xs transition-shadow outline-none focus-visible:ring-2 focus-visible:ring-slate-300" required>
            <option value="">Select drug</option>
            @foreach ($medications as $med)
                <option value="{{ $med->id }}">{{ $med->name }}</option>
            @endforeach
            </select>
            <i class="hgi-stroke hgi-arrow-down-01 pointer-events-none absolute right-2.5 top-1/2 -translate-y-1/2 text-sm text-gray-400"></i>
        </div>
        <input type="number" name="items[${index}][quantity]" placeholder="Qty" class="col-span-1 h-9 w-full rounded-md border border-gray-200 bg-white px-3 py-1 text-xs transition-shadow outline-none focus-visible:ring-2 focus-visible:ring-slate-300" required>
        <input type="text" name="items[${index}][dosage]" placeholder="Dose" class="col-span-2 h-9 w-full rounded-md border border-gray-200 bg-white px-3 py-1 text-xs transition-shadow outline-none focus-visible:ring-2 focus-visible:ring-slate-300" required>
        <input type="text" name="items[${index}][frequency]" placeholder="Frequency" class="col-span-2 h-9 w-full rounded-md border border-gray-200 bg-white px-3 py-1 text-xs transition-shadow outline-none focus-visible:ring-2 focus-visible:ring-slate-300" required>
        <input type="text" name="items[${index}][duration]" placeholder="Duration" class="col-span-2 h-9 w-full rounded-md border border-gray-200 bg-white px-3 py-1 text-xs transition-shadow outline-none focus-visible:ring-2 focus-visible:ring-slate-300" required>
        <input type="text" name="items[${index}][instructions]" placeholder="Instructions" class="col-span-2 h-9 w-full rounded-md border border-gray-200 bg-white px-3 py-1 text-xs transition-shadow outline-none focus-visible:ring-2 focus-visible:ring-slate-300">
        <button type="button" onclick="this.closest('.prescription-row').remove()" class="col-span-1 text-red-500 hover:text-red-700">
            <i class="hgi-stroke hgi-delete-02 text-base mx-auto"></i>
        </button>
    `;
    container.appendChild(row);
}

function toggleVitalsForm(visitId) {
    const display = document.getElementById('vitals-display-' + visitId);
    const form = document.getElementById('vitals-form-' + visitId);
    display.classList.toggle('hidden');
    form.classList.toggle('hidden');
}
</script>
                                @if ($visit->labOrders->isNotEmpty())
                                    <div class="relative">
                                        <span class="absolute -left-[21px] top-1 w-3 h-3 rounded-full bg-slate-400 border-2 border-white"></span>
                                        <div class="bg-gray-50 rounded p-3 border border-gray-100">
                                            <p class="text-xs font-semibold text-gray-700 mb-2 flex items-center gap-1.5">
                                                <i class="hgi-stroke hgi-test-tube-01 text-sm"></i>
                                                Lab Tests
                                            </p>
                                            @foreach ($visit->labOrders as $order)
                                                <div class="mb-2 last:mb-0">
                                                    <div class="flex items-center gap-2 mb-1">
                                                        <span class="text-xs font-medium text-gray-700">Order #{{ $order->id }}</span>
                                                        <span class="inline-flex items-center gap-1 text-[10px] font-medium text-gray-600"><span class="w-1.5 h-1.5 rounded-full {{ $order->status === 'completed' ? 'bg-emerald-500' : 'bg-amber-500' }}"></span>{{ $order->status }}</span>
                                                    </div>
                                                    <script>
function swalConfirmSubmit(event, message) {
    event.preventDefault();
    const form = event.target;
    Swal.fire({
        icon: 'question',
        title: 'Confirm',
        text: message,
        showCancelButton: true,
        confirmButtonText: 'Yes',
        cancelButtonText: 'Cancel',
        confirmButtonColor: '#0f172a',
    }).then((result) => {
        if (result.isConfirmed) form.submit();
    });
    return false;
}

function addPrescriptionItem(visitId) {
    const container = document.getElementById('prescription-items-' + visitId);
    const index = container.children.length;
    const row = document.createElement('div');
    row.className = 'grid grid-cols-12 gap-2 prescription-row';
    row.innerHTML = `
        <div class="col-span-3 relative">
            <select name="items[${index}][medication_id]" class="h-9 w-full appearance-none rounded-md border border-gray-200 bg-white px-3 pr-8 py-1 text-xs transition-shadow outline-none focus-visible:ring-2 focus-visible:ring-slate-300" required>
            <option value="">Select drug</option>
            @foreach ($medications as $med)
                <option value="{{ $med->id }}">{{ $med->name }}</option>
            @endforeach
            </select>
            <i class="hgi-stroke hgi-arrow-down-01 pointer-events-none absolute right-2.5 top-1/2 -translate-y-1/2 text-sm text-gray-400"></i>
        </div>
        <input type="number" name="items[${index}][quantity]" placeholder="Qty" class="col-span-1 h-9 w-full rounded-md border border-gray-200 bg-white px-3 py-1 text-xs transition-shadow outline-none focus-visible:ring-2 focus-visible:ring-slate-300" required>
        <input type="text" name="items[${index}][dosage]" placeholder="Dose" class="col-span-2 h-9 w-full rounded-md border border-gray-200 bg-white px-3 py-1 text-xs transition-shadow outline-none focus-visible:ring-2 focus-visible:ring-slate-300" required>
        <input type="text" name="items[${index}][frequency]" placeholder="Frequency" class="col-span-2 h-9 w-full rounded-md border border-gray-200 bg-white px-3 py-1 text-xs transition-shadow outline-none focus-visible:ring-2 focus-visible:ring-slate-300" required>
        <input type="text" name="items[${index}][duration]" placeholder="Duration" class="col-span-2 h-9 w-full rounded-md border border-gray-200 bg-white px-3 py-1 text-xs transition-shadow outline-none focus-visible:ring-2 focus-visible:ring-slate-300" required>
        <input type="text" name="items[${index}][instructions]" placeholder="Instructions" class="col-span-2 h-9 w-full rounded-md border border-gray-200 bg-white px-3 py-1 text-xs transition-shadow outline-none focus-visible:ring-2 focus-visible:ring-slate-300">
        <button type="button" onclick="this.closest('.prescription-row').remove()" class="col-span-1 text-red-500 hover:text-red-700">
            <i class="hgi-stroke hgi-delete-02 text-base mx-auto"></i>
        </button>
    `;
    container.appendChild(row);
}

function toggleVitalsForm(visitId) {
    const display = document.getElementById('vitals-display-' + visitId);
    const form = document.getElementById('vitals-form-' + visitId);
    display.classList.toggle('hidden');
    form.classList.toggle('hidden');
}
</script>
                                                    <div class="flex flex-wrap gap-1 mb-1.5">
                                                        @foreach ($order->items as $item)
                                                            <span class="inline-flex px-2 py-0.5 rounded text-[10px] font-medium bg-gray-100 text-gray-600">{{ $item->labTest?->name ?? 'Unknown' }}</span>
                                                        @endforeach
                                                    </div>
                                                    <script>
function swalConfirmSubmit(event, message) {
    event.preventDefault();
    const form = event.target;
    Swal.fire({
        icon: 'question',
        title: 'Confirm',
        text: message,
        showCancelButton: true,
        confirmButtonText: 'Yes',
        cancelButtonText: 'Cancel',
        confirmButtonColor: '#0f172a',
    }).then((result) => {
        if (result.isConfirmed) form.submit();
    });
    return false;
}

function addPrescriptionItem(visitId) {
    const container = document.getElementById('prescription-items-' + visitId);
    const index = container.children.length;
    const row = document.createElement('div');
    row.className = 'grid grid-cols-12 gap-2 prescription-row';
    row.innerHTML = `
        <div class="col-span-3 relative">
            <select name="items[${index}][medication_id]" class="h-9 w-full appearance-none rounded-md border border-gray-200 bg-white px-3 pr-8 py-1 text-xs transition-shadow outline-none focus-visible:ring-2 focus-visible:ring-slate-300" required>
            <option value="">Select drug</option>
            @foreach ($medications as $med)
                <option value="{{ $med->id }}">{{ $med->name }}</option>
            @endforeach
            </select>
            <i class="hgi-stroke hgi-arrow-down-01 pointer-events-none absolute right-2.5 top-1/2 -translate-y-1/2 text-sm text-gray-400"></i>
        </div>
        <input type="number" name="items[${index}][quantity]" placeholder="Qty" class="col-span-1 h-9 w-full rounded-md border border-gray-200 bg-white px-3 py-1 text-xs transition-shadow outline-none focus-visible:ring-2 focus-visible:ring-slate-300" required>
        <input type="text" name="items[${index}][dosage]" placeholder="Dose" class="col-span-2 h-9 w-full rounded-md border border-gray-200 bg-white px-3 py-1 text-xs transition-shadow outline-none focus-visible:ring-2 focus-visible:ring-slate-300" required>
        <input type="text" name="items[${index}][frequency]" placeholder="Frequency" class="col-span-2 h-9 w-full rounded-md border border-gray-200 bg-white px-3 py-1 text-xs transition-shadow outline-none focus-visible:ring-2 focus-visible:ring-slate-300" required>
        <input type="text" name="items[${index}][duration]" placeholder="Duration" class="col-span-2 h-9 w-full rounded-md border border-gray-200 bg-white px-3 py-1 text-xs transition-shadow outline-none focus-visible:ring-2 focus-visible:ring-slate-300" required>
        <input type="text" name="items[${index}][instructions]" placeholder="Instructions" class="col-span-2 h-9 w-full rounded-md border border-gray-200 bg-white px-3 py-1 text-xs transition-shadow outline-none focus-visible:ring-2 focus-visible:ring-slate-300">
        <button type="button" onclick="this.closest('.prescription-row').remove()" class="col-span-1 text-red-500 hover:text-red-700">
            <i class="hgi-stroke hgi-delete-02 text-base mx-auto"></i>
        </button>
    `;
    container.appendChild(row);
}

function toggleVitalsForm(visitId) {
    const display = document.getElementById('vitals-display-' + visitId);
    const form = document.getElementById('vitals-form-' + visitId);
    display.classList.toggle('hidden');
    form.classList.toggle('hidden');
}
</script>
                                                    @if($order->results->isNotEmpty())
                                                        @php $groupedResults = $order->results->groupBy('lab_order_item_id'); @endphp
                                                        @foreach ($groupedResults as $itemId => $results)
                                                            @php $testItem = $order->items->firstWhere('id', $itemId); @endphp
                                                            <div class="bg-white rounded border border-gray-200 overflow-hidden mb-1.5 last:mb-0">
                                                                <div class="px-3 py-1.5 bg-gray-50 border-b border-gray-100">
                                                                    <span class="text-xs font-semibold text-gray-800">{{ $testItem?->labTest?->name ?? 'Unknown' }}</span>
                                                                </div>
                                                                <table class="w-full text-xs">
                                                                    <thead class="text-gray-500">
                                                                        <tr>
                                                                            <th class="px-3 py-1.5 text-left font-medium">Parameter</th>
                                                                            <th class="px-3 py-1.5 text-left font-medium">Result</th>
                                                                            <th class="px-3 py-1.5 text-left font-medium">Unit</th>
                                                                            <th class="px-3 py-1.5 text-left font-medium">Ref</th>
                                                                            <th class="px-3 py-1.5 text-left font-medium">Flag</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody class="divide-y divide-gray-50">
                                                                        @foreach ($results as $result)
                                                                            @php
                                                                                $flagBg = match($result->flag) {
                                                                                    'normal' => 'bg-emerald-50 text-emerald-700',
                                                                                    'high', 'low' => 'bg-amber-50 text-amber-700',
                                                                                    'critical' => 'bg-red-50 text-red-700',
                                                                                    default => 'bg-gray-50 text-gray-700',
                                                                                };
                                                                                $valColor = $result->flag === 'critical' ? 'text-red-600' : ($result->flag !== 'normal' ? 'text-amber-600' : 'text-gray-900');
                                                                            @endphp
                                                                            <tr>
                                                                                <td class="px-3 py-1.5 font-medium text-gray-800">{{ $result->parameter }}</td>
                                                                                <td class="px-3 py-1.5 font-bold {{ $valColor }}">{{ $result->value }}</td>
                                                                                <td class="px-3 py-1.5 text-gray-600">{{ $result->unit ?? '-' }}</td>
                                                                                <td class="px-3 py-1.5 text-gray-500">{{ $result->reference_range ?? '-' }}</td>
                                                                                <td class="px-3 py-1.5"><span class="inline-flex px-1.5 py-0.5 rounded-full text-[10px] font-medium {{ $flagBg }}">{{ ucfirst($result->flag) }}</span></td>
                                                                            </tr>
                                                                        @endforeach
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        @endforeach
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif

                                <script>
function swalConfirmSubmit(event, message) {
    event.preventDefault();
    const form = event.target;
    Swal.fire({
        icon: 'question',
        title: 'Confirm',
        text: message,
        showCancelButton: true,
        confirmButtonText: 'Yes',
        cancelButtonText: 'Cancel',
        confirmButtonColor: '#0f172a',
    }).then((result) => {
        if (result.isConfirmed) form.submit();
    });
    return false;
}

function addPrescriptionItem(visitId) {
    const container = document.getElementById('prescription-items-' + visitId);
    const index = container.children.length;
    const row = document.createElement('div');
    row.className = 'grid grid-cols-12 gap-2 prescription-row';
    row.innerHTML = `
        <div class="col-span-3 relative">
            <select name="items[${index}][medication_id]" class="h-9 w-full appearance-none rounded-md border border-gray-200 bg-white px-3 pr-8 py-1 text-xs transition-shadow outline-none focus-visible:ring-2 focus-visible:ring-slate-300" required>
            <option value="">Select drug</option>
            @foreach ($medications as $med)
                <option value="{{ $med->id }}">{{ $med->name }}</option>
            @endforeach
            </select>
            <i class="hgi-stroke hgi-arrow-down-01 pointer-events-none absolute right-2.5 top-1/2 -translate-y-1/2 text-sm text-gray-400"></i>
        </div>
        <input type="number" name="items[${index}][quantity]" placeholder="Qty" class="col-span-1 h-9 w-full rounded-md border border-gray-200 bg-white px-3 py-1 text-xs transition-shadow outline-none focus-visible:ring-2 focus-visible:ring-slate-300" required>
        <input type="text" name="items[${index}][dosage]" placeholder="Dose" class="col-span-2 h-9 w-full rounded-md border border-gray-200 bg-white px-3 py-1 text-xs transition-shadow outline-none focus-visible:ring-2 focus-visible:ring-slate-300" required>
        <input type="text" name="items[${index}][frequency]" placeholder="Frequency" class="col-span-2 h-9 w-full rounded-md border border-gray-200 bg-white px-3 py-1 text-xs transition-shadow outline-none focus-visible:ring-2 focus-visible:ring-slate-300" required>
        <input type="text" name="items[${index}][duration]" placeholder="Duration" class="col-span-2 h-9 w-full rounded-md border border-gray-200 bg-white px-3 py-1 text-xs transition-shadow outline-none focus-visible:ring-2 focus-visible:ring-slate-300" required>
        <input type="text" name="items[${index}][instructions]" placeholder="Instructions" class="col-span-2 h-9 w-full rounded-md border border-gray-200 bg-white px-3 py-1 text-xs transition-shadow outline-none focus-visible:ring-2 focus-visible:ring-slate-300">
        <button type="button" onclick="this.closest('.prescription-row').remove()" class="col-span-1 text-red-500 hover:text-red-700">
            <i class="hgi-stroke hgi-delete-02 text-base mx-auto"></i>
        </button>
    `;
    container.appendChild(row);
}

function toggleVitalsForm(visitId) {
    const display = document.getElementById('vitals-display-' + visitId);
    const form = document.getElementById('vitals-form-' + visitId);
    display.classList.toggle('hidden');
    form.classList.toggle('hidden');
}
</script>
                                @if ($visit->ultrasoundOrders->isNotEmpty())
                                    <div class="relative">
                                        <span class="absolute -left-[21px] top-1 w-3 h-3 rounded-full bg-slate-400 border-2 border-white"></span>
                                        <div class="bg-gray-50 rounded p-3 border border-gray-100">
                                            <p class="text-xs font-semibold text-gray-700 mb-2 flex items-center gap-1.5">
                                                <i class="hgi-stroke hgi-file-01 text-sm"></i>
                                                Ultrasound
                                            </p>
                                            @foreach ($visit->ultrasoundOrders as $usOrder)
                                                <div class="mb-2 last:mb-0">
                                                    <div class="flex items-center gap-2 mb-1">
                                                        <span class="text-xs font-medium text-gray-700">Order #{{ $usOrder->id }}</span>
                                                        <span class="inline-flex items-center gap-1 text-[10px] font-medium text-gray-600"><span class="w-1.5 h-1.5 rounded-full {{ $usOrder->status === 'completed' ? 'bg-emerald-500' : 'bg-amber-500' }}"></span>{{ $usOrder->status }}</span>
                                                    </div>
                                                    <div class="flex flex-wrap gap-1 mb-1">
                                                        @foreach ($usOrder->items as $item)
                                                            <span class="inline-flex px-2 py-0.5 rounded text-[10px] font-medium bg-gray-100 text-gray-600">{{ $item->ultrasoundService?->name ?? 'Unknown' }}</span>
                                                        @endforeach
                                                    </div>
                                                    @if($usOrder->findings)
                                                        <p class="text-xs text-gray-700 mt-1"><strong>Findings:</strong> {{ $usOrder->findings }}</p>
                                                    @endif
                                                    @if($usOrder->impression)
                                                        <p class="text-xs text-gray-700"><strong>Impression:</strong> {{ $usOrder->impression }}</p>
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif

                                <script>
function swalConfirmSubmit(event, message) {
    event.preventDefault();
    const form = event.target;
    Swal.fire({
        icon: 'question',
        title: 'Confirm',
        text: message,
        showCancelButton: true,
        confirmButtonText: 'Yes',
        cancelButtonText: 'Cancel',
        confirmButtonColor: '#0f172a',
    }).then((result) => {
        if (result.isConfirmed) form.submit();
    });
    return false;
}

function addPrescriptionItem(visitId) {
    const container = document.getElementById('prescription-items-' + visitId);
    const index = container.children.length;
    const row = document.createElement('div');
    row.className = 'grid grid-cols-12 gap-2 prescription-row';
    row.innerHTML = `
        <div class="col-span-3 relative">
            <select name="items[${index}][medication_id]" class="h-9 w-full appearance-none rounded-md border border-gray-200 bg-white px-3 pr-8 py-1 text-xs transition-shadow outline-none focus-visible:ring-2 focus-visible:ring-slate-300" required>
            <option value="">Select drug</option>
            @foreach ($medications as $med)
                <option value="{{ $med->id }}">{{ $med->name }}</option>
            @endforeach
            </select>
            <i class="hgi-stroke hgi-arrow-down-01 pointer-events-none absolute right-2.5 top-1/2 -translate-y-1/2 text-sm text-gray-400"></i>
        </div>
        <input type="number" name="items[${index}][quantity]" placeholder="Qty" class="col-span-1 h-9 w-full rounded-md border border-gray-200 bg-white px-3 py-1 text-xs transition-shadow outline-none focus-visible:ring-2 focus-visible:ring-slate-300" required>
        <input type="text" name="items[${index}][dosage]" placeholder="Dose" class="col-span-2 h-9 w-full rounded-md border border-gray-200 bg-white px-3 py-1 text-xs transition-shadow outline-none focus-visible:ring-2 focus-visible:ring-slate-300" required>
        <input type="text" name="items[${index}][frequency]" placeholder="Frequency" class="col-span-2 h-9 w-full rounded-md border border-gray-200 bg-white px-3 py-1 text-xs transition-shadow outline-none focus-visible:ring-2 focus-visible:ring-slate-300" required>
        <input type="text" name="items[${index}][duration]" placeholder="Duration" class="col-span-2 h-9 w-full rounded-md border border-gray-200 bg-white px-3 py-1 text-xs transition-shadow outline-none focus-visible:ring-2 focus-visible:ring-slate-300" required>
        <input type="text" name="items[${index}][instructions]" placeholder="Instructions" class="col-span-2 h-9 w-full rounded-md border border-gray-200 bg-white px-3 py-1 text-xs transition-shadow outline-none focus-visible:ring-2 focus-visible:ring-slate-300">
        <button type="button" onclick="this.closest('.prescription-row').remove()" class="col-span-1 text-red-500 hover:text-red-700">
            <i class="hgi-stroke hgi-delete-02 text-base mx-auto"></i>
        </button>
    `;
    container.appendChild(row);
}

function toggleVitalsForm(visitId) {
    const display = document.getElementById('vitals-display-' + visitId);
    const form = document.getElementById('vitals-form-' + visitId);
    display.classList.toggle('hidden');
    form.classList.toggle('hidden');
}
</script>
                                @if ($visit->prescriptions->isNotEmpty())
                                    <div class="relative">
                                        <span class="absolute -left-[21px] top-1 w-3 h-3 rounded-full bg-slate-400 border-2 border-white"></span>
                                        <div class="bg-gray-50 rounded p-3 border border-gray-100">
                                            <p class="text-xs font-semibold text-gray-700 mb-2 flex items-center gap-1.5">
                                                <i class="hgi-stroke hgi-test-tube-01 text-sm"></i>
                                                Prescriptions
                                            </p>
                                            @foreach ($visit->prescriptions as $prescription)
                                                <div class="mb-2 last:mb-0">
                                                    <div class="flex items-center gap-2 mb-1">
                                                        <span class="text-xs font-medium text-gray-700">Rx #{{ $prescription->id }}</span>
                                                        <span class="inline-flex items-center gap-1 text-[10px] font-medium text-gray-600"><span class="w-1.5 h-1.5 rounded-full {{ $prescription->status === 'dispensed' ? 'bg-emerald-500' : 'bg-amber-500' }}"></span>{{ $prescription->status }}</span>
                                                    </div>
                                                    <ul class="space-y-0.5">
                                                        @foreach ($prescription->items as $item)
                                                            <li class="text-xs text-gray-700 flex items-center gap-1.5">
                                                                <span class="w-1 h-1 rounded-full bg-slate-400"></span>
                                                                <strong>{{ $item->medication?->name ?? 'Unknown' }}</strong> - {{ $item->dosage }} {{ $item->frequency }} x{{ $item->quantity }}
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                        @endif

                        <script>
function swalConfirmSubmit(event, message) {
    event.preventDefault();
    const form = event.target;
    Swal.fire({
        icon: 'question',
        title: 'Confirm',
        text: message,
        showCancelButton: true,
        confirmButtonText: 'Yes',
        cancelButtonText: 'Cancel',
        confirmButtonColor: '#0f172a',
    }).then((result) => {
        if (result.isConfirmed) form.submit();
    });
    return false;
}

function addPrescriptionItem(visitId) {
    const container = document.getElementById('prescription-items-' + visitId);
    const index = container.children.length;
    const row = document.createElement('div');
    row.className = 'grid grid-cols-12 gap-2 prescription-row';
    row.innerHTML = `
        <div class="col-span-3 relative">
            <select name="items[${index}][medication_id]" class="h-9 w-full appearance-none rounded-md border border-gray-200 bg-white px-3 pr-8 py-1 text-xs transition-shadow outline-none focus-visible:ring-2 focus-visible:ring-slate-300" required>
            <option value="">Select drug</option>
            @foreach ($medications as $med)
                <option value="{{ $med->id }}">{{ $med->name }}</option>
            @endforeach
            </select>
            <i class="hgi-stroke hgi-arrow-down-01 pointer-events-none absolute right-2.5 top-1/2 -translate-y-1/2 text-sm text-gray-400"></i>
        </div>
        <input type="number" name="items[${index}][quantity]" placeholder="Qty" class="col-span-1 h-9 w-full rounded-md border border-gray-200 bg-white px-3 py-1 text-xs transition-shadow outline-none focus-visible:ring-2 focus-visible:ring-slate-300" required>
        <input type="text" name="items[${index}][dosage]" placeholder="Dose" class="col-span-2 h-9 w-full rounded-md border border-gray-200 bg-white px-3 py-1 text-xs transition-shadow outline-none focus-visible:ring-2 focus-visible:ring-slate-300" required>
        <input type="text" name="items[${index}][frequency]" placeholder="Frequency" class="col-span-2 h-9 w-full rounded-md border border-gray-200 bg-white px-3 py-1 text-xs transition-shadow outline-none focus-visible:ring-2 focus-visible:ring-slate-300" required>
        <input type="text" name="items[${index}][duration]" placeholder="Duration" class="col-span-2 h-9 w-full rounded-md border border-gray-200 bg-white px-3 py-1 text-xs transition-shadow outline-none focus-visible:ring-2 focus-visible:ring-slate-300" required>
        <input type="text" name="items[${index}][instructions]" placeholder="Instructions" class="col-span-2 h-9 w-full rounded-md border border-gray-200 bg-white px-3 py-1 text-xs transition-shadow outline-none focus-visible:ring-2 focus-visible:ring-slate-300">
        <button type="button" onclick="this.closest('.prescription-row').remove()" class="col-span-1 text-red-500 hover:text-red-700">
            <i class="hgi-stroke hgi-delete-02 text-base mx-auto"></i>
        </button>
    `;
    container.appendChild(row);
}

function toggleVitalsForm(visitId) {
    const display = document.getElementById('vitals-display-' + visitId);
    const form = document.getElementById('vitals-form-' + visitId);
    display.classList.toggle('hidden');
    form.classList.toggle('hidden');
}
</script>
                        @if ($previousVisits->isNotEmpty())
                            <div>
                                <p class="text-xs font-semibold text-gray-500 mb-3 flex items-center gap-1.5">
                                    <i class="hgi-stroke hgi-clock-01 text-sm"></i>
                                    Past Visits ({{ $previousVisits->count() }})
                                </p>
                                <div class="space-y-3 ml-3 border-l-2 border-gray-100 pl-4">
                                    @foreach ($previousVisits as $prevVisit)
                                        <div class="relative">
                                            <span class="absolute -left-[21px] top-1 w-3 h-3 rounded-full bg-gray-400 border-2 border-white"></span>
                                            <div class="bg-gray-50 rounded-lg p-3 border border-gray-100">
                                                <div class="flex items-center justify-between mb-2">
                                                    <span class="text-xs font-semibold text-gray-700">{{ $prevVisit->registered_at->format('M d, Y') }}</span>
                                                    <span class="text-[10px] text-gray-400">{{ $prevVisit->visit_number }}</span>
                                                </div>

                                                <script>
function swalConfirmSubmit(event, message) {
    event.preventDefault();
    const form = event.target;
    Swal.fire({
        icon: 'question',
        title: 'Confirm',
        text: message,
        showCancelButton: true,
        confirmButtonText: 'Yes',
        cancelButtonText: 'Cancel',
        confirmButtonColor: '#0f172a',
    }).then((result) => {
        if (result.isConfirmed) form.submit();
    });
    return false;
}

function addPrescriptionItem(visitId) {
    const container = document.getElementById('prescription-items-' + visitId);
    const index = container.children.length;
    const row = document.createElement('div');
    row.className = 'grid grid-cols-12 gap-2 prescription-row';
    row.innerHTML = `
        <div class="col-span-3 relative">
            <select name="items[${index}][medication_id]" class="h-9 w-full appearance-none rounded-md border border-gray-200 bg-white px-3 pr-8 py-1 text-xs transition-shadow outline-none focus-visible:ring-2 focus-visible:ring-slate-300" required>
            <option value="">Select drug</option>
            @foreach ($medications as $med)
                <option value="{{ $med->id }}">{{ $med->name }}</option>
            @endforeach
            </select>
            <i class="hgi-stroke hgi-arrow-down-01 pointer-events-none absolute right-2.5 top-1/2 -translate-y-1/2 text-sm text-gray-400"></i>
        </div>
        <input type="number" name="items[${index}][quantity]" placeholder="Qty" class="col-span-1 h-9 w-full rounded-md border border-gray-200 bg-white px-3 py-1 text-xs transition-shadow outline-none focus-visible:ring-2 focus-visible:ring-slate-300" required>
        <input type="text" name="items[${index}][dosage]" placeholder="Dose" class="col-span-2 h-9 w-full rounded-md border border-gray-200 bg-white px-3 py-1 text-xs transition-shadow outline-none focus-visible:ring-2 focus-visible:ring-slate-300" required>
        <input type="text" name="items[${index}][frequency]" placeholder="Frequency" class="col-span-2 h-9 w-full rounded-md border border-gray-200 bg-white px-3 py-1 text-xs transition-shadow outline-none focus-visible:ring-2 focus-visible:ring-slate-300" required>
        <input type="text" name="items[${index}][duration]" placeholder="Duration" class="col-span-2 h-9 w-full rounded-md border border-gray-200 bg-white px-3 py-1 text-xs transition-shadow outline-none focus-visible:ring-2 focus-visible:ring-slate-300" required>
        <input type="text" name="items[${index}][instructions]" placeholder="Instructions" class="col-span-2 h-9 w-full rounded-md border border-gray-200 bg-white px-3 py-1 text-xs transition-shadow outline-none focus-visible:ring-2 focus-visible:ring-slate-300">
        <button type="button" onclick="this.closest('.prescription-row').remove()" class="col-span-1 text-red-500 hover:text-red-700">
            <i class="hgi-stroke hgi-delete-02 text-base mx-auto"></i>
        </button>
    `;
    container.appendChild(row);
}

function toggleVitalsForm(visitId) {
    const display = document.getElementById('vitals-display-' + visitId);
    const form = document.getElementById('vitals-form-' + visitId);
    display.classList.toggle('hidden');
    form.classList.toggle('hidden');
}
</script>
                                                @if($prevVisit->vitals)
                                                    <div class="flex flex-wrap gap-x-3 gap-y-0.5 text-[11px] text-gray-600 mb-1.5">
                                                        @if($prevVisit->vitals->temperature)<span>Temp: {{ $prevVisit->vitals->temperature }}°C</span>@endif
                                                        @if($prevVisit->vitals->blood_pressure)<span>BP: {{ $prevVisit->vitals->blood_pressure }}</span>@endif
                                                        @if($prevVisit->vitals->weight)<span>Wt: {{ $prevVisit->vitals->weight }}kg</span>@endif
                                                        @if($prevVisit->vitals->pulse)<span>Pulse: {{ $prevVisit->vitals->pulse }}</span>@endif
                                                    </div>
                                                @endif

                                                <script>
function swalConfirmSubmit(event, message) {
    event.preventDefault();
    const form = event.target;
    Swal.fire({
        icon: 'question',
        title: 'Confirm',
        text: message,
        showCancelButton: true,
        confirmButtonText: 'Yes',
        cancelButtonText: 'Cancel',
        confirmButtonColor: '#0f172a',
    }).then((result) => {
        if (result.isConfirmed) form.submit();
    });
    return false;
}

function addPrescriptionItem(visitId) {
    const container = document.getElementById('prescription-items-' + visitId);
    const index = container.children.length;
    const row = document.createElement('div');
    row.className = 'grid grid-cols-12 gap-2 prescription-row';
    row.innerHTML = `
        <div class="col-span-3 relative">
            <select name="items[${index}][medication_id]" class="h-9 w-full appearance-none rounded-md border border-gray-200 bg-white px-3 pr-8 py-1 text-xs transition-shadow outline-none focus-visible:ring-2 focus-visible:ring-slate-300" required>
            <option value="">Select drug</option>
            @foreach ($medications as $med)
                <option value="{{ $med->id }}">{{ $med->name }}</option>
            @endforeach
            </select>
            <i class="hgi-stroke hgi-arrow-down-01 pointer-events-none absolute right-2.5 top-1/2 -translate-y-1/2 text-sm text-gray-400"></i>
        </div>
        <input type="number" name="items[${index}][quantity]" placeholder="Qty" class="col-span-1 h-9 w-full rounded-md border border-gray-200 bg-white px-3 py-1 text-xs transition-shadow outline-none focus-visible:ring-2 focus-visible:ring-slate-300" required>
        <input type="text" name="items[${index}][dosage]" placeholder="Dose" class="col-span-2 h-9 w-full rounded-md border border-gray-200 bg-white px-3 py-1 text-xs transition-shadow outline-none focus-visible:ring-2 focus-visible:ring-slate-300" required>
        <input type="text" name="items[${index}][frequency]" placeholder="Frequency" class="col-span-2 h-9 w-full rounded-md border border-gray-200 bg-white px-3 py-1 text-xs transition-shadow outline-none focus-visible:ring-2 focus-visible:ring-slate-300" required>
        <input type="text" name="items[${index}][duration]" placeholder="Duration" class="col-span-2 h-9 w-full rounded-md border border-gray-200 bg-white px-3 py-1 text-xs transition-shadow outline-none focus-visible:ring-2 focus-visible:ring-slate-300" required>
        <input type="text" name="items[${index}][instructions]" placeholder="Instructions" class="col-span-2 h-9 w-full rounded-md border border-gray-200 bg-white px-3 py-1 text-xs transition-shadow outline-none focus-visible:ring-2 focus-visible:ring-slate-300">
        <button type="button" onclick="this.closest('.prescription-row').remove()" class="col-span-1 text-red-500 hover:text-red-700">
            <i class="hgi-stroke hgi-delete-02 text-base mx-auto"></i>
        </button>
    `;
    container.appendChild(row);
}

function toggleVitalsForm(visitId) {
    const display = document.getElementById('vitals-display-' + visitId);
    const form = document.getElementById('vitals-form-' + visitId);
    display.classList.toggle('hidden');
    form.classList.toggle('hidden');
}
</script>
                                                @if($prevVisit->consultation?->diagnosis)
                                                    <p class="text-[11px] text-gray-700 mb-1.5"><strong>Dx:</strong> {{ $prevVisit->consultation->diagnosis }}</p>
                                                @endif

                                                <script>
function swalConfirmSubmit(event, message) {
    event.preventDefault();
    const form = event.target;
    Swal.fire({
        icon: 'question',
        title: 'Confirm',
        text: message,
        showCancelButton: true,
        confirmButtonText: 'Yes',
        cancelButtonText: 'Cancel',
        confirmButtonColor: '#0f172a',
    }).then((result) => {
        if (result.isConfirmed) form.submit();
    });
    return false;
}

function addPrescriptionItem(visitId) {
    const container = document.getElementById('prescription-items-' + visitId);
    const index = container.children.length;
    const row = document.createElement('div');
    row.className = 'grid grid-cols-12 gap-2 prescription-row';
    row.innerHTML = `
        <div class="col-span-3 relative">
            <select name="items[${index}][medication_id]" class="h-9 w-full appearance-none rounded-md border border-gray-200 bg-white px-3 pr-8 py-1 text-xs transition-shadow outline-none focus-visible:ring-2 focus-visible:ring-slate-300" required>
            <option value="">Select drug</option>
            @foreach ($medications as $med)
                <option value="{{ $med->id }}">{{ $med->name }}</option>
            @endforeach
            </select>
            <i class="hgi-stroke hgi-arrow-down-01 pointer-events-none absolute right-2.5 top-1/2 -translate-y-1/2 text-sm text-gray-400"></i>
        </div>
        <input type="number" name="items[${index}][quantity]" placeholder="Qty" class="col-span-1 h-9 w-full rounded-md border border-gray-200 bg-white px-3 py-1 text-xs transition-shadow outline-none focus-visible:ring-2 focus-visible:ring-slate-300" required>
        <input type="text" name="items[${index}][dosage]" placeholder="Dose" class="col-span-2 h-9 w-full rounded-md border border-gray-200 bg-white px-3 py-1 text-xs transition-shadow outline-none focus-visible:ring-2 focus-visible:ring-slate-300" required>
        <input type="text" name="items[${index}][frequency]" placeholder="Frequency" class="col-span-2 h-9 w-full rounded-md border border-gray-200 bg-white px-3 py-1 text-xs transition-shadow outline-none focus-visible:ring-2 focus-visible:ring-slate-300" required>
        <input type="text" name="items[${index}][duration]" placeholder="Duration" class="col-span-2 h-9 w-full rounded-md border border-gray-200 bg-white px-3 py-1 text-xs transition-shadow outline-none focus-visible:ring-2 focus-visible:ring-slate-300" required>
        <input type="text" name="items[${index}][instructions]" placeholder="Instructions" class="col-span-2 h-9 w-full rounded-md border border-gray-200 bg-white px-3 py-1 text-xs transition-shadow outline-none focus-visible:ring-2 focus-visible:ring-slate-300">
        <button type="button" onclick="this.closest('.prescription-row').remove()" class="col-span-1 text-red-500 hover:text-red-700">
            <i class="hgi-stroke hgi-delete-02 text-base mx-auto"></i>
        </button>
    `;
    container.appendChild(row);
}

function toggleVitalsForm(visitId) {
    const display = document.getElementById('vitals-display-' + visitId);
    const form = document.getElementById('vitals-form-' + visitId);
    display.classList.toggle('hidden');
    form.classList.toggle('hidden');
}
</script>
                                                @if($prevVisit->labOrders->isNotEmpty())
                                                    @foreach($prevVisit->labOrders as $prevOrder)
                                                        @if($prevOrder->results->isNotEmpty())
                                                            <div class="mb-1.5">
                                                                <div class="flex items-center gap-1 mb-0.5">
                                                                    <span class="text-[10px] font-semibold text-gray-600">Lab #{{ $prevOrder->id }}</span>
                                                                    @foreach($prevOrder->items as $item)
                                                                        <span class="inline-flex px-1.5 py-0 rounded text-[9px] font-medium bg-gray-100 text-gray-600">{{ $item->labTest?->name }}</span>
                                                                    @endforeach
                                                                </div>
                                                                <div class="bg-white rounded border border-gray-100 overflow-hidden">
                                                                    <table class="w-full text-[10px]">
                                                                        <tbody class="divide-y divide-gray-50">
                                                                            @foreach($prevOrder->results as $result)
                                                                                @php
                                                                                    $flagBg = match($result->flag) {
                                                                                        'normal' => 'bg-emerald-50 text-emerald-700',
                                                                                        'high', 'low' => 'bg-amber-50 text-amber-700',
                                                                                        'critical' => 'bg-red-50 text-red-700',
                                                                                        default => 'bg-gray-50 text-gray-700',
                                                                                    };
                                                                                @endphp
                                                                                <tr>
                                                                                    <td class="px-2 py-1 font-medium text-gray-700">{{ $result->parameter }}</td>
                                                                                    <td class="px-2 py-1 font-bold {{ $result->flag === 'critical' ? 'text-red-600' : ($result->flag !== 'normal' ? 'text-amber-600' : 'text-gray-900') }}">{{ $result->value }}</td>
                                                                                    <td class="px-2 py-1 text-gray-500">{{ $result->unit ?? '' }}</td>
                                                                                    <td class="px-2 py-1"><span class="inline-flex px-1 py-0 rounded text-[9px] font-medium {{ $flagBg }}">{{ ucfirst($result->flag) }}</span></td>
                                                                                </tr>
                                                                            @endforeach
                                                                        </tbody>
                                                                    </table>
                                                                </div>
                                                            </div>
                                                        @endif
                                                    @endforeach
                                                @endif

                                                <script>
function swalConfirmSubmit(event, message) {
    event.preventDefault();
    const form = event.target;
    Swal.fire({
        icon: 'question',
        title: 'Confirm',
        text: message,
        showCancelButton: true,
        confirmButtonText: 'Yes',
        cancelButtonText: 'Cancel',
        confirmButtonColor: '#0f172a',
    }).then((result) => {
        if (result.isConfirmed) form.submit();
    });
    return false;
}

function addPrescriptionItem(visitId) {
    const container = document.getElementById('prescription-items-' + visitId);
    const index = container.children.length;
    const row = document.createElement('div');
    row.className = 'grid grid-cols-12 gap-2 prescription-row';
    row.innerHTML = `
        <div class="col-span-3 relative">
            <select name="items[${index}][medication_id]" class="h-9 w-full appearance-none rounded-md border border-gray-200 bg-white px-3 pr-8 py-1 text-xs transition-shadow outline-none focus-visible:ring-2 focus-visible:ring-slate-300" required>
            <option value="">Select drug</option>
            @foreach ($medications as $med)
                <option value="{{ $med->id }}">{{ $med->name }}</option>
            @endforeach
            </select>
            <i class="hgi-stroke hgi-arrow-down-01 pointer-events-none absolute right-2.5 top-1/2 -translate-y-1/2 text-sm text-gray-400"></i>
        </div>
        <input type="number" name="items[${index}][quantity]" placeholder="Qty" class="col-span-1 h-9 w-full rounded-md border border-gray-200 bg-white px-3 py-1 text-xs transition-shadow outline-none focus-visible:ring-2 focus-visible:ring-slate-300" required>
        <input type="text" name="items[${index}][dosage]" placeholder="Dose" class="col-span-2 h-9 w-full rounded-md border border-gray-200 bg-white px-3 py-1 text-xs transition-shadow outline-none focus-visible:ring-2 focus-visible:ring-slate-300" required>
        <input type="text" name="items[${index}][frequency]" placeholder="Frequency" class="col-span-2 h-9 w-full rounded-md border border-gray-200 bg-white px-3 py-1 text-xs transition-shadow outline-none focus-visible:ring-2 focus-visible:ring-slate-300" required>
        <input type="text" name="items[${index}][duration]" placeholder="Duration" class="col-span-2 h-9 w-full rounded-md border border-gray-200 bg-white px-3 py-1 text-xs transition-shadow outline-none focus-visible:ring-2 focus-visible:ring-slate-300" required>
        <input type="text" name="items[${index}][instructions]" placeholder="Instructions" class="col-span-2 h-9 w-full rounded-md border border-gray-200 bg-white px-3 py-1 text-xs transition-shadow outline-none focus-visible:ring-2 focus-visible:ring-slate-300">
        <button type="button" onclick="this.closest('.prescription-row').remove()" class="col-span-1 text-red-500 hover:text-red-700">
            <i class="hgi-stroke hgi-delete-02 text-base mx-auto"></i>
        </button>
    `;
    container.appendChild(row);
}

function toggleVitalsForm(visitId) {
    const display = document.getElementById('vitals-display-' + visitId);
    const form = document.getElementById('vitals-form-' + visitId);
    display.classList.toggle('hidden');
    form.classList.toggle('hidden');
}
</script>
                                                @if($prevVisit->ultrasoundOrders->isNotEmpty())
                                                    <div class="flex flex-wrap gap-1 mb-1">
                                                        @foreach($prevVisit->ultrasoundOrders as $usOrder)
                                                            @foreach($usOrder->items as $item)
                                                                <span class="inline-flex px-1.5 py-0 rounded text-[9px] font-medium bg-gray-100 text-gray-600">{{ $item->ultrasoundService?->name }}</span>
                                                            @endforeach
                                                        @endforeach
                                                    </div>
                                                @endif

                                                <script>
function swalConfirmSubmit(event, message) {
    event.preventDefault();
    const form = event.target;
    Swal.fire({
        icon: 'question',
        title: 'Confirm',
        text: message,
        showCancelButton: true,
        confirmButtonText: 'Yes',
        cancelButtonText: 'Cancel',
        confirmButtonColor: '#0f172a',
    }).then((result) => {
        if (result.isConfirmed) form.submit();
    });
    return false;
}

function addPrescriptionItem(visitId) {
    const container = document.getElementById('prescription-items-' + visitId);
    const index = container.children.length;
    const row = document.createElement('div');
    row.className = 'grid grid-cols-12 gap-2 prescription-row';
    row.innerHTML = `
        <div class="col-span-3 relative">
            <select name="items[${index}][medication_id]" class="h-9 w-full appearance-none rounded-md border border-gray-200 bg-white px-3 pr-8 py-1 text-xs transition-shadow outline-none focus-visible:ring-2 focus-visible:ring-slate-300" required>
            <option value="">Select drug</option>
            @foreach ($medications as $med)
                <option value="{{ $med->id }}">{{ $med->name }}</option>
            @endforeach
            </select>
            <i class="hgi-stroke hgi-arrow-down-01 pointer-events-none absolute right-2.5 top-1/2 -translate-y-1/2 text-sm text-gray-400"></i>
        </div>
        <input type="number" name="items[${index}][quantity]" placeholder="Qty" class="col-span-1 h-9 w-full rounded-md border border-gray-200 bg-white px-3 py-1 text-xs transition-shadow outline-none focus-visible:ring-2 focus-visible:ring-slate-300" required>
        <input type="text" name="items[${index}][dosage]" placeholder="Dose" class="col-span-2 h-9 w-full rounded-md border border-gray-200 bg-white px-3 py-1 text-xs transition-shadow outline-none focus-visible:ring-2 focus-visible:ring-slate-300" required>
        <input type="text" name="items[${index}][frequency]" placeholder="Frequency" class="col-span-2 h-9 w-full rounded-md border border-gray-200 bg-white px-3 py-1 text-xs transition-shadow outline-none focus-visible:ring-2 focus-visible:ring-slate-300" required>
        <input type="text" name="items[${index}][duration]" placeholder="Duration" class="col-span-2 h-9 w-full rounded-md border border-gray-200 bg-white px-3 py-1 text-xs transition-shadow outline-none focus-visible:ring-2 focus-visible:ring-slate-300" required>
        <input type="text" name="items[${index}][instructions]" placeholder="Instructions" class="col-span-2 h-9 w-full rounded-md border border-gray-200 bg-white px-3 py-1 text-xs transition-shadow outline-none focus-visible:ring-2 focus-visible:ring-slate-300">
        <button type="button" onclick="this.closest('.prescription-row').remove()" class="col-span-1 text-red-500 hover:text-red-700">
            <i class="hgi-stroke hgi-delete-02 text-base mx-auto"></i>
        </button>
    `;
    container.appendChild(row);
}

function toggleVitalsForm(visitId) {
    const display = document.getElementById('vitals-display-' + visitId);
    const form = document.getElementById('vitals-form-' + visitId);
    display.classList.toggle('hidden');
    form.classList.toggle('hidden');
}
</script>
                                                @if($prevVisit->prescriptions->isNotEmpty())
                                                    <div class="flex flex-wrap gap-1">
                                                        @foreach($prevVisit->prescriptions as $prevRx)
                                                            @foreach($prevRx->items as $item)
                                                                <span class="inline-flex px-1.5 py-0 rounded text-[9px] font-medium bg-gray-100 text-gray-600">{{ $item->medication?->name }}</span>
                                                            @endforeach
                                                        @endforeach
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    @empty
        <div class="bg-white rounded-md border border-gray-200 p-12 text-center">
            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="hgi-stroke hgi-checkmark-circle-02 text-3xl text-gray-400"></i>
            </div>
            <h3 class="text-base font-semibold text-gray-900">No patients in queue</h3>
            <p class="text-sm text-gray-500 mt-1">Your waiting list is clear. Check Results for patients returning from lab or ultrasound.</p>
            <a href="{{ route('doctor.lab-results') }}" class="inline-flex items-center gap-2 mt-4 px-4 py-2 bg-slate-800 hover:bg-slate-700 text-white text-sm font-medium rounded transition-colors">
                View Results
                @if($pendingResultsCount > 0)
                    <span class="bg-red-500 text-white text-[10px] font-bold rounded-full px-2 py-0.5 ml-1">{{ $pendingResultsCount }}</span>
                @endif
            </a>
        </div>
    @endforelse
</div>

<script>
function swalConfirmSubmit(event, message) {
    event.preventDefault();
    const form = event.target;
    Swal.fire({
        icon: 'question',
        title: 'Confirm',
        text: message,
        showCancelButton: true,
        confirmButtonText: 'Yes',
        cancelButtonText: 'Cancel',
        confirmButtonColor: '#0f172a',
    }).then((result) => {
        if (result.isConfirmed) form.submit();
    });
    return false;
}

function addPrescriptionItem(visitId) {
    const container = document.getElementById('prescription-items-' + visitId);
    const index = container.children.length;
    const row = document.createElement('div');
    row.className = 'grid grid-cols-12 gap-2 prescription-row';
    row.innerHTML = `
        <div class="col-span-3 relative">
            <select name="items[${index}][medication_id]" class="h-9 w-full appearance-none rounded-md border border-gray-200 bg-white px-3 pr-8 py-1 text-xs transition-shadow outline-none focus-visible:ring-2 focus-visible:ring-slate-300" required>
            <option value="">Select drug</option>
            @foreach ($medications as $med)
                <option value="{{ $med->id }}">{{ $med->name }}</option>
            @endforeach
            </select>
            <i class="hgi-stroke hgi-arrow-down-01 pointer-events-none absolute right-2.5 top-1/2 -translate-y-1/2 text-sm text-gray-400"></i>
        </div>
        <input type="number" name="items[${index}][quantity]" placeholder="Qty" class="col-span-1 h-9 w-full rounded-md border border-gray-200 bg-white px-3 py-1 text-xs transition-shadow outline-none focus-visible:ring-2 focus-visible:ring-slate-300" required>
        <input type="text" name="items[${index}][dosage]" placeholder="Dose" class="col-span-2 h-9 w-full rounded-md border border-gray-200 bg-white px-3 py-1 text-xs transition-shadow outline-none focus-visible:ring-2 focus-visible:ring-slate-300" required>
        <input type="text" name="items[${index}][frequency]" placeholder="Frequency" class="col-span-2 h-9 w-full rounded-md border border-gray-200 bg-white px-3 py-1 text-xs transition-shadow outline-none focus-visible:ring-2 focus-visible:ring-slate-300" required>
        <input type="text" name="items[${index}][duration]" placeholder="Duration" class="col-span-2 h-9 w-full rounded-md border border-gray-200 bg-white px-3 py-1 text-xs transition-shadow outline-none focus-visible:ring-2 focus-visible:ring-slate-300" required>
        <input type="text" name="items[${index}][instructions]" placeholder="Instructions" class="col-span-2 h-9 w-full rounded-md border border-gray-200 bg-white px-3 py-1 text-xs transition-shadow outline-none focus-visible:ring-2 focus-visible:ring-slate-300">
        <button type="button" onclick="this.closest('.prescription-row').remove()" class="col-span-1 text-red-500 hover:text-red-700">
            <i class="hgi-stroke hgi-delete-02 text-base mx-auto"></i>
        </button>
    `;
    container.appendChild(row);
}

function toggleVitalsForm(visitId) {
    const display = document.getElementById('vitals-display-' + visitId);
    const form = document.getElementById('vitals-form-' + visitId);
    display.classList.toggle('hidden');
    form.classList.toggle('hidden');
}
</script>
@endsection
