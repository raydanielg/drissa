@extends('layouts.dashboard')

@section('title', 'Reception Queue - ' . config('app.name', 'Laravel'))
@section('page_title', 'Reception Queue')

@section('content')
<div class="space-y-5">
    @if (session('status'))
        <div class="p-3 rounded-md bg-white border-l-2 border-emerald-500 text-emerald-700 text-xs">{{ session('status') }}</div>
    @endif

    <script>
function showTab(tab) {
    document.querySelectorAll('.queue-content').forEach(el => el.classList.add('hidden'));
    document.querySelectorAll('.queue-tab').forEach(el => {
        el.classList.remove('border-slate-700', 'bg-gray-50', 'text-slate-800');
        el.classList.add('border-transparent', 'text-gray-500');
    });
    
    document.getElementById('queue-' + tab).classList.remove('hidden');
    const activeTab = document.getElementById('tab-' + tab);
    activeTab.classList.remove('border-transparent', 'text-gray-500');
    activeTab.classList.add('border-slate-700', 'bg-gray-50', 'text-slate-800');
}

// Modal helpers
function openPatientModal() {
    document.getElementById('patientModal').classList.remove('hidden');
}

function closePatientModal() {
    document.getElementById('patientModal').classList.add('hidden');
}

function openVisitModal() {
    document.getElementById('visitModal').classList.remove('hidden');
    // Focus search input when modal opens
    setTimeout(() => document.getElementById('patientSearchInput').focus(), 100);
}

function closeVisitModal() {
    document.getElementById('visitModal').classList.add('hidden');
    clearPatientSelection();
}

// Patient AJAX Search Logic
const searchInput = document.getElementById('patientSearchInput');
const resultsDropdown = document.getElementById('patientSearchResults');
const resultsList = document.getElementById('resultsList');
const searchLoading = document.getElementById('searchLoading');
const searchPlaceholder = document.getElementById('searchPlaceholder');
const noResults = document.getElementById('noResults');
const selectedPatientId = document.getElementById('selectedPatientId');
const selectedPatientIndicator = document.getElementById('selectedPatientIndicator');
const clearPatientBtn = document.getElementById('clearPatientBtn');
const indicatorInitial = document.getElementById('indicatorInitial');
const indicatorName = document.getElementById('indicatorName');
const indicatorMRN = document.getElementById('indicatorMRN');

let searchTimeout = null;

function clearPatientSelection() {
    selectedPatientId.value = '';
    searchInput.value = '';
    searchInput.classList.remove('hidden');
    selectedPatientIndicator.classList.add('hidden');
    clearPatientBtn.classList.add('hidden');
    resultsDropdown.classList.add('hidden');
}

clearPatientBtn.addEventListener('click', clearPatientSelection);

searchInput.addEventListener('input', function(e) {
    const query = e.target.value.trim();
    
    if (query.length === 0) {
        resultsDropdown.classList.add('hidden');
        return;
    }

    resultsDropdown.classList.remove('hidden');
    resultsList.innerHTML = '';
    searchPlaceholder.classList.add('hidden');
    searchLoading.classList.remove('hidden');
    noResults.classList.add('hidden');

    if (searchTimeout) clearTimeout(searchTimeout);

    searchTimeout = setTimeout(() => {
        fetch(`{{ route('patients.ajax-search') }}?q=${encodeURIComponent(query)}`)
            .then(response => response.json())
            .then(data => {
                searchLoading.classList.add('hidden');
                
                if (data.length === 0) {
                    noResults.classList.remove('hidden');
                    return;
                }

                data.forEach(patient => {
                    const row = document.createElement('button');
                    row.type = 'button';
                    row.className = 'w-full text-left p-3 hover:bg-gray-50 transition-colors flex items-center justify-between group';
                    row.innerHTML = `
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center text-slate-600 font-bold text-xs group-hover:bg-slate-700 group-hover:text-white transition-colors">
                                ${patient.text.charAt(0)}
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-gray-900">${patient.text}</p>
                                <p class="text-[10px] text-gray-500">${patient.phone || 'No phone'} • ${patient.gender} • ${patient.age} yrs</p>
                            </div>
                        </div>
                        <i class="hgi-stroke hgi-tick-02 text-base text-slate-500 opacity-0 group-hover:opacity-100 transition-opacity"></i>
                    `;
                    
                    row.addEventListener('click', () => {
                        selectPatient(patient);
                    });
                    
                    resultsList.appendChild(row);
                });
            })
            .catch(error => {
                console.error('Search error:', error);
                searchLoading.classList.add('hidden');
            });
    }, 300);
});

function selectPatient(patient) {
    selectedPatientId.value = patient.id;
    indicatorName.textContent = patient.text.split(' (')[0];
    indicatorMRN.textContent = `(${patient.text.split(' (')[1]}`;
    indicatorInitial.textContent = patient.text.charAt(0);
    
    searchInput.classList.add('hidden');
    selectedPatientIndicator.classList.remove('hidden');
    clearPatientBtn.classList.remove('hidden');
    resultsDropdown.classList.add('hidden');
}

// Close dropdown on click outside
document.addEventListener('click', function(e) {
    if (!document.getElementById('patientSearchContainer').contains(e.target)) {
        resultsDropdown.classList.add('hidden');
    }
});

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
    <div class="bg-slate-800 rounded-md p-6 text-white">
        <div class="flex flex-wrap items-end justify-between gap-3">
            <div>
                <p class="text-[10px] uppercase tracking-wider text-slate-400 mb-1">Reception</p>
                <h1 class="text-xl font-bold">Reception Queue</h1>
                <p class="text-xs text-slate-400 mt-1">Manage patient queues and create new visits.</p>
            </div>
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded bg-slate-700/60 border border-slate-600 text-xs font-medium text-slate-100">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span> Live Queue
            </span>
        </div>
    </div>

    <script>
function showTab(tab) {
    document.querySelectorAll('.queue-content').forEach(el => el.classList.add('hidden'));
    document.querySelectorAll('.queue-tab').forEach(el => {
        el.classList.remove('border-slate-700', 'bg-gray-50', 'text-slate-800');
        el.classList.add('border-transparent', 'text-gray-500');
    });
    
    document.getElementById('queue-' + tab).classList.remove('hidden');
    const activeTab = document.getElementById('tab-' + tab);
    activeTab.classList.remove('border-transparent', 'text-gray-500');
    activeTab.classList.add('border-slate-700', 'bg-gray-50', 'text-slate-800');
}

// Modal helpers
function openPatientModal() {
    document.getElementById('patientModal').classList.remove('hidden');
}

function closePatientModal() {
    document.getElementById('patientModal').classList.add('hidden');
}

function openVisitModal() {
    document.getElementById('visitModal').classList.remove('hidden');
    // Focus search input when modal opens
    setTimeout(() => document.getElementById('patientSearchInput').focus(), 100);
}

function closeVisitModal() {
    document.getElementById('visitModal').classList.add('hidden');
    clearPatientSelection();
}

// Patient AJAX Search Logic
const searchInput = document.getElementById('patientSearchInput');
const resultsDropdown = document.getElementById('patientSearchResults');
const resultsList = document.getElementById('resultsList');
const searchLoading = document.getElementById('searchLoading');
const searchPlaceholder = document.getElementById('searchPlaceholder');
const noResults = document.getElementById('noResults');
const selectedPatientId = document.getElementById('selectedPatientId');
const selectedPatientIndicator = document.getElementById('selectedPatientIndicator');
const clearPatientBtn = document.getElementById('clearPatientBtn');
const indicatorInitial = document.getElementById('indicatorInitial');
const indicatorName = document.getElementById('indicatorName');
const indicatorMRN = document.getElementById('indicatorMRN');

let searchTimeout = null;

function clearPatientSelection() {
    selectedPatientId.value = '';
    searchInput.value = '';
    searchInput.classList.remove('hidden');
    selectedPatientIndicator.classList.add('hidden');
    clearPatientBtn.classList.add('hidden');
    resultsDropdown.classList.add('hidden');
}

clearPatientBtn.addEventListener('click', clearPatientSelection);

searchInput.addEventListener('input', function(e) {
    const query = e.target.value.trim();
    
    if (query.length === 0) {
        resultsDropdown.classList.add('hidden');
        return;
    }

    resultsDropdown.classList.remove('hidden');
    resultsList.innerHTML = '';
    searchPlaceholder.classList.add('hidden');
    searchLoading.classList.remove('hidden');
    noResults.classList.add('hidden');

    if (searchTimeout) clearTimeout(searchTimeout);

    searchTimeout = setTimeout(() => {
        fetch(`{{ route('patients.ajax-search') }}?q=${encodeURIComponent(query)}`)
            .then(response => response.json())
            .then(data => {
                searchLoading.classList.add('hidden');
                
                if (data.length === 0) {
                    noResults.classList.remove('hidden');
                    return;
                }

                data.forEach(patient => {
                    const row = document.createElement('button');
                    row.type = 'button';
                    row.className = 'w-full text-left p-3 hover:bg-gray-50 transition-colors flex items-center justify-between group';
                    row.innerHTML = `
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center text-slate-600 font-bold text-xs group-hover:bg-slate-700 group-hover:text-white transition-colors">
                                ${patient.text.charAt(0)}
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-gray-900">${patient.text}</p>
                                <p class="text-[10px] text-gray-500">${patient.phone || 'No phone'} • ${patient.gender} • ${patient.age} yrs</p>
                            </div>
                        </div>
                        <i class="hgi-stroke hgi-tick-02 text-base text-slate-500 opacity-0 group-hover:opacity-100 transition-opacity"></i>
                    `;
                    
                    row.addEventListener('click', () => {
                        selectPatient(patient);
                    });
                    
                    resultsList.appendChild(row);
                });
            })
            .catch(error => {
                console.error('Search error:', error);
                searchLoading.classList.add('hidden');
            });
    }, 300);
});

function selectPatient(patient) {
    selectedPatientId.value = patient.id;
    indicatorName.textContent = patient.text.split(' (')[0];
    indicatorMRN.textContent = `(${patient.text.split(' (')[1]}`;
    indicatorInitial.textContent = patient.text.charAt(0);
    
    searchInput.classList.add('hidden');
    selectedPatientIndicator.classList.remove('hidden');
    clearPatientBtn.classList.remove('hidden');
    resultsDropdown.classList.add('hidden');
}

// Close dropdown on click outside
document.addEventListener('click', function(e) {
    if (!document.getElementById('patientSearchContainer').contains(e.target)) {
        resultsDropdown.classList.add('hidden');
    }
});

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
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
        <div class="bg-white rounded-md border border-emerald-200 p-4">
            <div class="flex items-start justify-between">
                <p class="text-[10px] font-medium text-gray-500 uppercase tracking-wide">Registered</p>
                <i class="hgi-stroke hgi-checkmark-circle-02 text-base text-gray-400"></i>
            </div>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ $registeredQueue->count() }}</p>
        </div>
        <div class="bg-white rounded-md border border-amber-200 p-4">
            <div class="flex items-start justify-between">
                <p class="text-[10px] font-medium text-gray-500 uppercase tracking-wide">Waiting Doctor</p>
                <i class="hgi-stroke hgi-clock-01 text-base text-gray-400"></i>
            </div>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ $waitingForDoctorQueue->count() }}</p>
        </div>
        <div class="bg-white rounded-md border border-gray-200 p-4">
            <div class="flex items-start justify-between">
                <p class="text-[10px] font-medium text-gray-500 uppercase tracking-wide">With Doctor</p>
                <i class="hgi-stroke hgi-user text-base text-gray-400"></i>
            </div>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ $withDoctorQueue->count() }}</p>
        </div>
        <div class="bg-white rounded-md border border-gray-200 p-4">
            <div class="flex items-start justify-between">
                <p class="text-[10px] font-medium text-gray-500 uppercase tracking-wide">Total Today</p>
                <i class="hgi-stroke hgi-user-multiple-02 text-base text-gray-400"></i>
            </div>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ $allQueues->count() }}</p>
        </div>
    </div>

    <script>
function showTab(tab) {
    document.querySelectorAll('.queue-content').forEach(el => el.classList.add('hidden'));
    document.querySelectorAll('.queue-tab').forEach(el => {
        el.classList.remove('border-slate-700', 'bg-gray-50', 'text-slate-800');
        el.classList.add('border-transparent', 'text-gray-500');
    });
    
    document.getElementById('queue-' + tab).classList.remove('hidden');
    const activeTab = document.getElementById('tab-' + tab);
    activeTab.classList.remove('border-transparent', 'text-gray-500');
    activeTab.classList.add('border-slate-700', 'bg-gray-50', 'text-slate-800');
}

// Modal helpers
function openPatientModal() {
    document.getElementById('patientModal').classList.remove('hidden');
}

function closePatientModal() {
    document.getElementById('patientModal').classList.add('hidden');
}

function openVisitModal() {
    document.getElementById('visitModal').classList.remove('hidden');
    // Focus search input when modal opens
    setTimeout(() => document.getElementById('patientSearchInput').focus(), 100);
}

function closeVisitModal() {
    document.getElementById('visitModal').classList.add('hidden');
    clearPatientSelection();
}

// Patient AJAX Search Logic
const searchInput = document.getElementById('patientSearchInput');
const resultsDropdown = document.getElementById('patientSearchResults');
const resultsList = document.getElementById('resultsList');
const searchLoading = document.getElementById('searchLoading');
const searchPlaceholder = document.getElementById('searchPlaceholder');
const noResults = document.getElementById('noResults');
const selectedPatientId = document.getElementById('selectedPatientId');
const selectedPatientIndicator = document.getElementById('selectedPatientIndicator');
const clearPatientBtn = document.getElementById('clearPatientBtn');
const indicatorInitial = document.getElementById('indicatorInitial');
const indicatorName = document.getElementById('indicatorName');
const indicatorMRN = document.getElementById('indicatorMRN');

let searchTimeout = null;

function clearPatientSelection() {
    selectedPatientId.value = '';
    searchInput.value = '';
    searchInput.classList.remove('hidden');
    selectedPatientIndicator.classList.add('hidden');
    clearPatientBtn.classList.add('hidden');
    resultsDropdown.classList.add('hidden');
}

clearPatientBtn.addEventListener('click', clearPatientSelection);

searchInput.addEventListener('input', function(e) {
    const query = e.target.value.trim();
    
    if (query.length === 0) {
        resultsDropdown.classList.add('hidden');
        return;
    }

    resultsDropdown.classList.remove('hidden');
    resultsList.innerHTML = '';
    searchPlaceholder.classList.add('hidden');
    searchLoading.classList.remove('hidden');
    noResults.classList.add('hidden');

    if (searchTimeout) clearTimeout(searchTimeout);

    searchTimeout = setTimeout(() => {
        fetch(`{{ route('patients.ajax-search') }}?q=${encodeURIComponent(query)}`)
            .then(response => response.json())
            .then(data => {
                searchLoading.classList.add('hidden');
                
                if (data.length === 0) {
                    noResults.classList.remove('hidden');
                    return;
                }

                data.forEach(patient => {
                    const row = document.createElement('button');
                    row.type = 'button';
                    row.className = 'w-full text-left p-3 hover:bg-gray-50 transition-colors flex items-center justify-between group';
                    row.innerHTML = `
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center text-slate-600 font-bold text-xs group-hover:bg-slate-700 group-hover:text-white transition-colors">
                                ${patient.text.charAt(0)}
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-gray-900">${patient.text}</p>
                                <p class="text-[10px] text-gray-500">${patient.phone || 'No phone'} • ${patient.gender} • ${patient.age} yrs</p>
                            </div>
                        </div>
                        <i class="hgi-stroke hgi-tick-02 text-base text-slate-500 opacity-0 group-hover:opacity-100 transition-opacity"></i>
                    `;
                    
                    row.addEventListener('click', () => {
                        selectPatient(patient);
                    });
                    
                    resultsList.appendChild(row);
                });
            })
            .catch(error => {
                console.error('Search error:', error);
                searchLoading.classList.add('hidden');
            });
    }, 300);
});

function selectPatient(patient) {
    selectedPatientId.value = patient.id;
    indicatorName.textContent = patient.text.split(' (')[0];
    indicatorMRN.textContent = `(${patient.text.split(' (')[1]}`;
    indicatorInitial.textContent = patient.text.charAt(0);
    
    searchInput.classList.add('hidden');
    selectedPatientIndicator.classList.remove('hidden');
    clearPatientBtn.classList.remove('hidden');
    resultsDropdown.classList.add('hidden');
}

// Close dropdown on click outside
document.addEventListener('click', function(e) {
    if (!document.getElementById('patientSearchContainer').contains(e.target)) {
        resultsDropdown.classList.add('hidden');
    }
});

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
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
        <button onclick="openPatientModal()" class="flex flex-col items-center gap-2 p-4 bg-slate-800 rounded-md text-white hover:bg-slate-700 transition-colors">
            <i class="hgi-stroke hgi-user-add-01 text-xl"></i>
            <span class="text-xs font-semibold">Register Patient</span>
        </button>
        <button onclick="openVisitModal()" class="flex flex-col items-center gap-2 p-4 bg-white border border-gray-200 rounded-md text-gray-700 hover:bg-gray-50 transition-colors">
            <i class="hgi-stroke hgi-file-01 text-xl text-gray-400"></i>
            <span class="text-xs font-semibold">Open Visit</span>
        </button>
        <a href="{{ route('patients.index') }}" class="flex flex-col items-center gap-2 p-4 bg-white border border-gray-200 rounded-md text-gray-700 hover:bg-gray-50 transition-colors">
            <i class="hgi-stroke hgi-user-multiple-02 text-xl text-gray-400"></i>
            <span class="text-xs font-semibold">Patients</span>
        </a>
        <a href="{{ route('appointments.index') }}" class="flex flex-col items-center gap-2 p-4 bg-white border border-gray-200 rounded-md text-gray-700 hover:bg-gray-50 transition-colors">
            <i class="hgi-stroke hgi-calendar-03 text-xl text-gray-400"></i>
            <span class="text-xs font-semibold">Appointments</span>
        </a>
    </div>

    <script>
function showTab(tab) {
    document.querySelectorAll('.queue-content').forEach(el => el.classList.add('hidden'));
    document.querySelectorAll('.queue-tab').forEach(el => {
        el.classList.remove('border-slate-700', 'bg-gray-50', 'text-slate-800');
        el.classList.add('border-transparent', 'text-gray-500');
    });
    
    document.getElementById('queue-' + tab).classList.remove('hidden');
    const activeTab = document.getElementById('tab-' + tab);
    activeTab.classList.remove('border-transparent', 'text-gray-500');
    activeTab.classList.add('border-slate-700', 'bg-gray-50', 'text-slate-800');
}

// Modal helpers
function openPatientModal() {
    document.getElementById('patientModal').classList.remove('hidden');
}

function closePatientModal() {
    document.getElementById('patientModal').classList.add('hidden');
}

function openVisitModal() {
    document.getElementById('visitModal').classList.remove('hidden');
    // Focus search input when modal opens
    setTimeout(() => document.getElementById('patientSearchInput').focus(), 100);
}

function closeVisitModal() {
    document.getElementById('visitModal').classList.add('hidden');
    clearPatientSelection();
}

// Patient AJAX Search Logic
const searchInput = document.getElementById('patientSearchInput');
const resultsDropdown = document.getElementById('patientSearchResults');
const resultsList = document.getElementById('resultsList');
const searchLoading = document.getElementById('searchLoading');
const searchPlaceholder = document.getElementById('searchPlaceholder');
const noResults = document.getElementById('noResults');
const selectedPatientId = document.getElementById('selectedPatientId');
const selectedPatientIndicator = document.getElementById('selectedPatientIndicator');
const clearPatientBtn = document.getElementById('clearPatientBtn');
const indicatorInitial = document.getElementById('indicatorInitial');
const indicatorName = document.getElementById('indicatorName');
const indicatorMRN = document.getElementById('indicatorMRN');

let searchTimeout = null;

function clearPatientSelection() {
    selectedPatientId.value = '';
    searchInput.value = '';
    searchInput.classList.remove('hidden');
    selectedPatientIndicator.classList.add('hidden');
    clearPatientBtn.classList.add('hidden');
    resultsDropdown.classList.add('hidden');
}

clearPatientBtn.addEventListener('click', clearPatientSelection);

searchInput.addEventListener('input', function(e) {
    const query = e.target.value.trim();
    
    if (query.length === 0) {
        resultsDropdown.classList.add('hidden');
        return;
    }

    resultsDropdown.classList.remove('hidden');
    resultsList.innerHTML = '';
    searchPlaceholder.classList.add('hidden');
    searchLoading.classList.remove('hidden');
    noResults.classList.add('hidden');

    if (searchTimeout) clearTimeout(searchTimeout);

    searchTimeout = setTimeout(() => {
        fetch(`{{ route('patients.ajax-search') }}?q=${encodeURIComponent(query)}`)
            .then(response => response.json())
            .then(data => {
                searchLoading.classList.add('hidden');
                
                if (data.length === 0) {
                    noResults.classList.remove('hidden');
                    return;
                }

                data.forEach(patient => {
                    const row = document.createElement('button');
                    row.type = 'button';
                    row.className = 'w-full text-left p-3 hover:bg-gray-50 transition-colors flex items-center justify-between group';
                    row.innerHTML = `
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center text-slate-600 font-bold text-xs group-hover:bg-slate-700 group-hover:text-white transition-colors">
                                ${patient.text.charAt(0)}
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-gray-900">${patient.text}</p>
                                <p class="text-[10px] text-gray-500">${patient.phone || 'No phone'} • ${patient.gender} • ${patient.age} yrs</p>
                            </div>
                        </div>
                        <i class="hgi-stroke hgi-tick-02 text-base text-slate-500 opacity-0 group-hover:opacity-100 transition-opacity"></i>
                    `;
                    
                    row.addEventListener('click', () => {
                        selectPatient(patient);
                    });
                    
                    resultsList.appendChild(row);
                });
            })
            .catch(error => {
                console.error('Search error:', error);
                searchLoading.classList.add('hidden');
            });
    }, 300);
});

function selectPatient(patient) {
    selectedPatientId.value = patient.id;
    indicatorName.textContent = patient.text.split(' (')[0];
    indicatorMRN.textContent = `(${patient.text.split(' (')[1]}`;
    indicatorInitial.textContent = patient.text.charAt(0);
    
    searchInput.classList.add('hidden');
    selectedPatientIndicator.classList.remove('hidden');
    clearPatientBtn.classList.remove('hidden');
    resultsDropdown.classList.add('hidden');
}

// Close dropdown on click outside
document.addEventListener('click', function(e) {
    if (!document.getElementById('patientSearchContainer').contains(e.target)) {
        resultsDropdown.classList.add('hidden');
    }
});

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
    <div class="bg-white rounded-md border border-gray-200 overflow-hidden">
        <div class="flex border-b border-gray-100 overflow-x-auto">
            <button onclick="showTab('all')" id="tab-all" class="queue-tab px-6 py-3 text-sm font-medium text-slate-800 border-b-2 border-slate-700 bg-gray-50 whitespace-nowrap">
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

        <script>
function showTab(tab) {
    document.querySelectorAll('.queue-content').forEach(el => el.classList.add('hidden'));
    document.querySelectorAll('.queue-tab').forEach(el => {
        el.classList.remove('border-slate-700', 'bg-gray-50', 'text-slate-800');
        el.classList.add('border-transparent', 'text-gray-500');
    });
    
    document.getElementById('queue-' + tab).classList.remove('hidden');
    const activeTab = document.getElementById('tab-' + tab);
    activeTab.classList.remove('border-transparent', 'text-gray-500');
    activeTab.classList.add('border-slate-700', 'bg-gray-50', 'text-slate-800');
}

// Modal helpers
function openPatientModal() {
    document.getElementById('patientModal').classList.remove('hidden');
}

function closePatientModal() {
    document.getElementById('patientModal').classList.add('hidden');
}

function openVisitModal() {
    document.getElementById('visitModal').classList.remove('hidden');
    // Focus search input when modal opens
    setTimeout(() => document.getElementById('patientSearchInput').focus(), 100);
}

function closeVisitModal() {
    document.getElementById('visitModal').classList.add('hidden');
    clearPatientSelection();
}

// Patient AJAX Search Logic
const searchInput = document.getElementById('patientSearchInput');
const resultsDropdown = document.getElementById('patientSearchResults');
const resultsList = document.getElementById('resultsList');
const searchLoading = document.getElementById('searchLoading');
const searchPlaceholder = document.getElementById('searchPlaceholder');
const noResults = document.getElementById('noResults');
const selectedPatientId = document.getElementById('selectedPatientId');
const selectedPatientIndicator = document.getElementById('selectedPatientIndicator');
const clearPatientBtn = document.getElementById('clearPatientBtn');
const indicatorInitial = document.getElementById('indicatorInitial');
const indicatorName = document.getElementById('indicatorName');
const indicatorMRN = document.getElementById('indicatorMRN');

let searchTimeout = null;

function clearPatientSelection() {
    selectedPatientId.value = '';
    searchInput.value = '';
    searchInput.classList.remove('hidden');
    selectedPatientIndicator.classList.add('hidden');
    clearPatientBtn.classList.add('hidden');
    resultsDropdown.classList.add('hidden');
}

clearPatientBtn.addEventListener('click', clearPatientSelection);

searchInput.addEventListener('input', function(e) {
    const query = e.target.value.trim();
    
    if (query.length === 0) {
        resultsDropdown.classList.add('hidden');
        return;
    }

    resultsDropdown.classList.remove('hidden');
    resultsList.innerHTML = '';
    searchPlaceholder.classList.add('hidden');
    searchLoading.classList.remove('hidden');
    noResults.classList.add('hidden');

    if (searchTimeout) clearTimeout(searchTimeout);

    searchTimeout = setTimeout(() => {
        fetch(`{{ route('patients.ajax-search') }}?q=${encodeURIComponent(query)}`)
            .then(response => response.json())
            .then(data => {
                searchLoading.classList.add('hidden');
                
                if (data.length === 0) {
                    noResults.classList.remove('hidden');
                    return;
                }

                data.forEach(patient => {
                    const row = document.createElement('button');
                    row.type = 'button';
                    row.className = 'w-full text-left p-3 hover:bg-gray-50 transition-colors flex items-center justify-between group';
                    row.innerHTML = `
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center text-slate-600 font-bold text-xs group-hover:bg-slate-700 group-hover:text-white transition-colors">
                                ${patient.text.charAt(0)}
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-gray-900">${patient.text}</p>
                                <p class="text-[10px] text-gray-500">${patient.phone || 'No phone'} • ${patient.gender} • ${patient.age} yrs</p>
                            </div>
                        </div>
                        <i class="hgi-stroke hgi-tick-02 text-base text-slate-500 opacity-0 group-hover:opacity-100 transition-opacity"></i>
                    `;
                    
                    row.addEventListener('click', () => {
                        selectPatient(patient);
                    });
                    
                    resultsList.appendChild(row);
                });
            })
            .catch(error => {
                console.error('Search error:', error);
                searchLoading.classList.add('hidden');
            });
    }, 300);
});

function selectPatient(patient) {
    selectedPatientId.value = patient.id;
    indicatorName.textContent = patient.text.split(' (')[0];
    indicatorMRN.textContent = `(${patient.text.split(' (')[1]}`;
    indicatorInitial.textContent = patient.text.charAt(0);
    
    searchInput.classList.add('hidden');
    selectedPatientIndicator.classList.remove('hidden');
    clearPatientBtn.classList.remove('hidden');
    resultsDropdown.classList.add('hidden');
}

// Close dropdown on click outside
document.addEventListener('click', function(e) {
    if (!document.getElementById('patientSearchContainer').contains(e.target)) {
        resultsDropdown.classList.add('hidden');
    }
});

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
        <div id="queue-all" class="queue-content p-4">
            @if($allQueues->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="text-left text-gray-400 text-[10px] uppercase tracking-wider">
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
                                        <div class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center text-slate-600 font-bold text-xs">
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
                                        $statusDot = match($visit->status) {
                                            'registered' => 'bg-emerald-500',
                                            'waiting_for_doctor' => 'bg-amber-500',
                                            'with_doctor' => 'bg-slate-600',
                                            default => 'bg-slate-400'
                                        };
                                        $statusLabel = match($visit->status) {
                                            'registered' => 'Registered',
                                            'waiting_for_doctor' => 'Waiting for Doctor',
                                            'with_doctor' => 'With Doctor',
                                            default => ucfirst(str_replace('_', ' ', $visit->status))
                                        };
                                    @endphp
                                    <span class="inline-flex items-center gap-1.5 text-[10px] font-medium text-gray-600">
                                        <span class="w-1.5 h-1.5 rounded-full {{ $statusDot }}"></span>
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
                                            <button onclick="openAssignDoctorModal({{ $visit->id }})" class="p-2 text-gray-400 hover:text-slate-700 hover:bg-gray-100 rounded transition-colors" title="Assign Doctor">
                                                <i class="hgi-stroke hgi-user text-base"></i>
                                            </button>
                                        @endif
                                        <a href="{{ route('patients.show', $visit->patient) }}" class="p-2 text-gray-400 hover:text-slate-700 hover:bg-gray-100 rounded transition-colors" title="View Patient">
                                            <i class="hgi-stroke hgi-view text-base"></i>
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
                    <i class="hgi-stroke hgi-user-multiple-02 text-base text-gray-300 mx-auto mb-4"></i>
                    <p class="text-gray-500">No patients in queue</p>
                    <button onclick="openVisitModal()" class="mt-4 px-4 py-2 bg-slate-800 hover:bg-slate-700 text-white rounded text-sm font-medium transition-colors">
                        Create First Visit
                    </button>
                </div>
            @endif
        </div>

        <script>
function showTab(tab) {
    document.querySelectorAll('.queue-content').forEach(el => el.classList.add('hidden'));
    document.querySelectorAll('.queue-tab').forEach(el => {
        el.classList.remove('border-slate-700', 'bg-gray-50', 'text-slate-800');
        el.classList.add('border-transparent', 'text-gray-500');
    });
    
    document.getElementById('queue-' + tab).classList.remove('hidden');
    const activeTab = document.getElementById('tab-' + tab);
    activeTab.classList.remove('border-transparent', 'text-gray-500');
    activeTab.classList.add('border-slate-700', 'bg-gray-50', 'text-slate-800');
}

// Modal helpers
function openPatientModal() {
    document.getElementById('patientModal').classList.remove('hidden');
}

function closePatientModal() {
    document.getElementById('patientModal').classList.add('hidden');
}

function openVisitModal() {
    document.getElementById('visitModal').classList.remove('hidden');
    // Focus search input when modal opens
    setTimeout(() => document.getElementById('patientSearchInput').focus(), 100);
}

function closeVisitModal() {
    document.getElementById('visitModal').classList.add('hidden');
    clearPatientSelection();
}

// Patient AJAX Search Logic
const searchInput = document.getElementById('patientSearchInput');
const resultsDropdown = document.getElementById('patientSearchResults');
const resultsList = document.getElementById('resultsList');
const searchLoading = document.getElementById('searchLoading');
const searchPlaceholder = document.getElementById('searchPlaceholder');
const noResults = document.getElementById('noResults');
const selectedPatientId = document.getElementById('selectedPatientId');
const selectedPatientIndicator = document.getElementById('selectedPatientIndicator');
const clearPatientBtn = document.getElementById('clearPatientBtn');
const indicatorInitial = document.getElementById('indicatorInitial');
const indicatorName = document.getElementById('indicatorName');
const indicatorMRN = document.getElementById('indicatorMRN');

let searchTimeout = null;

function clearPatientSelection() {
    selectedPatientId.value = '';
    searchInput.value = '';
    searchInput.classList.remove('hidden');
    selectedPatientIndicator.classList.add('hidden');
    clearPatientBtn.classList.add('hidden');
    resultsDropdown.classList.add('hidden');
}

clearPatientBtn.addEventListener('click', clearPatientSelection);

searchInput.addEventListener('input', function(e) {
    const query = e.target.value.trim();
    
    if (query.length === 0) {
        resultsDropdown.classList.add('hidden');
        return;
    }

    resultsDropdown.classList.remove('hidden');
    resultsList.innerHTML = '';
    searchPlaceholder.classList.add('hidden');
    searchLoading.classList.remove('hidden');
    noResults.classList.add('hidden');

    if (searchTimeout) clearTimeout(searchTimeout);

    searchTimeout = setTimeout(() => {
        fetch(`{{ route('patients.ajax-search') }}?q=${encodeURIComponent(query)}`)
            .then(response => response.json())
            .then(data => {
                searchLoading.classList.add('hidden');
                
                if (data.length === 0) {
                    noResults.classList.remove('hidden');
                    return;
                }

                data.forEach(patient => {
                    const row = document.createElement('button');
                    row.type = 'button';
                    row.className = 'w-full text-left p-3 hover:bg-gray-50 transition-colors flex items-center justify-between group';
                    row.innerHTML = `
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center text-slate-600 font-bold text-xs group-hover:bg-slate-700 group-hover:text-white transition-colors">
                                ${patient.text.charAt(0)}
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-gray-900">${patient.text}</p>
                                <p class="text-[10px] text-gray-500">${patient.phone || 'No phone'} • ${patient.gender} • ${patient.age} yrs</p>
                            </div>
                        </div>
                        <i class="hgi-stroke hgi-tick-02 text-base text-slate-500 opacity-0 group-hover:opacity-100 transition-opacity"></i>
                    `;
                    
                    row.addEventListener('click', () => {
                        selectPatient(patient);
                    });
                    
                    resultsList.appendChild(row);
                });
            })
            .catch(error => {
                console.error('Search error:', error);
                searchLoading.classList.add('hidden');
            });
    }, 300);
});

function selectPatient(patient) {
    selectedPatientId.value = patient.id;
    indicatorName.textContent = patient.text.split(' (')[0];
    indicatorMRN.textContent = `(${patient.text.split(' (')[1]}`;
    indicatorInitial.textContent = patient.text.charAt(0);
    
    searchInput.classList.add('hidden');
    selectedPatientIndicator.classList.remove('hidden');
    clearPatientBtn.classList.remove('hidden');
    resultsDropdown.classList.add('hidden');
}

// Close dropdown on click outside
document.addEventListener('click', function(e) {
    if (!document.getElementById('patientSearchContainer').contains(e.target)) {
        resultsDropdown.classList.add('hidden');
    }
});

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
                                        <div class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center text-slate-600 font-bold text-xs">
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
                                        <button onclick="openAssignDoctorModal({{ $visit->id }})" class="px-3 py-1.5 bg-slate-800 hover:bg-slate-700 text-white rounded text-xs font-medium transition-colors flex items-center gap-1">
                                            <i class="hgi-stroke hgi-user text-xs"></i>
                                            Assign Doctor
                                        </button>
                                        <a href="{{ route('patients.show', $visit->patient) }}" class="p-2 text-gray-400 hover:text-slate-700 hover:bg-gray-100 rounded transition-colors">
                                            <i class="hgi-stroke hgi-view text-base"></i>
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

        <script>
function showTab(tab) {
    document.querySelectorAll('.queue-content').forEach(el => el.classList.add('hidden'));
    document.querySelectorAll('.queue-tab').forEach(el => {
        el.classList.remove('border-slate-700', 'bg-gray-50', 'text-slate-800');
        el.classList.add('border-transparent', 'text-gray-500');
    });
    
    document.getElementById('queue-' + tab).classList.remove('hidden');
    const activeTab = document.getElementById('tab-' + tab);
    activeTab.classList.remove('border-transparent', 'text-gray-500');
    activeTab.classList.add('border-slate-700', 'bg-gray-50', 'text-slate-800');
}

// Modal helpers
function openPatientModal() {
    document.getElementById('patientModal').classList.remove('hidden');
}

function closePatientModal() {
    document.getElementById('patientModal').classList.add('hidden');
}

function openVisitModal() {
    document.getElementById('visitModal').classList.remove('hidden');
    // Focus search input when modal opens
    setTimeout(() => document.getElementById('patientSearchInput').focus(), 100);
}

function closeVisitModal() {
    document.getElementById('visitModal').classList.add('hidden');
    clearPatientSelection();
}

// Patient AJAX Search Logic
const searchInput = document.getElementById('patientSearchInput');
const resultsDropdown = document.getElementById('patientSearchResults');
const resultsList = document.getElementById('resultsList');
const searchLoading = document.getElementById('searchLoading');
const searchPlaceholder = document.getElementById('searchPlaceholder');
const noResults = document.getElementById('noResults');
const selectedPatientId = document.getElementById('selectedPatientId');
const selectedPatientIndicator = document.getElementById('selectedPatientIndicator');
const clearPatientBtn = document.getElementById('clearPatientBtn');
const indicatorInitial = document.getElementById('indicatorInitial');
const indicatorName = document.getElementById('indicatorName');
const indicatorMRN = document.getElementById('indicatorMRN');

let searchTimeout = null;

function clearPatientSelection() {
    selectedPatientId.value = '';
    searchInput.value = '';
    searchInput.classList.remove('hidden');
    selectedPatientIndicator.classList.add('hidden');
    clearPatientBtn.classList.add('hidden');
    resultsDropdown.classList.add('hidden');
}

clearPatientBtn.addEventListener('click', clearPatientSelection);

searchInput.addEventListener('input', function(e) {
    const query = e.target.value.trim();
    
    if (query.length === 0) {
        resultsDropdown.classList.add('hidden');
        return;
    }

    resultsDropdown.classList.remove('hidden');
    resultsList.innerHTML = '';
    searchPlaceholder.classList.add('hidden');
    searchLoading.classList.remove('hidden');
    noResults.classList.add('hidden');

    if (searchTimeout) clearTimeout(searchTimeout);

    searchTimeout = setTimeout(() => {
        fetch(`{{ route('patients.ajax-search') }}?q=${encodeURIComponent(query)}`)
            .then(response => response.json())
            .then(data => {
                searchLoading.classList.add('hidden');
                
                if (data.length === 0) {
                    noResults.classList.remove('hidden');
                    return;
                }

                data.forEach(patient => {
                    const row = document.createElement('button');
                    row.type = 'button';
                    row.className = 'w-full text-left p-3 hover:bg-gray-50 transition-colors flex items-center justify-between group';
                    row.innerHTML = `
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center text-slate-600 font-bold text-xs group-hover:bg-slate-700 group-hover:text-white transition-colors">
                                ${patient.text.charAt(0)}
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-gray-900">${patient.text}</p>
                                <p class="text-[10px] text-gray-500">${patient.phone || 'No phone'} • ${patient.gender} • ${patient.age} yrs</p>
                            </div>
                        </div>
                        <i class="hgi-stroke hgi-tick-02 text-base text-slate-500 opacity-0 group-hover:opacity-100 transition-opacity"></i>
                    `;
                    
                    row.addEventListener('click', () => {
                        selectPatient(patient);
                    });
                    
                    resultsList.appendChild(row);
                });
            })
            .catch(error => {
                console.error('Search error:', error);
                searchLoading.classList.add('hidden');
            });
    }, 300);
});

function selectPatient(patient) {
    selectedPatientId.value = patient.id;
    indicatorName.textContent = patient.text.split(' (')[0];
    indicatorMRN.textContent = `(${patient.text.split(' (')[1]}`;
    indicatorInitial.textContent = patient.text.charAt(0);
    
    searchInput.classList.add('hidden');
    selectedPatientIndicator.classList.remove('hidden');
    clearPatientBtn.classList.remove('hidden');
    resultsDropdown.classList.add('hidden');
}

// Close dropdown on click outside
document.addEventListener('click', function(e) {
    if (!document.getElementById('patientSearchContainer').contains(e.target)) {
        resultsDropdown.classList.add('hidden');
    }
});

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
                                        <div class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center text-slate-600 font-bold text-xs">
                                            {{ strtoupper(substr($visit->patient->first_name ?? 'U', 0, 1)) }}
                                        </div>
                                        <div>
                                            <p class="text-gray-900 text-sm font-medium">{{ $visit->patient->fullName() }}</p>
                                            <p class="text-gray-500 text-xs">{{ $visit->patient->phone ?? 'N/A' }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3 text-gray-600 text-sm font-mono">{{ $visit->patient->mrn }}</td>
                                <td class="py-3 text-gray-600 text-sm font-medium">{{ $visit->doctor ? $visit->doctor->name : 'Not Assigned' }}</td>
                                <td class="py-3 text-gray-500 text-sm">{{ $visit->registered_at->format('H:i') }}</td>
                                <td class="py-3">
                                    <div class="flex items-center gap-2">
                                        <button onclick="openChangeDoctorModal({{ $visit->id }})" class="p-2 text-gray-400 hover:text-slate-700 hover:bg-gray-100 rounded transition-colors" title="Change Doctor">
                                            <i class="hgi-stroke hgi-arrow-left-right text-base"></i>
                                        </button>
                                        <a href="{{ route('patients.show', $visit->patient) }}" class="p-2 text-gray-400 hover:text-slate-700 hover:bg-gray-100 rounded transition-colors">
                                            <i class="hgi-stroke hgi-view text-base"></i>
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

        <script>
function showTab(tab) {
    document.querySelectorAll('.queue-content').forEach(el => el.classList.add('hidden'));
    document.querySelectorAll('.queue-tab').forEach(el => {
        el.classList.remove('border-slate-700', 'bg-gray-50', 'text-slate-800');
        el.classList.add('border-transparent', 'text-gray-500');
    });
    
    document.getElementById('queue-' + tab).classList.remove('hidden');
    const activeTab = document.getElementById('tab-' + tab);
    activeTab.classList.remove('border-transparent', 'text-gray-500');
    activeTab.classList.add('border-slate-700', 'bg-gray-50', 'text-slate-800');
}

// Modal helpers
function openPatientModal() {
    document.getElementById('patientModal').classList.remove('hidden');
}

function closePatientModal() {
    document.getElementById('patientModal').classList.add('hidden');
}

function openVisitModal() {
    document.getElementById('visitModal').classList.remove('hidden');
    // Focus search input when modal opens
    setTimeout(() => document.getElementById('patientSearchInput').focus(), 100);
}

function closeVisitModal() {
    document.getElementById('visitModal').classList.add('hidden');
    clearPatientSelection();
}

// Patient AJAX Search Logic
const searchInput = document.getElementById('patientSearchInput');
const resultsDropdown = document.getElementById('patientSearchResults');
const resultsList = document.getElementById('resultsList');
const searchLoading = document.getElementById('searchLoading');
const searchPlaceholder = document.getElementById('searchPlaceholder');
const noResults = document.getElementById('noResults');
const selectedPatientId = document.getElementById('selectedPatientId');
const selectedPatientIndicator = document.getElementById('selectedPatientIndicator');
const clearPatientBtn = document.getElementById('clearPatientBtn');
const indicatorInitial = document.getElementById('indicatorInitial');
const indicatorName = document.getElementById('indicatorName');
const indicatorMRN = document.getElementById('indicatorMRN');

let searchTimeout = null;

function clearPatientSelection() {
    selectedPatientId.value = '';
    searchInput.value = '';
    searchInput.classList.remove('hidden');
    selectedPatientIndicator.classList.add('hidden');
    clearPatientBtn.classList.add('hidden');
    resultsDropdown.classList.add('hidden');
}

clearPatientBtn.addEventListener('click', clearPatientSelection);

searchInput.addEventListener('input', function(e) {
    const query = e.target.value.trim();
    
    if (query.length === 0) {
        resultsDropdown.classList.add('hidden');
        return;
    }

    resultsDropdown.classList.remove('hidden');
    resultsList.innerHTML = '';
    searchPlaceholder.classList.add('hidden');
    searchLoading.classList.remove('hidden');
    noResults.classList.add('hidden');

    if (searchTimeout) clearTimeout(searchTimeout);

    searchTimeout = setTimeout(() => {
        fetch(`{{ route('patients.ajax-search') }}?q=${encodeURIComponent(query)}`)
            .then(response => response.json())
            .then(data => {
                searchLoading.classList.add('hidden');
                
                if (data.length === 0) {
                    noResults.classList.remove('hidden');
                    return;
                }

                data.forEach(patient => {
                    const row = document.createElement('button');
                    row.type = 'button';
                    row.className = 'w-full text-left p-3 hover:bg-gray-50 transition-colors flex items-center justify-between group';
                    row.innerHTML = `
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center text-slate-600 font-bold text-xs group-hover:bg-slate-700 group-hover:text-white transition-colors">
                                ${patient.text.charAt(0)}
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-gray-900">${patient.text}</p>
                                <p class="text-[10px] text-gray-500">${patient.phone || 'No phone'} • ${patient.gender} • ${patient.age} yrs</p>
                            </div>
                        </div>
                        <i class="hgi-stroke hgi-tick-02 text-base text-slate-500 opacity-0 group-hover:opacity-100 transition-opacity"></i>
                    `;
                    
                    row.addEventListener('click', () => {
                        selectPatient(patient);
                    });
                    
                    resultsList.appendChild(row);
                });
            })
            .catch(error => {
                console.error('Search error:', error);
                searchLoading.classList.add('hidden');
            });
    }, 300);
});

function selectPatient(patient) {
    selectedPatientId.value = patient.id;
    indicatorName.textContent = patient.text.split(' (')[0];
    indicatorMRN.textContent = `(${patient.text.split(' (')[1]}`;
    indicatorInitial.textContent = patient.text.charAt(0);
    
    searchInput.classList.add('hidden');
    selectedPatientIndicator.classList.remove('hidden');
    clearPatientBtn.classList.remove('hidden');
    resultsDropdown.classList.add('hidden');
}

// Close dropdown on click outside
document.addEventListener('click', function(e) {
    if (!document.getElementById('patientSearchContainer').contains(e.target)) {
        resultsDropdown.classList.add('hidden');
    }
});

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
                                        <div class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center text-slate-600 font-bold text-xs">
                                            {{ strtoupper(substr($visit->patient->first_name ?? 'U', 0, 1)) }}
                                        </div>
                                        <div>
                                            <p class="text-gray-900 text-sm font-medium">{{ $visit->patient->fullName() }}</p>
                                            <p class="text-gray-500 text-xs">{{ $visit->patient->phone ?? 'N/A' }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3 text-gray-600 text-sm font-mono">{{ $visit->patient->mrn }}</td>
                                <td class="py-3 text-gray-600 text-sm font-medium">{{ $visit->doctor ? $visit->doctor->name : 'Not Assigned' }}</td>
                                <td class="py-3 text-gray-500 text-sm">{{ $visit->registered_at->format('H:i') }}</td>
                                <td class="py-3">
                                    <a href="{{ route('patients.show', $visit->patient) }}" class="p-2 text-gray-400 hover:text-slate-700 hover:bg-gray-100 rounded transition-colors">
                                        <i class="hgi-stroke hgi-view text-base"></i>
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

<script>
function showTab(tab) {
    document.querySelectorAll('.queue-content').forEach(el => el.classList.add('hidden'));
    document.querySelectorAll('.queue-tab').forEach(el => {
        el.classList.remove('border-slate-700', 'bg-gray-50', 'text-slate-800');
        el.classList.add('border-transparent', 'text-gray-500');
    });
    
    document.getElementById('queue-' + tab).classList.remove('hidden');
    const activeTab = document.getElementById('tab-' + tab);
    activeTab.classList.remove('border-transparent', 'text-gray-500');
    activeTab.classList.add('border-slate-700', 'bg-gray-50', 'text-slate-800');
}

// Modal helpers
function openPatientModal() {
    document.getElementById('patientModal').classList.remove('hidden');
}

function closePatientModal() {
    document.getElementById('patientModal').classList.add('hidden');
}

function openVisitModal() {
    document.getElementById('visitModal').classList.remove('hidden');
    // Focus search input when modal opens
    setTimeout(() => document.getElementById('patientSearchInput').focus(), 100);
}

function closeVisitModal() {
    document.getElementById('visitModal').classList.add('hidden');
    clearPatientSelection();
}

// Patient AJAX Search Logic
const searchInput = document.getElementById('patientSearchInput');
const resultsDropdown = document.getElementById('patientSearchResults');
const resultsList = document.getElementById('resultsList');
const searchLoading = document.getElementById('searchLoading');
const searchPlaceholder = document.getElementById('searchPlaceholder');
const noResults = document.getElementById('noResults');
const selectedPatientId = document.getElementById('selectedPatientId');
const selectedPatientIndicator = document.getElementById('selectedPatientIndicator');
const clearPatientBtn = document.getElementById('clearPatientBtn');
const indicatorInitial = document.getElementById('indicatorInitial');
const indicatorName = document.getElementById('indicatorName');
const indicatorMRN = document.getElementById('indicatorMRN');

let searchTimeout = null;

function clearPatientSelection() {
    selectedPatientId.value = '';
    searchInput.value = '';
    searchInput.classList.remove('hidden');
    selectedPatientIndicator.classList.add('hidden');
    clearPatientBtn.classList.add('hidden');
    resultsDropdown.classList.add('hidden');
}

clearPatientBtn.addEventListener('click', clearPatientSelection);

searchInput.addEventListener('input', function(e) {
    const query = e.target.value.trim();
    
    if (query.length === 0) {
        resultsDropdown.classList.add('hidden');
        return;
    }

    resultsDropdown.classList.remove('hidden');
    resultsList.innerHTML = '';
    searchPlaceholder.classList.add('hidden');
    searchLoading.classList.remove('hidden');
    noResults.classList.add('hidden');

    if (searchTimeout) clearTimeout(searchTimeout);

    searchTimeout = setTimeout(() => {
        fetch(`{{ route('patients.ajax-search') }}?q=${encodeURIComponent(query)}`)
            .then(response => response.json())
            .then(data => {
                searchLoading.classList.add('hidden');
                
                if (data.length === 0) {
                    noResults.classList.remove('hidden');
                    return;
                }

                data.forEach(patient => {
                    const row = document.createElement('button');
                    row.type = 'button';
                    row.className = 'w-full text-left p-3 hover:bg-gray-50 transition-colors flex items-center justify-between group';
                    row.innerHTML = `
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center text-slate-600 font-bold text-xs group-hover:bg-slate-700 group-hover:text-white transition-colors">
                                ${patient.text.charAt(0)}
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-gray-900">${patient.text}</p>
                                <p class="text-[10px] text-gray-500">${patient.phone || 'No phone'} • ${patient.gender} • ${patient.age} yrs</p>
                            </div>
                        </div>
                        <i class="hgi-stroke hgi-tick-02 text-base text-slate-500 opacity-0 group-hover:opacity-100 transition-opacity"></i>
                    `;
                    
                    row.addEventListener('click', () => {
                        selectPatient(patient);
                    });
                    
                    resultsList.appendChild(row);
                });
            })
            .catch(error => {
                console.error('Search error:', error);
                searchLoading.classList.add('hidden');
            });
    }, 300);
});

function selectPatient(patient) {
    selectedPatientId.value = patient.id;
    indicatorName.textContent = patient.text.split(' (')[0];
    indicatorMRN.textContent = `(${patient.text.split(' (')[1]}`;
    indicatorInitial.textContent = patient.text.charAt(0);
    
    searchInput.classList.add('hidden');
    selectedPatientIndicator.classList.remove('hidden');
    clearPatientBtn.classList.remove('hidden');
    resultsDropdown.classList.add('hidden');
}

// Close dropdown on click outside
document.addEventListener('click', function(e) {
    if (!document.getElementById('patientSearchContainer').contains(e.target)) {
        resultsDropdown.classList.add('hidden');
    }
});

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
<div id="patientModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white border border-gray-200 rounded-2xl w-full max-w-lg shadow-2xl">
        <div class="p-6 border-b border-gray-100">
            <div class="flex items-center justify-between">
                <h3 class="text-xl font-bold text-gray-900">Register New Patient</h3>
                <button onclick="closePatientModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <i class="hgi-stroke hgi-cancel-01 text-2xl"></i>
                </button>
            </div>
        </div>
        <form action="{{ route('reception.patients.store') }}" method="POST" class="p-6 space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Full Name</label>
                <x-ui.input type="text" name="name" required class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-lg text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-slate-300 transition-all" placeholder="John Doe" />
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Date of Birth</label>
                    <x-ui.input type="text" data-fp-date name="date_of_birth" required class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-lg text-gray-900 focus:outline-none focus:ring-2 focus:ring-slate-300 transition-all" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Gender</label>
                    <x-ui.select name="gender" required class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-lg text-gray-900 focus:outline-none focus:ring-2 focus:ring-slate-300 transition-all">
                        <option value="">Select</option>
                        <option value="male">Male</option>
                        <option value="female">Female</option>
                        <option value="other">Other</option>
                    </x-ui.select>
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Phone</label>
                <x-ui.input type="text" name="phone" class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-lg text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-slate-300 transition-all" placeholder="+255 123 456 789" />
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Address</label>
                <x-ui.textarea name="address" rows="2" class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-lg text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-slate-300 transition-all resize-none" placeholder="Patient address"></x-ui.textarea>
            </div>
            <div class="flex gap-3 pt-4">
                <button type="button" onclick="closePatientModal()" class="flex-1 px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg font-medium transition-all border border-gray-200">
                    Cancel
                </button>
                <button type="submit" class="flex-1 px-4 py-2.5 bg-slate-800 hover:bg-slate-700 text-white rounded font-medium transition-colors">
                    Register Patient
                </button>
            </div>
        </form>
    </div>
</div>

 <script>
function showTab(tab) {
    document.querySelectorAll('.queue-content').forEach(el => el.classList.add('hidden'));
    document.querySelectorAll('.queue-tab').forEach(el => {
        el.classList.remove('border-slate-700', 'bg-gray-50', 'text-slate-800');
        el.classList.add('border-transparent', 'text-gray-500');
    });
    
    document.getElementById('queue-' + tab).classList.remove('hidden');
    const activeTab = document.getElementById('tab-' + tab);
    activeTab.classList.remove('border-transparent', 'text-gray-500');
    activeTab.classList.add('border-slate-700', 'bg-gray-50', 'text-slate-800');
}

// Modal helpers
function openPatientModal() {
    document.getElementById('patientModal').classList.remove('hidden');
}

function closePatientModal() {
    document.getElementById('patientModal').classList.add('hidden');
}

function openVisitModal() {
    document.getElementById('visitModal').classList.remove('hidden');
    // Focus search input when modal opens
    setTimeout(() => document.getElementById('patientSearchInput').focus(), 100);
}

function closeVisitModal() {
    document.getElementById('visitModal').classList.add('hidden');
    clearPatientSelection();
}

// Patient AJAX Search Logic
const searchInput = document.getElementById('patientSearchInput');
const resultsDropdown = document.getElementById('patientSearchResults');
const resultsList = document.getElementById('resultsList');
const searchLoading = document.getElementById('searchLoading');
const searchPlaceholder = document.getElementById('searchPlaceholder');
const noResults = document.getElementById('noResults');
const selectedPatientId = document.getElementById('selectedPatientId');
const selectedPatientIndicator = document.getElementById('selectedPatientIndicator');
const clearPatientBtn = document.getElementById('clearPatientBtn');
const indicatorInitial = document.getElementById('indicatorInitial');
const indicatorName = document.getElementById('indicatorName');
const indicatorMRN = document.getElementById('indicatorMRN');

let searchTimeout = null;

function clearPatientSelection() {
    selectedPatientId.value = '';
    searchInput.value = '';
    searchInput.classList.remove('hidden');
    selectedPatientIndicator.classList.add('hidden');
    clearPatientBtn.classList.add('hidden');
    resultsDropdown.classList.add('hidden');
}

clearPatientBtn.addEventListener('click', clearPatientSelection);

searchInput.addEventListener('input', function(e) {
    const query = e.target.value.trim();
    
    if (query.length === 0) {
        resultsDropdown.classList.add('hidden');
        return;
    }

    resultsDropdown.classList.remove('hidden');
    resultsList.innerHTML = '';
    searchPlaceholder.classList.add('hidden');
    searchLoading.classList.remove('hidden');
    noResults.classList.add('hidden');

    if (searchTimeout) clearTimeout(searchTimeout);

    searchTimeout = setTimeout(() => {
        fetch(`{{ route('patients.ajax-search') }}?q=${encodeURIComponent(query)}`)
            .then(response => response.json())
            .then(data => {
                searchLoading.classList.add('hidden');
                
                if (data.length === 0) {
                    noResults.classList.remove('hidden');
                    return;
                }

                data.forEach(patient => {
                    const row = document.createElement('button');
                    row.type = 'button';
                    row.className = 'w-full text-left p-3 hover:bg-gray-50 transition-colors flex items-center justify-between group';
                    row.innerHTML = `
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center text-slate-600 font-bold text-xs group-hover:bg-slate-700 group-hover:text-white transition-colors">
                                ${patient.text.charAt(0)}
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-gray-900">${patient.text}</p>
                                <p class="text-[10px] text-gray-500">${patient.phone || 'No phone'} • ${patient.gender} • ${patient.age} yrs</p>
                            </div>
                        </div>
                        <i class="hgi-stroke hgi-tick-02 text-base text-slate-500 opacity-0 group-hover:opacity-100 transition-opacity"></i>
                    `;
                    
                    row.addEventListener('click', () => {
                        selectPatient(patient);
                    });
                    
                    resultsList.appendChild(row);
                });
            })
            .catch(error => {
                console.error('Search error:', error);
                searchLoading.classList.add('hidden');
            });
    }, 300);
});

function selectPatient(patient) {
    selectedPatientId.value = patient.id;
    indicatorName.textContent = patient.text.split(' (')[0];
    indicatorMRN.textContent = `(${patient.text.split(' (')[1]}`;
    indicatorInitial.textContent = patient.text.charAt(0);
    
    searchInput.classList.add('hidden');
    selectedPatientIndicator.classList.remove('hidden');
    clearPatientBtn.classList.remove('hidden');
    resultsDropdown.classList.add('hidden');
}

// Close dropdown on click outside
document.addEventListener('click', function(e) {
    if (!document.getElementById('patientSearchContainer').contains(e.target)) {
        resultsDropdown.classList.add('hidden');
    }
});

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
<div id="visitModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white border border-gray-200 rounded-2xl w-full max-w-lg shadow-2xl">
        <div class="p-6 border-b border-gray-100">
            <div class="flex items-center justify-between">
                <h3 class="text-xl font-bold text-gray-900">Create New Visit</h3>
                <button onclick="closeVisitModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <i class="hgi-stroke hgi-cancel-01 text-2xl"></i>
                </button>
            </div>
        </div>
        <form action="{{ route('reception.visits.store') }}" method="POST" class="p-6 space-y-4">
            @csrf
            <div class="relative" id="patientSearchContainer">
                <label class="block text-sm font-medium text-gray-700 mb-2">Select Patient</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="hgi-stroke hgi-search-01 text-xl text-gray-400"></i>
                    </div>
                    <x-ui.input type="text" id="patientSearchInput" autocomplete="off" class="w-full pl-10 pr-4 py-2.5 bg-white border border-gray-300 rounded-lg text-gray-900 focus:outline-none focus:ring-2 focus:ring-slate-300 transition-all" placeholder="Search by name, MRN or phone..." />
                    <input type="hidden" name="patient_id" id="selectedPatientId" required>
                    
                    <script>
function showTab(tab) {
    document.querySelectorAll('.queue-content').forEach(el => el.classList.add('hidden'));
    document.querySelectorAll('.queue-tab').forEach(el => {
        el.classList.remove('border-slate-700', 'bg-gray-50', 'text-slate-800');
        el.classList.add('border-transparent', 'text-gray-500');
    });
    
    document.getElementById('queue-' + tab).classList.remove('hidden');
    const activeTab = document.getElementById('tab-' + tab);
    activeTab.classList.remove('border-transparent', 'text-gray-500');
    activeTab.classList.add('border-slate-700', 'bg-gray-50', 'text-slate-800');
}

// Modal helpers
function openPatientModal() {
    document.getElementById('patientModal').classList.remove('hidden');
}

function closePatientModal() {
    document.getElementById('patientModal').classList.add('hidden');
}

function openVisitModal() {
    document.getElementById('visitModal').classList.remove('hidden');
    // Focus search input when modal opens
    setTimeout(() => document.getElementById('patientSearchInput').focus(), 100);
}

function closeVisitModal() {
    document.getElementById('visitModal').classList.add('hidden');
    clearPatientSelection();
}

// Patient AJAX Search Logic
const searchInput = document.getElementById('patientSearchInput');
const resultsDropdown = document.getElementById('patientSearchResults');
const resultsList = document.getElementById('resultsList');
const searchLoading = document.getElementById('searchLoading');
const searchPlaceholder = document.getElementById('searchPlaceholder');
const noResults = document.getElementById('noResults');
const selectedPatientId = document.getElementById('selectedPatientId');
const selectedPatientIndicator = document.getElementById('selectedPatientIndicator');
const clearPatientBtn = document.getElementById('clearPatientBtn');
const indicatorInitial = document.getElementById('indicatorInitial');
const indicatorName = document.getElementById('indicatorName');
const indicatorMRN = document.getElementById('indicatorMRN');

let searchTimeout = null;

function clearPatientSelection() {
    selectedPatientId.value = '';
    searchInput.value = '';
    searchInput.classList.remove('hidden');
    selectedPatientIndicator.classList.add('hidden');
    clearPatientBtn.classList.add('hidden');
    resultsDropdown.classList.add('hidden');
}

clearPatientBtn.addEventListener('click', clearPatientSelection);

searchInput.addEventListener('input', function(e) {
    const query = e.target.value.trim();
    
    if (query.length === 0) {
        resultsDropdown.classList.add('hidden');
        return;
    }

    resultsDropdown.classList.remove('hidden');
    resultsList.innerHTML = '';
    searchPlaceholder.classList.add('hidden');
    searchLoading.classList.remove('hidden');
    noResults.classList.add('hidden');

    if (searchTimeout) clearTimeout(searchTimeout);

    searchTimeout = setTimeout(() => {
        fetch(`{{ route('patients.ajax-search') }}?q=${encodeURIComponent(query)}`)
            .then(response => response.json())
            .then(data => {
                searchLoading.classList.add('hidden');
                
                if (data.length === 0) {
                    noResults.classList.remove('hidden');
                    return;
                }

                data.forEach(patient => {
                    const row = document.createElement('button');
                    row.type = 'button';
                    row.className = 'w-full text-left p-3 hover:bg-gray-50 transition-colors flex items-center justify-between group';
                    row.innerHTML = `
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center text-slate-600 font-bold text-xs group-hover:bg-slate-700 group-hover:text-white transition-colors">
                                ${patient.text.charAt(0)}
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-gray-900">${patient.text}</p>
                                <p class="text-[10px] text-gray-500">${patient.phone || 'No phone'} • ${patient.gender} • ${patient.age} yrs</p>
                            </div>
                        </div>
                        <i class="hgi-stroke hgi-tick-02 text-base text-slate-500 opacity-0 group-hover:opacity-100 transition-opacity"></i>
                    `;
                    
                    row.addEventListener('click', () => {
                        selectPatient(patient);
                    });
                    
                    resultsList.appendChild(row);
                });
            })
            .catch(error => {
                console.error('Search error:', error);
                searchLoading.classList.add('hidden');
            });
    }, 300);
});

function selectPatient(patient) {
    selectedPatientId.value = patient.id;
    indicatorName.textContent = patient.text.split(' (')[0];
    indicatorMRN.textContent = `(${patient.text.split(' (')[1]}`;
    indicatorInitial.textContent = patient.text.charAt(0);
    
    searchInput.classList.add('hidden');
    selectedPatientIndicator.classList.remove('hidden');
    clearPatientBtn.classList.remove('hidden');
    resultsDropdown.classList.add('hidden');
}

// Close dropdown on click outside
document.addEventListener('click', function(e) {
    if (!document.getElementById('patientSearchContainer').contains(e.target)) {
        resultsDropdown.classList.add('hidden');
    }
});

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
                    <div id="selectedPatientIndicator" class="hidden absolute inset-y-0 left-0 right-10 flex items-center pl-3 bg-gray-50 rounded-lg pointer-events-none">
                        <div class="flex items-center gap-2">
                            <div class="w-6 h-6 rounded-full bg-slate-700 flex items-center justify-center text-white text-[10px] font-bold" id="indicatorInitial"></div>
                            <span class="text-sm font-medium text-gray-900" id="indicatorName"></span>
                            <span class="text-xs text-slate-500" id="indicatorMRN"></span>
                        </div>
                    </div>
                    
                    <button type="button" id="clearPatientBtn" class="hidden absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600">
                        <i class="hgi-stroke hgi-cancel-01 text-xl"></i>
                    </button>
                </div>
                
                <script>
function showTab(tab) {
    document.querySelectorAll('.queue-content').forEach(el => el.classList.add('hidden'));
    document.querySelectorAll('.queue-tab').forEach(el => {
        el.classList.remove('border-slate-700', 'bg-gray-50', 'text-slate-800');
        el.classList.add('border-transparent', 'text-gray-500');
    });
    
    document.getElementById('queue-' + tab).classList.remove('hidden');
    const activeTab = document.getElementById('tab-' + tab);
    activeTab.classList.remove('border-transparent', 'text-gray-500');
    activeTab.classList.add('border-slate-700', 'bg-gray-50', 'text-slate-800');
}

// Modal helpers
function openPatientModal() {
    document.getElementById('patientModal').classList.remove('hidden');
}

function closePatientModal() {
    document.getElementById('patientModal').classList.add('hidden');
}

function openVisitModal() {
    document.getElementById('visitModal').classList.remove('hidden');
    // Focus search input when modal opens
    setTimeout(() => document.getElementById('patientSearchInput').focus(), 100);
}

function closeVisitModal() {
    document.getElementById('visitModal').classList.add('hidden');
    clearPatientSelection();
}

// Patient AJAX Search Logic
const searchInput = document.getElementById('patientSearchInput');
const resultsDropdown = document.getElementById('patientSearchResults');
const resultsList = document.getElementById('resultsList');
const searchLoading = document.getElementById('searchLoading');
const searchPlaceholder = document.getElementById('searchPlaceholder');
const noResults = document.getElementById('noResults');
const selectedPatientId = document.getElementById('selectedPatientId');
const selectedPatientIndicator = document.getElementById('selectedPatientIndicator');
const clearPatientBtn = document.getElementById('clearPatientBtn');
const indicatorInitial = document.getElementById('indicatorInitial');
const indicatorName = document.getElementById('indicatorName');
const indicatorMRN = document.getElementById('indicatorMRN');

let searchTimeout = null;

function clearPatientSelection() {
    selectedPatientId.value = '';
    searchInput.value = '';
    searchInput.classList.remove('hidden');
    selectedPatientIndicator.classList.add('hidden');
    clearPatientBtn.classList.add('hidden');
    resultsDropdown.classList.add('hidden');
}

clearPatientBtn.addEventListener('click', clearPatientSelection);

searchInput.addEventListener('input', function(e) {
    const query = e.target.value.trim();
    
    if (query.length === 0) {
        resultsDropdown.classList.add('hidden');
        return;
    }

    resultsDropdown.classList.remove('hidden');
    resultsList.innerHTML = '';
    searchPlaceholder.classList.add('hidden');
    searchLoading.classList.remove('hidden');
    noResults.classList.add('hidden');

    if (searchTimeout) clearTimeout(searchTimeout);

    searchTimeout = setTimeout(() => {
        fetch(`{{ route('patients.ajax-search') }}?q=${encodeURIComponent(query)}`)
            .then(response => response.json())
            .then(data => {
                searchLoading.classList.add('hidden');
                
                if (data.length === 0) {
                    noResults.classList.remove('hidden');
                    return;
                }

                data.forEach(patient => {
                    const row = document.createElement('button');
                    row.type = 'button';
                    row.className = 'w-full text-left p-3 hover:bg-gray-50 transition-colors flex items-center justify-between group';
                    row.innerHTML = `
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center text-slate-600 font-bold text-xs group-hover:bg-slate-700 group-hover:text-white transition-colors">
                                ${patient.text.charAt(0)}
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-gray-900">${patient.text}</p>
                                <p class="text-[10px] text-gray-500">${patient.phone || 'No phone'} • ${patient.gender} • ${patient.age} yrs</p>
                            </div>
                        </div>
                        <i class="hgi-stroke hgi-tick-02 text-base text-slate-500 opacity-0 group-hover:opacity-100 transition-opacity"></i>
                    `;
                    
                    row.addEventListener('click', () => {
                        selectPatient(patient);
                    });
                    
                    resultsList.appendChild(row);
                });
            })
            .catch(error => {
                console.error('Search error:', error);
                searchLoading.classList.add('hidden');
            });
    }, 300);
});

function selectPatient(patient) {
    selectedPatientId.value = patient.id;
    indicatorName.textContent = patient.text.split(' (')[0];
    indicatorMRN.textContent = `(${patient.text.split(' (')[1]}`;
    indicatorInitial.textContent = patient.text.charAt(0);
    
    searchInput.classList.add('hidden');
    selectedPatientIndicator.classList.remove('hidden');
    clearPatientBtn.classList.remove('hidden');
    resultsDropdown.classList.add('hidden');
}

// Close dropdown on click outside
document.addEventListener('click', function(e) {
    if (!document.getElementById('patientSearchContainer').contains(e.target)) {
        resultsDropdown.classList.add('hidden');
    }
});

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
                <div id="patientSearchResults" class="hidden absolute z-[60] left-0 right-0 mt-1 bg-white border border-gray-200 rounded-xl shadow-xl max-h-64 overflow-y-auto overflow-x-hidden animate-fade">
                    <div class="p-4 text-center text-gray-500 text-sm" id="searchPlaceholder">Start typing to search...</div>
                    <div class="p-4 text-center text-gray-500 text-sm hidden" id="searchLoading">Searching...</div>
                    <div class="p-4 text-center text-gray-500 text-sm hidden" id="noResults">No patients found</div>
                    <div class="divide-y divide-gray-50" id="resultsList"></div>
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Assign Doctor (Optional)</label>
                <x-ui.select name="doctor_id" class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-lg text-gray-900 focus:outline-none focus:ring-2 focus:ring-slate-300 transition-all">
                    <option value="">No Doctor (Register Only)</option>
                    @foreach($doctors as $doctor)
                        <option value="{{ $doctor->id }}">{{ $doctor->name }}</option>
                    @endforeach
                </x-ui.select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Visit Type</label>
                <x-ui.select name="type" required class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-lg text-gray-900 focus:outline-none focus:ring-2 focus:ring-slate-300 transition-all">
                    <option value="outpatient">Outpatient</option>
                    <option value="emergency">Emergency</option>
                    <option value="followup">Follow-up</option>
                </x-ui.select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Chief Complaint</label>
                <x-ui.textarea name="chief_complaint" rows="2" class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-lg text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-slate-300 transition-all resize-none" placeholder="Reason for visit"></x-ui.textarea>
            </div>
            <div class="flex gap-3 pt-4">
                <button type="button" onclick="closeVisitModal()" class="flex-1 px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg font-medium transition-all border border-gray-200">
                    Cancel
                </button>
                <button type="submit" class="flex-1 px-4 py-2.5 bg-slate-800 hover:bg-slate-700 text-white rounded font-medium transition-colors">
                    Create Visit
                </button>
            </div>
        </form>
    </div>
</div>

 <script>
function showTab(tab) {
    document.querySelectorAll('.queue-content').forEach(el => el.classList.add('hidden'));
    document.querySelectorAll('.queue-tab').forEach(el => {
        el.classList.remove('border-slate-700', 'bg-gray-50', 'text-slate-800');
        el.classList.add('border-transparent', 'text-gray-500');
    });
    
    document.getElementById('queue-' + tab).classList.remove('hidden');
    const activeTab = document.getElementById('tab-' + tab);
    activeTab.classList.remove('border-transparent', 'text-gray-500');
    activeTab.classList.add('border-slate-700', 'bg-gray-50', 'text-slate-800');
}

// Modal helpers
function openPatientModal() {
    document.getElementById('patientModal').classList.remove('hidden');
}

function closePatientModal() {
    document.getElementById('patientModal').classList.add('hidden');
}

function openVisitModal() {
    document.getElementById('visitModal').classList.remove('hidden');
    // Focus search input when modal opens
    setTimeout(() => document.getElementById('patientSearchInput').focus(), 100);
}

function closeVisitModal() {
    document.getElementById('visitModal').classList.add('hidden');
    clearPatientSelection();
}

// Patient AJAX Search Logic
const searchInput = document.getElementById('patientSearchInput');
const resultsDropdown = document.getElementById('patientSearchResults');
const resultsList = document.getElementById('resultsList');
const searchLoading = document.getElementById('searchLoading');
const searchPlaceholder = document.getElementById('searchPlaceholder');
const noResults = document.getElementById('noResults');
const selectedPatientId = document.getElementById('selectedPatientId');
const selectedPatientIndicator = document.getElementById('selectedPatientIndicator');
const clearPatientBtn = document.getElementById('clearPatientBtn');
const indicatorInitial = document.getElementById('indicatorInitial');
const indicatorName = document.getElementById('indicatorName');
const indicatorMRN = document.getElementById('indicatorMRN');

let searchTimeout = null;

function clearPatientSelection() {
    selectedPatientId.value = '';
    searchInput.value = '';
    searchInput.classList.remove('hidden');
    selectedPatientIndicator.classList.add('hidden');
    clearPatientBtn.classList.add('hidden');
    resultsDropdown.classList.add('hidden');
}

clearPatientBtn.addEventListener('click', clearPatientSelection);

searchInput.addEventListener('input', function(e) {
    const query = e.target.value.trim();
    
    if (query.length === 0) {
        resultsDropdown.classList.add('hidden');
        return;
    }

    resultsDropdown.classList.remove('hidden');
    resultsList.innerHTML = '';
    searchPlaceholder.classList.add('hidden');
    searchLoading.classList.remove('hidden');
    noResults.classList.add('hidden');

    if (searchTimeout) clearTimeout(searchTimeout);

    searchTimeout = setTimeout(() => {
        fetch(`{{ route('patients.ajax-search') }}?q=${encodeURIComponent(query)}`)
            .then(response => response.json())
            .then(data => {
                searchLoading.classList.add('hidden');
                
                if (data.length === 0) {
                    noResults.classList.remove('hidden');
                    return;
                }

                data.forEach(patient => {
                    const row = document.createElement('button');
                    row.type = 'button';
                    row.className = 'w-full text-left p-3 hover:bg-gray-50 transition-colors flex items-center justify-between group';
                    row.innerHTML = `
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center text-slate-600 font-bold text-xs group-hover:bg-slate-700 group-hover:text-white transition-colors">
                                ${patient.text.charAt(0)}
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-gray-900">${patient.text}</p>
                                <p class="text-[10px] text-gray-500">${patient.phone || 'No phone'} • ${patient.gender} • ${patient.age} yrs</p>
                            </div>
                        </div>
                        <i class="hgi-stroke hgi-tick-02 text-base text-slate-500 opacity-0 group-hover:opacity-100 transition-opacity"></i>
                    `;
                    
                    row.addEventListener('click', () => {
                        selectPatient(patient);
                    });
                    
                    resultsList.appendChild(row);
                });
            })
            .catch(error => {
                console.error('Search error:', error);
                searchLoading.classList.add('hidden');
            });
    }, 300);
});

function selectPatient(patient) {
    selectedPatientId.value = patient.id;
    indicatorName.textContent = patient.text.split(' (')[0];
    indicatorMRN.textContent = `(${patient.text.split(' (')[1]}`;
    indicatorInitial.textContent = patient.text.charAt(0);
    
    searchInput.classList.add('hidden');
    selectedPatientIndicator.classList.remove('hidden');
    clearPatientBtn.classList.remove('hidden');
    resultsDropdown.classList.add('hidden');
}

// Close dropdown on click outside
document.addEventListener('click', function(e) {
    if (!document.getElementById('patientSearchContainer').contains(e.target)) {
        resultsDropdown.classList.add('hidden');
    }
});

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
<div id="assignDoctorModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white border border-gray-200 rounded-2xl w-full max-w-md shadow-2xl">
        <div class="p-6 border-b border-gray-100">
            <div class="flex items-center justify-between">
                <h3 class="text-xl font-bold text-gray-900">Assign Doctor</h3>
                <button onclick="closeAssignDoctorModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <i class="hgi-stroke hgi-cancel-01 text-2xl"></i>
                </button>
            </div>
        </div>
        <form id="assignDoctorForm" action="" method="POST" class="p-6 space-y-4">
            @csrf
            <input type="hidden" name="doctor_id" id="assignDoctorId">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Select Doctor</label>
                <x-ui.select name="doctor_id" required class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-lg text-gray-900 focus:outline-none focus:ring-2 focus:ring-slate-300 transition-all">
                    <option value="">Select Doctor</option>
                    @foreach($doctors as $doctor)
                        <option value="{{ $doctor->id }}">{{ $doctor->name }}</option>
                    @endforeach
                </x-ui.select>
            </div>
            <div class="flex gap-3 pt-4">
                <button type="button" onclick="closeAssignDoctorModal()" class="flex-1 px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg font-medium transition-all border border-gray-200">
                    Cancel
                </button>
                <button type="submit" class="flex-1 px-4 py-2.5 bg-slate-800 hover:bg-slate-700 text-white rounded font-medium transition-colors">
                    Assign Doctor
                </button>
            </div>
        </form>
    </div>
</div>

 <script>
function showTab(tab) {
    document.querySelectorAll('.queue-content').forEach(el => el.classList.add('hidden'));
    document.querySelectorAll('.queue-tab').forEach(el => {
        el.classList.remove('border-slate-700', 'bg-gray-50', 'text-slate-800');
        el.classList.add('border-transparent', 'text-gray-500');
    });
    
    document.getElementById('queue-' + tab).classList.remove('hidden');
    const activeTab = document.getElementById('tab-' + tab);
    activeTab.classList.remove('border-transparent', 'text-gray-500');
    activeTab.classList.add('border-slate-700', 'bg-gray-50', 'text-slate-800');
}

// Modal helpers
function openPatientModal() {
    document.getElementById('patientModal').classList.remove('hidden');
}

function closePatientModal() {
    document.getElementById('patientModal').classList.add('hidden');
}

function openVisitModal() {
    document.getElementById('visitModal').classList.remove('hidden');
    // Focus search input when modal opens
    setTimeout(() => document.getElementById('patientSearchInput').focus(), 100);
}

function closeVisitModal() {
    document.getElementById('visitModal').classList.add('hidden');
    clearPatientSelection();
}

// Patient AJAX Search Logic
const searchInput = document.getElementById('patientSearchInput');
const resultsDropdown = document.getElementById('patientSearchResults');
const resultsList = document.getElementById('resultsList');
const searchLoading = document.getElementById('searchLoading');
const searchPlaceholder = document.getElementById('searchPlaceholder');
const noResults = document.getElementById('noResults');
const selectedPatientId = document.getElementById('selectedPatientId');
const selectedPatientIndicator = document.getElementById('selectedPatientIndicator');
const clearPatientBtn = document.getElementById('clearPatientBtn');
const indicatorInitial = document.getElementById('indicatorInitial');
const indicatorName = document.getElementById('indicatorName');
const indicatorMRN = document.getElementById('indicatorMRN');

let searchTimeout = null;

function clearPatientSelection() {
    selectedPatientId.value = '';
    searchInput.value = '';
    searchInput.classList.remove('hidden');
    selectedPatientIndicator.classList.add('hidden');
    clearPatientBtn.classList.add('hidden');
    resultsDropdown.classList.add('hidden');
}

clearPatientBtn.addEventListener('click', clearPatientSelection);

searchInput.addEventListener('input', function(e) {
    const query = e.target.value.trim();
    
    if (query.length === 0) {
        resultsDropdown.classList.add('hidden');
        return;
    }

    resultsDropdown.classList.remove('hidden');
    resultsList.innerHTML = '';
    searchPlaceholder.classList.add('hidden');
    searchLoading.classList.remove('hidden');
    noResults.classList.add('hidden');

    if (searchTimeout) clearTimeout(searchTimeout);

    searchTimeout = setTimeout(() => {
        fetch(`{{ route('patients.ajax-search') }}?q=${encodeURIComponent(query)}`)
            .then(response => response.json())
            .then(data => {
                searchLoading.classList.add('hidden');
                
                if (data.length === 0) {
                    noResults.classList.remove('hidden');
                    return;
                }

                data.forEach(patient => {
                    const row = document.createElement('button');
                    row.type = 'button';
                    row.className = 'w-full text-left p-3 hover:bg-gray-50 transition-colors flex items-center justify-between group';
                    row.innerHTML = `
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center text-slate-600 font-bold text-xs group-hover:bg-slate-700 group-hover:text-white transition-colors">
                                ${patient.text.charAt(0)}
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-gray-900">${patient.text}</p>
                                <p class="text-[10px] text-gray-500">${patient.phone || 'No phone'} • ${patient.gender} • ${patient.age} yrs</p>
                            </div>
                        </div>
                        <i class="hgi-stroke hgi-tick-02 text-base text-slate-500 opacity-0 group-hover:opacity-100 transition-opacity"></i>
                    `;
                    
                    row.addEventListener('click', () => {
                        selectPatient(patient);
                    });
                    
                    resultsList.appendChild(row);
                });
            })
            .catch(error => {
                console.error('Search error:', error);
                searchLoading.classList.add('hidden');
            });
    }, 300);
});

function selectPatient(patient) {
    selectedPatientId.value = patient.id;
    indicatorName.textContent = patient.text.split(' (')[0];
    indicatorMRN.textContent = `(${patient.text.split(' (')[1]}`;
    indicatorInitial.textContent = patient.text.charAt(0);
    
    searchInput.classList.add('hidden');
    selectedPatientIndicator.classList.remove('hidden');
    clearPatientBtn.classList.remove('hidden');
    resultsDropdown.classList.add('hidden');
}

// Close dropdown on click outside
document.addEventListener('click', function(e) {
    if (!document.getElementById('patientSearchContainer').contains(e.target)) {
        resultsDropdown.classList.add('hidden');
    }
});

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
<div id="changeDoctorModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white border border-gray-200 rounded-2xl w-full max-w-md shadow-2xl">
        <div class="p-6 border-b border-gray-100">
            <div class="flex items-center justify-between">
                <h3 class="text-xl font-bold text-gray-900">Change Doctor</h3>
                <button onclick="closeChangeDoctorModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <i class="hgi-stroke hgi-cancel-01 text-2xl"></i>
                </button>
            </div>
        </div>
        <form id="changeDoctorForm" action="" method="POST" class="p-6 space-y-4">
            @csrf
            <input type="hidden" name="doctor_id" id="changeDoctorId">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Select New Doctor</label>
                <x-ui.select name="doctor_id" required class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-lg text-gray-900 focus:outline-none focus:ring-2 focus:ring-slate-300 transition-all">
                    <option value="">Select Doctor</option>
                    @foreach($doctors as $doctor)
                        <option value="{{ $doctor->id }}">{{ $doctor->name }}</option>
                    @endforeach
                </x-ui.select>
            </div>
            <div class="flex gap-3 pt-4">
                <button type="button" onclick="closeChangeDoctorModal()" class="flex-1 px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg font-medium transition-all border border-gray-200">
                    Cancel
                </button>
                <button type="submit" class="flex-1 px-4 py-2.5 bg-slate-800 hover:bg-slate-700 text-white rounded font-medium transition-colors">
                    Change Doctor
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function showTab(tab) {
    document.querySelectorAll('.queue-content').forEach(el => el.classList.add('hidden'));
    document.querySelectorAll('.queue-tab').forEach(el => {
        el.classList.remove('border-slate-700', 'bg-gray-50', 'text-slate-800');
        el.classList.add('border-transparent', 'text-gray-500');
    });
    
    document.getElementById('queue-' + tab).classList.remove('hidden');
    const activeTab = document.getElementById('tab-' + tab);
    activeTab.classList.remove('border-transparent', 'text-gray-500');
    activeTab.classList.add('border-slate-700', 'bg-gray-50', 'text-slate-800');
}

// Modal helpers
function openPatientModal() {
    document.getElementById('patientModal').classList.remove('hidden');
}

function closePatientModal() {
    document.getElementById('patientModal').classList.add('hidden');
}

function openVisitModal() {
    document.getElementById('visitModal').classList.remove('hidden');
    // Focus search input when modal opens
    setTimeout(() => document.getElementById('patientSearchInput').focus(), 100);
}

function closeVisitModal() {
    document.getElementById('visitModal').classList.add('hidden');
    clearPatientSelection();
}

// Patient AJAX Search Logic
const searchInput = document.getElementById('patientSearchInput');
const resultsDropdown = document.getElementById('patientSearchResults');
const resultsList = document.getElementById('resultsList');
const searchLoading = document.getElementById('searchLoading');
const searchPlaceholder = document.getElementById('searchPlaceholder');
const noResults = document.getElementById('noResults');
const selectedPatientId = document.getElementById('selectedPatientId');
const selectedPatientIndicator = document.getElementById('selectedPatientIndicator');
const clearPatientBtn = document.getElementById('clearPatientBtn');
const indicatorInitial = document.getElementById('indicatorInitial');
const indicatorName = document.getElementById('indicatorName');
const indicatorMRN = document.getElementById('indicatorMRN');

let searchTimeout = null;

function clearPatientSelection() {
    selectedPatientId.value = '';
    searchInput.value = '';
    searchInput.classList.remove('hidden');
    selectedPatientIndicator.classList.add('hidden');
    clearPatientBtn.classList.add('hidden');
    resultsDropdown.classList.add('hidden');
}

clearPatientBtn.addEventListener('click', clearPatientSelection);

searchInput.addEventListener('input', function(e) {
    const query = e.target.value.trim();
    
    if (query.length === 0) {
        resultsDropdown.classList.add('hidden');
        return;
    }

    resultsDropdown.classList.remove('hidden');
    resultsList.innerHTML = '';
    searchPlaceholder.classList.add('hidden');
    searchLoading.classList.remove('hidden');
    noResults.classList.add('hidden');

    if (searchTimeout) clearTimeout(searchTimeout);

    searchTimeout = setTimeout(() => {
        fetch(`{{ route('patients.ajax-search') }}?q=${encodeURIComponent(query)}`)
            .then(response => response.json())
            .then(data => {
                searchLoading.classList.add('hidden');
                
                if (data.length === 0) {
                    noResults.classList.remove('hidden');
                    return;
                }

                data.forEach(patient => {
                    const row = document.createElement('button');
                    row.type = 'button';
                    row.className = 'w-full text-left p-3 hover:bg-gray-50 transition-colors flex items-center justify-between group';
                    row.innerHTML = `
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center text-slate-600 font-bold text-xs group-hover:bg-slate-700 group-hover:text-white transition-colors">
                                ${patient.text.charAt(0)}
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-gray-900">${patient.text}</p>
                                <p class="text-[10px] text-gray-500">${patient.phone || 'No phone'} • ${patient.gender} • ${patient.age} yrs</p>
                            </div>
                        </div>
                        <i class="hgi-stroke hgi-tick-02 text-base text-slate-500 opacity-0 group-hover:opacity-100 transition-opacity"></i>
                    `;
                    
                    row.addEventListener('click', () => {
                        selectPatient(patient);
                    });
                    
                    resultsList.appendChild(row);
                });
            })
            .catch(error => {
                console.error('Search error:', error);
                searchLoading.classList.add('hidden');
            });
    }, 300);
});

function selectPatient(patient) {
    selectedPatientId.value = patient.id;
    indicatorName.textContent = patient.text.split(' (')[0];
    indicatorMRN.textContent = `(${patient.text.split(' (')[1]}`;
    indicatorInitial.textContent = patient.text.charAt(0);
    
    searchInput.classList.add('hidden');
    selectedPatientIndicator.classList.remove('hidden');
    clearPatientBtn.classList.remove('hidden');
    resultsDropdown.classList.add('hidden');
}

// Close dropdown on click outside
document.addEventListener('click', function(e) {
    if (!document.getElementById('patientSearchContainer').contains(e.target)) {
        resultsDropdown.classList.add('hidden');
    }
});

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
