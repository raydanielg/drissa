@extends('layouts.dashboard')

@section('title', 'Patient Queue - ' . config('app.name', 'Laravel'))
@section('page_title', 'Patient Queue - Admin View')

@section('content')
<div class="space-y-5">
    @if (session('status'))
        <div class="p-3 rounded-md bg-white border-l-2 border-emerald-500 text-emerald-700 text-xs">{{ session('status') }}</div>
    @endif

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
        { name: 'Registration', icon: 'hgi-checkmark-circle-02', completed: true },
        { name: 'Payment', icon: 'hgi-money-03', completed: status.includes('waiting_for_doctor') || status.includes('with') || status.includes('lab') || status.includes('pharmacy') || status.includes('completed') },
        { name: 'Waiting for Doctor', icon: 'hgi-clock-01', completed: status.includes('waiting_for_doctor') || status.includes('with') || status.includes('lab') || status.includes('pharmacy') || status.includes('completed') },
        { name: 'With Doctor', icon: 'hgi-checkmark-circle-02', completed: status.includes('with') || status.includes('lab') || status.includes('pharmacy') || status.includes('completed') },
        { name: 'Lab Tests', icon: 'hgi-test-tube-01', completed: status.includes('lab') || status.includes('pharmacy') || status.includes('completed') },
        { name: 'Pharmacy', icon: 'hgi-test-tube-01', completed: status.includes('pharmacy') || status.includes('completed') },
        { name: 'Completed', icon: 'hgi-checkmark-circle-02', completed: status.includes('completed') },
    ];
    
    // Find current step index
    const currentStepIndex = steps.findIndex(step => {
        if (status === 'registered') return step.name === 'Payment';
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
        
        const bgClass = isCompleted ? 'bg-slate-700' : (isCurrent ? 'bg-emerald-600' : 'bg-gray-200');
        const textClass = isCompleted ? 'text-gray-900' : (isCurrent ? 'text-emerald-600' : 'text-gray-400');
        const iconColor = isCompleted ? 'text-white' : (isCurrent ? 'text-white' : 'text-gray-400');
        
        // Generate action button based on step
        let actionButton = '';
        if (isCurrent) {
            if (step.name === 'Waiting for Doctor') {
                actionButton = `<button onclick="callToDoctor(${visitId})" class="mt-2 flex items-center gap-1.5 px-3 py-1.5 bg-slate-800 text-white text-xs font-medium rounded hover:bg-slate-700 transition-colors">
                    <i class="hgi-stroke hgi-video-01 text-sm"></i>
                    Call to Doctor
                </button>`;
            } else if (step.name === 'With Doctor') {
                actionButton = `<div class="mt-2 flex gap-2">
                    <button onclick="sendToLab(${visitId})" class="flex items-center gap-1.5 px-3 py-1.5 bg-slate-800 text-white text-xs font-medium rounded hover:bg-slate-700 transition-colors">
                        <i class="hgi-stroke hgi-test-tube-01 text-sm"></i>
                        Send to Lab
                    </button>
                    <button onclick="sendToPharmacy(${visitId})" class="flex items-center gap-1.5 px-3 py-1.5 bg-slate-800 text-white text-xs font-medium rounded hover:bg-slate-700 transition-colors">
                        <i class="hgi-stroke hgi-test-tube-01 text-sm"></i>
                        Send to Pharmacy
                    </button>
                </div>`;
            } else if (step.name === 'Lab Tests') {
                actionButton = `<button onclick="completeLab(${visitId})" class="mt-2 flex items-center gap-1.5 px-3 py-1.5 bg-slate-800 text-white text-xs font-medium rounded hover:bg-slate-700 transition-colors">
                    <i class="hgi-stroke hgi-checkmark-circle-02 text-sm"></i>
                    Lab Completed
                </button>`;
            } else if (step.name === 'Pharmacy') {
                actionButton = `<button onclick="completeVisit(${visitId})" class="mt-2 flex items-center gap-1.5 px-3 py-1.5 bg-slate-800 text-white text-xs font-medium rounded hover:bg-slate-700 transition-colors">
                    <i class="hgi-stroke hgi-checkmark-circle-02 text-sm"></i>
                    Complete Visit
                </button>`;
            }
        }
        
        stepperHTML += `
            <div class="flex items-start gap-4">
                <div class="flex flex-col items-center">
                    <div class="w-10 h-10 rounded-full ${bgClass} flex items-center justify-center">
                        <i class="hgi-stroke ${step.icon} text-xl ${iconColor}"></i>
                    </div>
                    ${index < steps.length - 1 ? `<div class="w-0.5 h-12 ${isCompleted ? 'bg-slate-700' : 'bg-gray-200'} mt-2"></div>` : ''}
                </div>
                <div class="flex-1 pb-4">
                    <div class="flex items-center gap-2">
                        <h4 class="text-sm font-semibold ${textClass}">${step.name}</h4>
                        ${isCurrent ? '<span class="inline-flex items-center gap-1 text-[10px] font-medium text-gray-600"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>Current</span>' : ''}
                        ${isCompleted ? '<span class="inline-flex items-center gap-1 text-[10px] font-medium text-gray-600"><span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>Done</span>' : ''}
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
        confirmButtonColor: '#0f172a',
        cancelButtonColor: '#6b7280',
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
        confirmButtonColor: '#0f172a',
        cancelButtonColor: '#6b7280',
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
        confirmButtonColor: '#0f172a',
        cancelButtonColor: '#6b7280',
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
        confirmButtonColor: '#0f172a',
        cancelButtonColor: '#6b7280',
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
        confirmButtonColor: '#0f172a',
        cancelButtonColor: '#6b7280',
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
        confirmButtonColor: '#0f172a',
        cancelButtonColor: '#6b7280',
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
    <div class="bg-slate-800 rounded-md p-6 text-white">
        <div class="flex flex-wrap items-end justify-between gap-3">
            <div>
                <p class="text-[10px] uppercase tracking-wider text-slate-400 mb-1">Admin</p>
                <h1 class="text-xl font-bold">Patient Queue</h1>
                <p class="text-xs text-slate-400 mt-1">Today's patients — {{ today()->format('D, M j') }}</p>
            </div>
            <x-ui.select id="doctorFilter" onchange="filterByDoctor()" class="text-xs border border-slate-600 bg-slate-700 text-white rounded px-3 py-1.5 focus:ring-2 focus:ring-slate-400">
                <option value="">All Doctors</option>
                @foreach($doctors as $d)
                    <option value="{{ $d->id }}">{{ $d->name }}</option>
                @endforeach
            </x-ui.select>
        </div>
    </div>

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
        { name: 'Registration', icon: 'hgi-checkmark-circle-02', completed: true },
        { name: 'Payment', icon: 'hgi-money-03', completed: status.includes('waiting_for_doctor') || status.includes('with') || status.includes('lab') || status.includes('pharmacy') || status.includes('completed') },
        { name: 'Waiting for Doctor', icon: 'hgi-clock-01', completed: status.includes('waiting_for_doctor') || status.includes('with') || status.includes('lab') || status.includes('pharmacy') || status.includes('completed') },
        { name: 'With Doctor', icon: 'hgi-checkmark-circle-02', completed: status.includes('with') || status.includes('lab') || status.includes('pharmacy') || status.includes('completed') },
        { name: 'Lab Tests', icon: 'hgi-test-tube-01', completed: status.includes('lab') || status.includes('pharmacy') || status.includes('completed') },
        { name: 'Pharmacy', icon: 'hgi-test-tube-01', completed: status.includes('pharmacy') || status.includes('completed') },
        { name: 'Completed', icon: 'hgi-checkmark-circle-02', completed: status.includes('completed') },
    ];
    
    // Find current step index
    const currentStepIndex = steps.findIndex(step => {
        if (status === 'registered') return step.name === 'Payment';
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
        
        const bgClass = isCompleted ? 'bg-slate-700' : (isCurrent ? 'bg-emerald-600' : 'bg-gray-200');
        const textClass = isCompleted ? 'text-gray-900' : (isCurrent ? 'text-emerald-600' : 'text-gray-400');
        const iconColor = isCompleted ? 'text-white' : (isCurrent ? 'text-white' : 'text-gray-400');
        
        // Generate action button based on step
        let actionButton = '';
        if (isCurrent) {
            if (step.name === 'Waiting for Doctor') {
                actionButton = `<button onclick="callToDoctor(${visitId})" class="mt-2 flex items-center gap-1.5 px-3 py-1.5 bg-slate-800 text-white text-xs font-medium rounded hover:bg-slate-700 transition-colors">
                    <i class="hgi-stroke hgi-video-01 text-sm"></i>
                    Call to Doctor
                </button>`;
            } else if (step.name === 'With Doctor') {
                actionButton = `<div class="mt-2 flex gap-2">
                    <button onclick="sendToLab(${visitId})" class="flex items-center gap-1.5 px-3 py-1.5 bg-slate-800 text-white text-xs font-medium rounded hover:bg-slate-700 transition-colors">
                        <i class="hgi-stroke hgi-test-tube-01 text-sm"></i>
                        Send to Lab
                    </button>
                    <button onclick="sendToPharmacy(${visitId})" class="flex items-center gap-1.5 px-3 py-1.5 bg-slate-800 text-white text-xs font-medium rounded hover:bg-slate-700 transition-colors">
                        <i class="hgi-stroke hgi-test-tube-01 text-sm"></i>
                        Send to Pharmacy
                    </button>
                </div>`;
            } else if (step.name === 'Lab Tests') {
                actionButton = `<button onclick="completeLab(${visitId})" class="mt-2 flex items-center gap-1.5 px-3 py-1.5 bg-slate-800 text-white text-xs font-medium rounded hover:bg-slate-700 transition-colors">
                    <i class="hgi-stroke hgi-checkmark-circle-02 text-sm"></i>
                    Lab Completed
                </button>`;
            } else if (step.name === 'Pharmacy') {
                actionButton = `<button onclick="completeVisit(${visitId})" class="mt-2 flex items-center gap-1.5 px-3 py-1.5 bg-slate-800 text-white text-xs font-medium rounded hover:bg-slate-700 transition-colors">
                    <i class="hgi-stroke hgi-checkmark-circle-02 text-sm"></i>
                    Complete Visit
                </button>`;
            }
        }
        
        stepperHTML += `
            <div class="flex items-start gap-4">
                <div class="flex flex-col items-center">
                    <div class="w-10 h-10 rounded-full ${bgClass} flex items-center justify-center">
                        <i class="hgi-stroke ${step.icon} text-xl ${iconColor}"></i>
                    </div>
                    ${index < steps.length - 1 ? `<div class="w-0.5 h-12 ${isCompleted ? 'bg-slate-700' : 'bg-gray-200'} mt-2"></div>` : ''}
                </div>
                <div class="flex-1 pb-4">
                    <div class="flex items-center gap-2">
                        <h4 class="text-sm font-semibold ${textClass}">${step.name}</h4>
                        ${isCurrent ? '<span class="inline-flex items-center gap-1 text-[10px] font-medium text-gray-600"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>Current</span>' : ''}
                        ${isCompleted ? '<span class="inline-flex items-center gap-1 text-[10px] font-medium text-gray-600"><span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>Done</span>' : ''}
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
        confirmButtonColor: '#0f172a',
        cancelButtonColor: '#6b7280',
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
        confirmButtonColor: '#0f172a',
        cancelButtonColor: '#6b7280',
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
        confirmButtonColor: '#0f172a',
        cancelButtonColor: '#6b7280',
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
        confirmButtonColor: '#0f172a',
        cancelButtonColor: '#6b7280',
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
        confirmButtonColor: '#0f172a',
        cancelButtonColor: '#6b7280',
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
        confirmButtonColor: '#0f172a',
        cancelButtonColor: '#6b7280',
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
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3">
        <div class="bg-white rounded-md border border-gray-200 p-4">
            <div class="flex items-start justify-between">
                <p class="text-[10px] font-medium text-gray-500 uppercase tracking-wide">Total Patients</p>
                <i class="hgi-stroke hgi-user-multiple-02 text-base text-gray-400"></i>
            </div>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['total'] }}</p>
        </div>
        <div class="bg-white rounded-md border border-amber-200 p-4">
            <div class="flex items-start justify-between">
                <p class="text-[10px] font-medium text-gray-500 uppercase tracking-wide">Waiting</p>
                <i class="hgi-stroke hgi-clock-01 text-base text-amber-600"></i>
            </div>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['waiting'] }}</p>
        </div>
        <div class="bg-white rounded-md border border-emerald-200 p-4">
            <div class="flex items-start justify-between">
                <p class="text-[10px] font-medium text-gray-500 uppercase tracking-wide">With Doctor</p>
                <i class="hgi-stroke hgi-checkmark-circle-02 text-base text-emerald-600"></i>
            </div>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['with_doctor'] }}</p>
        </div>
        <div class="bg-white rounded-md border border-gray-200 p-4">
            <div class="flex items-start justify-between">
                <p class="text-[10px] font-medium text-gray-500 uppercase tracking-wide">Lab</p>
                <i class="hgi-stroke hgi-test-tube-01 text-base text-gray-400"></i>
            </div>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['lab'] }}</p>
        </div>
        <div class="bg-white rounded-md border border-gray-200 p-4">
            <div class="flex items-start justify-between">
                <p class="text-[10px] font-medium text-gray-500 uppercase tracking-wide">Pharmacy</p>
                <i class="hgi-stroke hgi-test-tube-01 text-base text-gray-400"></i>
            </div>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['pharmacy'] }}</p>
        </div>
        <div class="bg-white rounded-md border border-red-200 p-4">
            <div class="flex items-start justify-between">
                <p class="text-[10px] font-medium text-gray-500 uppercase tracking-wide">Payment</p>
                <i class="hgi-stroke hgi-money-03 text-base text-red-600"></i>
            </div>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['payment'] }}</p>
        </div>
    </div>

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
        { name: 'Registration', icon: 'hgi-checkmark-circle-02', completed: true },
        { name: 'Payment', icon: 'hgi-money-03', completed: status.includes('waiting_for_doctor') || status.includes('with') || status.includes('lab') || status.includes('pharmacy') || status.includes('completed') },
        { name: 'Waiting for Doctor', icon: 'hgi-clock-01', completed: status.includes('waiting_for_doctor') || status.includes('with') || status.includes('lab') || status.includes('pharmacy') || status.includes('completed') },
        { name: 'With Doctor', icon: 'hgi-checkmark-circle-02', completed: status.includes('with') || status.includes('lab') || status.includes('pharmacy') || status.includes('completed') },
        { name: 'Lab Tests', icon: 'hgi-test-tube-01', completed: status.includes('lab') || status.includes('pharmacy') || status.includes('completed') },
        { name: 'Pharmacy', icon: 'hgi-test-tube-01', completed: status.includes('pharmacy') || status.includes('completed') },
        { name: 'Completed', icon: 'hgi-checkmark-circle-02', completed: status.includes('completed') },
    ];
    
    // Find current step index
    const currentStepIndex = steps.findIndex(step => {
        if (status === 'registered') return step.name === 'Payment';
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
        
        const bgClass = isCompleted ? 'bg-slate-700' : (isCurrent ? 'bg-emerald-600' : 'bg-gray-200');
        const textClass = isCompleted ? 'text-gray-900' : (isCurrent ? 'text-emerald-600' : 'text-gray-400');
        const iconColor = isCompleted ? 'text-white' : (isCurrent ? 'text-white' : 'text-gray-400');
        
        // Generate action button based on step
        let actionButton = '';
        if (isCurrent) {
            if (step.name === 'Waiting for Doctor') {
                actionButton = `<button onclick="callToDoctor(${visitId})" class="mt-2 flex items-center gap-1.5 px-3 py-1.5 bg-slate-800 text-white text-xs font-medium rounded hover:bg-slate-700 transition-colors">
                    <i class="hgi-stroke hgi-video-01 text-sm"></i>
                    Call to Doctor
                </button>`;
            } else if (step.name === 'With Doctor') {
                actionButton = `<div class="mt-2 flex gap-2">
                    <button onclick="sendToLab(${visitId})" class="flex items-center gap-1.5 px-3 py-1.5 bg-slate-800 text-white text-xs font-medium rounded hover:bg-slate-700 transition-colors">
                        <i class="hgi-stroke hgi-test-tube-01 text-sm"></i>
                        Send to Lab
                    </button>
                    <button onclick="sendToPharmacy(${visitId})" class="flex items-center gap-1.5 px-3 py-1.5 bg-slate-800 text-white text-xs font-medium rounded hover:bg-slate-700 transition-colors">
                        <i class="hgi-stroke hgi-test-tube-01 text-sm"></i>
                        Send to Pharmacy
                    </button>
                </div>`;
            } else if (step.name === 'Lab Tests') {
                actionButton = `<button onclick="completeLab(${visitId})" class="mt-2 flex items-center gap-1.5 px-3 py-1.5 bg-slate-800 text-white text-xs font-medium rounded hover:bg-slate-700 transition-colors">
                    <i class="hgi-stroke hgi-checkmark-circle-02 text-sm"></i>
                    Lab Completed
                </button>`;
            } else if (step.name === 'Pharmacy') {
                actionButton = `<button onclick="completeVisit(${visitId})" class="mt-2 flex items-center gap-1.5 px-3 py-1.5 bg-slate-800 text-white text-xs font-medium rounded hover:bg-slate-700 transition-colors">
                    <i class="hgi-stroke hgi-checkmark-circle-02 text-sm"></i>
                    Complete Visit
                </button>`;
            }
        }
        
        stepperHTML += `
            <div class="flex items-start gap-4">
                <div class="flex flex-col items-center">
                    <div class="w-10 h-10 rounded-full ${bgClass} flex items-center justify-center">
                        <i class="hgi-stroke ${step.icon} text-xl ${iconColor}"></i>
                    </div>
                    ${index < steps.length - 1 ? `<div class="w-0.5 h-12 ${isCompleted ? 'bg-slate-700' : 'bg-gray-200'} mt-2"></div>` : ''}
                </div>
                <div class="flex-1 pb-4">
                    <div class="flex items-center gap-2">
                        <h4 class="text-sm font-semibold ${textClass}">${step.name}</h4>
                        ${isCurrent ? '<span class="inline-flex items-center gap-1 text-[10px] font-medium text-gray-600"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>Current</span>' : ''}
                        ${isCompleted ? '<span class="inline-flex items-center gap-1 text-[10px] font-medium text-gray-600"><span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>Done</span>' : ''}
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
        confirmButtonColor: '#0f172a',
        cancelButtonColor: '#6b7280',
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
        confirmButtonColor: '#0f172a',
        cancelButtonColor: '#6b7280',
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
        confirmButtonColor: '#0f172a',
        cancelButtonColor: '#6b7280',
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
        confirmButtonColor: '#0f172a',
        cancelButtonColor: '#6b7280',
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
        confirmButtonColor: '#0f172a',
        cancelButtonColor: '#6b7280',
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
        confirmButtonColor: '#0f172a',
        cancelButtonColor: '#6b7280',
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
    <div class="bg-white rounded-md border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-50 text-[10px] uppercase text-gray-400">
                    <tr>
                        <th class="px-5 py-3">Visit #</th>
                        <th class="px-5 py-3">Patient</th>
                        <th class="px-5 py-3">Doctor</th>
                        <th class="px-5 py-3">Status</th>
                        <th class="px-5 py-3">Registered</th>
                        <th class="px-5 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse ($visits as $visit)
                        <tr class="hover:bg-gray-50/50 transition-colors visit-row" data-doctor-id="{{ $visit->doctor_id ?? '' }}">
                            <td class="px-5 py-3 text-xs font-medium text-gray-700">{{ $visit->visit_number }}</td>
                            <td class="px-5 py-3">
                                <div class="flex items-center gap-2">
                                    <div class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center text-slate-600 text-xs font-bold">
                                        {{ strtoupper(substr($visit->patient->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="text-xs text-gray-900 font-medium">{{ $visit->patient->fullName() }}</div>
                                        <div class="text-[10px] text-gray-400">{{ $visit->patient->mrn }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-3">
                                <div class="flex items-center gap-2">
                                    <span class="text-xs text-gray-700">{{ $visit->doctor?->name ?? 'Unassigned' }}</span>
                                    <button type="button" onclick="openChangeDoctorModal({{ $visit->id }}, '{{ $visit->doctor_id ?? '' }}')" class="text-slate-600 hover:text-slate-800">
                                        <i class="hgi-stroke hgi-arrow-left-right text-base"></i>
                                    </button>
                                </div>
                            </td>
                            <td class="px-5 py-3">
                                @php
                                    $statusDot = match($visit->status) {
                                        'completed' => 'bg-emerald-500',
                                        'waiting_for_doctor', 'with_doctor', 'waiting_for_lab', 'waiting_for_pharmacy' => 'bg-amber-500',
                                        'waiting_for_payment' => 'bg-red-500',
                                        default => 'bg-slate-400',
                                    };
                                @endphp
                                <span class="inline-flex items-center gap-1.5 text-[10px] font-medium capitalize text-gray-600">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $statusDot }}"></span>
                                    {{ str_replace('_', ' ', $visit->status) }}
                                </span>
                            </td>
                            <td class="px-5 py-3 text-gray-500 text-xs">{{ $visit->registered_at->format('H:i') }}</td>
                            <td class="px-5 py-3 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <button type="button" onclick="openStepperModal({{ $visit->id }}, '{{ $visit->patient->fullName() }}', '{{ $visit->status }}')" class="flex items-center gap-1.5 px-3 py-1.5 bg-slate-800 text-white text-xs font-medium rounded hover:bg-slate-700 transition-colors">
                                        <i class="hgi-stroke hgi-clipboard text-sm"></i>
                                        Journey
                                    </button>
                                    <button type="button" onclick="openDischargeModal({{ $visit->id }})" class="flex items-center gap-1.5 px-3 py-1.5 text-red-600 text-xs font-medium rounded hover:bg-red-50 transition-colors">
                                        <i class="hgi-stroke hgi-logout-02 text-sm"></i>
                                        Discharge
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-5 py-10 text-center text-gray-400 text-xs">
                            <div class="flex flex-col items-center gap-2">
                                <i class="hgi-stroke hgi-user-multiple-02 text-4xl text-gray-300"></i>
                                <p>No patients in queue today</p>
                            </div>
                        </td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

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
        { name: 'Registration', icon: 'hgi-checkmark-circle-02', completed: true },
        { name: 'Payment', icon: 'hgi-money-03', completed: status.includes('waiting_for_doctor') || status.includes('with') || status.includes('lab') || status.includes('pharmacy') || status.includes('completed') },
        { name: 'Waiting for Doctor', icon: 'hgi-clock-01', completed: status.includes('waiting_for_doctor') || status.includes('with') || status.includes('lab') || status.includes('pharmacy') || status.includes('completed') },
        { name: 'With Doctor', icon: 'hgi-checkmark-circle-02', completed: status.includes('with') || status.includes('lab') || status.includes('pharmacy') || status.includes('completed') },
        { name: 'Lab Tests', icon: 'hgi-test-tube-01', completed: status.includes('lab') || status.includes('pharmacy') || status.includes('completed') },
        { name: 'Pharmacy', icon: 'hgi-test-tube-01', completed: status.includes('pharmacy') || status.includes('completed') },
        { name: 'Completed', icon: 'hgi-checkmark-circle-02', completed: status.includes('completed') },
    ];
    
    // Find current step index
    const currentStepIndex = steps.findIndex(step => {
        if (status === 'registered') return step.name === 'Payment';
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
        
        const bgClass = isCompleted ? 'bg-slate-700' : (isCurrent ? 'bg-emerald-600' : 'bg-gray-200');
        const textClass = isCompleted ? 'text-gray-900' : (isCurrent ? 'text-emerald-600' : 'text-gray-400');
        const iconColor = isCompleted ? 'text-white' : (isCurrent ? 'text-white' : 'text-gray-400');
        
        // Generate action button based on step
        let actionButton = '';
        if (isCurrent) {
            if (step.name === 'Waiting for Doctor') {
                actionButton = `<button onclick="callToDoctor(${visitId})" class="mt-2 flex items-center gap-1.5 px-3 py-1.5 bg-slate-800 text-white text-xs font-medium rounded hover:bg-slate-700 transition-colors">
                    <i class="hgi-stroke hgi-video-01 text-sm"></i>
                    Call to Doctor
                </button>`;
            } else if (step.name === 'With Doctor') {
                actionButton = `<div class="mt-2 flex gap-2">
                    <button onclick="sendToLab(${visitId})" class="flex items-center gap-1.5 px-3 py-1.5 bg-slate-800 text-white text-xs font-medium rounded hover:bg-slate-700 transition-colors">
                        <i class="hgi-stroke hgi-test-tube-01 text-sm"></i>
                        Send to Lab
                    </button>
                    <button onclick="sendToPharmacy(${visitId})" class="flex items-center gap-1.5 px-3 py-1.5 bg-slate-800 text-white text-xs font-medium rounded hover:bg-slate-700 transition-colors">
                        <i class="hgi-stroke hgi-test-tube-01 text-sm"></i>
                        Send to Pharmacy
                    </button>
                </div>`;
            } else if (step.name === 'Lab Tests') {
                actionButton = `<button onclick="completeLab(${visitId})" class="mt-2 flex items-center gap-1.5 px-3 py-1.5 bg-slate-800 text-white text-xs font-medium rounded hover:bg-slate-700 transition-colors">
                    <i class="hgi-stroke hgi-checkmark-circle-02 text-sm"></i>
                    Lab Completed
                </button>`;
            } else if (step.name === 'Pharmacy') {
                actionButton = `<button onclick="completeVisit(${visitId})" class="mt-2 flex items-center gap-1.5 px-3 py-1.5 bg-slate-800 text-white text-xs font-medium rounded hover:bg-slate-700 transition-colors">
                    <i class="hgi-stroke hgi-checkmark-circle-02 text-sm"></i>
                    Complete Visit
                </button>`;
            }
        }
        
        stepperHTML += `
            <div class="flex items-start gap-4">
                <div class="flex flex-col items-center">
                    <div class="w-10 h-10 rounded-full ${bgClass} flex items-center justify-center">
                        <i class="hgi-stroke ${step.icon} text-xl ${iconColor}"></i>
                    </div>
                    ${index < steps.length - 1 ? `<div class="w-0.5 h-12 ${isCompleted ? 'bg-slate-700' : 'bg-gray-200'} mt-2"></div>` : ''}
                </div>
                <div class="flex-1 pb-4">
                    <div class="flex items-center gap-2">
                        <h4 class="text-sm font-semibold ${textClass}">${step.name}</h4>
                        ${isCurrent ? '<span class="inline-flex items-center gap-1 text-[10px] font-medium text-gray-600"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>Current</span>' : ''}
                        ${isCompleted ? '<span class="inline-flex items-center gap-1 text-[10px] font-medium text-gray-600"><span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>Done</span>' : ''}
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
        confirmButtonColor: '#0f172a',
        cancelButtonColor: '#6b7280',
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
        confirmButtonColor: '#0f172a',
        cancelButtonColor: '#6b7280',
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
        confirmButtonColor: '#0f172a',
        cancelButtonColor: '#6b7280',
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
        confirmButtonColor: '#0f172a',
        cancelButtonColor: '#6b7280',
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
        confirmButtonColor: '#0f172a',
        cancelButtonColor: '#6b7280',
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
        confirmButtonColor: '#0f172a',
        cancelButtonColor: '#6b7280',
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
<div id="changeDoctorModal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm transition-opacity" onclick="closeChangeDoctorModal()"></div>
    <div class="absolute inset-0 flex items-center justify-center p-4">
        <div class="bg-white rounded-md shadow-xl w-full max-w-md transform transition-all scale-95 opacity-0" id="changeDoctorPanel">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <h3 class="text-sm font-semibold text-gray-900">Change Doctor</h3>
                <button onclick="closeChangeDoctorModal()" class="p-1.5 rounded-lg hover:bg-gray-100 text-gray-500">
                    <i class="hgi-stroke hgi-cancel-01 text-base"></i>
                </button>
            </div>
            <div class="p-6">
                <form id="changeDoctorForm" method="POST" action="">
                    @csrf
                    <input type="hidden" name="visit_id" id="changeDoctorVisitId">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Select Doctor</label>
                        <x-ui.select name="doctor_id" id="changeDoctorSelect" class="w-full border border-gray-200 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-slate-300 bg-white" required>
                            <option value="">Select doctor...</option>
                            @foreach($doctors as $d)
                            <option value="{{ $d->id }}">{{ $d->name }}</option>
                            @endforeach
                        </x-ui.select>
                    </div>
                    <div class="flex justify-end gap-2 mt-4">
                        <button type="button" onclick="closeChangeDoctorModal()" class="px-4 py-2 text-sm text-gray-700 border border-gray-200 hover:bg-gray-100 rounded">Cancel</button>
                        <button type="submit" class="px-4 py-2 bg-slate-800 text-white text-sm font-medium rounded hover:bg-slate-700">Change Doctor</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

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
        { name: 'Registration', icon: 'hgi-checkmark-circle-02', completed: true },
        { name: 'Payment', icon: 'hgi-money-03', completed: status.includes('waiting_for_doctor') || status.includes('with') || status.includes('lab') || status.includes('pharmacy') || status.includes('completed') },
        { name: 'Waiting for Doctor', icon: 'hgi-clock-01', completed: status.includes('waiting_for_doctor') || status.includes('with') || status.includes('lab') || status.includes('pharmacy') || status.includes('completed') },
        { name: 'With Doctor', icon: 'hgi-checkmark-circle-02', completed: status.includes('with') || status.includes('lab') || status.includes('pharmacy') || status.includes('completed') },
        { name: 'Lab Tests', icon: 'hgi-test-tube-01', completed: status.includes('lab') || status.includes('pharmacy') || status.includes('completed') },
        { name: 'Pharmacy', icon: 'hgi-test-tube-01', completed: status.includes('pharmacy') || status.includes('completed') },
        { name: 'Completed', icon: 'hgi-checkmark-circle-02', completed: status.includes('completed') },
    ];
    
    // Find current step index
    const currentStepIndex = steps.findIndex(step => {
        if (status === 'registered') return step.name === 'Payment';
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
        
        const bgClass = isCompleted ? 'bg-slate-700' : (isCurrent ? 'bg-emerald-600' : 'bg-gray-200');
        const textClass = isCompleted ? 'text-gray-900' : (isCurrent ? 'text-emerald-600' : 'text-gray-400');
        const iconColor = isCompleted ? 'text-white' : (isCurrent ? 'text-white' : 'text-gray-400');
        
        // Generate action button based on step
        let actionButton = '';
        if (isCurrent) {
            if (step.name === 'Waiting for Doctor') {
                actionButton = `<button onclick="callToDoctor(${visitId})" class="mt-2 flex items-center gap-1.5 px-3 py-1.5 bg-slate-800 text-white text-xs font-medium rounded hover:bg-slate-700 transition-colors">
                    <i class="hgi-stroke hgi-video-01 text-sm"></i>
                    Call to Doctor
                </button>`;
            } else if (step.name === 'With Doctor') {
                actionButton = `<div class="mt-2 flex gap-2">
                    <button onclick="sendToLab(${visitId})" class="flex items-center gap-1.5 px-3 py-1.5 bg-slate-800 text-white text-xs font-medium rounded hover:bg-slate-700 transition-colors">
                        <i class="hgi-stroke hgi-test-tube-01 text-sm"></i>
                        Send to Lab
                    </button>
                    <button onclick="sendToPharmacy(${visitId})" class="flex items-center gap-1.5 px-3 py-1.5 bg-slate-800 text-white text-xs font-medium rounded hover:bg-slate-700 transition-colors">
                        <i class="hgi-stroke hgi-test-tube-01 text-sm"></i>
                        Send to Pharmacy
                    </button>
                </div>`;
            } else if (step.name === 'Lab Tests') {
                actionButton = `<button onclick="completeLab(${visitId})" class="mt-2 flex items-center gap-1.5 px-3 py-1.5 bg-slate-800 text-white text-xs font-medium rounded hover:bg-slate-700 transition-colors">
                    <i class="hgi-stroke hgi-checkmark-circle-02 text-sm"></i>
                    Lab Completed
                </button>`;
            } else if (step.name === 'Pharmacy') {
                actionButton = `<button onclick="completeVisit(${visitId})" class="mt-2 flex items-center gap-1.5 px-3 py-1.5 bg-slate-800 text-white text-xs font-medium rounded hover:bg-slate-700 transition-colors">
                    <i class="hgi-stroke hgi-checkmark-circle-02 text-sm"></i>
                    Complete Visit
                </button>`;
            }
        }
        
        stepperHTML += `
            <div class="flex items-start gap-4">
                <div class="flex flex-col items-center">
                    <div class="w-10 h-10 rounded-full ${bgClass} flex items-center justify-center">
                        <i class="hgi-stroke ${step.icon} text-xl ${iconColor}"></i>
                    </div>
                    ${index < steps.length - 1 ? `<div class="w-0.5 h-12 ${isCompleted ? 'bg-slate-700' : 'bg-gray-200'} mt-2"></div>` : ''}
                </div>
                <div class="flex-1 pb-4">
                    <div class="flex items-center gap-2">
                        <h4 class="text-sm font-semibold ${textClass}">${step.name}</h4>
                        ${isCurrent ? '<span class="inline-flex items-center gap-1 text-[10px] font-medium text-gray-600"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>Current</span>' : ''}
                        ${isCompleted ? '<span class="inline-flex items-center gap-1 text-[10px] font-medium text-gray-600"><span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>Done</span>' : ''}
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
        confirmButtonColor: '#0f172a',
        cancelButtonColor: '#6b7280',
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
        confirmButtonColor: '#0f172a',
        cancelButtonColor: '#6b7280',
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
        confirmButtonColor: '#0f172a',
        cancelButtonColor: '#6b7280',
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
        confirmButtonColor: '#0f172a',
        cancelButtonColor: '#6b7280',
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
        confirmButtonColor: '#0f172a',
        cancelButtonColor: '#6b7280',
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
        confirmButtonColor: '#0f172a',
        cancelButtonColor: '#6b7280',
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
<div id="dischargeModal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm transition-opacity" onclick="closeDischargeModal()"></div>
    <div class="absolute inset-0 flex items-center justify-center p-4">
        <div class="bg-white rounded-md shadow-xl w-full max-w-md transform transition-all scale-95 opacity-0" id="dischargePanel">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <h3 class="text-sm font-semibold text-gray-900">Discharge Patient</h3>
                <button onclick="closeDischargeModal()" class="p-1.5 rounded-lg hover:bg-gray-100 text-gray-500">
                    <i class="hgi-stroke hgi-cancel-01 text-base"></i>
                </button>
            </div>
            <div class="p-6">
                <form id="dischargeForm" method="POST" action="">
                    @csrf
                    <input type="hidden" name="visit_id" id="dischargeVisitId">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Discharge Notes</label>
                        <x-ui.textarea name="notes" rows="3" class="w-full border border-gray-200 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-slate-300" placeholder="Add discharge notes..."></x-ui.textarea>
                    </div>
                    <div class="flex justify-end gap-2 mt-4">
                        <button type="button" onclick="closeDischargeModal()" class="px-4 py-2 text-sm text-gray-700 border border-gray-200 hover:bg-gray-100 rounded">Cancel</button>
                        <button type="submit" class="px-4 py-2 bg-red-600 text-white text-sm font-medium rounded hover:bg-red-700">Discharge</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

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
        { name: 'Registration', icon: 'hgi-checkmark-circle-02', completed: true },
        { name: 'Payment', icon: 'hgi-money-03', completed: status.includes('waiting_for_doctor') || status.includes('with') || status.includes('lab') || status.includes('pharmacy') || status.includes('completed') },
        { name: 'Waiting for Doctor', icon: 'hgi-clock-01', completed: status.includes('waiting_for_doctor') || status.includes('with') || status.includes('lab') || status.includes('pharmacy') || status.includes('completed') },
        { name: 'With Doctor', icon: 'hgi-checkmark-circle-02', completed: status.includes('with') || status.includes('lab') || status.includes('pharmacy') || status.includes('completed') },
        { name: 'Lab Tests', icon: 'hgi-test-tube-01', completed: status.includes('lab') || status.includes('pharmacy') || status.includes('completed') },
        { name: 'Pharmacy', icon: 'hgi-test-tube-01', completed: status.includes('pharmacy') || status.includes('completed') },
        { name: 'Completed', icon: 'hgi-checkmark-circle-02', completed: status.includes('completed') },
    ];
    
    // Find current step index
    const currentStepIndex = steps.findIndex(step => {
        if (status === 'registered') return step.name === 'Payment';
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
        
        const bgClass = isCompleted ? 'bg-slate-700' : (isCurrent ? 'bg-emerald-600' : 'bg-gray-200');
        const textClass = isCompleted ? 'text-gray-900' : (isCurrent ? 'text-emerald-600' : 'text-gray-400');
        const iconColor = isCompleted ? 'text-white' : (isCurrent ? 'text-white' : 'text-gray-400');
        
        // Generate action button based on step
        let actionButton = '';
        if (isCurrent) {
            if (step.name === 'Waiting for Doctor') {
                actionButton = `<button onclick="callToDoctor(${visitId})" class="mt-2 flex items-center gap-1.5 px-3 py-1.5 bg-slate-800 text-white text-xs font-medium rounded hover:bg-slate-700 transition-colors">
                    <i class="hgi-stroke hgi-video-01 text-sm"></i>
                    Call to Doctor
                </button>`;
            } else if (step.name === 'With Doctor') {
                actionButton = `<div class="mt-2 flex gap-2">
                    <button onclick="sendToLab(${visitId})" class="flex items-center gap-1.5 px-3 py-1.5 bg-slate-800 text-white text-xs font-medium rounded hover:bg-slate-700 transition-colors">
                        <i class="hgi-stroke hgi-test-tube-01 text-sm"></i>
                        Send to Lab
                    </button>
                    <button onclick="sendToPharmacy(${visitId})" class="flex items-center gap-1.5 px-3 py-1.5 bg-slate-800 text-white text-xs font-medium rounded hover:bg-slate-700 transition-colors">
                        <i class="hgi-stroke hgi-test-tube-01 text-sm"></i>
                        Send to Pharmacy
                    </button>
                </div>`;
            } else if (step.name === 'Lab Tests') {
                actionButton = `<button onclick="completeLab(${visitId})" class="mt-2 flex items-center gap-1.5 px-3 py-1.5 bg-slate-800 text-white text-xs font-medium rounded hover:bg-slate-700 transition-colors">
                    <i class="hgi-stroke hgi-checkmark-circle-02 text-sm"></i>
                    Lab Completed
                </button>`;
            } else if (step.name === 'Pharmacy') {
                actionButton = `<button onclick="completeVisit(${visitId})" class="mt-2 flex items-center gap-1.5 px-3 py-1.5 bg-slate-800 text-white text-xs font-medium rounded hover:bg-slate-700 transition-colors">
                    <i class="hgi-stroke hgi-checkmark-circle-02 text-sm"></i>
                    Complete Visit
                </button>`;
            }
        }
        
        stepperHTML += `
            <div class="flex items-start gap-4">
                <div class="flex flex-col items-center">
                    <div class="w-10 h-10 rounded-full ${bgClass} flex items-center justify-center">
                        <i class="hgi-stroke ${step.icon} text-xl ${iconColor}"></i>
                    </div>
                    ${index < steps.length - 1 ? `<div class="w-0.5 h-12 ${isCompleted ? 'bg-slate-700' : 'bg-gray-200'} mt-2"></div>` : ''}
                </div>
                <div class="flex-1 pb-4">
                    <div class="flex items-center gap-2">
                        <h4 class="text-sm font-semibold ${textClass}">${step.name}</h4>
                        ${isCurrent ? '<span class="inline-flex items-center gap-1 text-[10px] font-medium text-gray-600"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>Current</span>' : ''}
                        ${isCompleted ? '<span class="inline-flex items-center gap-1 text-[10px] font-medium text-gray-600"><span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>Done</span>' : ''}
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
        confirmButtonColor: '#0f172a',
        cancelButtonColor: '#6b7280',
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
        confirmButtonColor: '#0f172a',
        cancelButtonColor: '#6b7280',
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
        confirmButtonColor: '#0f172a',
        cancelButtonColor: '#6b7280',
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
        confirmButtonColor: '#0f172a',
        cancelButtonColor: '#6b7280',
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
        confirmButtonColor: '#0f172a',
        cancelButtonColor: '#6b7280',
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
        confirmButtonColor: '#0f172a',
        cancelButtonColor: '#6b7280',
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
<div id="stepperModal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm transition-opacity" onclick="closeStepperModal()"></div>
    <div class="absolute inset-y-0 right-0 w-full max-w-lg bg-white shadow-2xl transform transition-transform translate-x-full duration-300 ease-in-out flex flex-col" id="stepperPanel">
        <div class="px-6 py-4 border-b border-slate-700 flex items-center justify-between bg-slate-800">
            <div>
                <h3 class="text-sm font-semibold text-white">Patient Journey</h3>
                <p class="text-xs text-slate-400" id="stepperPatientName">-</p>
            </div>
            <button onclick="closeStepperModal()" class="p-1.5 rounded hover:bg-slate-700 text-slate-300">
                <i class="hgi-stroke hgi-cancel-01 text-base"></i>
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
        { name: 'Registration', icon: 'hgi-checkmark-circle-02', completed: true },
        { name: 'Payment', icon: 'hgi-money-03', completed: status.includes('waiting_for_doctor') || status.includes('with') || status.includes('lab') || status.includes('pharmacy') || status.includes('completed') },
        { name: 'Waiting for Doctor', icon: 'hgi-clock-01', completed: status.includes('waiting_for_doctor') || status.includes('with') || status.includes('lab') || status.includes('pharmacy') || status.includes('completed') },
        { name: 'With Doctor', icon: 'hgi-checkmark-circle-02', completed: status.includes('with') || status.includes('lab') || status.includes('pharmacy') || status.includes('completed') },
        { name: 'Lab Tests', icon: 'hgi-test-tube-01', completed: status.includes('lab') || status.includes('pharmacy') || status.includes('completed') },
        { name: 'Pharmacy', icon: 'hgi-test-tube-01', completed: status.includes('pharmacy') || status.includes('completed') },
        { name: 'Completed', icon: 'hgi-checkmark-circle-02', completed: status.includes('completed') },
    ];
    
    // Find current step index
    const currentStepIndex = steps.findIndex(step => {
        if (status === 'registered') return step.name === 'Payment';
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
        
        const bgClass = isCompleted ? 'bg-slate-700' : (isCurrent ? 'bg-emerald-600' : 'bg-gray-200');
        const textClass = isCompleted ? 'text-gray-900' : (isCurrent ? 'text-emerald-600' : 'text-gray-400');
        const iconColor = isCompleted ? 'text-white' : (isCurrent ? 'text-white' : 'text-gray-400');
        
        // Generate action button based on step
        let actionButton = '';
        if (isCurrent) {
            if (step.name === 'Waiting for Doctor') {
                actionButton = `<button onclick="callToDoctor(${visitId})" class="mt-2 flex items-center gap-1.5 px-3 py-1.5 bg-slate-800 text-white text-xs font-medium rounded hover:bg-slate-700 transition-colors">
                    <i class="hgi-stroke hgi-video-01 text-sm"></i>
                    Call to Doctor
                </button>`;
            } else if (step.name === 'With Doctor') {
                actionButton = `<div class="mt-2 flex gap-2">
                    <button onclick="sendToLab(${visitId})" class="flex items-center gap-1.5 px-3 py-1.5 bg-slate-800 text-white text-xs font-medium rounded hover:bg-slate-700 transition-colors">
                        <i class="hgi-stroke hgi-test-tube-01 text-sm"></i>
                        Send to Lab
                    </button>
                    <button onclick="sendToPharmacy(${visitId})" class="flex items-center gap-1.5 px-3 py-1.5 bg-slate-800 text-white text-xs font-medium rounded hover:bg-slate-700 transition-colors">
                        <i class="hgi-stroke hgi-test-tube-01 text-sm"></i>
                        Send to Pharmacy
                    </button>
                </div>`;
            } else if (step.name === 'Lab Tests') {
                actionButton = `<button onclick="completeLab(${visitId})" class="mt-2 flex items-center gap-1.5 px-3 py-1.5 bg-slate-800 text-white text-xs font-medium rounded hover:bg-slate-700 transition-colors">
                    <i class="hgi-stroke hgi-checkmark-circle-02 text-sm"></i>
                    Lab Completed
                </button>`;
            } else if (step.name === 'Pharmacy') {
                actionButton = `<button onclick="completeVisit(${visitId})" class="mt-2 flex items-center gap-1.5 px-3 py-1.5 bg-slate-800 text-white text-xs font-medium rounded hover:bg-slate-700 transition-colors">
                    <i class="hgi-stroke hgi-checkmark-circle-02 text-sm"></i>
                    Complete Visit
                </button>`;
            }
        }
        
        stepperHTML += `
            <div class="flex items-start gap-4">
                <div class="flex flex-col items-center">
                    <div class="w-10 h-10 rounded-full ${bgClass} flex items-center justify-center">
                        <i class="hgi-stroke ${step.icon} text-xl ${iconColor}"></i>
                    </div>
                    ${index < steps.length - 1 ? `<div class="w-0.5 h-12 ${isCompleted ? 'bg-slate-700' : 'bg-gray-200'} mt-2"></div>` : ''}
                </div>
                <div class="flex-1 pb-4">
                    <div class="flex items-center gap-2">
                        <h4 class="text-sm font-semibold ${textClass}">${step.name}</h4>
                        ${isCurrent ? '<span class="inline-flex items-center gap-1 text-[10px] font-medium text-gray-600"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>Current</span>' : ''}
                        ${isCompleted ? '<span class="inline-flex items-center gap-1 text-[10px] font-medium text-gray-600"><span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>Done</span>' : ''}
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
        confirmButtonColor: '#0f172a',
        cancelButtonColor: '#6b7280',
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
        confirmButtonColor: '#0f172a',
        cancelButtonColor: '#6b7280',
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
        confirmButtonColor: '#0f172a',
        cancelButtonColor: '#6b7280',
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
        confirmButtonColor: '#0f172a',
        cancelButtonColor: '#6b7280',
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
        confirmButtonColor: '#0f172a',
        cancelButtonColor: '#6b7280',
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
        confirmButtonColor: '#0f172a',
        cancelButtonColor: '#6b7280',
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
