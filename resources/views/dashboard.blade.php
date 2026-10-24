@extends('layouts.dashboard')

@section('title', 'Dashboard - ' . config('app.name', 'Laravel'))
@section('page_title', 'Dashboard Overview')

@section('content')
<style>
    .card-sm { transition: border-color 0.2s ease; }
    .card-sm:hover { border-color: #cbd5e1; }
    .circular-chart { display: block; margin: 0 auto; max-width: 100%; max-height: 130px; }
    .circle-bg { fill: none; stroke: #e5e7eb; stroke-width: 3; }
    .circle { fill: none; stroke-width: 3; stroke-linecap: round; animation: progress 1s ease-out forwards; }
    @keyframes progress { 0% { stroke-dasharray: 0 100; } }
    .percentage { fill: #111827; font-weight: 700; font-size: 0.5rem; }
    .label { fill: #6b7280; font-size: 0.22rem; }
</style>

@php
    $roleLabels = ['admin' => 'Administrator', 'reception' => 'Receptionist', 'doctor' => 'Doctor', 'lab' => 'Lab Technician', 'pharmacy' => 'Pharmacist'];
    $fmt = fn($n) => 'TSh ' . number_format($n, 0);
@endphp

@if($user->isDoctor())
@php
    $doctorId = auth()->id();
    $todayDoctorVisits = \App\Models\Visit::where('doctor_id', $doctorId)->whereDate('registered_at', today())->count();
    $pendingConsultations = \App\Models\Visit::where('doctor_id', $doctorId)->whereIn('status', ['waiting_for_doctor', 'with_doctor'])->count();
    $completedToday = \App\Models\Visit::where('doctor_id', $doctorId)->whereDate('completed_at', today())->where('status', 'completed')->count();
    $pendingLabResults = \App\Models\Visit::where('doctor_id', $doctorId)->whereIn('status', ['waiting_for_lab', 'in_lab', 'lab_completed'])->count();
    $doctorAppointments = \App\Models\Appointment::whereDate('scheduled_at', today())->where('doctor_id', $doctorId)->with('patient')->orderBy('scheduled_at')->get();
    $recentDoctorPatients = \App\Models\Visit::where('doctor_id', $doctorId)->with('patient')->latest()->limit(5)->get();
@endphp
@endif

<script>
const fmt = (n) => 'TSh ' + n.toLocaleString();

const weeklyData = {{ $revenueDays->values() }};
const weeklyLabels = @json($dayLabels);
const monthlyData = {{ $monthlyDays->values() }};
const monthlyLabels = @json($monthLabels);
const visitData = {{ $visitTrend->values() }};
const visitLabels = @json($visitLabels);
const deptLabels = @json($departmentPerformance->pluck('name'));
const deptData = {{ $departmentPerformance->pluck('visits') }};

let revenueChart, visitChart, deptChart;

function initCharts() {
    const revCtx = document.getElementById('revenueChart').getContext('2d');
    const gradient = revCtx.createLinearGradient(0, 0, 0, 300);
    gradient.addColorStop(0, 'rgba(5, 150, 105, 0.08)');
    gradient.addColorStop(1, 'rgba(5, 150, 105, 0)');

    revenueChart = new Chart(revCtx, {
        type: 'line',
        data: {
            labels: weeklyLabels,
            datasets: [{
                label: 'Revenue',
                data: weeklyData,
                borderColor: '#059669',
                backgroundColor: gradient,
                borderWidth: 2,
                pointBackgroundColor: '#ffffff',
                pointBorderColor: '#059669',
                pointBorderWidth: 2,
                pointRadius: 2,
                pointHoverRadius: 4,
                fill: true,
                tension: 0.35
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false }, tooltip: { callbacks: { label: (ctx) => fmt(ctx.raw) } } },
            scales: { y: { beginAtZero: true, grid: { color: '#f1f5f9' }, ticks: { font: { size: 10 } } }, x: { grid: { display: false }, ticks: { font: { size: 10 } } } }
        }
    });

    visitChart = new Chart(document.getElementById('visitTrendChart'), {
        type: 'line',
        data: {
            labels: visitLabels,
            datasets: [{
                label: 'Visits',
                data: visitData,
                borderColor: '#94a3b8',
                backgroundColor: 'rgba(148, 163, 184, 0.08)',
                borderWidth: 2,
                borderDash: [5, 4],
                pointBackgroundColor: '#ffffff',
                pointBorderColor: '#94a3b8',
                pointBorderWidth: 2,
                pointRadius: 2,
                fill: true,
                tension: 0.35
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true, grid: { color: '#f1f5f9' }, ticks: { font: { size: 10 } } }, x: { grid: { display: false }, ticks: { font: { size: 10 } } } }
        }
    });

    deptChart = new Chart(document.getElementById('departmentChart'), {
        type: 'bar',
        data: {
            labels: deptLabels,
            datasets: [{
                label: 'Visits',
                data: deptData,
                backgroundColor: ['#334155', '#94a3b8', '#059669', '#f59e0b', '#cbd5e1', '#dc2626'],
                borderRadius: 6,
                barThickness: 24
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true, grid: { color: '#f1f5f9' }, ticks: { font: { size: 10 } } }, x: { grid: { display: false }, ticks: { font: { size: 10 } } } }
        }
    });
}

function switchRevenue(period) {
    document.getElementById('revWeeklyBtn').className = period === 'weekly'
        ? 'px-3 py-1 text-xs font-medium rounded-md bg-white text-slate-800 shadow-sm'
        : 'px-3 py-1 text-xs font-medium rounded-md text-gray-500 hover:text-gray-700';
    document.getElementById('revMonthlyBtn').className = period === 'monthly'
        ? 'px-3 py-1 text-xs font-medium rounded-md bg-white text-slate-800 shadow-sm'
        : 'px-3 py-1 text-xs font-medium rounded-md text-gray-500 hover:text-gray-700';

    revenueChart.data.labels = period === 'weekly' ? weeklyLabels : monthlyLabels;
    revenueChart.data.datasets[0].data = period === 'weekly' ? weeklyData : monthlyData;
    revenueChart.update();
}

function openDashboardPatientModal() {
    const modal = document.getElementById('dashboardPatientModal');
    const panel = document.getElementById('dashboardPatientSlidePanel');
    modal.classList.remove('hidden');
    setTimeout(() => panel.classList.remove('translate-x-full'), 10);
    document.body.style.overflow = 'hidden';
}
function closeDashboardPatientModal() {
    const modal = document.getElementById('dashboardPatientModal');
    const panel = document.getElementById('dashboardPatientSlidePanel');
    panel.classList.add('translate-x-full');
    setTimeout(() => { modal.classList.add('hidden'); document.body.style.overflow = ''; }, 300);
}

function openDashboardAppointmentModal() {
    const modal = document.getElementById('dashboardAppointmentModal');
    const panel = document.getElementById('dashboardAppointmentSlidePanel');
    modal.classList.remove('hidden');
    setTimeout(() => panel.classList.remove('translate-x-full'), 10);
    document.body.style.overflow = 'hidden';
}
function closeDashboardAppointmentModal() {
    const modal = document.getElementById('dashboardAppointmentModal');
    const panel = document.getElementById('dashboardAppointmentSlidePanel');
    panel.classList.add('translate-x-full');
    setTimeout(() => { modal.classList.add('hidden'); document.body.style.overflow = ''; }, 300);
}

document.getElementById('dashApptPatientSearch')?.addEventListener('input', function() {
    const q = this.value.toLowerCase();
    const select = document.getElementById('dashApptPatientSelect');
    Array.from(select.querySelectorAll('option')).forEach(opt => {
        if (!opt.value) { opt.style.display = 'block'; return; }
        opt.style.display = opt.dataset.search?.includes(q) ? 'block' : 'none';
    });
});

// AJAX patient form
setupAjaxForm('#dashboardPatientForm', 'Patient saved successfully.', closeDashboardPatientModal);
// AJAX appointment form
setupAjaxForm('#dashboardAppointmentForm', 'Appointment booked successfully.', closeDashboardAppointmentModal);

// Doctor filter
function filterVisitsByDoctor() {
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

// Change doctor modal
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

// Change doctor form AJAX
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

// Initialize charts
initCharts();

// Real-time refresh every 60 seconds
setInterval(() => {
    fetch('/dashboard/stats', { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
        .then(r => r.json())
        .then(data => {
            if (data.revenueDays) {
                revenueChart.data.datasets[0].data = data.revenueDays;
                revenueChart.data.labels = data.dayLabels;
                revenueChart.update();
            }
            if (data.visitTrend) {
                visitChart.data.datasets[0].data = data.visitTrend;
                visitChart.data.labels = data.visitLabels;
                visitChart.update();
            }
            if (data.stats) {
                document.querySelectorAll('.card-sm p.text-xl').forEach(el => {
                    const label = el.previousElementSibling?.textContent?.trim();
                    if (label === 'Today\'s Visits') el.textContent = data.stats.visits_today;
                    if (label === 'Weekly Revenue') el.textContent = 'TSh ' + Number(data.stats.revenue_this_week).toLocaleString();
                    if (label === 'Pending Payments') el.textContent = data.stats.pending_payments;
                });
            }
        })
        .catch(() => {});
}, 60000);
</script>
<div class="bg-slate-800 rounded-md p-6 mb-5 text-white">
    <div class="flex flex-wrap items-end justify-between gap-3">
        <div>
            <p class="text-[10px] uppercase tracking-wider text-slate-400 mb-1">{{ $roleLabels[$user->roles->first()?->name] ?? 'User' }}</p>
            <h1 class="text-xl font-bold">Hello {{ $user->name }}</h1>
            <p class="text-xs text-slate-400 mt-1">Here's what's happening at {{ config('app.name', 'Laravel') }}.</p>
        </div>
        <div class="flex items-center gap-2">
            @if($user->isReception() || $user->isAdmin())
                <button type="button" onclick="openDashboardPatientModal()" class="px-3 py-1.5 text-xs font-medium border border-slate-600 rounded text-slate-200 hover:bg-slate-700 transition-colors inline-flex items-center gap-1.5">
                    <i class="hgi-stroke hgi-user-multiple-02 text-sm"></i>
                    Add Patient
                </button>
                <button type="button" onclick="openDashboardAppointmentModal()" class="px-3 py-1.5 text-xs font-semibold bg-white text-slate-800 rounded hover:bg-gray-100 transition-colors inline-flex items-center gap-1.5">
                    <i class="hgi-stroke hgi-add-01 text-sm"></i>
                    New Appointment
                </button>
            @endif
            @if($user->isDoctor())
                <div class="text-center px-3 py-1.5 bg-slate-700/60 rounded">
                    <div class="text-base font-bold leading-tight">{{ $pendingConsultations }}</div>
                    <div class="text-[9px] text-slate-400 uppercase tracking-wide">Waiting</div>
                </div>
                <div class="text-center px-3 py-1.5 bg-slate-700/60 rounded">
                    <div class="text-base font-bold leading-tight">{{ $completedToday }}</div>
                    <div class="text-[9px] text-slate-400 uppercase tracking-wide">Done Today</div>
                </div>
                <a href="{{ route('doctor.queue') }}" class="px-3 py-1.5 text-xs font-semibold bg-white text-slate-800 rounded hover:bg-gray-100 transition-colors inline-flex items-center gap-1.5">
                    <i class="hgi-stroke hgi-building-03 text-sm"></i>
                    My Queue
                </a>
            @endif
        </div>
    </div>
</div>

<script>
const fmt = (n) => 'TSh ' + n.toLocaleString();

const weeklyData = {{ $revenueDays->values() }};
const weeklyLabels = @json($dayLabels);
const monthlyData = {{ $monthlyDays->values() }};
const monthlyLabels = @json($monthLabels);
const visitData = {{ $visitTrend->values() }};
const visitLabels = @json($visitLabels);
const deptLabels = @json($departmentPerformance->pluck('name'));
const deptData = {{ $departmentPerformance->pluck('visits') }};

let revenueChart, visitChart, deptChart;

function initCharts() {
    const revCtx = document.getElementById('revenueChart').getContext('2d');
    const gradient = revCtx.createLinearGradient(0, 0, 0, 300);
    gradient.addColorStop(0, 'rgba(5, 150, 105, 0.08)');
    gradient.addColorStop(1, 'rgba(5, 150, 105, 0)');

    revenueChart = new Chart(revCtx, {
        type: 'line',
        data: {
            labels: weeklyLabels,
            datasets: [{
                label: 'Revenue',
                data: weeklyData,
                borderColor: '#059669',
                backgroundColor: gradient,
                borderWidth: 2,
                pointBackgroundColor: '#ffffff',
                pointBorderColor: '#059669',
                pointBorderWidth: 2,
                pointRadius: 2,
                pointHoverRadius: 4,
                fill: true,
                tension: 0.35
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false }, tooltip: { callbacks: { label: (ctx) => fmt(ctx.raw) } } },
            scales: { y: { beginAtZero: true, grid: { color: '#f1f5f9' }, ticks: { font: { size: 10 } } }, x: { grid: { display: false }, ticks: { font: { size: 10 } } } }
        }
    });

    visitChart = new Chart(document.getElementById('visitTrendChart'), {
        type: 'line',
        data: {
            labels: visitLabels,
            datasets: [{
                label: 'Visits',
                data: visitData,
                borderColor: '#94a3b8',
                backgroundColor: 'rgba(148, 163, 184, 0.08)',
                borderWidth: 2,
                borderDash: [5, 4],
                pointBackgroundColor: '#ffffff',
                pointBorderColor: '#94a3b8',
                pointBorderWidth: 2,
                pointRadius: 2,
                fill: true,
                tension: 0.35
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true, grid: { color: '#f1f5f9' }, ticks: { font: { size: 10 } } }, x: { grid: { display: false }, ticks: { font: { size: 10 } } } }
        }
    });

    deptChart = new Chart(document.getElementById('departmentChart'), {
        type: 'bar',
        data: {
            labels: deptLabels,
            datasets: [{
                label: 'Visits',
                data: deptData,
                backgroundColor: ['#334155', '#94a3b8', '#059669', '#f59e0b', '#cbd5e1', '#dc2626'],
                borderRadius: 6,
                barThickness: 24
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true, grid: { color: '#f1f5f9' }, ticks: { font: { size: 10 } } }, x: { grid: { display: false }, ticks: { font: { size: 10 } } } }
        }
    });
}

function switchRevenue(period) {
    document.getElementById('revWeeklyBtn').className = period === 'weekly'
        ? 'px-3 py-1 text-xs font-medium rounded-md bg-white text-slate-800 shadow-sm'
        : 'px-3 py-1 text-xs font-medium rounded-md text-gray-500 hover:text-gray-700';
    document.getElementById('revMonthlyBtn').className = period === 'monthly'
        ? 'px-3 py-1 text-xs font-medium rounded-md bg-white text-slate-800 shadow-sm'
        : 'px-3 py-1 text-xs font-medium rounded-md text-gray-500 hover:text-gray-700';

    revenueChart.data.labels = period === 'weekly' ? weeklyLabels : monthlyLabels;
    revenueChart.data.datasets[0].data = period === 'weekly' ? weeklyData : monthlyData;
    revenueChart.update();
}

function openDashboardPatientModal() {
    const modal = document.getElementById('dashboardPatientModal');
    const panel = document.getElementById('dashboardPatientSlidePanel');
    modal.classList.remove('hidden');
    setTimeout(() => panel.classList.remove('translate-x-full'), 10);
    document.body.style.overflow = 'hidden';
}
function closeDashboardPatientModal() {
    const modal = document.getElementById('dashboardPatientModal');
    const panel = document.getElementById('dashboardPatientSlidePanel');
    panel.classList.add('translate-x-full');
    setTimeout(() => { modal.classList.add('hidden'); document.body.style.overflow = ''; }, 300);
}

function openDashboardAppointmentModal() {
    const modal = document.getElementById('dashboardAppointmentModal');
    const panel = document.getElementById('dashboardAppointmentSlidePanel');
    modal.classList.remove('hidden');
    setTimeout(() => panel.classList.remove('translate-x-full'), 10);
    document.body.style.overflow = 'hidden';
}
function closeDashboardAppointmentModal() {
    const modal = document.getElementById('dashboardAppointmentModal');
    const panel = document.getElementById('dashboardAppointmentSlidePanel');
    panel.classList.add('translate-x-full');
    setTimeout(() => { modal.classList.add('hidden'); document.body.style.overflow = ''; }, 300);
}

document.getElementById('dashApptPatientSearch')?.addEventListener('input', function() {
    const q = this.value.toLowerCase();
    const select = document.getElementById('dashApptPatientSelect');
    Array.from(select.querySelectorAll('option')).forEach(opt => {
        if (!opt.value) { opt.style.display = 'block'; return; }
        opt.style.display = opt.dataset.search?.includes(q) ? 'block' : 'none';
    });
});

// AJAX patient form
setupAjaxForm('#dashboardPatientForm', 'Patient saved successfully.', closeDashboardPatientModal);
// AJAX appointment form
setupAjaxForm('#dashboardAppointmentForm', 'Appointment booked successfully.', closeDashboardAppointmentModal);

// Doctor filter
function filterVisitsByDoctor() {
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

// Change doctor modal
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

// Change doctor form AJAX
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

// Initialize charts
initCharts();

// Real-time refresh every 60 seconds
setInterval(() => {
    fetch('/dashboard/stats', { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
        .then(r => r.json())
        .then(data => {
            if (data.revenueDays) {
                revenueChart.data.datasets[0].data = data.revenueDays;
                revenueChart.data.labels = data.dayLabels;
                revenueChart.update();
            }
            if (data.visitTrend) {
                visitChart.data.datasets[0].data = data.visitTrend;
                visitChart.data.labels = data.visitLabels;
                visitChart.update();
            }
            if (data.stats) {
                document.querySelectorAll('.card-sm p.text-xl').forEach(el => {
                    const label = el.previousElementSibling?.textContent?.trim();
                    if (label === 'Today\'s Visits') el.textContent = data.stats.visits_today;
                    if (label === 'Weekly Revenue') el.textContent = 'TSh ' + Number(data.stats.revenue_this_week).toLocaleString();
                    if (label === 'Pending Payments') el.textContent = data.stats.pending_payments;
                });
            }
        })
        .catch(() => {});
}, 60000);
</script>
@if($user->isDoctor())

<script>
const fmt = (n) => 'TSh ' + n.toLocaleString();

const weeklyData = {{ $revenueDays->values() }};
const weeklyLabels = @json($dayLabels);
const monthlyData = {{ $monthlyDays->values() }};
const monthlyLabels = @json($monthLabels);
const visitData = {{ $visitTrend->values() }};
const visitLabels = @json($visitLabels);
const deptLabels = @json($departmentPerformance->pluck('name'));
const deptData = {{ $departmentPerformance->pluck('visits') }};

let revenueChart, visitChart, deptChart;

function initCharts() {
    const revCtx = document.getElementById('revenueChart').getContext('2d');
    const gradient = revCtx.createLinearGradient(0, 0, 0, 300);
    gradient.addColorStop(0, 'rgba(5, 150, 105, 0.08)');
    gradient.addColorStop(1, 'rgba(5, 150, 105, 0)');

    revenueChart = new Chart(revCtx, {
        type: 'line',
        data: {
            labels: weeklyLabels,
            datasets: [{
                label: 'Revenue',
                data: weeklyData,
                borderColor: '#059669',
                backgroundColor: gradient,
                borderWidth: 2,
                pointBackgroundColor: '#ffffff',
                pointBorderColor: '#059669',
                pointBorderWidth: 2,
                pointRadius: 2,
                pointHoverRadius: 4,
                fill: true,
                tension: 0.35
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false }, tooltip: { callbacks: { label: (ctx) => fmt(ctx.raw) } } },
            scales: { y: { beginAtZero: true, grid: { color: '#f1f5f9' }, ticks: { font: { size: 10 } } }, x: { grid: { display: false }, ticks: { font: { size: 10 } } } }
        }
    });

    visitChart = new Chart(document.getElementById('visitTrendChart'), {
        type: 'line',
        data: {
            labels: visitLabels,
            datasets: [{
                label: 'Visits',
                data: visitData,
                borderColor: '#94a3b8',
                backgroundColor: 'rgba(148, 163, 184, 0.08)',
                borderWidth: 2,
                borderDash: [5, 4],
                pointBackgroundColor: '#ffffff',
                pointBorderColor: '#94a3b8',
                pointBorderWidth: 2,
                pointRadius: 2,
                fill: true,
                tension: 0.35
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true, grid: { color: '#f1f5f9' }, ticks: { font: { size: 10 } } }, x: { grid: { display: false }, ticks: { font: { size: 10 } } } }
        }
    });

    deptChart = new Chart(document.getElementById('departmentChart'), {
        type: 'bar',
        data: {
            labels: deptLabels,
            datasets: [{
                label: 'Visits',
                data: deptData,
                backgroundColor: ['#334155', '#94a3b8', '#059669', '#f59e0b', '#cbd5e1', '#dc2626'],
                borderRadius: 6,
                barThickness: 24
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true, grid: { color: '#f1f5f9' }, ticks: { font: { size: 10 } } }, x: { grid: { display: false }, ticks: { font: { size: 10 } } } }
        }
    });
}

function switchRevenue(period) {
    document.getElementById('revWeeklyBtn').className = period === 'weekly'
        ? 'px-3 py-1 text-xs font-medium rounded-md bg-white text-slate-800 shadow-sm'
        : 'px-3 py-1 text-xs font-medium rounded-md text-gray-500 hover:text-gray-700';
    document.getElementById('revMonthlyBtn').className = period === 'monthly'
        ? 'px-3 py-1 text-xs font-medium rounded-md bg-white text-slate-800 shadow-sm'
        : 'px-3 py-1 text-xs font-medium rounded-md text-gray-500 hover:text-gray-700';

    revenueChart.data.labels = period === 'weekly' ? weeklyLabels : monthlyLabels;
    revenueChart.data.datasets[0].data = period === 'weekly' ? weeklyData : monthlyData;
    revenueChart.update();
}

function openDashboardPatientModal() {
    const modal = document.getElementById('dashboardPatientModal');
    const panel = document.getElementById('dashboardPatientSlidePanel');
    modal.classList.remove('hidden');
    setTimeout(() => panel.classList.remove('translate-x-full'), 10);
    document.body.style.overflow = 'hidden';
}
function closeDashboardPatientModal() {
    const modal = document.getElementById('dashboardPatientModal');
    const panel = document.getElementById('dashboardPatientSlidePanel');
    panel.classList.add('translate-x-full');
    setTimeout(() => { modal.classList.add('hidden'); document.body.style.overflow = ''; }, 300);
}

function openDashboardAppointmentModal() {
    const modal = document.getElementById('dashboardAppointmentModal');
    const panel = document.getElementById('dashboardAppointmentSlidePanel');
    modal.classList.remove('hidden');
    setTimeout(() => panel.classList.remove('translate-x-full'), 10);
    document.body.style.overflow = 'hidden';
}
function closeDashboardAppointmentModal() {
    const modal = document.getElementById('dashboardAppointmentModal');
    const panel = document.getElementById('dashboardAppointmentSlidePanel');
    panel.classList.add('translate-x-full');
    setTimeout(() => { modal.classList.add('hidden'); document.body.style.overflow = ''; }, 300);
}

document.getElementById('dashApptPatientSearch')?.addEventListener('input', function() {
    const q = this.value.toLowerCase();
    const select = document.getElementById('dashApptPatientSelect');
    Array.from(select.querySelectorAll('option')).forEach(opt => {
        if (!opt.value) { opt.style.display = 'block'; return; }
        opt.style.display = opt.dataset.search?.includes(q) ? 'block' : 'none';
    });
});

// AJAX patient form
setupAjaxForm('#dashboardPatientForm', 'Patient saved successfully.', closeDashboardPatientModal);
// AJAX appointment form
setupAjaxForm('#dashboardAppointmentForm', 'Appointment booked successfully.', closeDashboardAppointmentModal);

// Doctor filter
function filterVisitsByDoctor() {
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

// Change doctor modal
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

// Change doctor form AJAX
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

// Initialize charts
initCharts();

// Real-time refresh every 60 seconds
setInterval(() => {
    fetch('/dashboard/stats', { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
        .then(r => r.json())
        .then(data => {
            if (data.revenueDays) {
                revenueChart.data.datasets[0].data = data.revenueDays;
                revenueChart.data.labels = data.dayLabels;
                revenueChart.update();
            }
            if (data.visitTrend) {
                visitChart.data.datasets[0].data = data.visitTrend;
                visitChart.data.labels = data.visitLabels;
                visitChart.update();
            }
            if (data.stats) {
                document.querySelectorAll('.card-sm p.text-xl').forEach(el => {
                    const label = el.previousElementSibling?.textContent?.trim();
                    if (label === 'Today\'s Visits') el.textContent = data.stats.visits_today;
                    if (label === 'Weekly Revenue') el.textContent = 'TSh ' + Number(data.stats.revenue_this_week).toLocaleString();
                    if (label === 'Pending Payments') el.textContent = data.stats.pending_payments;
                });
            }
        })
        .catch(() => {});
}, 60000);
</script>
<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-5">
    <a href="{{ route('doctor.queue') }}" class="card-sm block bg-white rounded-md border border-gray-200 p-4">
        <div class="flex items-start justify-between">
            <p class="text-[10px] font-medium uppercase tracking-wide text-gray-500">Today's Patients</p>
            <i class="hgi-stroke hgi-user-multiple-02 text-base text-gray-400"></i>
        </div>
        <p class="text-2xl font-bold text-gray-900 mt-1">{{ $todayDoctorVisits }}</p>
        <p class="text-[10px] text-gray-400 mt-0.5">Visits registered today</p>
    </a>
    <a href="{{ route('doctor.queue') }}" class="card-sm block bg-white rounded-md border {{ $pendingConsultations > 0 ? 'border-amber-200' : 'border-gray-200' }} p-4">
        <div class="flex items-start justify-between">
            <p class="text-[10px] font-medium uppercase tracking-wide text-gray-500">Pending Consultations</p>
            <i class="hgi-stroke hgi-clock-01 text-base text-gray-400"></i>
        </div>
        <p class="text-2xl font-bold text-gray-900 mt-1">{{ $pendingConsultations }}</p>
        <p class="text-[10px] {{ $pendingConsultations > 0 ? 'text-amber-600' : 'text-gray-400' }} mt-0.5">In your queue</p>
    </a>
    <a href="{{ route('doctor.lab-results') }}" class="card-sm block bg-white rounded-md border border-gray-200 p-4">
        <div class="flex items-start justify-between">
            <p class="text-[10px] font-medium uppercase tracking-wide text-gray-500">Waiting Lab Results</p>
            <i class="hgi-stroke hgi-test-tube-01 text-base text-gray-400"></i>
        </div>
        <p class="text-2xl font-bold text-gray-900 mt-1">{{ $pendingLabResults }}</p>
        <p class="text-[10px] text-gray-400 mt-0.5">Lab workflow</p>
    </a>
    <div class="card-sm block bg-white rounded-md border border-emerald-200 p-4">
        <div class="flex items-start justify-between">
            <p class="text-[10px] font-medium uppercase tracking-wide text-gray-500">Completed Today</p>
            <i class="hgi-stroke hgi-checkmark-circle-02 text-base text-emerald-500"></i>
        </div>
        <p class="text-2xl font-bold text-gray-900 mt-1">{{ $completedToday }}</p>
        <p class="text-[10px] text-emerald-600 mt-0.5">Consultations done</p>
    </div>
</div>

<script>
const fmt = (n) => 'TSh ' + n.toLocaleString();

const weeklyData = {{ $revenueDays->values() }};
const weeklyLabels = @json($dayLabels);
const monthlyData = {{ $monthlyDays->values() }};
const monthlyLabels = @json($monthLabels);
const visitData = {{ $visitTrend->values() }};
const visitLabels = @json($visitLabels);
const deptLabels = @json($departmentPerformance->pluck('name'));
const deptData = {{ $departmentPerformance->pluck('visits') }};

let revenueChart, visitChart, deptChart;

function initCharts() {
    const revCtx = document.getElementById('revenueChart').getContext('2d');
    const gradient = revCtx.createLinearGradient(0, 0, 0, 300);
    gradient.addColorStop(0, 'rgba(5, 150, 105, 0.08)');
    gradient.addColorStop(1, 'rgba(5, 150, 105, 0)');

    revenueChart = new Chart(revCtx, {
        type: 'line',
        data: {
            labels: weeklyLabels,
            datasets: [{
                label: 'Revenue',
                data: weeklyData,
                borderColor: '#059669',
                backgroundColor: gradient,
                borderWidth: 2,
                pointBackgroundColor: '#ffffff',
                pointBorderColor: '#059669',
                pointBorderWidth: 2,
                pointRadius: 2,
                pointHoverRadius: 4,
                fill: true,
                tension: 0.35
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false }, tooltip: { callbacks: { label: (ctx) => fmt(ctx.raw) } } },
            scales: { y: { beginAtZero: true, grid: { color: '#f1f5f9' }, ticks: { font: { size: 10 } } }, x: { grid: { display: false }, ticks: { font: { size: 10 } } } }
        }
    });

    visitChart = new Chart(document.getElementById('visitTrendChart'), {
        type: 'line',
        data: {
            labels: visitLabels,
            datasets: [{
                label: 'Visits',
                data: visitData,
                borderColor: '#94a3b8',
                backgroundColor: 'rgba(148, 163, 184, 0.08)',
                borderWidth: 2,
                borderDash: [5, 4],
                pointBackgroundColor: '#ffffff',
                pointBorderColor: '#94a3b8',
                pointBorderWidth: 2,
                pointRadius: 2,
                fill: true,
                tension: 0.35
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true, grid: { color: '#f1f5f9' }, ticks: { font: { size: 10 } } }, x: { grid: { display: false }, ticks: { font: { size: 10 } } } }
        }
    });

    deptChart = new Chart(document.getElementById('departmentChart'), {
        type: 'bar',
        data: {
            labels: deptLabels,
            datasets: [{
                label: 'Visits',
                data: deptData,
                backgroundColor: ['#334155', '#94a3b8', '#059669', '#f59e0b', '#cbd5e1', '#dc2626'],
                borderRadius: 6,
                barThickness: 24
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true, grid: { color: '#f1f5f9' }, ticks: { font: { size: 10 } } }, x: { grid: { display: false }, ticks: { font: { size: 10 } } } }
        }
    });
}

function switchRevenue(period) {
    document.getElementById('revWeeklyBtn').className = period === 'weekly'
        ? 'px-3 py-1 text-xs font-medium rounded-md bg-white text-slate-800 shadow-sm'
        : 'px-3 py-1 text-xs font-medium rounded-md text-gray-500 hover:text-gray-700';
    document.getElementById('revMonthlyBtn').className = period === 'monthly'
        ? 'px-3 py-1 text-xs font-medium rounded-md bg-white text-slate-800 shadow-sm'
        : 'px-3 py-1 text-xs font-medium rounded-md text-gray-500 hover:text-gray-700';

    revenueChart.data.labels = period === 'weekly' ? weeklyLabels : monthlyLabels;
    revenueChart.data.datasets[0].data = period === 'weekly' ? weeklyData : monthlyData;
    revenueChart.update();
}

function openDashboardPatientModal() {
    const modal = document.getElementById('dashboardPatientModal');
    const panel = document.getElementById('dashboardPatientSlidePanel');
    modal.classList.remove('hidden');
    setTimeout(() => panel.classList.remove('translate-x-full'), 10);
    document.body.style.overflow = 'hidden';
}
function closeDashboardPatientModal() {
    const modal = document.getElementById('dashboardPatientModal');
    const panel = document.getElementById('dashboardPatientSlidePanel');
    panel.classList.add('translate-x-full');
    setTimeout(() => { modal.classList.add('hidden'); document.body.style.overflow = ''; }, 300);
}

function openDashboardAppointmentModal() {
    const modal = document.getElementById('dashboardAppointmentModal');
    const panel = document.getElementById('dashboardAppointmentSlidePanel');
    modal.classList.remove('hidden');
    setTimeout(() => panel.classList.remove('translate-x-full'), 10);
    document.body.style.overflow = 'hidden';
}
function closeDashboardAppointmentModal() {
    const modal = document.getElementById('dashboardAppointmentModal');
    const panel = document.getElementById('dashboardAppointmentSlidePanel');
    panel.classList.add('translate-x-full');
    setTimeout(() => { modal.classList.add('hidden'); document.body.style.overflow = ''; }, 300);
}

document.getElementById('dashApptPatientSearch')?.addEventListener('input', function() {
    const q = this.value.toLowerCase();
    const select = document.getElementById('dashApptPatientSelect');
    Array.from(select.querySelectorAll('option')).forEach(opt => {
        if (!opt.value) { opt.style.display = 'block'; return; }
        opt.style.display = opt.dataset.search?.includes(q) ? 'block' : 'none';
    });
});

// AJAX patient form
setupAjaxForm('#dashboardPatientForm', 'Patient saved successfully.', closeDashboardPatientModal);
// AJAX appointment form
setupAjaxForm('#dashboardAppointmentForm', 'Appointment booked successfully.', closeDashboardAppointmentModal);

// Doctor filter
function filterVisitsByDoctor() {
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

// Change doctor modal
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

// Change doctor form AJAX
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

// Initialize charts
initCharts();

// Real-time refresh every 60 seconds
setInterval(() => {
    fetch('/dashboard/stats', { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
        .then(r => r.json())
        .then(data => {
            if (data.revenueDays) {
                revenueChart.data.datasets[0].data = data.revenueDays;
                revenueChart.data.labels = data.dayLabels;
                revenueChart.update();
            }
            if (data.visitTrend) {
                visitChart.data.datasets[0].data = data.visitTrend;
                visitChart.data.labels = data.visitLabels;
                visitChart.update();
            }
            if (data.stats) {
                document.querySelectorAll('.card-sm p.text-xl').forEach(el => {
                    const label = el.previousElementSibling?.textContent?.trim();
                    if (label === 'Today\'s Visits') el.textContent = data.stats.visits_today;
                    if (label === 'Weekly Revenue') el.textContent = 'TSh ' + Number(data.stats.revenue_this_week).toLocaleString();
                    if (label === 'Pending Payments') el.textContent = data.stats.pending_payments;
                });
            }
        })
        .catch(() => {});
}, 60000);
</script>
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    <script>
const fmt = (n) => 'TSh ' + n.toLocaleString();

const weeklyData = {{ $revenueDays->values() }};
const weeklyLabels = @json($dayLabels);
const monthlyData = {{ $monthlyDays->values() }};
const monthlyLabels = @json($monthLabels);
const visitData = {{ $visitTrend->values() }};
const visitLabels = @json($visitLabels);
const deptLabels = @json($departmentPerformance->pluck('name'));
const deptData = {{ $departmentPerformance->pluck('visits') }};

let revenueChart, visitChart, deptChart;

function initCharts() {
    const revCtx = document.getElementById('revenueChart').getContext('2d');
    const gradient = revCtx.createLinearGradient(0, 0, 0, 300);
    gradient.addColorStop(0, 'rgba(5, 150, 105, 0.08)');
    gradient.addColorStop(1, 'rgba(5, 150, 105, 0)');

    revenueChart = new Chart(revCtx, {
        type: 'line',
        data: {
            labels: weeklyLabels,
            datasets: [{
                label: 'Revenue',
                data: weeklyData,
                borderColor: '#059669',
                backgroundColor: gradient,
                borderWidth: 2,
                pointBackgroundColor: '#ffffff',
                pointBorderColor: '#059669',
                pointBorderWidth: 2,
                pointRadius: 2,
                pointHoverRadius: 4,
                fill: true,
                tension: 0.35
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false }, tooltip: { callbacks: { label: (ctx) => fmt(ctx.raw) } } },
            scales: { y: { beginAtZero: true, grid: { color: '#f1f5f9' }, ticks: { font: { size: 10 } } }, x: { grid: { display: false }, ticks: { font: { size: 10 } } } }
        }
    });

    visitChart = new Chart(document.getElementById('visitTrendChart'), {
        type: 'line',
        data: {
            labels: visitLabels,
            datasets: [{
                label: 'Visits',
                data: visitData,
                borderColor: '#94a3b8',
                backgroundColor: 'rgba(148, 163, 184, 0.08)',
                borderWidth: 2,
                borderDash: [5, 4],
                pointBackgroundColor: '#ffffff',
                pointBorderColor: '#94a3b8',
                pointBorderWidth: 2,
                pointRadius: 2,
                fill: true,
                tension: 0.35
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true, grid: { color: '#f1f5f9' }, ticks: { font: { size: 10 } } }, x: { grid: { display: false }, ticks: { font: { size: 10 } } } }
        }
    });

    deptChart = new Chart(document.getElementById('departmentChart'), {
        type: 'bar',
        data: {
            labels: deptLabels,
            datasets: [{
                label: 'Visits',
                data: deptData,
                backgroundColor: ['#334155', '#94a3b8', '#059669', '#f59e0b', '#cbd5e1', '#dc2626'],
                borderRadius: 6,
                barThickness: 24
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true, grid: { color: '#f1f5f9' }, ticks: { font: { size: 10 } } }, x: { grid: { display: false }, ticks: { font: { size: 10 } } } }
        }
    });
}

function switchRevenue(period) {
    document.getElementById('revWeeklyBtn').className = period === 'weekly'
        ? 'px-3 py-1 text-xs font-medium rounded-md bg-white text-slate-800 shadow-sm'
        : 'px-3 py-1 text-xs font-medium rounded-md text-gray-500 hover:text-gray-700';
    document.getElementById('revMonthlyBtn').className = period === 'monthly'
        ? 'px-3 py-1 text-xs font-medium rounded-md bg-white text-slate-800 shadow-sm'
        : 'px-3 py-1 text-xs font-medium rounded-md text-gray-500 hover:text-gray-700';

    revenueChart.data.labels = period === 'weekly' ? weeklyLabels : monthlyLabels;
    revenueChart.data.datasets[0].data = period === 'weekly' ? weeklyData : monthlyData;
    revenueChart.update();
}

function openDashboardPatientModal() {
    const modal = document.getElementById('dashboardPatientModal');
    const panel = document.getElementById('dashboardPatientSlidePanel');
    modal.classList.remove('hidden');
    setTimeout(() => panel.classList.remove('translate-x-full'), 10);
    document.body.style.overflow = 'hidden';
}
function closeDashboardPatientModal() {
    const modal = document.getElementById('dashboardPatientModal');
    const panel = document.getElementById('dashboardPatientSlidePanel');
    panel.classList.add('translate-x-full');
    setTimeout(() => { modal.classList.add('hidden'); document.body.style.overflow = ''; }, 300);
}

function openDashboardAppointmentModal() {
    const modal = document.getElementById('dashboardAppointmentModal');
    const panel = document.getElementById('dashboardAppointmentSlidePanel');
    modal.classList.remove('hidden');
    setTimeout(() => panel.classList.remove('translate-x-full'), 10);
    document.body.style.overflow = 'hidden';
}
function closeDashboardAppointmentModal() {
    const modal = document.getElementById('dashboardAppointmentModal');
    const panel = document.getElementById('dashboardAppointmentSlidePanel');
    panel.classList.add('translate-x-full');
    setTimeout(() => { modal.classList.add('hidden'); document.body.style.overflow = ''; }, 300);
}

document.getElementById('dashApptPatientSearch')?.addEventListener('input', function() {
    const q = this.value.toLowerCase();
    const select = document.getElementById('dashApptPatientSelect');
    Array.from(select.querySelectorAll('option')).forEach(opt => {
        if (!opt.value) { opt.style.display = 'block'; return; }
        opt.style.display = opt.dataset.search?.includes(q) ? 'block' : 'none';
    });
});

// AJAX patient form
setupAjaxForm('#dashboardPatientForm', 'Patient saved successfully.', closeDashboardPatientModal);
// AJAX appointment form
setupAjaxForm('#dashboardAppointmentForm', 'Appointment booked successfully.', closeDashboardAppointmentModal);

// Doctor filter
function filterVisitsByDoctor() {
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

// Change doctor modal
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

// Change doctor form AJAX
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

// Initialize charts
initCharts();

// Real-time refresh every 60 seconds
setInterval(() => {
    fetch('/dashboard/stats', { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
        .then(r => r.json())
        .then(data => {
            if (data.revenueDays) {
                revenueChart.data.datasets[0].data = data.revenueDays;
                revenueChart.data.labels = data.dayLabels;
                revenueChart.update();
            }
            if (data.visitTrend) {
                visitChart.data.datasets[0].data = data.visitTrend;
                visitChart.data.labels = data.visitLabels;
                visitChart.update();
            }
            if (data.stats) {
                document.querySelectorAll('.card-sm p.text-xl').forEach(el => {
                    const label = el.previousElementSibling?.textContent?.trim();
                    if (label === 'Today\'s Visits') el.textContent = data.stats.visits_today;
                    if (label === 'Weekly Revenue') el.textContent = 'TSh ' + Number(data.stats.revenue_this_week).toLocaleString();
                    if (label === 'Pending Payments') el.textContent = data.stats.pending_payments;
                });
            }
        })
        .catch(() => {});
}, 60000);
</script>
    <div class="bg-white rounded-md border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-200 flex items-center justify-between">
            <div>
                <h3 class="text-sm font-semibold text-gray-900">Today's Schedule</h3>
                <p class="text-xs text-gray-400">{{ today()->format('D, M j') }}</p>
            </div>
            <a href="{{ route('appointments.index') }}" class="text-xs font-medium text-slate-600 hover:text-slate-800">View All</a>
        </div>
        <div class="p-5">
            @forelse($doctorAppointments as $appt)
                <div class="flex items-center gap-3 py-3 {{ !$loop->last ? 'border-b border-gray-50' : '' }}">
                    <div class="w-12 h-12 rounded-md bg-slate-100 text-slate-700 flex flex-col items-center justify-center">
                        <span class="text-[10px] font-bold">{{ date('H:i', strtotime($appt->scheduled_at)) }}</span>
                    </div>
                    <div class="flex-1">
                        <div class="text-sm font-medium text-gray-900">{{ $appt->patient?->name ?? 'Unknown' }}</div>
                        <div class="text-xs text-gray-500">{{ ucfirst($appt->status) }}</div>
                    </div>
                    <span class="inline-flex items-center gap-1.5 text-[10px] font-medium capitalize text-gray-600">
                        <span class="w-1.5 h-1.5 rounded-full {{ $appt->status === 'completed' ? 'bg-emerald-500' : ($appt->status === 'cancelled' ? 'bg-red-500' : 'bg-amber-500') }}"></span>
                        {{ str_replace('_', ' ', $appt->status) }}
                    </span>
                </div>
            @empty
                <div class="text-center py-8 text-gray-400">
                    <i class="hgi-stroke hgi-calendar-03 text-4xl mx-auto text-gray-300 mb-2"></i>
                    <p class="text-xs">No appointments today</p>
                </div>
            @endforelse
        </div>
    </div>

    <script>
const fmt = (n) => 'TSh ' + n.toLocaleString();

const weeklyData = {{ $revenueDays->values() }};
const weeklyLabels = @json($dayLabels);
const monthlyData = {{ $monthlyDays->values() }};
const monthlyLabels = @json($monthLabels);
const visitData = {{ $visitTrend->values() }};
const visitLabels = @json($visitLabels);
const deptLabels = @json($departmentPerformance->pluck('name'));
const deptData = {{ $departmentPerformance->pluck('visits') }};

let revenueChart, visitChart, deptChart;

function initCharts() {
    const revCtx = document.getElementById('revenueChart').getContext('2d');
    const gradient = revCtx.createLinearGradient(0, 0, 0, 300);
    gradient.addColorStop(0, 'rgba(5, 150, 105, 0.08)');
    gradient.addColorStop(1, 'rgba(5, 150, 105, 0)');

    revenueChart = new Chart(revCtx, {
        type: 'line',
        data: {
            labels: weeklyLabels,
            datasets: [{
                label: 'Revenue',
                data: weeklyData,
                borderColor: '#059669',
                backgroundColor: gradient,
                borderWidth: 2,
                pointBackgroundColor: '#ffffff',
                pointBorderColor: '#059669',
                pointBorderWidth: 2,
                pointRadius: 2,
                pointHoverRadius: 4,
                fill: true,
                tension: 0.35
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false }, tooltip: { callbacks: { label: (ctx) => fmt(ctx.raw) } } },
            scales: { y: { beginAtZero: true, grid: { color: '#f1f5f9' }, ticks: { font: { size: 10 } } }, x: { grid: { display: false }, ticks: { font: { size: 10 } } } }
        }
    });

    visitChart = new Chart(document.getElementById('visitTrendChart'), {
        type: 'line',
        data: {
            labels: visitLabels,
            datasets: [{
                label: 'Visits',
                data: visitData,
                borderColor: '#94a3b8',
                backgroundColor: 'rgba(148, 163, 184, 0.08)',
                borderWidth: 2,
                borderDash: [5, 4],
                pointBackgroundColor: '#ffffff',
                pointBorderColor: '#94a3b8',
                pointBorderWidth: 2,
                pointRadius: 2,
                fill: true,
                tension: 0.35
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true, grid: { color: '#f1f5f9' }, ticks: { font: { size: 10 } } }, x: { grid: { display: false }, ticks: { font: { size: 10 } } } }
        }
    });

    deptChart = new Chart(document.getElementById('departmentChart'), {
        type: 'bar',
        data: {
            labels: deptLabels,
            datasets: [{
                label: 'Visits',
                data: deptData,
                backgroundColor: ['#334155', '#94a3b8', '#059669', '#f59e0b', '#cbd5e1', '#dc2626'],
                borderRadius: 6,
                barThickness: 24
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true, grid: { color: '#f1f5f9' }, ticks: { font: { size: 10 } } }, x: { grid: { display: false }, ticks: { font: { size: 10 } } } }
        }
    });
}

function switchRevenue(period) {
    document.getElementById('revWeeklyBtn').className = period === 'weekly'
        ? 'px-3 py-1 text-xs font-medium rounded-md bg-white text-slate-800 shadow-sm'
        : 'px-3 py-1 text-xs font-medium rounded-md text-gray-500 hover:text-gray-700';
    document.getElementById('revMonthlyBtn').className = period === 'monthly'
        ? 'px-3 py-1 text-xs font-medium rounded-md bg-white text-slate-800 shadow-sm'
        : 'px-3 py-1 text-xs font-medium rounded-md text-gray-500 hover:text-gray-700';

    revenueChart.data.labels = period === 'weekly' ? weeklyLabels : monthlyLabels;
    revenueChart.data.datasets[0].data = period === 'weekly' ? weeklyData : monthlyData;
    revenueChart.update();
}

function openDashboardPatientModal() {
    const modal = document.getElementById('dashboardPatientModal');
    const panel = document.getElementById('dashboardPatientSlidePanel');
    modal.classList.remove('hidden');
    setTimeout(() => panel.classList.remove('translate-x-full'), 10);
    document.body.style.overflow = 'hidden';
}
function closeDashboardPatientModal() {
    const modal = document.getElementById('dashboardPatientModal');
    const panel = document.getElementById('dashboardPatientSlidePanel');
    panel.classList.add('translate-x-full');
    setTimeout(() => { modal.classList.add('hidden'); document.body.style.overflow = ''; }, 300);
}

function openDashboardAppointmentModal() {
    const modal = document.getElementById('dashboardAppointmentModal');
    const panel = document.getElementById('dashboardAppointmentSlidePanel');
    modal.classList.remove('hidden');
    setTimeout(() => panel.classList.remove('translate-x-full'), 10);
    document.body.style.overflow = 'hidden';
}
function closeDashboardAppointmentModal() {
    const modal = document.getElementById('dashboardAppointmentModal');
    const panel = document.getElementById('dashboardAppointmentSlidePanel');
    panel.classList.add('translate-x-full');
    setTimeout(() => { modal.classList.add('hidden'); document.body.style.overflow = ''; }, 300);
}

document.getElementById('dashApptPatientSearch')?.addEventListener('input', function() {
    const q = this.value.toLowerCase();
    const select = document.getElementById('dashApptPatientSelect');
    Array.from(select.querySelectorAll('option')).forEach(opt => {
        if (!opt.value) { opt.style.display = 'block'; return; }
        opt.style.display = opt.dataset.search?.includes(q) ? 'block' : 'none';
    });
});

// AJAX patient form
setupAjaxForm('#dashboardPatientForm', 'Patient saved successfully.', closeDashboardPatientModal);
// AJAX appointment form
setupAjaxForm('#dashboardAppointmentForm', 'Appointment booked successfully.', closeDashboardAppointmentModal);

// Doctor filter
function filterVisitsByDoctor() {
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

// Change doctor modal
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

// Change doctor form AJAX
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

// Initialize charts
initCharts();

// Real-time refresh every 60 seconds
setInterval(() => {
    fetch('/dashboard/stats', { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
        .then(r => r.json())
        .then(data => {
            if (data.revenueDays) {
                revenueChart.data.datasets[0].data = data.revenueDays;
                revenueChart.data.labels = data.dayLabels;
                revenueChart.update();
            }
            if (data.visitTrend) {
                visitChart.data.datasets[0].data = data.visitTrend;
                visitChart.data.labels = data.visitLabels;
                visitChart.update();
            }
            if (data.stats) {
                document.querySelectorAll('.card-sm p.text-xl').forEach(el => {
                    const label = el.previousElementSibling?.textContent?.trim();
                    if (label === 'Today\'s Visits') el.textContent = data.stats.visits_today;
                    if (label === 'Weekly Revenue') el.textContent = 'TSh ' + Number(data.stats.revenue_this_week).toLocaleString();
                    if (label === 'Pending Payments') el.textContent = data.stats.pending_payments;
                });
            }
        })
        .catch(() => {});
}, 60000);
</script>
    <div class="bg-white rounded-md border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-200 flex items-center justify-between">
            <div>
                <h3 class="text-sm font-semibold text-gray-900">Recent Patients</h3>
                <p class="text-xs text-gray-400">Last 5 consultations</p>
            </div>
            <a href="{{ route('patients.index') }}" class="text-xs font-medium text-slate-600 hover:text-slate-800">All Patients</a>
        </div>
        <div class="p-5">
            @forelse($recentDoctorPatients as $visit)
                <div class="flex items-center gap-3 py-3 {{ !$loop->last ? 'border-b border-gray-50' : '' }}">
                    <div class="w-10 h-10 rounded-full bg-slate-100 text-slate-600 flex items-center justify-center text-sm font-bold">
                        {{ strtoupper(substr($visit->patient->name ?? 'U', 0, 1)) }}
                    </div>
                    <div class="flex-1">
                        <div class="text-sm font-medium text-gray-900">{{ $visit->patient->fullName() ?? 'Unknown' }}</div>
                        <div class="text-xs text-gray-500">{{ $visit->visit_number }} • {{ $visit->registered_at->format('M j, H:i') }}</div>
                    </div>
                    <span class="inline-flex items-center gap-1.5 text-[10px] font-medium capitalize text-gray-600">
                        <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                        {{ str_replace('_', ' ', $visit->status) }}
                    </span>
                </div>
            @empty
                <div class="text-center py-8 text-gray-400">
                    <i class="hgi-stroke hgi-user-multiple-02 text-4xl mx-auto text-gray-300 mb-2"></i>
                    <p class="text-xs">No recent patients</p>
                </div>
            @endforelse
        </div>
    </div>
</div>

<script>
const fmt = (n) => 'TSh ' + n.toLocaleString();

const weeklyData = {{ $revenueDays->values() }};
const weeklyLabels = @json($dayLabels);
const monthlyData = {{ $monthlyDays->values() }};
const monthlyLabels = @json($monthLabels);
const visitData = {{ $visitTrend->values() }};
const visitLabels = @json($visitLabels);
const deptLabels = @json($departmentPerformance->pluck('name'));
const deptData = {{ $departmentPerformance->pluck('visits') }};

let revenueChart, visitChart, deptChart;

function initCharts() {
    const revCtx = document.getElementById('revenueChart').getContext('2d');
    const gradient = revCtx.createLinearGradient(0, 0, 0, 300);
    gradient.addColorStop(0, 'rgba(5, 150, 105, 0.08)');
    gradient.addColorStop(1, 'rgba(5, 150, 105, 0)');

    revenueChart = new Chart(revCtx, {
        type: 'line',
        data: {
            labels: weeklyLabels,
            datasets: [{
                label: 'Revenue',
                data: weeklyData,
                borderColor: '#059669',
                backgroundColor: gradient,
                borderWidth: 2,
                pointBackgroundColor: '#ffffff',
                pointBorderColor: '#059669',
                pointBorderWidth: 2,
                pointRadius: 2,
                pointHoverRadius: 4,
                fill: true,
                tension: 0.35
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false }, tooltip: { callbacks: { label: (ctx) => fmt(ctx.raw) } } },
            scales: { y: { beginAtZero: true, grid: { color: '#f1f5f9' }, ticks: { font: { size: 10 } } }, x: { grid: { display: false }, ticks: { font: { size: 10 } } } }
        }
    });

    visitChart = new Chart(document.getElementById('visitTrendChart'), {
        type: 'line',
        data: {
            labels: visitLabels,
            datasets: [{
                label: 'Visits',
                data: visitData,
                borderColor: '#94a3b8',
                backgroundColor: 'rgba(148, 163, 184, 0.08)',
                borderWidth: 2,
                borderDash: [5, 4],
                pointBackgroundColor: '#ffffff',
                pointBorderColor: '#94a3b8',
                pointBorderWidth: 2,
                pointRadius: 2,
                fill: true,
                tension: 0.35
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true, grid: { color: '#f1f5f9' }, ticks: { font: { size: 10 } } }, x: { grid: { display: false }, ticks: { font: { size: 10 } } } }
        }
    });

    deptChart = new Chart(document.getElementById('departmentChart'), {
        type: 'bar',
        data: {
            labels: deptLabels,
            datasets: [{
                label: 'Visits',
                data: deptData,
                backgroundColor: ['#334155', '#94a3b8', '#059669', '#f59e0b', '#cbd5e1', '#dc2626'],
                borderRadius: 6,
                barThickness: 24
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true, grid: { color: '#f1f5f9' }, ticks: { font: { size: 10 } } }, x: { grid: { display: false }, ticks: { font: { size: 10 } } } }
        }
    });
}

function switchRevenue(period) {
    document.getElementById('revWeeklyBtn').className = period === 'weekly'
        ? 'px-3 py-1 text-xs font-medium rounded-md bg-white text-slate-800 shadow-sm'
        : 'px-3 py-1 text-xs font-medium rounded-md text-gray-500 hover:text-gray-700';
    document.getElementById('revMonthlyBtn').className = period === 'monthly'
        ? 'px-3 py-1 text-xs font-medium rounded-md bg-white text-slate-800 shadow-sm'
        : 'px-3 py-1 text-xs font-medium rounded-md text-gray-500 hover:text-gray-700';

    revenueChart.data.labels = period === 'weekly' ? weeklyLabels : monthlyLabels;
    revenueChart.data.datasets[0].data = period === 'weekly' ? weeklyData : monthlyData;
    revenueChart.update();
}

function openDashboardPatientModal() {
    const modal = document.getElementById('dashboardPatientModal');
    const panel = document.getElementById('dashboardPatientSlidePanel');
    modal.classList.remove('hidden');
    setTimeout(() => panel.classList.remove('translate-x-full'), 10);
    document.body.style.overflow = 'hidden';
}
function closeDashboardPatientModal() {
    const modal = document.getElementById('dashboardPatientModal');
    const panel = document.getElementById('dashboardPatientSlidePanel');
    panel.classList.add('translate-x-full');
    setTimeout(() => { modal.classList.add('hidden'); document.body.style.overflow = ''; }, 300);
}

function openDashboardAppointmentModal() {
    const modal = document.getElementById('dashboardAppointmentModal');
    const panel = document.getElementById('dashboardAppointmentSlidePanel');
    modal.classList.remove('hidden');
    setTimeout(() => panel.classList.remove('translate-x-full'), 10);
    document.body.style.overflow = 'hidden';
}
function closeDashboardAppointmentModal() {
    const modal = document.getElementById('dashboardAppointmentModal');
    const panel = document.getElementById('dashboardAppointmentSlidePanel');
    panel.classList.add('translate-x-full');
    setTimeout(() => { modal.classList.add('hidden'); document.body.style.overflow = ''; }, 300);
}

document.getElementById('dashApptPatientSearch')?.addEventListener('input', function() {
    const q = this.value.toLowerCase();
    const select = document.getElementById('dashApptPatientSelect');
    Array.from(select.querySelectorAll('option')).forEach(opt => {
        if (!opt.value) { opt.style.display = 'block'; return; }
        opt.style.display = opt.dataset.search?.includes(q) ? 'block' : 'none';
    });
});

// AJAX patient form
setupAjaxForm('#dashboardPatientForm', 'Patient saved successfully.', closeDashboardPatientModal);
// AJAX appointment form
setupAjaxForm('#dashboardAppointmentForm', 'Appointment booked successfully.', closeDashboardAppointmentModal);

// Doctor filter
function filterVisitsByDoctor() {
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

// Change doctor modal
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

// Change doctor form AJAX
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

// Initialize charts
initCharts();

// Real-time refresh every 60 seconds
setInterval(() => {
    fetch('/dashboard/stats', { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
        .then(r => r.json())
        .then(data => {
            if (data.revenueDays) {
                revenueChart.data.datasets[0].data = data.revenueDays;
                revenueChart.data.labels = data.dayLabels;
                revenueChart.update();
            }
            if (data.visitTrend) {
                visitChart.data.datasets[0].data = data.visitTrend;
                visitChart.data.labels = data.visitLabels;
                visitChart.update();
            }
            if (data.stats) {
                document.querySelectorAll('.card-sm p.text-xl').forEach(el => {
                    const label = el.previousElementSibling?.textContent?.trim();
                    if (label === 'Today\'s Visits') el.textContent = data.stats.visits_today;
                    if (label === 'Weekly Revenue') el.textContent = 'TSh ' + Number(data.stats.revenue_this_week).toLocaleString();
                    if (label === 'Pending Payments') el.textContent = data.stats.pending_payments;
                });
            }
        })
        .catch(() => {});
}, 60000);
</script>
<div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-5">
    <a href="{{ route('doctor.queue') }}" class="flex flex-col items-center gap-2 p-4 bg-white rounded-md border border-gray-200 hover:border-gray-300 transition-colors">
        <div class="w-9 h-9 rounded-md bg-gray-100 text-slate-600 flex items-center justify-center">
            <i class="hgi-stroke hgi-building-03 text-base"></i>
        </div>
        <span class="text-xs font-medium text-gray-700">My Queue</span>
    </a>
    <a href="{{ route('appointments.index') }}" class="flex flex-col items-center gap-2 p-4 bg-white rounded-md border border-gray-200 hover:border-gray-300 transition-colors">
        <div class="w-9 h-9 rounded-md bg-gray-100 text-slate-600 flex items-center justify-center">
            <i class="hgi-stroke hgi-calendar-03 text-base"></i>
        </div>
        <span class="text-xs font-medium text-gray-700">Appointments</span>
    </a>
    <a href="{{ route('patients.index') }}" class="flex flex-col items-center gap-2 p-4 bg-white rounded-md border border-gray-200 hover:border-gray-300 transition-colors">
        <div class="w-9 h-9 rounded-md bg-gray-100 text-slate-600 flex items-center justify-center">
            <i class="hgi-stroke hgi-user-multiple-02 text-base"></i>
        </div>
        <span class="text-xs font-medium text-gray-700">Patients</span>
    </a>
    <a href="{{ route('clinical-records.index') }}" class="flex flex-col items-center gap-2 p-4 bg-white rounded-md border border-gray-200 hover:border-gray-300 transition-colors">
        <div class="w-9 h-9 rounded-md bg-gray-100 text-slate-600 flex items-center justify-center">
            <i class="hgi-stroke hgi-file-02 text-base"></i>
        </div>
        <span class="text-xs font-medium text-gray-700">Records</span>
    </a>
</div>
@endif

<script>
const fmt = (n) => 'TSh ' + n.toLocaleString();

const weeklyData = {{ $revenueDays->values() }};
const weeklyLabels = @json($dayLabels);
const monthlyData = {{ $monthlyDays->values() }};
const monthlyLabels = @json($monthLabels);
const visitData = {{ $visitTrend->values() }};
const visitLabels = @json($visitLabels);
const deptLabels = @json($departmentPerformance->pluck('name'));
const deptData = {{ $departmentPerformance->pluck('visits') }};

let revenueChart, visitChart, deptChart;

function initCharts() {
    const revCtx = document.getElementById('revenueChart').getContext('2d');
    const gradient = revCtx.createLinearGradient(0, 0, 0, 300);
    gradient.addColorStop(0, 'rgba(5, 150, 105, 0.08)');
    gradient.addColorStop(1, 'rgba(5, 150, 105, 0)');

    revenueChart = new Chart(revCtx, {
        type: 'line',
        data: {
            labels: weeklyLabels,
            datasets: [{
                label: 'Revenue',
                data: weeklyData,
                borderColor: '#059669',
                backgroundColor: gradient,
                borderWidth: 2,
                pointBackgroundColor: '#ffffff',
                pointBorderColor: '#059669',
                pointBorderWidth: 2,
                pointRadius: 2,
                pointHoverRadius: 4,
                fill: true,
                tension: 0.35
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false }, tooltip: { callbacks: { label: (ctx) => fmt(ctx.raw) } } },
            scales: { y: { beginAtZero: true, grid: { color: '#f1f5f9' }, ticks: { font: { size: 10 } } }, x: { grid: { display: false }, ticks: { font: { size: 10 } } } }
        }
    });

    visitChart = new Chart(document.getElementById('visitTrendChart'), {
        type: 'line',
        data: {
            labels: visitLabels,
            datasets: [{
                label: 'Visits',
                data: visitData,
                borderColor: '#94a3b8',
                backgroundColor: 'rgba(148, 163, 184, 0.08)',
                borderWidth: 2,
                borderDash: [5, 4],
                pointBackgroundColor: '#ffffff',
                pointBorderColor: '#94a3b8',
                pointBorderWidth: 2,
                pointRadius: 2,
                fill: true,
                tension: 0.35
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true, grid: { color: '#f1f5f9' }, ticks: { font: { size: 10 } } }, x: { grid: { display: false }, ticks: { font: { size: 10 } } } }
        }
    });

    deptChart = new Chart(document.getElementById('departmentChart'), {
        type: 'bar',
        data: {
            labels: deptLabels,
            datasets: [{
                label: 'Visits',
                data: deptData,
                backgroundColor: ['#334155', '#94a3b8', '#059669', '#f59e0b', '#cbd5e1', '#dc2626'],
                borderRadius: 6,
                barThickness: 24
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true, grid: { color: '#f1f5f9' }, ticks: { font: { size: 10 } } }, x: { grid: { display: false }, ticks: { font: { size: 10 } } } }
        }
    });
}

function switchRevenue(period) {
    document.getElementById('revWeeklyBtn').className = period === 'weekly'
        ? 'px-3 py-1 text-xs font-medium rounded-md bg-white text-slate-800 shadow-sm'
        : 'px-3 py-1 text-xs font-medium rounded-md text-gray-500 hover:text-gray-700';
    document.getElementById('revMonthlyBtn').className = period === 'monthly'
        ? 'px-3 py-1 text-xs font-medium rounded-md bg-white text-slate-800 shadow-sm'
        : 'px-3 py-1 text-xs font-medium rounded-md text-gray-500 hover:text-gray-700';

    revenueChart.data.labels = period === 'weekly' ? weeklyLabels : monthlyLabels;
    revenueChart.data.datasets[0].data = period === 'weekly' ? weeklyData : monthlyData;
    revenueChart.update();
}

function openDashboardPatientModal() {
    const modal = document.getElementById('dashboardPatientModal');
    const panel = document.getElementById('dashboardPatientSlidePanel');
    modal.classList.remove('hidden');
    setTimeout(() => panel.classList.remove('translate-x-full'), 10);
    document.body.style.overflow = 'hidden';
}
function closeDashboardPatientModal() {
    const modal = document.getElementById('dashboardPatientModal');
    const panel = document.getElementById('dashboardPatientSlidePanel');
    panel.classList.add('translate-x-full');
    setTimeout(() => { modal.classList.add('hidden'); document.body.style.overflow = ''; }, 300);
}

function openDashboardAppointmentModal() {
    const modal = document.getElementById('dashboardAppointmentModal');
    const panel = document.getElementById('dashboardAppointmentSlidePanel');
    modal.classList.remove('hidden');
    setTimeout(() => panel.classList.remove('translate-x-full'), 10);
    document.body.style.overflow = 'hidden';
}
function closeDashboardAppointmentModal() {
    const modal = document.getElementById('dashboardAppointmentModal');
    const panel = document.getElementById('dashboardAppointmentSlidePanel');
    panel.classList.add('translate-x-full');
    setTimeout(() => { modal.classList.add('hidden'); document.body.style.overflow = ''; }, 300);
}

document.getElementById('dashApptPatientSearch')?.addEventListener('input', function() {
    const q = this.value.toLowerCase();
    const select = document.getElementById('dashApptPatientSelect');
    Array.from(select.querySelectorAll('option')).forEach(opt => {
        if (!opt.value) { opt.style.display = 'block'; return; }
        opt.style.display = opt.dataset.search?.includes(q) ? 'block' : 'none';
    });
});

// AJAX patient form
setupAjaxForm('#dashboardPatientForm', 'Patient saved successfully.', closeDashboardPatientModal);
// AJAX appointment form
setupAjaxForm('#dashboardAppointmentForm', 'Appointment booked successfully.', closeDashboardAppointmentModal);

// Doctor filter
function filterVisitsByDoctor() {
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

// Change doctor modal
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

// Change doctor form AJAX
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

// Initialize charts
initCharts();

// Real-time refresh every 60 seconds
setInterval(() => {
    fetch('/dashboard/stats', { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
        .then(r => r.json())
        .then(data => {
            if (data.revenueDays) {
                revenueChart.data.datasets[0].data = data.revenueDays;
                revenueChart.data.labels = data.dayLabels;
                revenueChart.update();
            }
            if (data.visitTrend) {
                visitChart.data.datasets[0].data = data.visitTrend;
                visitChart.data.labels = data.visitLabels;
                visitChart.update();
            }
            if (data.stats) {
                document.querySelectorAll('.card-sm p.text-xl').forEach(el => {
                    const label = el.previousElementSibling?.textContent?.trim();
                    if (label === 'Today\'s Visits') el.textContent = data.stats.visits_today;
                    if (label === 'Weekly Revenue') el.textContent = 'TSh ' + Number(data.stats.revenue_this_week).toLocaleString();
                    if (label === 'Pending Payments') el.textContent = data.stats.pending_payments;
                });
            }
        })
        .catch(() => {});
}, 60000);
</script>
@if(!$user->isDoctor())
<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-5">
    @foreach([
        ['label'=>'Total Patients','value'=>number_format($stats['total_patients']),'change'=>'+'.$stats['total_visits'].' visits','link'=>null,'icon' => 'hgi-user-multiple-02'],
        ['label'=>'Today\'s Visits','value'=>$stats['visits_today'],'change'=>$todayAppointments->count().' appts today','link'=>null,'icon' => 'hgi-calendar-03'],
        ['label'=>'Weekly Revenue','value'=>$fmt($stats['revenue_this_week']),'change'=>'Today: '.$fmt($stats['revenue_today']),'link'=>route('invoices.index'),'icon' => 'hgi-money-03'],
        ['label'=>'Pending Payments','value'=>$stats['pending_payments'],'change'=>$stats['waiting_pharmacy'].' pharmacy','link'=>route('reception.dashboard'),'icon' => 'hgi-money-03']
    ] as $card)
    <a href="{{ $card['link'] ?? '#' }}" class="card-sm block bg-white rounded-md border border-gray-200 p-4">
        <div class="flex items-start justify-between">
            <p class="text-[10px] font-medium uppercase tracking-wide text-gray-500">{{ $card['label'] }}</p>
            <i class="hgi-stroke {{ $card['icon'] }} text-base text-gray-400"></i>
        </div>
        <p class="text-2xl font-bold text-gray-900 mt-1">{{ $card['value'] }}</p>
        <p class="text-[10px] text-gray-400 mt-0.5">{{ $card['change'] }}</p>
    </a>
    @endforeach
</div>

<script>
const fmt = (n) => 'TSh ' + n.toLocaleString();

const weeklyData = {{ $revenueDays->values() }};
const weeklyLabels = @json($dayLabels);
const monthlyData = {{ $monthlyDays->values() }};
const monthlyLabels = @json($monthLabels);
const visitData = {{ $visitTrend->values() }};
const visitLabels = @json($visitLabels);
const deptLabels = @json($departmentPerformance->pluck('name'));
const deptData = {{ $departmentPerformance->pluck('visits') }};

let revenueChart, visitChart, deptChart;

function initCharts() {
    const revCtx = document.getElementById('revenueChart').getContext('2d');
    const gradient = revCtx.createLinearGradient(0, 0, 0, 300);
    gradient.addColorStop(0, 'rgba(5, 150, 105, 0.08)');
    gradient.addColorStop(1, 'rgba(5, 150, 105, 0)');

    revenueChart = new Chart(revCtx, {
        type: 'line',
        data: {
            labels: weeklyLabels,
            datasets: [{
                label: 'Revenue',
                data: weeklyData,
                borderColor: '#059669',
                backgroundColor: gradient,
                borderWidth: 2,
                pointBackgroundColor: '#ffffff',
                pointBorderColor: '#059669',
                pointBorderWidth: 2,
                pointRadius: 2,
                pointHoverRadius: 4,
                fill: true,
                tension: 0.35
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false }, tooltip: { callbacks: { label: (ctx) => fmt(ctx.raw) } } },
            scales: { y: { beginAtZero: true, grid: { color: '#f1f5f9' }, ticks: { font: { size: 10 } } }, x: { grid: { display: false }, ticks: { font: { size: 10 } } } }
        }
    });

    visitChart = new Chart(document.getElementById('visitTrendChart'), {
        type: 'line',
        data: {
            labels: visitLabels,
            datasets: [{
                label: 'Visits',
                data: visitData,
                borderColor: '#94a3b8',
                backgroundColor: 'rgba(148, 163, 184, 0.08)',
                borderWidth: 2,
                borderDash: [5, 4],
                pointBackgroundColor: '#ffffff',
                pointBorderColor: '#94a3b8',
                pointBorderWidth: 2,
                pointRadius: 2,
                fill: true,
                tension: 0.35
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true, grid: { color: '#f1f5f9' }, ticks: { font: { size: 10 } } }, x: { grid: { display: false }, ticks: { font: { size: 10 } } } }
        }
    });

    deptChart = new Chart(document.getElementById('departmentChart'), {
        type: 'bar',
        data: {
            labels: deptLabels,
            datasets: [{
                label: 'Visits',
                data: deptData,
                backgroundColor: ['#334155', '#94a3b8', '#059669', '#f59e0b', '#cbd5e1', '#dc2626'],
                borderRadius: 6,
                barThickness: 24
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true, grid: { color: '#f1f5f9' }, ticks: { font: { size: 10 } } }, x: { grid: { display: false }, ticks: { font: { size: 10 } } } }
        }
    });
}

function switchRevenue(period) {
    document.getElementById('revWeeklyBtn').className = period === 'weekly'
        ? 'px-3 py-1 text-xs font-medium rounded-md bg-white text-slate-800 shadow-sm'
        : 'px-3 py-1 text-xs font-medium rounded-md text-gray-500 hover:text-gray-700';
    document.getElementById('revMonthlyBtn').className = period === 'monthly'
        ? 'px-3 py-1 text-xs font-medium rounded-md bg-white text-slate-800 shadow-sm'
        : 'px-3 py-1 text-xs font-medium rounded-md text-gray-500 hover:text-gray-700';

    revenueChart.data.labels = period === 'weekly' ? weeklyLabels : monthlyLabels;
    revenueChart.data.datasets[0].data = period === 'weekly' ? weeklyData : monthlyData;
    revenueChart.update();
}

function openDashboardPatientModal() {
    const modal = document.getElementById('dashboardPatientModal');
    const panel = document.getElementById('dashboardPatientSlidePanel');
    modal.classList.remove('hidden');
    setTimeout(() => panel.classList.remove('translate-x-full'), 10);
    document.body.style.overflow = 'hidden';
}
function closeDashboardPatientModal() {
    const modal = document.getElementById('dashboardPatientModal');
    const panel = document.getElementById('dashboardPatientSlidePanel');
    panel.classList.add('translate-x-full');
    setTimeout(() => { modal.classList.add('hidden'); document.body.style.overflow = ''; }, 300);
}

function openDashboardAppointmentModal() {
    const modal = document.getElementById('dashboardAppointmentModal');
    const panel = document.getElementById('dashboardAppointmentSlidePanel');
    modal.classList.remove('hidden');
    setTimeout(() => panel.classList.remove('translate-x-full'), 10);
    document.body.style.overflow = 'hidden';
}
function closeDashboardAppointmentModal() {
    const modal = document.getElementById('dashboardAppointmentModal');
    const panel = document.getElementById('dashboardAppointmentSlidePanel');
    panel.classList.add('translate-x-full');
    setTimeout(() => { modal.classList.add('hidden'); document.body.style.overflow = ''; }, 300);
}

document.getElementById('dashApptPatientSearch')?.addEventListener('input', function() {
    const q = this.value.toLowerCase();
    const select = document.getElementById('dashApptPatientSelect');
    Array.from(select.querySelectorAll('option')).forEach(opt => {
        if (!opt.value) { opt.style.display = 'block'; return; }
        opt.style.display = opt.dataset.search?.includes(q) ? 'block' : 'none';
    });
});

// AJAX patient form
setupAjaxForm('#dashboardPatientForm', 'Patient saved successfully.', closeDashboardPatientModal);
// AJAX appointment form
setupAjaxForm('#dashboardAppointmentForm', 'Appointment booked successfully.', closeDashboardAppointmentModal);

// Doctor filter
function filterVisitsByDoctor() {
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

// Change doctor modal
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

// Change doctor form AJAX
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

// Initialize charts
initCharts();

// Real-time refresh every 60 seconds
setInterval(() => {
    fetch('/dashboard/stats', { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
        .then(r => r.json())
        .then(data => {
            if (data.revenueDays) {
                revenueChart.data.datasets[0].data = data.revenueDays;
                revenueChart.data.labels = data.dayLabels;
                revenueChart.update();
            }
            if (data.visitTrend) {
                visitChart.data.datasets[0].data = data.visitTrend;
                visitChart.data.labels = data.visitLabels;
                visitChart.update();
            }
            if (data.stats) {
                document.querySelectorAll('.card-sm p.text-xl').forEach(el => {
                    const label = el.previousElementSibling?.textContent?.trim();
                    if (label === 'Today\'s Visits') el.textContent = data.stats.visits_today;
                    if (label === 'Weekly Revenue') el.textContent = 'TSh ' + Number(data.stats.revenue_this_week).toLocaleString();
                    if (label === 'Pending Payments') el.textContent = data.stats.pending_payments;
                });
            }
        })
        .catch(() => {});
}, 60000);
</script>
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
    <script>
const fmt = (n) => 'TSh ' + n.toLocaleString();

const weeklyData = {{ $revenueDays->values() }};
const weeklyLabels = @json($dayLabels);
const monthlyData = {{ $monthlyDays->values() }};
const monthlyLabels = @json($monthLabels);
const visitData = {{ $visitTrend->values() }};
const visitLabels = @json($visitLabels);
const deptLabels = @json($departmentPerformance->pluck('name'));
const deptData = {{ $departmentPerformance->pluck('visits') }};

let revenueChart, visitChart, deptChart;

function initCharts() {
    const revCtx = document.getElementById('revenueChart').getContext('2d');
    const gradient = revCtx.createLinearGradient(0, 0, 0, 300);
    gradient.addColorStop(0, 'rgba(5, 150, 105, 0.08)');
    gradient.addColorStop(1, 'rgba(5, 150, 105, 0)');

    revenueChart = new Chart(revCtx, {
        type: 'line',
        data: {
            labels: weeklyLabels,
            datasets: [{
                label: 'Revenue',
                data: weeklyData,
                borderColor: '#059669',
                backgroundColor: gradient,
                borderWidth: 2,
                pointBackgroundColor: '#ffffff',
                pointBorderColor: '#059669',
                pointBorderWidth: 2,
                pointRadius: 2,
                pointHoverRadius: 4,
                fill: true,
                tension: 0.35
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false }, tooltip: { callbacks: { label: (ctx) => fmt(ctx.raw) } } },
            scales: { y: { beginAtZero: true, grid: { color: '#f1f5f9' }, ticks: { font: { size: 10 } } }, x: { grid: { display: false }, ticks: { font: { size: 10 } } } }
        }
    });

    visitChart = new Chart(document.getElementById('visitTrendChart'), {
        type: 'line',
        data: {
            labels: visitLabels,
            datasets: [{
                label: 'Visits',
                data: visitData,
                borderColor: '#94a3b8',
                backgroundColor: 'rgba(148, 163, 184, 0.08)',
                borderWidth: 2,
                borderDash: [5, 4],
                pointBackgroundColor: '#ffffff',
                pointBorderColor: '#94a3b8',
                pointBorderWidth: 2,
                pointRadius: 2,
                fill: true,
                tension: 0.35
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true, grid: { color: '#f1f5f9' }, ticks: { font: { size: 10 } } }, x: { grid: { display: false }, ticks: { font: { size: 10 } } } }
        }
    });

    deptChart = new Chart(document.getElementById('departmentChart'), {
        type: 'bar',
        data: {
            labels: deptLabels,
            datasets: [{
                label: 'Visits',
                data: deptData,
                backgroundColor: ['#334155', '#94a3b8', '#059669', '#f59e0b', '#cbd5e1', '#dc2626'],
                borderRadius: 6,
                barThickness: 24
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true, grid: { color: '#f1f5f9' }, ticks: { font: { size: 10 } } }, x: { grid: { display: false }, ticks: { font: { size: 10 } } } }
        }
    });
}

function switchRevenue(period) {
    document.getElementById('revWeeklyBtn').className = period === 'weekly'
        ? 'px-3 py-1 text-xs font-medium rounded-md bg-white text-slate-800 shadow-sm'
        : 'px-3 py-1 text-xs font-medium rounded-md text-gray-500 hover:text-gray-700';
    document.getElementById('revMonthlyBtn').className = period === 'monthly'
        ? 'px-3 py-1 text-xs font-medium rounded-md bg-white text-slate-800 shadow-sm'
        : 'px-3 py-1 text-xs font-medium rounded-md text-gray-500 hover:text-gray-700';

    revenueChart.data.labels = period === 'weekly' ? weeklyLabels : monthlyLabels;
    revenueChart.data.datasets[0].data = period === 'weekly' ? weeklyData : monthlyData;
    revenueChart.update();
}

function openDashboardPatientModal() {
    const modal = document.getElementById('dashboardPatientModal');
    const panel = document.getElementById('dashboardPatientSlidePanel');
    modal.classList.remove('hidden');
    setTimeout(() => panel.classList.remove('translate-x-full'), 10);
    document.body.style.overflow = 'hidden';
}
function closeDashboardPatientModal() {
    const modal = document.getElementById('dashboardPatientModal');
    const panel = document.getElementById('dashboardPatientSlidePanel');
    panel.classList.add('translate-x-full');
    setTimeout(() => { modal.classList.add('hidden'); document.body.style.overflow = ''; }, 300);
}

function openDashboardAppointmentModal() {
    const modal = document.getElementById('dashboardAppointmentModal');
    const panel = document.getElementById('dashboardAppointmentSlidePanel');
    modal.classList.remove('hidden');
    setTimeout(() => panel.classList.remove('translate-x-full'), 10);
    document.body.style.overflow = 'hidden';
}
function closeDashboardAppointmentModal() {
    const modal = document.getElementById('dashboardAppointmentModal');
    const panel = document.getElementById('dashboardAppointmentSlidePanel');
    panel.classList.add('translate-x-full');
    setTimeout(() => { modal.classList.add('hidden'); document.body.style.overflow = ''; }, 300);
}

document.getElementById('dashApptPatientSearch')?.addEventListener('input', function() {
    const q = this.value.toLowerCase();
    const select = document.getElementById('dashApptPatientSelect');
    Array.from(select.querySelectorAll('option')).forEach(opt => {
        if (!opt.value) { opt.style.display = 'block'; return; }
        opt.style.display = opt.dataset.search?.includes(q) ? 'block' : 'none';
    });
});

// AJAX patient form
setupAjaxForm('#dashboardPatientForm', 'Patient saved successfully.', closeDashboardPatientModal);
// AJAX appointment form
setupAjaxForm('#dashboardAppointmentForm', 'Appointment booked successfully.', closeDashboardAppointmentModal);

// Doctor filter
function filterVisitsByDoctor() {
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

// Change doctor modal
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

// Change doctor form AJAX
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

// Initialize charts
initCharts();

// Real-time refresh every 60 seconds
setInterval(() => {
    fetch('/dashboard/stats', { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
        .then(r => r.json())
        .then(data => {
            if (data.revenueDays) {
                revenueChart.data.datasets[0].data = data.revenueDays;
                revenueChart.data.labels = data.dayLabels;
                revenueChart.update();
            }
            if (data.visitTrend) {
                visitChart.data.datasets[0].data = data.visitTrend;
                visitChart.data.labels = data.visitLabels;
                visitChart.update();
            }
            if (data.stats) {
                document.querySelectorAll('.card-sm p.text-xl').forEach(el => {
                    const label = el.previousElementSibling?.textContent?.trim();
                    if (label === 'Today\'s Visits') el.textContent = data.stats.visits_today;
                    if (label === 'Weekly Revenue') el.textContent = 'TSh ' + Number(data.stats.revenue_this_week).toLocaleString();
                    if (label === 'Pending Payments') el.textContent = data.stats.pending_payments;
                });
            }
        })
        .catch(() => {});
}, 60000);
</script>
    <div class="lg:col-span-2 bg-white rounded-md border border-gray-200 p-5 shadow-sm">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="text-sm font-semibold text-gray-900">Revenue Trend</h3>
                <p class="text-xs text-gray-400">Live financial overview</p>
            </div>
            <div class="flex items-center gap-3">
                <div class="text-right">
                    <div class="text-lg font-semibold text-gray-900">{{ $fmt($stats['revenue_this_week']) }}</div>
                    <div class="text-xs text-gray-500 font-medium">This Week</div>
                </div>
                <div class="flex bg-gray-100 rounded-lg p-0.5">
                    <button type="button" onclick="switchRevenue('weekly')" id="revWeeklyBtn" class="px-3 py-1 text-xs font-medium rounded-md bg-white text-slate-800 shadow-sm">7 Days</button>
                    <button type="button" onclick="switchRevenue('monthly')" id="revMonthlyBtn" class="px-3 py-1 text-xs font-medium rounded-md text-gray-500 hover:text-gray-700">30 Days</button>
                </div>
            </div>
        </div>
        <div class="relative h-64">
            <canvas id="revenueChart"></canvas>
        </div>
    </div>

    <script>
const fmt = (n) => 'TSh ' + n.toLocaleString();

const weeklyData = {{ $revenueDays->values() }};
const weeklyLabels = @json($dayLabels);
const monthlyData = {{ $monthlyDays->values() }};
const monthlyLabels = @json($monthLabels);
const visitData = {{ $visitTrend->values() }};
const visitLabels = @json($visitLabels);
const deptLabels = @json($departmentPerformance->pluck('name'));
const deptData = {{ $departmentPerformance->pluck('visits') }};

let revenueChart, visitChart, deptChart;

function initCharts() {
    const revCtx = document.getElementById('revenueChart').getContext('2d');
    const gradient = revCtx.createLinearGradient(0, 0, 0, 300);
    gradient.addColorStop(0, 'rgba(5, 150, 105, 0.08)');
    gradient.addColorStop(1, 'rgba(5, 150, 105, 0)');

    revenueChart = new Chart(revCtx, {
        type: 'line',
        data: {
            labels: weeklyLabels,
            datasets: [{
                label: 'Revenue',
                data: weeklyData,
                borderColor: '#059669',
                backgroundColor: gradient,
                borderWidth: 2,
                pointBackgroundColor: '#ffffff',
                pointBorderColor: '#059669',
                pointBorderWidth: 2,
                pointRadius: 2,
                pointHoverRadius: 4,
                fill: true,
                tension: 0.35
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false }, tooltip: { callbacks: { label: (ctx) => fmt(ctx.raw) } } },
            scales: { y: { beginAtZero: true, grid: { color: '#f1f5f9' }, ticks: { font: { size: 10 } } }, x: { grid: { display: false }, ticks: { font: { size: 10 } } } }
        }
    });

    visitChart = new Chart(document.getElementById('visitTrendChart'), {
        type: 'line',
        data: {
            labels: visitLabels,
            datasets: [{
                label: 'Visits',
                data: visitData,
                borderColor: '#94a3b8',
                backgroundColor: 'rgba(148, 163, 184, 0.08)',
                borderWidth: 2,
                borderDash: [5, 4],
                pointBackgroundColor: '#ffffff',
                pointBorderColor: '#94a3b8',
                pointBorderWidth: 2,
                pointRadius: 2,
                fill: true,
                tension: 0.35
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true, grid: { color: '#f1f5f9' }, ticks: { font: { size: 10 } } }, x: { grid: { display: false }, ticks: { font: { size: 10 } } } }
        }
    });

    deptChart = new Chart(document.getElementById('departmentChart'), {
        type: 'bar',
        data: {
            labels: deptLabels,
            datasets: [{
                label: 'Visits',
                data: deptData,
                backgroundColor: ['#334155', '#94a3b8', '#059669', '#f59e0b', '#cbd5e1', '#dc2626'],
                borderRadius: 6,
                barThickness: 24
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true, grid: { color: '#f1f5f9' }, ticks: { font: { size: 10 } } }, x: { grid: { display: false }, ticks: { font: { size: 10 } } } }
        }
    });
}

function switchRevenue(period) {
    document.getElementById('revWeeklyBtn').className = period === 'weekly'
        ? 'px-3 py-1 text-xs font-medium rounded-md bg-white text-slate-800 shadow-sm'
        : 'px-3 py-1 text-xs font-medium rounded-md text-gray-500 hover:text-gray-700';
    document.getElementById('revMonthlyBtn').className = period === 'monthly'
        ? 'px-3 py-1 text-xs font-medium rounded-md bg-white text-slate-800 shadow-sm'
        : 'px-3 py-1 text-xs font-medium rounded-md text-gray-500 hover:text-gray-700';

    revenueChart.data.labels = period === 'weekly' ? weeklyLabels : monthlyLabels;
    revenueChart.data.datasets[0].data = period === 'weekly' ? weeklyData : monthlyData;
    revenueChart.update();
}

function openDashboardPatientModal() {
    const modal = document.getElementById('dashboardPatientModal');
    const panel = document.getElementById('dashboardPatientSlidePanel');
    modal.classList.remove('hidden');
    setTimeout(() => panel.classList.remove('translate-x-full'), 10);
    document.body.style.overflow = 'hidden';
}
function closeDashboardPatientModal() {
    const modal = document.getElementById('dashboardPatientModal');
    const panel = document.getElementById('dashboardPatientSlidePanel');
    panel.classList.add('translate-x-full');
    setTimeout(() => { modal.classList.add('hidden'); document.body.style.overflow = ''; }, 300);
}

function openDashboardAppointmentModal() {
    const modal = document.getElementById('dashboardAppointmentModal');
    const panel = document.getElementById('dashboardAppointmentSlidePanel');
    modal.classList.remove('hidden');
    setTimeout(() => panel.classList.remove('translate-x-full'), 10);
    document.body.style.overflow = 'hidden';
}
function closeDashboardAppointmentModal() {
    const modal = document.getElementById('dashboardAppointmentModal');
    const panel = document.getElementById('dashboardAppointmentSlidePanel');
    panel.classList.add('translate-x-full');
    setTimeout(() => { modal.classList.add('hidden'); document.body.style.overflow = ''; }, 300);
}

document.getElementById('dashApptPatientSearch')?.addEventListener('input', function() {
    const q = this.value.toLowerCase();
    const select = document.getElementById('dashApptPatientSelect');
    Array.from(select.querySelectorAll('option')).forEach(opt => {
        if (!opt.value) { opt.style.display = 'block'; return; }
        opt.style.display = opt.dataset.search?.includes(q) ? 'block' : 'none';
    });
});

// AJAX patient form
setupAjaxForm('#dashboardPatientForm', 'Patient saved successfully.', closeDashboardPatientModal);
// AJAX appointment form
setupAjaxForm('#dashboardAppointmentForm', 'Appointment booked successfully.', closeDashboardAppointmentModal);

// Doctor filter
function filterVisitsByDoctor() {
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

// Change doctor modal
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

// Change doctor form AJAX
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

// Initialize charts
initCharts();

// Real-time refresh every 60 seconds
setInterval(() => {
    fetch('/dashboard/stats', { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
        .then(r => r.json())
        .then(data => {
            if (data.revenueDays) {
                revenueChart.data.datasets[0].data = data.revenueDays;
                revenueChart.data.labels = data.dayLabels;
                revenueChart.update();
            }
            if (data.visitTrend) {
                visitChart.data.datasets[0].data = data.visitTrend;
                visitChart.data.labels = data.visitLabels;
                visitChart.update();
            }
            if (data.stats) {
                document.querySelectorAll('.card-sm p.text-xl').forEach(el => {
                    const label = el.previousElementSibling?.textContent?.trim();
                    if (label === 'Today\'s Visits') el.textContent = data.stats.visits_today;
                    if (label === 'Weekly Revenue') el.textContent = 'TSh ' + Number(data.stats.revenue_this_week).toLocaleString();
                    if (label === 'Pending Payments') el.textContent = data.stats.pending_payments;
                });
            }
        })
        .catch(() => {});
}, 60000);
</script>
    <div class="bg-white rounded-md border border-gray-200 p-5 shadow-sm space-y-6">
        <div class="flex items-center justify-between">
            <h3 class="text-sm font-semibold text-gray-900">Performance</h3>
            <span class="text-[10px] text-gray-400">Live</span>
        </div>
        @php
            $circles = [
                ['label' => 'Success Rate', 'value' => $successRate, 'color' => '#10b981'],
                ['label' => 'Occupancy', 'value' => $occupancyRate, 'color' => '#f59e0b'],
                ['label' => 'Monthly Target', 'value' => $treatmentTargetPct, 'color' => '#94a3b8'],
            ];
        @endphp
        <div class="grid grid-cols-3 gap-2">
            @foreach($circles as $c)
            <div class="text-center">
                <svg viewBox="0 0 36 36" class="circular-chart">
                    <path class="circle-bg" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"/>
                    <path class="circle" stroke="{{ $c['color'] }}" stroke-dasharray="{{ $c['value'] }}, 100" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"/>
                    <text x="18" y="17" class="percentage" text-anchor="middle">{{ $c['value'] }}%</text>
                    <text x="18" y="22" class="label" text-anchor="middle">{{ $c['label'] }}</text>
                </svg>
            </div>
            @endforeach
        </div>

        <div class="border-t border-gray-200 pt-4 space-y-3">
            <h4 class="text-xs font-semibold text-gray-700">Top Doctors</h4>
            @foreach($topDoctors as $doc)
                <div class="flex items-center justify-between text-sm">
                    <div class="flex items-center gap-2">
                        <div class="w-6 h-6 rounded-full bg-slate-100 text-slate-600 flex items-center justify-center text-[10px] font-bold">{{ strtoupper(substr($doc->name,0,1)) }}</div>
                        <span class="text-xs text-gray-700 truncate max-w-[120px]">{{ $doc->name }}</span>
                    </div>
                    <span class="text-xs font-medium text-gray-500">{{ $doc->completed_visits }} visits</span>
                </div>
            @endforeach
        </div>
    </div>
</div>

<script>
const fmt = (n) => 'TSh ' + n.toLocaleString();

const weeklyData = {{ $revenueDays->values() }};
const weeklyLabels = @json($dayLabels);
const monthlyData = {{ $monthlyDays->values() }};
const monthlyLabels = @json($monthLabels);
const visitData = {{ $visitTrend->values() }};
const visitLabels = @json($visitLabels);
const deptLabels = @json($departmentPerformance->pluck('name'));
const deptData = {{ $departmentPerformance->pluck('visits') }};

let revenueChart, visitChart, deptChart;

function initCharts() {
    const revCtx = document.getElementById('revenueChart').getContext('2d');
    const gradient = revCtx.createLinearGradient(0, 0, 0, 300);
    gradient.addColorStop(0, 'rgba(5, 150, 105, 0.08)');
    gradient.addColorStop(1, 'rgba(5, 150, 105, 0)');

    revenueChart = new Chart(revCtx, {
        type: 'line',
        data: {
            labels: weeklyLabels,
            datasets: [{
                label: 'Revenue',
                data: weeklyData,
                borderColor: '#059669',
                backgroundColor: gradient,
                borderWidth: 2,
                pointBackgroundColor: '#ffffff',
                pointBorderColor: '#059669',
                pointBorderWidth: 2,
                pointRadius: 2,
                pointHoverRadius: 4,
                fill: true,
                tension: 0.35
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false }, tooltip: { callbacks: { label: (ctx) => fmt(ctx.raw) } } },
            scales: { y: { beginAtZero: true, grid: { color: '#f1f5f9' }, ticks: { font: { size: 10 } } }, x: { grid: { display: false }, ticks: { font: { size: 10 } } } }
        }
    });

    visitChart = new Chart(document.getElementById('visitTrendChart'), {
        type: 'line',
        data: {
            labels: visitLabels,
            datasets: [{
                label: 'Visits',
                data: visitData,
                borderColor: '#94a3b8',
                backgroundColor: 'rgba(148, 163, 184, 0.08)',
                borderWidth: 2,
                borderDash: [5, 4],
                pointBackgroundColor: '#ffffff',
                pointBorderColor: '#94a3b8',
                pointBorderWidth: 2,
                pointRadius: 2,
                fill: true,
                tension: 0.35
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true, grid: { color: '#f1f5f9' }, ticks: { font: { size: 10 } } }, x: { grid: { display: false }, ticks: { font: { size: 10 } } } }
        }
    });

    deptChart = new Chart(document.getElementById('departmentChart'), {
        type: 'bar',
        data: {
            labels: deptLabels,
            datasets: [{
                label: 'Visits',
                data: deptData,
                backgroundColor: ['#334155', '#94a3b8', '#059669', '#f59e0b', '#cbd5e1', '#dc2626'],
                borderRadius: 6,
                barThickness: 24
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true, grid: { color: '#f1f5f9' }, ticks: { font: { size: 10 } } }, x: { grid: { display: false }, ticks: { font: { size: 10 } } } }
        }
    });
}

function switchRevenue(period) {
    document.getElementById('revWeeklyBtn').className = period === 'weekly'
        ? 'px-3 py-1 text-xs font-medium rounded-md bg-white text-slate-800 shadow-sm'
        : 'px-3 py-1 text-xs font-medium rounded-md text-gray-500 hover:text-gray-700';
    document.getElementById('revMonthlyBtn').className = period === 'monthly'
        ? 'px-3 py-1 text-xs font-medium rounded-md bg-white text-slate-800 shadow-sm'
        : 'px-3 py-1 text-xs font-medium rounded-md text-gray-500 hover:text-gray-700';

    revenueChart.data.labels = period === 'weekly' ? weeklyLabels : monthlyLabels;
    revenueChart.data.datasets[0].data = period === 'weekly' ? weeklyData : monthlyData;
    revenueChart.update();
}

function openDashboardPatientModal() {
    const modal = document.getElementById('dashboardPatientModal');
    const panel = document.getElementById('dashboardPatientSlidePanel');
    modal.classList.remove('hidden');
    setTimeout(() => panel.classList.remove('translate-x-full'), 10);
    document.body.style.overflow = 'hidden';
}
function closeDashboardPatientModal() {
    const modal = document.getElementById('dashboardPatientModal');
    const panel = document.getElementById('dashboardPatientSlidePanel');
    panel.classList.add('translate-x-full');
    setTimeout(() => { modal.classList.add('hidden'); document.body.style.overflow = ''; }, 300);
}

function openDashboardAppointmentModal() {
    const modal = document.getElementById('dashboardAppointmentModal');
    const panel = document.getElementById('dashboardAppointmentSlidePanel');
    modal.classList.remove('hidden');
    setTimeout(() => panel.classList.remove('translate-x-full'), 10);
    document.body.style.overflow = 'hidden';
}
function closeDashboardAppointmentModal() {
    const modal = document.getElementById('dashboardAppointmentModal');
    const panel = document.getElementById('dashboardAppointmentSlidePanel');
    panel.classList.add('translate-x-full');
    setTimeout(() => { modal.classList.add('hidden'); document.body.style.overflow = ''; }, 300);
}

document.getElementById('dashApptPatientSearch')?.addEventListener('input', function() {
    const q = this.value.toLowerCase();
    const select = document.getElementById('dashApptPatientSelect');
    Array.from(select.querySelectorAll('option')).forEach(opt => {
        if (!opt.value) { opt.style.display = 'block'; return; }
        opt.style.display = opt.dataset.search?.includes(q) ? 'block' : 'none';
    });
});

// AJAX patient form
setupAjaxForm('#dashboardPatientForm', 'Patient saved successfully.', closeDashboardPatientModal);
// AJAX appointment form
setupAjaxForm('#dashboardAppointmentForm', 'Appointment booked successfully.', closeDashboardAppointmentModal);

// Doctor filter
function filterVisitsByDoctor() {
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

// Change doctor modal
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

// Change doctor form AJAX
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

// Initialize charts
initCharts();

// Real-time refresh every 60 seconds
setInterval(() => {
    fetch('/dashboard/stats', { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
        .then(r => r.json())
        .then(data => {
            if (data.revenueDays) {
                revenueChart.data.datasets[0].data = data.revenueDays;
                revenueChart.data.labels = data.dayLabels;
                revenueChart.update();
            }
            if (data.visitTrend) {
                visitChart.data.datasets[0].data = data.visitTrend;
                visitChart.data.labels = data.visitLabels;
                visitChart.update();
            }
            if (data.stats) {
                document.querySelectorAll('.card-sm p.text-xl').forEach(el => {
                    const label = el.previousElementSibling?.textContent?.trim();
                    if (label === 'Today\'s Visits') el.textContent = data.stats.visits_today;
                    if (label === 'Weekly Revenue') el.textContent = 'TSh ' + Number(data.stats.revenue_this_week).toLocaleString();
                    if (label === 'Pending Payments') el.textContent = data.stats.pending_payments;
                });
            }
        })
        .catch(() => {});
}, 60000);
</script>
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    <script>
const fmt = (n) => 'TSh ' + n.toLocaleString();

const weeklyData = {{ $revenueDays->values() }};
const weeklyLabels = @json($dayLabels);
const monthlyData = {{ $monthlyDays->values() }};
const monthlyLabels = @json($monthLabels);
const visitData = {{ $visitTrend->values() }};
const visitLabels = @json($visitLabels);
const deptLabels = @json($departmentPerformance->pluck('name'));
const deptData = {{ $departmentPerformance->pluck('visits') }};

let revenueChart, visitChart, deptChart;

function initCharts() {
    const revCtx = document.getElementById('revenueChart').getContext('2d');
    const gradient = revCtx.createLinearGradient(0, 0, 0, 300);
    gradient.addColorStop(0, 'rgba(5, 150, 105, 0.08)');
    gradient.addColorStop(1, 'rgba(5, 150, 105, 0)');

    revenueChart = new Chart(revCtx, {
        type: 'line',
        data: {
            labels: weeklyLabels,
            datasets: [{
                label: 'Revenue',
                data: weeklyData,
                borderColor: '#059669',
                backgroundColor: gradient,
                borderWidth: 2,
                pointBackgroundColor: '#ffffff',
                pointBorderColor: '#059669',
                pointBorderWidth: 2,
                pointRadius: 2,
                pointHoverRadius: 4,
                fill: true,
                tension: 0.35
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false }, tooltip: { callbacks: { label: (ctx) => fmt(ctx.raw) } } },
            scales: { y: { beginAtZero: true, grid: { color: '#f1f5f9' }, ticks: { font: { size: 10 } } }, x: { grid: { display: false }, ticks: { font: { size: 10 } } } }
        }
    });

    visitChart = new Chart(document.getElementById('visitTrendChart'), {
        type: 'line',
        data: {
            labels: visitLabels,
            datasets: [{
                label: 'Visits',
                data: visitData,
                borderColor: '#94a3b8',
                backgroundColor: 'rgba(148, 163, 184, 0.08)',
                borderWidth: 2,
                borderDash: [5, 4],
                pointBackgroundColor: '#ffffff',
                pointBorderColor: '#94a3b8',
                pointBorderWidth: 2,
                pointRadius: 2,
                fill: true,
                tension: 0.35
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true, grid: { color: '#f1f5f9' }, ticks: { font: { size: 10 } } }, x: { grid: { display: false }, ticks: { font: { size: 10 } } } }
        }
    });

    deptChart = new Chart(document.getElementById('departmentChart'), {
        type: 'bar',
        data: {
            labels: deptLabels,
            datasets: [{
                label: 'Visits',
                data: deptData,
                backgroundColor: ['#334155', '#94a3b8', '#059669', '#f59e0b', '#cbd5e1', '#dc2626'],
                borderRadius: 6,
                barThickness: 24
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true, grid: { color: '#f1f5f9' }, ticks: { font: { size: 10 } } }, x: { grid: { display: false }, ticks: { font: { size: 10 } } } }
        }
    });
}

function switchRevenue(period) {
    document.getElementById('revWeeklyBtn').className = period === 'weekly'
        ? 'px-3 py-1 text-xs font-medium rounded-md bg-white text-slate-800 shadow-sm'
        : 'px-3 py-1 text-xs font-medium rounded-md text-gray-500 hover:text-gray-700';
    document.getElementById('revMonthlyBtn').className = period === 'monthly'
        ? 'px-3 py-1 text-xs font-medium rounded-md bg-white text-slate-800 shadow-sm'
        : 'px-3 py-1 text-xs font-medium rounded-md text-gray-500 hover:text-gray-700';

    revenueChart.data.labels = period === 'weekly' ? weeklyLabels : monthlyLabels;
    revenueChart.data.datasets[0].data = period === 'weekly' ? weeklyData : monthlyData;
    revenueChart.update();
}

function openDashboardPatientModal() {
    const modal = document.getElementById('dashboardPatientModal');
    const panel = document.getElementById('dashboardPatientSlidePanel');
    modal.classList.remove('hidden');
    setTimeout(() => panel.classList.remove('translate-x-full'), 10);
    document.body.style.overflow = 'hidden';
}
function closeDashboardPatientModal() {
    const modal = document.getElementById('dashboardPatientModal');
    const panel = document.getElementById('dashboardPatientSlidePanel');
    panel.classList.add('translate-x-full');
    setTimeout(() => { modal.classList.add('hidden'); document.body.style.overflow = ''; }, 300);
}

function openDashboardAppointmentModal() {
    const modal = document.getElementById('dashboardAppointmentModal');
    const panel = document.getElementById('dashboardAppointmentSlidePanel');
    modal.classList.remove('hidden');
    setTimeout(() => panel.classList.remove('translate-x-full'), 10);
    document.body.style.overflow = 'hidden';
}
function closeDashboardAppointmentModal() {
    const modal = document.getElementById('dashboardAppointmentModal');
    const panel = document.getElementById('dashboardAppointmentSlidePanel');
    panel.classList.add('translate-x-full');
    setTimeout(() => { modal.classList.add('hidden'); document.body.style.overflow = ''; }, 300);
}

document.getElementById('dashApptPatientSearch')?.addEventListener('input', function() {
    const q = this.value.toLowerCase();
    const select = document.getElementById('dashApptPatientSelect');
    Array.from(select.querySelectorAll('option')).forEach(opt => {
        if (!opt.value) { opt.style.display = 'block'; return; }
        opt.style.display = opt.dataset.search?.includes(q) ? 'block' : 'none';
    });
});

// AJAX patient form
setupAjaxForm('#dashboardPatientForm', 'Patient saved successfully.', closeDashboardPatientModal);
// AJAX appointment form
setupAjaxForm('#dashboardAppointmentForm', 'Appointment booked successfully.', closeDashboardAppointmentModal);

// Doctor filter
function filterVisitsByDoctor() {
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

// Change doctor modal
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

// Change doctor form AJAX
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

// Initialize charts
initCharts();

// Real-time refresh every 60 seconds
setInterval(() => {
    fetch('/dashboard/stats', { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
        .then(r => r.json())
        .then(data => {
            if (data.revenueDays) {
                revenueChart.data.datasets[0].data = data.revenueDays;
                revenueChart.data.labels = data.dayLabels;
                revenueChart.update();
            }
            if (data.visitTrend) {
                visitChart.data.datasets[0].data = data.visitTrend;
                visitChart.data.labels = data.visitLabels;
                visitChart.update();
            }
            if (data.stats) {
                document.querySelectorAll('.card-sm p.text-xl').forEach(el => {
                    const label = el.previousElementSibling?.textContent?.trim();
                    if (label === 'Today\'s Visits') el.textContent = data.stats.visits_today;
                    if (label === 'Weekly Revenue') el.textContent = 'TSh ' + Number(data.stats.revenue_this_week).toLocaleString();
                    if (label === 'Pending Payments') el.textContent = data.stats.pending_payments;
                });
            }
        })
        .catch(() => {});
}, 60000);
</script>
    <div class="bg-white rounded-md border border-gray-200 p-5 shadow-sm">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="text-sm font-semibold text-gray-900">Visit Trend</h3>
                <p class="text-xs text-gray-400">Patient flow over 7 days</p>
            </div>
            <div class="text-right">
                <div class="text-lg font-semibold text-gray-900">{{ $stats['visits_today'] }}</div>
                <div class="text-xs text-gray-400">Today</div>
            </div>
        </div>
        <div class="relative h-56">
            <canvas id="visitTrendChart"></canvas>
        </div>
    </div>

    <script>
const fmt = (n) => 'TSh ' + n.toLocaleString();

const weeklyData = {{ $revenueDays->values() }};
const weeklyLabels = @json($dayLabels);
const monthlyData = {{ $monthlyDays->values() }};
const monthlyLabels = @json($monthLabels);
const visitData = {{ $visitTrend->values() }};
const visitLabels = @json($visitLabels);
const deptLabels = @json($departmentPerformance->pluck('name'));
const deptData = {{ $departmentPerformance->pluck('visits') }};

let revenueChart, visitChart, deptChart;

function initCharts() {
    const revCtx = document.getElementById('revenueChart').getContext('2d');
    const gradient = revCtx.createLinearGradient(0, 0, 0, 300);
    gradient.addColorStop(0, 'rgba(5, 150, 105, 0.08)');
    gradient.addColorStop(1, 'rgba(5, 150, 105, 0)');

    revenueChart = new Chart(revCtx, {
        type: 'line',
        data: {
            labels: weeklyLabels,
            datasets: [{
                label: 'Revenue',
                data: weeklyData,
                borderColor: '#059669',
                backgroundColor: gradient,
                borderWidth: 2,
                pointBackgroundColor: '#ffffff',
                pointBorderColor: '#059669',
                pointBorderWidth: 2,
                pointRadius: 2,
                pointHoverRadius: 4,
                fill: true,
                tension: 0.35
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false }, tooltip: { callbacks: { label: (ctx) => fmt(ctx.raw) } } },
            scales: { y: { beginAtZero: true, grid: { color: '#f1f5f9' }, ticks: { font: { size: 10 } } }, x: { grid: { display: false }, ticks: { font: { size: 10 } } } }
        }
    });

    visitChart = new Chart(document.getElementById('visitTrendChart'), {
        type: 'line',
        data: {
            labels: visitLabels,
            datasets: [{
                label: 'Visits',
                data: visitData,
                borderColor: '#94a3b8',
                backgroundColor: 'rgba(148, 163, 184, 0.08)',
                borderWidth: 2,
                borderDash: [5, 4],
                pointBackgroundColor: '#ffffff',
                pointBorderColor: '#94a3b8',
                pointBorderWidth: 2,
                pointRadius: 2,
                fill: true,
                tension: 0.35
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true, grid: { color: '#f1f5f9' }, ticks: { font: { size: 10 } } }, x: { grid: { display: false }, ticks: { font: { size: 10 } } } }
        }
    });

    deptChart = new Chart(document.getElementById('departmentChart'), {
        type: 'bar',
        data: {
            labels: deptLabels,
            datasets: [{
                label: 'Visits',
                data: deptData,
                backgroundColor: ['#334155', '#94a3b8', '#059669', '#f59e0b', '#cbd5e1', '#dc2626'],
                borderRadius: 6,
                barThickness: 24
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true, grid: { color: '#f1f5f9' }, ticks: { font: { size: 10 } } }, x: { grid: { display: false }, ticks: { font: { size: 10 } } } }
        }
    });
}

function switchRevenue(period) {
    document.getElementById('revWeeklyBtn').className = period === 'weekly'
        ? 'px-3 py-1 text-xs font-medium rounded-md bg-white text-slate-800 shadow-sm'
        : 'px-3 py-1 text-xs font-medium rounded-md text-gray-500 hover:text-gray-700';
    document.getElementById('revMonthlyBtn').className = period === 'monthly'
        ? 'px-3 py-1 text-xs font-medium rounded-md bg-white text-slate-800 shadow-sm'
        : 'px-3 py-1 text-xs font-medium rounded-md text-gray-500 hover:text-gray-700';

    revenueChart.data.labels = period === 'weekly' ? weeklyLabels : monthlyLabels;
    revenueChart.data.datasets[0].data = period === 'weekly' ? weeklyData : monthlyData;
    revenueChart.update();
}

function openDashboardPatientModal() {
    const modal = document.getElementById('dashboardPatientModal');
    const panel = document.getElementById('dashboardPatientSlidePanel');
    modal.classList.remove('hidden');
    setTimeout(() => panel.classList.remove('translate-x-full'), 10);
    document.body.style.overflow = 'hidden';
}
function closeDashboardPatientModal() {
    const modal = document.getElementById('dashboardPatientModal');
    const panel = document.getElementById('dashboardPatientSlidePanel');
    panel.classList.add('translate-x-full');
    setTimeout(() => { modal.classList.add('hidden'); document.body.style.overflow = ''; }, 300);
}

function openDashboardAppointmentModal() {
    const modal = document.getElementById('dashboardAppointmentModal');
    const panel = document.getElementById('dashboardAppointmentSlidePanel');
    modal.classList.remove('hidden');
    setTimeout(() => panel.classList.remove('translate-x-full'), 10);
    document.body.style.overflow = 'hidden';
}
function closeDashboardAppointmentModal() {
    const modal = document.getElementById('dashboardAppointmentModal');
    const panel = document.getElementById('dashboardAppointmentSlidePanel');
    panel.classList.add('translate-x-full');
    setTimeout(() => { modal.classList.add('hidden'); document.body.style.overflow = ''; }, 300);
}

document.getElementById('dashApptPatientSearch')?.addEventListener('input', function() {
    const q = this.value.toLowerCase();
    const select = document.getElementById('dashApptPatientSelect');
    Array.from(select.querySelectorAll('option')).forEach(opt => {
        if (!opt.value) { opt.style.display = 'block'; return; }
        opt.style.display = opt.dataset.search?.includes(q) ? 'block' : 'none';
    });
});

// AJAX patient form
setupAjaxForm('#dashboardPatientForm', 'Patient saved successfully.', closeDashboardPatientModal);
// AJAX appointment form
setupAjaxForm('#dashboardAppointmentForm', 'Appointment booked successfully.', closeDashboardAppointmentModal);

// Doctor filter
function filterVisitsByDoctor() {
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

// Change doctor modal
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

// Change doctor form AJAX
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

// Initialize charts
initCharts();

// Real-time refresh every 60 seconds
setInterval(() => {
    fetch('/dashboard/stats', { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
        .then(r => r.json())
        .then(data => {
            if (data.revenueDays) {
                revenueChart.data.datasets[0].data = data.revenueDays;
                revenueChart.data.labels = data.dayLabels;
                revenueChart.update();
            }
            if (data.visitTrend) {
                visitChart.data.datasets[0].data = data.visitTrend;
                visitChart.data.labels = data.visitLabels;
                visitChart.update();
            }
            if (data.stats) {
                document.querySelectorAll('.card-sm p.text-xl').forEach(el => {
                    const label = el.previousElementSibling?.textContent?.trim();
                    if (label === 'Today\'s Visits') el.textContent = data.stats.visits_today;
                    if (label === 'Weekly Revenue') el.textContent = 'TSh ' + Number(data.stats.revenue_this_week).toLocaleString();
                    if (label === 'Pending Payments') el.textContent = data.stats.pending_payments;
                });
            }
        })
        .catch(() => {});
}, 60000);
</script>
    <div class="bg-white rounded-md border border-gray-200 p-5 shadow-sm">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="text-sm font-semibold text-gray-900">Department Performance</h3>
                <p class="text-xs text-gray-400">Visits by department this month</p>
            </div>
        </div>
        <div class="relative h-56">
            <canvas id="departmentChart"></canvas>
        </div>
    </div>
</div>
@endif

<script>
const fmt = (n) => 'TSh ' + n.toLocaleString();

const weeklyData = {{ $revenueDays->values() }};
const weeklyLabels = @json($dayLabels);
const monthlyData = {{ $monthlyDays->values() }};
const monthlyLabels = @json($monthLabels);
const visitData = {{ $visitTrend->values() }};
const visitLabels = @json($visitLabels);
const deptLabels = @json($departmentPerformance->pluck('name'));
const deptData = {{ $departmentPerformance->pluck('visits') }};

let revenueChart, visitChart, deptChart;

function initCharts() {
    const revCtx = document.getElementById('revenueChart').getContext('2d');
    const gradient = revCtx.createLinearGradient(0, 0, 0, 300);
    gradient.addColorStop(0, 'rgba(5, 150, 105, 0.08)');
    gradient.addColorStop(1, 'rgba(5, 150, 105, 0)');

    revenueChart = new Chart(revCtx, {
        type: 'line',
        data: {
            labels: weeklyLabels,
            datasets: [{
                label: 'Revenue',
                data: weeklyData,
                borderColor: '#059669',
                backgroundColor: gradient,
                borderWidth: 2,
                pointBackgroundColor: '#ffffff',
                pointBorderColor: '#059669',
                pointBorderWidth: 2,
                pointRadius: 2,
                pointHoverRadius: 4,
                fill: true,
                tension: 0.35
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false }, tooltip: { callbacks: { label: (ctx) => fmt(ctx.raw) } } },
            scales: { y: { beginAtZero: true, grid: { color: '#f1f5f9' }, ticks: { font: { size: 10 } } }, x: { grid: { display: false }, ticks: { font: { size: 10 } } } }
        }
    });

    visitChart = new Chart(document.getElementById('visitTrendChart'), {
        type: 'line',
        data: {
            labels: visitLabels,
            datasets: [{
                label: 'Visits',
                data: visitData,
                borderColor: '#94a3b8',
                backgroundColor: 'rgba(148, 163, 184, 0.08)',
                borderWidth: 2,
                borderDash: [5, 4],
                pointBackgroundColor: '#ffffff',
                pointBorderColor: '#94a3b8',
                pointBorderWidth: 2,
                pointRadius: 2,
                fill: true,
                tension: 0.35
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true, grid: { color: '#f1f5f9' }, ticks: { font: { size: 10 } } }, x: { grid: { display: false }, ticks: { font: { size: 10 } } } }
        }
    });

    deptChart = new Chart(document.getElementById('departmentChart'), {
        type: 'bar',
        data: {
            labels: deptLabels,
            datasets: [{
                label: 'Visits',
                data: deptData,
                backgroundColor: ['#334155', '#94a3b8', '#059669', '#f59e0b', '#cbd5e1', '#dc2626'],
                borderRadius: 6,
                barThickness: 24
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true, grid: { color: '#f1f5f9' }, ticks: { font: { size: 10 } } }, x: { grid: { display: false }, ticks: { font: { size: 10 } } } }
        }
    });
}

function switchRevenue(period) {
    document.getElementById('revWeeklyBtn').className = period === 'weekly'
        ? 'px-3 py-1 text-xs font-medium rounded-md bg-white text-slate-800 shadow-sm'
        : 'px-3 py-1 text-xs font-medium rounded-md text-gray-500 hover:text-gray-700';
    document.getElementById('revMonthlyBtn').className = period === 'monthly'
        ? 'px-3 py-1 text-xs font-medium rounded-md bg-white text-slate-800 shadow-sm'
        : 'px-3 py-1 text-xs font-medium rounded-md text-gray-500 hover:text-gray-700';

    revenueChart.data.labels = period === 'weekly' ? weeklyLabels : monthlyLabels;
    revenueChart.data.datasets[0].data = period === 'weekly' ? weeklyData : monthlyData;
    revenueChart.update();
}

function openDashboardPatientModal() {
    const modal = document.getElementById('dashboardPatientModal');
    const panel = document.getElementById('dashboardPatientSlidePanel');
    modal.classList.remove('hidden');
    setTimeout(() => panel.classList.remove('translate-x-full'), 10);
    document.body.style.overflow = 'hidden';
}
function closeDashboardPatientModal() {
    const modal = document.getElementById('dashboardPatientModal');
    const panel = document.getElementById('dashboardPatientSlidePanel');
    panel.classList.add('translate-x-full');
    setTimeout(() => { modal.classList.add('hidden'); document.body.style.overflow = ''; }, 300);
}

function openDashboardAppointmentModal() {
    const modal = document.getElementById('dashboardAppointmentModal');
    const panel = document.getElementById('dashboardAppointmentSlidePanel');
    modal.classList.remove('hidden');
    setTimeout(() => panel.classList.remove('translate-x-full'), 10);
    document.body.style.overflow = 'hidden';
}
function closeDashboardAppointmentModal() {
    const modal = document.getElementById('dashboardAppointmentModal');
    const panel = document.getElementById('dashboardAppointmentSlidePanel');
    panel.classList.add('translate-x-full');
    setTimeout(() => { modal.classList.add('hidden'); document.body.style.overflow = ''; }, 300);
}

document.getElementById('dashApptPatientSearch')?.addEventListener('input', function() {
    const q = this.value.toLowerCase();
    const select = document.getElementById('dashApptPatientSelect');
    Array.from(select.querySelectorAll('option')).forEach(opt => {
        if (!opt.value) { opt.style.display = 'block'; return; }
        opt.style.display = opt.dataset.search?.includes(q) ? 'block' : 'none';
    });
});

// AJAX patient form
setupAjaxForm('#dashboardPatientForm', 'Patient saved successfully.', closeDashboardPatientModal);
// AJAX appointment form
setupAjaxForm('#dashboardAppointmentForm', 'Appointment booked successfully.', closeDashboardAppointmentModal);

// Doctor filter
function filterVisitsByDoctor() {
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

// Change doctor modal
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

// Change doctor form AJAX
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

// Initialize charts
initCharts();

// Real-time refresh every 60 seconds
setInterval(() => {
    fetch('/dashboard/stats', { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
        .then(r => r.json())
        .then(data => {
            if (data.revenueDays) {
                revenueChart.data.datasets[0].data = data.revenueDays;
                revenueChart.data.labels = data.dayLabels;
                revenueChart.update();
            }
            if (data.visitTrend) {
                visitChart.data.datasets[0].data = data.visitTrend;
                visitChart.data.labels = data.visitLabels;
                visitChart.update();
            }
            if (data.stats) {
                document.querySelectorAll('.card-sm p.text-xl').forEach(el => {
                    const label = el.previousElementSibling?.textContent?.trim();
                    if (label === 'Today\'s Visits') el.textContent = data.stats.visits_today;
                    if (label === 'Weekly Revenue') el.textContent = 'TSh ' + Number(data.stats.revenue_this_week).toLocaleString();
                    if (label === 'Pending Payments') el.textContent = data.stats.pending_payments;
                });
            }
        })
        .catch(() => {});
}, 60000);
</script>
@if($waitingForDoctor->isNotEmpty())
<div class="bg-white rounded-md border border-gray-200 shadow-sm overflow-hidden mb-6">
    <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
        <h3 class="text-sm font-semibold text-gray-900">Patients Waiting for Doctor</h3>
        <a href="{{ route('doctor.queue') }}" class="text-xs font-medium text-slate-600 hover:text-slate-800">View Queue</a>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left">
            <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                <tr>
                    <th class="px-6 py-3">Visit #</th>
                    <th class="px-6 py-3">Patient</th>
                    <th class="px-6 py-3">Status</th>
                    <th class="px-6 py-3">Registered</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($waitingForDoctor as $visit)
                    <tr class="border-t border-gray-200 hover:bg-gray-50/50">
                        <td class="px-6 py-3 font-medium">{{ $visit->visit_number }}</td>
                        <td class="px-6 py-3">{{ $visit->patient->fullName() }}</td>
                        <td class="px-6 py-3"><span class="inline-flex items-center gap-1.5 text-[10px] font-medium text-gray-600"><span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>Waiting</span></td>
                        <td class="px-6 py-3 text-gray-500">{{ $visit->registered_at->diffForHumans() }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

<script>
const fmt = (n) => 'TSh ' + n.toLocaleString();

const weeklyData = {{ $revenueDays->values() }};
const weeklyLabels = @json($dayLabels);
const monthlyData = {{ $monthlyDays->values() }};
const monthlyLabels = @json($monthLabels);
const visitData = {{ $visitTrend->values() }};
const visitLabels = @json($visitLabels);
const deptLabels = @json($departmentPerformance->pluck('name'));
const deptData = {{ $departmentPerformance->pluck('visits') }};

let revenueChart, visitChart, deptChart;

function initCharts() {
    const revCtx = document.getElementById('revenueChart').getContext('2d');
    const gradient = revCtx.createLinearGradient(0, 0, 0, 300);
    gradient.addColorStop(0, 'rgba(5, 150, 105, 0.08)');
    gradient.addColorStop(1, 'rgba(5, 150, 105, 0)');

    revenueChart = new Chart(revCtx, {
        type: 'line',
        data: {
            labels: weeklyLabels,
            datasets: [{
                label: 'Revenue',
                data: weeklyData,
                borderColor: '#059669',
                backgroundColor: gradient,
                borderWidth: 2,
                pointBackgroundColor: '#ffffff',
                pointBorderColor: '#059669',
                pointBorderWidth: 2,
                pointRadius: 2,
                pointHoverRadius: 4,
                fill: true,
                tension: 0.35
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false }, tooltip: { callbacks: { label: (ctx) => fmt(ctx.raw) } } },
            scales: { y: { beginAtZero: true, grid: { color: '#f1f5f9' }, ticks: { font: { size: 10 } } }, x: { grid: { display: false }, ticks: { font: { size: 10 } } } }
        }
    });

    visitChart = new Chart(document.getElementById('visitTrendChart'), {
        type: 'line',
        data: {
            labels: visitLabels,
            datasets: [{
                label: 'Visits',
                data: visitData,
                borderColor: '#94a3b8',
                backgroundColor: 'rgba(148, 163, 184, 0.08)',
                borderWidth: 2,
                borderDash: [5, 4],
                pointBackgroundColor: '#ffffff',
                pointBorderColor: '#94a3b8',
                pointBorderWidth: 2,
                pointRadius: 2,
                fill: true,
                tension: 0.35
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true, grid: { color: '#f1f5f9' }, ticks: { font: { size: 10 } } }, x: { grid: { display: false }, ticks: { font: { size: 10 } } } }
        }
    });

    deptChart = new Chart(document.getElementById('departmentChart'), {
        type: 'bar',
        data: {
            labels: deptLabels,
            datasets: [{
                label: 'Visits',
                data: deptData,
                backgroundColor: ['#334155', '#94a3b8', '#059669', '#f59e0b', '#cbd5e1', '#dc2626'],
                borderRadius: 6,
                barThickness: 24
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true, grid: { color: '#f1f5f9' }, ticks: { font: { size: 10 } } }, x: { grid: { display: false }, ticks: { font: { size: 10 } } } }
        }
    });
}

function switchRevenue(period) {
    document.getElementById('revWeeklyBtn').className = period === 'weekly'
        ? 'px-3 py-1 text-xs font-medium rounded-md bg-white text-slate-800 shadow-sm'
        : 'px-3 py-1 text-xs font-medium rounded-md text-gray-500 hover:text-gray-700';
    document.getElementById('revMonthlyBtn').className = period === 'monthly'
        ? 'px-3 py-1 text-xs font-medium rounded-md bg-white text-slate-800 shadow-sm'
        : 'px-3 py-1 text-xs font-medium rounded-md text-gray-500 hover:text-gray-700';

    revenueChart.data.labels = period === 'weekly' ? weeklyLabels : monthlyLabels;
    revenueChart.data.datasets[0].data = period === 'weekly' ? weeklyData : monthlyData;
    revenueChart.update();
}

function openDashboardPatientModal() {
    const modal = document.getElementById('dashboardPatientModal');
    const panel = document.getElementById('dashboardPatientSlidePanel');
    modal.classList.remove('hidden');
    setTimeout(() => panel.classList.remove('translate-x-full'), 10);
    document.body.style.overflow = 'hidden';
}
function closeDashboardPatientModal() {
    const modal = document.getElementById('dashboardPatientModal');
    const panel = document.getElementById('dashboardPatientSlidePanel');
    panel.classList.add('translate-x-full');
    setTimeout(() => { modal.classList.add('hidden'); document.body.style.overflow = ''; }, 300);
}

function openDashboardAppointmentModal() {
    const modal = document.getElementById('dashboardAppointmentModal');
    const panel = document.getElementById('dashboardAppointmentSlidePanel');
    modal.classList.remove('hidden');
    setTimeout(() => panel.classList.remove('translate-x-full'), 10);
    document.body.style.overflow = 'hidden';
}
function closeDashboardAppointmentModal() {
    const modal = document.getElementById('dashboardAppointmentModal');
    const panel = document.getElementById('dashboardAppointmentSlidePanel');
    panel.classList.add('translate-x-full');
    setTimeout(() => { modal.classList.add('hidden'); document.body.style.overflow = ''; }, 300);
}

document.getElementById('dashApptPatientSearch')?.addEventListener('input', function() {
    const q = this.value.toLowerCase();
    const select = document.getElementById('dashApptPatientSelect');
    Array.from(select.querySelectorAll('option')).forEach(opt => {
        if (!opt.value) { opt.style.display = 'block'; return; }
        opt.style.display = opt.dataset.search?.includes(q) ? 'block' : 'none';
    });
});

// AJAX patient form
setupAjaxForm('#dashboardPatientForm', 'Patient saved successfully.', closeDashboardPatientModal);
// AJAX appointment form
setupAjaxForm('#dashboardAppointmentForm', 'Appointment booked successfully.', closeDashboardAppointmentModal);

// Doctor filter
function filterVisitsByDoctor() {
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

// Change doctor modal
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

// Change doctor form AJAX
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

// Initialize charts
initCharts();

// Real-time refresh every 60 seconds
setInterval(() => {
    fetch('/dashboard/stats', { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
        .then(r => r.json())
        .then(data => {
            if (data.revenueDays) {
                revenueChart.data.datasets[0].data = data.revenueDays;
                revenueChart.data.labels = data.dayLabels;
                revenueChart.update();
            }
            if (data.visitTrend) {
                visitChart.data.datasets[0].data = data.visitTrend;
                visitChart.data.labels = data.visitLabels;
                visitChart.update();
            }
            if (data.stats) {
                document.querySelectorAll('.card-sm p.text-xl').forEach(el => {
                    const label = el.previousElementSibling?.textContent?.trim();
                    if (label === 'Today\'s Visits') el.textContent = data.stats.visits_today;
                    if (label === 'Weekly Revenue') el.textContent = 'TSh ' + Number(data.stats.revenue_this_week).toLocaleString();
                    if (label === 'Pending Payments') el.textContent = data.stats.pending_payments;
                });
            }
        })
        .catch(() => {});
}, 60000);
</script>
@if($user->isAdmin() || $user->isReception())
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    <div class="bg-white rounded-md border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-200 flex items-center justify-between">
            <div>
                <h3 class="text-sm font-semibold text-gray-900">Today's Appointments</h3>
                <p class="text-xs text-gray-400">{{ today()->format('D, M j') }}</p>
            </div>
            <a href="{{ route('appointments.index') }}" class="text-xs font-medium text-slate-600 hover:text-slate-800">View All</a>
        </div>
        <div class="p-5">
            @php
                $apptStats = [
                    'scheduled' => $todayAppointments->where('status', 'scheduled')->count(),
                    'confirmed' => $todayAppointments->where('status', 'confirmed')->count(),
                    'completed' => $todayAppointments->where('status', 'completed')->count(),
                    'cancelled' => $todayAppointments->where('status', 'cancelled')->count(),
                ];
            @endphp
            <div class="grid grid-cols-4 gap-3 mb-4">
                <div class="text-center p-3 bg-gray-50 rounded border border-gray-200">
                    <div class="text-xl font-bold text-amber-600">{{ $apptStats['scheduled'] }}</div>
                    <div class="text-[10px] text-gray-500 font-medium">Scheduled</div>
                </div>
                <div class="text-center p-3 bg-gray-50 rounded border border-gray-200">
                    <div class="text-xl font-bold text-gray-900">{{ $apptStats['confirmed'] }}</div>
                    <div class="text-[10px] text-gray-500 font-medium">Confirmed</div>
                </div>
                <div class="text-center p-3 bg-gray-50 rounded border border-gray-200">
                    <div class="text-xl font-bold text-emerald-600">{{ $apptStats['completed'] }}</div>
                    <div class="text-[10px] text-gray-500 font-medium">Completed</div>
                </div>
                <div class="text-center p-3 bg-gray-50 rounded border border-gray-200">
                    <div class="text-xl font-bold text-red-600">{{ $apptStats['cancelled'] }}</div>
                    <div class="text-[10px] text-gray-500 font-medium">Cancelled</div>
                </div>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead><tr class="text-left text-xs text-gray-500 bg-gray-50/50">
                    <th class="px-5 py-2.5 font-medium">Time</th>
                    <th class="px-5 py-2.5 font-medium">Patient</th>
                    <th class="px-5 py-2.5 font-medium">Doctor</th>
                    <th class="px-5 py-2.5 font-medium">Status</th>
                </tr></thead>
                <tbody>
                    @forelse($todayAppointments as $appt)
                    <tr class="border-t border-gray-200 hover:bg-gray-50/50 transition-colors cursor-pointer" onclick="window.location.href='{{ route('appointments.edit', $appt) }}'">
                        <td class="px-5 py-2.5">
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 rounded-full bg-slate-100 text-slate-600 flex items-center justify-center text-xs font-bold">
                                    {{ substr($appt->startTime(), 0, 2) }}
                                </div>
                                <span class="text-xs text-gray-900 font-medium">{{ $appt->startTime() }}</span>
                            </div>
                        </td>
                        <td class="px-5 py-2.5">
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 rounded-full bg-slate-100 text-slate-600 flex items-center justify-center text-xs font-bold">
                                    {{ strtoupper(substr($appt->patient?->name ?? '', 0, 1)) }}
                                </div>
                                <div>
                                    <div class="text-xs text-gray-900 font-medium">{{ $appt->patient?->name ?? '-' }}</div>
                                    <div class="text-[10px] text-gray-400">{{ $appt->patient?->mrn ?? '' }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-2.5">
                            <div class="flex items-center gap-2">
                                <span class="text-xs text-gray-700">{{ $appt->doctor?->name ?? 'Unassigned' }}</span>
                                @if($appt->status !== 'completed')
                                    <button type="button" onclick="openChangeDoctorModal({{ $appt->id }}, '{{ $appt->doctor_id ?? '' }}')" class="text-slate-600 hover:text-slate-800">
                                        <i class="hgi-stroke hgi-arrow-left-right text-base"></i>
                                    </button>
                                @endif
                            </div>
                        </td>
                        <td class="px-5 py-2.5">
                            <span class="inline-flex items-center gap-1.5 text-[10px] font-medium capitalize text-gray-600">
                                <span class="w-1.5 h-1.5 rounded-full {{ $appt->status === 'completed' ? 'bg-emerald-500' : ($appt->status === 'cancelled' ? 'bg-red-500' : 'bg-amber-500') }}"></span>
                                {{ str_replace('_', ' ', $appt->status) }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="px-5 py-10 text-center text-gray-400 text-xs">
                        <div class="flex flex-col items-center gap-2">
                            <i class="hgi-stroke hgi-calendar-03 text-4xl text-gray-300"></i>
                            <p>No appointments today</p>
                        </div>
                    </td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="bg-white rounded-md border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-200 flex items-center justify-between">
            <h3 class="text-sm font-semibold text-gray-900">Recent Patients</h3>
            <a href="{{ route('patients.index') }}" class="text-xs font-medium text-slate-600 hover:text-slate-800">View All</a>
        </div>
        <div class="p-5 space-y-3">
            @forelse($recentPatients as $p)
            <div class="flex items-center gap-3 p-2 rounded-lg hover:bg-gray-50 transition-colors cursor-pointer" onclick="window.location.href='{{ route('patients.show', $p) }}'">
                <div class="w-10 h-10 rounded-full bg-slate-100 text-slate-600 flex items-center justify-center font-bold text-sm">
                    {{ strtoupper(substr($p->name, 0, 1)) }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-gray-900 truncate">{{ $p->name }}</p>
                    <p class="text-xs text-gray-500">{{ $p->mrn }} • {{ $p->phone ?? 'No phone' }}</p>
                </div>
                <div class="text-right">
                    <span class="text-[10px] text-gray-400">{{ $p->created_at->diffForHumans() }}</span>
                    <div class="text-[10px] text-gray-500 font-medium">{{ $p->visits_count ?? 0 }} visits</div>
                </div>
            </div>
            @empty
            <p class="text-sm text-gray-400 text-center py-4">No patients yet</p>
            @endforelse
        </div>
    </div>
</div>
@endif

<script>
const fmt = (n) => 'TSh ' + n.toLocaleString();

const weeklyData = {{ $revenueDays->values() }};
const weeklyLabels = @json($dayLabels);
const monthlyData = {{ $monthlyDays->values() }};
const monthlyLabels = @json($monthLabels);
const visitData = {{ $visitTrend->values() }};
const visitLabels = @json($visitLabels);
const deptLabels = @json($departmentPerformance->pluck('name'));
const deptData = {{ $departmentPerformance->pluck('visits') }};

let revenueChart, visitChart, deptChart;

function initCharts() {
    const revCtx = document.getElementById('revenueChart').getContext('2d');
    const gradient = revCtx.createLinearGradient(0, 0, 0, 300);
    gradient.addColorStop(0, 'rgba(5, 150, 105, 0.08)');
    gradient.addColorStop(1, 'rgba(5, 150, 105, 0)');

    revenueChart = new Chart(revCtx, {
        type: 'line',
        data: {
            labels: weeklyLabels,
            datasets: [{
                label: 'Revenue',
                data: weeklyData,
                borderColor: '#059669',
                backgroundColor: gradient,
                borderWidth: 2,
                pointBackgroundColor: '#ffffff',
                pointBorderColor: '#059669',
                pointBorderWidth: 2,
                pointRadius: 2,
                pointHoverRadius: 4,
                fill: true,
                tension: 0.35
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false }, tooltip: { callbacks: { label: (ctx) => fmt(ctx.raw) } } },
            scales: { y: { beginAtZero: true, grid: { color: '#f1f5f9' }, ticks: { font: { size: 10 } } }, x: { grid: { display: false }, ticks: { font: { size: 10 } } } }
        }
    });

    visitChart = new Chart(document.getElementById('visitTrendChart'), {
        type: 'line',
        data: {
            labels: visitLabels,
            datasets: [{
                label: 'Visits',
                data: visitData,
                borderColor: '#94a3b8',
                backgroundColor: 'rgba(148, 163, 184, 0.08)',
                borderWidth: 2,
                borderDash: [5, 4],
                pointBackgroundColor: '#ffffff',
                pointBorderColor: '#94a3b8',
                pointBorderWidth: 2,
                pointRadius: 2,
                fill: true,
                tension: 0.35
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true, grid: { color: '#f1f5f9' }, ticks: { font: { size: 10 } } }, x: { grid: { display: false }, ticks: { font: { size: 10 } } } }
        }
    });

    deptChart = new Chart(document.getElementById('departmentChart'), {
        type: 'bar',
        data: {
            labels: deptLabels,
            datasets: [{
                label: 'Visits',
                data: deptData,
                backgroundColor: ['#334155', '#94a3b8', '#059669', '#f59e0b', '#cbd5e1', '#dc2626'],
                borderRadius: 6,
                barThickness: 24
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true, grid: { color: '#f1f5f9' }, ticks: { font: { size: 10 } } }, x: { grid: { display: false }, ticks: { font: { size: 10 } } } }
        }
    });
}

function switchRevenue(period) {
    document.getElementById('revWeeklyBtn').className = period === 'weekly'
        ? 'px-3 py-1 text-xs font-medium rounded-md bg-white text-slate-800 shadow-sm'
        : 'px-3 py-1 text-xs font-medium rounded-md text-gray-500 hover:text-gray-700';
    document.getElementById('revMonthlyBtn').className = period === 'monthly'
        ? 'px-3 py-1 text-xs font-medium rounded-md bg-white text-slate-800 shadow-sm'
        : 'px-3 py-1 text-xs font-medium rounded-md text-gray-500 hover:text-gray-700';

    revenueChart.data.labels = period === 'weekly' ? weeklyLabels : monthlyLabels;
    revenueChart.data.datasets[0].data = period === 'weekly' ? weeklyData : monthlyData;
    revenueChart.update();
}

function openDashboardPatientModal() {
    const modal = document.getElementById('dashboardPatientModal');
    const panel = document.getElementById('dashboardPatientSlidePanel');
    modal.classList.remove('hidden');
    setTimeout(() => panel.classList.remove('translate-x-full'), 10);
    document.body.style.overflow = 'hidden';
}
function closeDashboardPatientModal() {
    const modal = document.getElementById('dashboardPatientModal');
    const panel = document.getElementById('dashboardPatientSlidePanel');
    panel.classList.add('translate-x-full');
    setTimeout(() => { modal.classList.add('hidden'); document.body.style.overflow = ''; }, 300);
}

function openDashboardAppointmentModal() {
    const modal = document.getElementById('dashboardAppointmentModal');
    const panel = document.getElementById('dashboardAppointmentSlidePanel');
    modal.classList.remove('hidden');
    setTimeout(() => panel.classList.remove('translate-x-full'), 10);
    document.body.style.overflow = 'hidden';
}
function closeDashboardAppointmentModal() {
    const modal = document.getElementById('dashboardAppointmentModal');
    const panel = document.getElementById('dashboardAppointmentSlidePanel');
    panel.classList.add('translate-x-full');
    setTimeout(() => { modal.classList.add('hidden'); document.body.style.overflow = ''; }, 300);
}

document.getElementById('dashApptPatientSearch')?.addEventListener('input', function() {
    const q = this.value.toLowerCase();
    const select = document.getElementById('dashApptPatientSelect');
    Array.from(select.querySelectorAll('option')).forEach(opt => {
        if (!opt.value) { opt.style.display = 'block'; return; }
        opt.style.display = opt.dataset.search?.includes(q) ? 'block' : 'none';
    });
});

// AJAX patient form
setupAjaxForm('#dashboardPatientForm', 'Patient saved successfully.', closeDashboardPatientModal);
// AJAX appointment form
setupAjaxForm('#dashboardAppointmentForm', 'Appointment booked successfully.', closeDashboardAppointmentModal);

// Doctor filter
function filterVisitsByDoctor() {
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

// Change doctor modal
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

// Change doctor form AJAX
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

// Initialize charts
initCharts();

// Real-time refresh every 60 seconds
setInterval(() => {
    fetch('/dashboard/stats', { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
        .then(r => r.json())
        .then(data => {
            if (data.revenueDays) {
                revenueChart.data.datasets[0].data = data.revenueDays;
                revenueChart.data.labels = data.dayLabels;
                revenueChart.update();
            }
            if (data.visitTrend) {
                visitChart.data.datasets[0].data = data.visitTrend;
                visitChart.data.labels = data.visitLabels;
                visitChart.update();
            }
            if (data.stats) {
                document.querySelectorAll('.card-sm p.text-xl').forEach(el => {
                    const label = el.previousElementSibling?.textContent?.trim();
                    if (label === 'Today\'s Visits') el.textContent = data.stats.visits_today;
                    if (label === 'Weekly Revenue') el.textContent = 'TSh ' + Number(data.stats.revenue_this_week).toLocaleString();
                    if (label === 'Pending Payments') el.textContent = data.stats.pending_payments;
                });
            }
        })
        .catch(() => {});
}, 60000);
</script>
<div class="bg-white rounded-md border border-gray-200 shadow-sm overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
        <h3 class="text-sm font-semibold text-gray-900">Recent Visits</h3>
        <div class="flex items-center gap-2">
            <x-ui.select id="doctorFilter" onchange="filterVisitsByDoctor()" class="text-xs border border-gray-200 rounded-lg px-2 py-1.5 focus:ring-2 focus:ring-slate-300 bg-white">
                <option value="">All Doctors</option>
                @foreach($doctorsList as $d)
                    <option value="{{ $d->id }}">{{ $d->name }}</option>
                @endforeach
            </x-ui.select>
        </div>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left">
            <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                <tr>
                    <th class="px-6 py-3">Visit #</th>
                    <th class="px-6 py-3">Patient</th>
                    <th class="px-6 py-3">Doctor</th>
                    <th class="px-6 py-3">Status</th>
                    <th class="px-6 py-3">Date</th>
                    <th class="px-6 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody id="visitsTableBody">
                @forelse ($recentVisits as $visit)
                    <tr class="border-t border-gray-200 hover:bg-gray-50/50 transition-colors visit-row" data-doctor-id="{{ $visit->doctor_id ?? '' }}">
                        <td class="px-6 py-3 font-medium">{{ $visit->visit_number }}</td>
                        <td class="px-6 py-3">
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 rounded-full bg-slate-100 text-slate-600 flex items-center justify-center text-xs font-bold">
                                    {{ strtoupper(substr($visit->patient->name, 0, 1)) }}
                                </div>
                                <span>{{ $visit->patient->fullName() }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-3">
                            <div class="flex items-center gap-2">
                                <span class="text-xs text-gray-700">{{ $visit->doctor?->name ?? 'Unassigned' }}</span>
                                @if($visit->status !== 'completed')
                                    <button type="button" onclick="openChangeDoctorModal({{ $visit->id }}, '{{ $visit->doctor_id ?? '' }}')" class="text-slate-600 hover:text-slate-800">
                                        <i class="hgi-stroke hgi-arrow-left-right text-base"></i>
                                    </button>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-3">
                            <span class="inline-flex items-center gap-1.5 text-[10px] font-medium capitalize text-gray-600">
                                <span class="w-1.5 h-1.5 rounded-full {{ $visit->status === 'completed' ? 'bg-emerald-500' : ($visit->status === 'cancelled' ? 'bg-red-500' : 'bg-amber-500') }}"></span>
                                {{ str_replace('_', ' ', $visit->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-3 text-gray-500 text-xs">{{ $visit->registered_at->format('d M Y H:i') }}</td>
                        <td class="px-6 py-3 text-right">
                            <span class="text-gray-400 text-xs">-</span>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-6 py-6 text-center text-gray-400">No visits yet</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<script>
const fmt = (n) => 'TSh ' + n.toLocaleString();

const weeklyData = {{ $revenueDays->values() }};
const weeklyLabels = @json($dayLabels);
const monthlyData = {{ $monthlyDays->values() }};
const monthlyLabels = @json($monthLabels);
const visitData = {{ $visitTrend->values() }};
const visitLabels = @json($visitLabels);
const deptLabels = @json($departmentPerformance->pluck('name'));
const deptData = {{ $departmentPerformance->pluck('visits') }};

let revenueChart, visitChart, deptChart;

function initCharts() {
    const revCtx = document.getElementById('revenueChart').getContext('2d');
    const gradient = revCtx.createLinearGradient(0, 0, 0, 300);
    gradient.addColorStop(0, 'rgba(5, 150, 105, 0.08)');
    gradient.addColorStop(1, 'rgba(5, 150, 105, 0)');

    revenueChart = new Chart(revCtx, {
        type: 'line',
        data: {
            labels: weeklyLabels,
            datasets: [{
                label: 'Revenue',
                data: weeklyData,
                borderColor: '#059669',
                backgroundColor: gradient,
                borderWidth: 2,
                pointBackgroundColor: '#ffffff',
                pointBorderColor: '#059669',
                pointBorderWidth: 2,
                pointRadius: 2,
                pointHoverRadius: 4,
                fill: true,
                tension: 0.35
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false }, tooltip: { callbacks: { label: (ctx) => fmt(ctx.raw) } } },
            scales: { y: { beginAtZero: true, grid: { color: '#f1f5f9' }, ticks: { font: { size: 10 } } }, x: { grid: { display: false }, ticks: { font: { size: 10 } } } }
        }
    });

    visitChart = new Chart(document.getElementById('visitTrendChart'), {
        type: 'line',
        data: {
            labels: visitLabels,
            datasets: [{
                label: 'Visits',
                data: visitData,
                borderColor: '#94a3b8',
                backgroundColor: 'rgba(148, 163, 184, 0.08)',
                borderWidth: 2,
                borderDash: [5, 4],
                pointBackgroundColor: '#ffffff',
                pointBorderColor: '#94a3b8',
                pointBorderWidth: 2,
                pointRadius: 2,
                fill: true,
                tension: 0.35
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true, grid: { color: '#f1f5f9' }, ticks: { font: { size: 10 } } }, x: { grid: { display: false }, ticks: { font: { size: 10 } } } }
        }
    });

    deptChart = new Chart(document.getElementById('departmentChart'), {
        type: 'bar',
        data: {
            labels: deptLabels,
            datasets: [{
                label: 'Visits',
                data: deptData,
                backgroundColor: ['#334155', '#94a3b8', '#059669', '#f59e0b', '#cbd5e1', '#dc2626'],
                borderRadius: 6,
                barThickness: 24
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true, grid: { color: '#f1f5f9' }, ticks: { font: { size: 10 } } }, x: { grid: { display: false }, ticks: { font: { size: 10 } } } }
        }
    });
}

function switchRevenue(period) {
    document.getElementById('revWeeklyBtn').className = period === 'weekly'
        ? 'px-3 py-1 text-xs font-medium rounded-md bg-white text-slate-800 shadow-sm'
        : 'px-3 py-1 text-xs font-medium rounded-md text-gray-500 hover:text-gray-700';
    document.getElementById('revMonthlyBtn').className = period === 'monthly'
        ? 'px-3 py-1 text-xs font-medium rounded-md bg-white text-slate-800 shadow-sm'
        : 'px-3 py-1 text-xs font-medium rounded-md text-gray-500 hover:text-gray-700';

    revenueChart.data.labels = period === 'weekly' ? weeklyLabels : monthlyLabels;
    revenueChart.data.datasets[0].data = period === 'weekly' ? weeklyData : monthlyData;
    revenueChart.update();
}

function openDashboardPatientModal() {
    const modal = document.getElementById('dashboardPatientModal');
    const panel = document.getElementById('dashboardPatientSlidePanel');
    modal.classList.remove('hidden');
    setTimeout(() => panel.classList.remove('translate-x-full'), 10);
    document.body.style.overflow = 'hidden';
}
function closeDashboardPatientModal() {
    const modal = document.getElementById('dashboardPatientModal');
    const panel = document.getElementById('dashboardPatientSlidePanel');
    panel.classList.add('translate-x-full');
    setTimeout(() => { modal.classList.add('hidden'); document.body.style.overflow = ''; }, 300);
}

function openDashboardAppointmentModal() {
    const modal = document.getElementById('dashboardAppointmentModal');
    const panel = document.getElementById('dashboardAppointmentSlidePanel');
    modal.classList.remove('hidden');
    setTimeout(() => panel.classList.remove('translate-x-full'), 10);
    document.body.style.overflow = 'hidden';
}
function closeDashboardAppointmentModal() {
    const modal = document.getElementById('dashboardAppointmentModal');
    const panel = document.getElementById('dashboardAppointmentSlidePanel');
    panel.classList.add('translate-x-full');
    setTimeout(() => { modal.classList.add('hidden'); document.body.style.overflow = ''; }, 300);
}

document.getElementById('dashApptPatientSearch')?.addEventListener('input', function() {
    const q = this.value.toLowerCase();
    const select = document.getElementById('dashApptPatientSelect');
    Array.from(select.querySelectorAll('option')).forEach(opt => {
        if (!opt.value) { opt.style.display = 'block'; return; }
        opt.style.display = opt.dataset.search?.includes(q) ? 'block' : 'none';
    });
});

// AJAX patient form
setupAjaxForm('#dashboardPatientForm', 'Patient saved successfully.', closeDashboardPatientModal);
// AJAX appointment form
setupAjaxForm('#dashboardAppointmentForm', 'Appointment booked successfully.', closeDashboardAppointmentModal);

// Doctor filter
function filterVisitsByDoctor() {
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

// Change doctor modal
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

// Change doctor form AJAX
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

// Initialize charts
initCharts();

// Real-time refresh every 60 seconds
setInterval(() => {
    fetch('/dashboard/stats', { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
        .then(r => r.json())
        .then(data => {
            if (data.revenueDays) {
                revenueChart.data.datasets[0].data = data.revenueDays;
                revenueChart.data.labels = data.dayLabels;
                revenueChart.update();
            }
            if (data.visitTrend) {
                visitChart.data.datasets[0].data = data.visitTrend;
                visitChart.data.labels = data.visitLabels;
                visitChart.update();
            }
            if (data.stats) {
                document.querySelectorAll('.card-sm p.text-xl').forEach(el => {
                    const label = el.previousElementSibling?.textContent?.trim();
                    if (label === 'Today\'s Visits') el.textContent = data.stats.visits_today;
                    if (label === 'Weekly Revenue') el.textContent = 'TSh ' + Number(data.stats.revenue_this_week).toLocaleString();
                    if (label === 'Pending Payments') el.textContent = data.stats.pending_payments;
                });
            }
        })
        .catch(() => {});
}, 60000);
</script>
<div id="changeDoctorModal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm transition-opacity" onclick="closeChangeDoctorModal()"></div>
    <div class="absolute inset-0 flex items-center justify-center p-4">
        <div class="bg-white rounded-md shadow-2xl w-full max-w-md transform transition-all scale-95 opacity-0" id="changeDoctorPanel">
            <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
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
                        <x-ui.select name="doctor_id" id="changeDoctorSelect" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-slate-300 bg-white" required>
                            <option value="">Select doctor...</option>
                            @foreach($doctorsList as $d)
                            <option value="{{ $d->id }}">{{ $d->name }}</option>
                            @endforeach
                        </x-ui.select>
                    </div>
                    <div class="flex justify-end gap-2 mt-4">
                        <button type="button" onclick="closeChangeDoctorModal()" class="px-4 py-2 text-sm text-gray-600 hover:bg-gray-100 rounded-lg">Cancel</button>
                        <button type="submit" class="px-4 py-2 bg-slate-800 text-white text-sm font-medium rounded hover:bg-slate-700">Change Doctor</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
const fmt = (n) => 'TSh ' + n.toLocaleString();

const weeklyData = {{ $revenueDays->values() }};
const weeklyLabels = @json($dayLabels);
const monthlyData = {{ $monthlyDays->values() }};
const monthlyLabels = @json($monthLabels);
const visitData = {{ $visitTrend->values() }};
const visitLabels = @json($visitLabels);
const deptLabels = @json($departmentPerformance->pluck('name'));
const deptData = {{ $departmentPerformance->pluck('visits') }};

let revenueChart, visitChart, deptChart;

function initCharts() {
    const revCtx = document.getElementById('revenueChart').getContext('2d');
    const gradient = revCtx.createLinearGradient(0, 0, 0, 300);
    gradient.addColorStop(0, 'rgba(5, 150, 105, 0.08)');
    gradient.addColorStop(1, 'rgba(5, 150, 105, 0)');

    revenueChart = new Chart(revCtx, {
        type: 'line',
        data: {
            labels: weeklyLabels,
            datasets: [{
                label: 'Revenue',
                data: weeklyData,
                borderColor: '#059669',
                backgroundColor: gradient,
                borderWidth: 2,
                pointBackgroundColor: '#ffffff',
                pointBorderColor: '#059669',
                pointBorderWidth: 2,
                pointRadius: 2,
                pointHoverRadius: 4,
                fill: true,
                tension: 0.35
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false }, tooltip: { callbacks: { label: (ctx) => fmt(ctx.raw) } } },
            scales: { y: { beginAtZero: true, grid: { color: '#f1f5f9' }, ticks: { font: { size: 10 } } }, x: { grid: { display: false }, ticks: { font: { size: 10 } } } }
        }
    });

    visitChart = new Chart(document.getElementById('visitTrendChart'), {
        type: 'line',
        data: {
            labels: visitLabels,
            datasets: [{
                label: 'Visits',
                data: visitData,
                borderColor: '#94a3b8',
                backgroundColor: 'rgba(148, 163, 184, 0.08)',
                borderWidth: 2,
                borderDash: [5, 4],
                pointBackgroundColor: '#ffffff',
                pointBorderColor: '#94a3b8',
                pointBorderWidth: 2,
                pointRadius: 2,
                fill: true,
                tension: 0.35
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true, grid: { color: '#f1f5f9' }, ticks: { font: { size: 10 } } }, x: { grid: { display: false }, ticks: { font: { size: 10 } } } }
        }
    });

    deptChart = new Chart(document.getElementById('departmentChart'), {
        type: 'bar',
        data: {
            labels: deptLabels,
            datasets: [{
                label: 'Visits',
                data: deptData,
                backgroundColor: ['#334155', '#94a3b8', '#059669', '#f59e0b', '#cbd5e1', '#dc2626'],
                borderRadius: 6,
                barThickness: 24
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true, grid: { color: '#f1f5f9' }, ticks: { font: { size: 10 } } }, x: { grid: { display: false }, ticks: { font: { size: 10 } } } }
        }
    });
}

function switchRevenue(period) {
    document.getElementById('revWeeklyBtn').className = period === 'weekly'
        ? 'px-3 py-1 text-xs font-medium rounded-md bg-white text-slate-800 shadow-sm'
        : 'px-3 py-1 text-xs font-medium rounded-md text-gray-500 hover:text-gray-700';
    document.getElementById('revMonthlyBtn').className = period === 'monthly'
        ? 'px-3 py-1 text-xs font-medium rounded-md bg-white text-slate-800 shadow-sm'
        : 'px-3 py-1 text-xs font-medium rounded-md text-gray-500 hover:text-gray-700';

    revenueChart.data.labels = period === 'weekly' ? weeklyLabels : monthlyLabels;
    revenueChart.data.datasets[0].data = period === 'weekly' ? weeklyData : monthlyData;
    revenueChart.update();
}

function openDashboardPatientModal() {
    const modal = document.getElementById('dashboardPatientModal');
    const panel = document.getElementById('dashboardPatientSlidePanel');
    modal.classList.remove('hidden');
    setTimeout(() => panel.classList.remove('translate-x-full'), 10);
    document.body.style.overflow = 'hidden';
}
function closeDashboardPatientModal() {
    const modal = document.getElementById('dashboardPatientModal');
    const panel = document.getElementById('dashboardPatientSlidePanel');
    panel.classList.add('translate-x-full');
    setTimeout(() => { modal.classList.add('hidden'); document.body.style.overflow = ''; }, 300);
}

function openDashboardAppointmentModal() {
    const modal = document.getElementById('dashboardAppointmentModal');
    const panel = document.getElementById('dashboardAppointmentSlidePanel');
    modal.classList.remove('hidden');
    setTimeout(() => panel.classList.remove('translate-x-full'), 10);
    document.body.style.overflow = 'hidden';
}
function closeDashboardAppointmentModal() {
    const modal = document.getElementById('dashboardAppointmentModal');
    const panel = document.getElementById('dashboardAppointmentSlidePanel');
    panel.classList.add('translate-x-full');
    setTimeout(() => { modal.classList.add('hidden'); document.body.style.overflow = ''; }, 300);
}

document.getElementById('dashApptPatientSearch')?.addEventListener('input', function() {
    const q = this.value.toLowerCase();
    const select = document.getElementById('dashApptPatientSelect');
    Array.from(select.querySelectorAll('option')).forEach(opt => {
        if (!opt.value) { opt.style.display = 'block'; return; }
        opt.style.display = opt.dataset.search?.includes(q) ? 'block' : 'none';
    });
});

// AJAX patient form
setupAjaxForm('#dashboardPatientForm', 'Patient saved successfully.', closeDashboardPatientModal);
// AJAX appointment form
setupAjaxForm('#dashboardAppointmentForm', 'Appointment booked successfully.', closeDashboardAppointmentModal);

// Doctor filter
function filterVisitsByDoctor() {
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

// Change doctor modal
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

// Change doctor form AJAX
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

// Initialize charts
initCharts();

// Real-time refresh every 60 seconds
setInterval(() => {
    fetch('/dashboard/stats', { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
        .then(r => r.json())
        .then(data => {
            if (data.revenueDays) {
                revenueChart.data.datasets[0].data = data.revenueDays;
                revenueChart.data.labels = data.dayLabels;
                revenueChart.update();
            }
            if (data.visitTrend) {
                visitChart.data.datasets[0].data = data.visitTrend;
                visitChart.data.labels = data.visitLabels;
                visitChart.update();
            }
            if (data.stats) {
                document.querySelectorAll('.card-sm p.text-xl').forEach(el => {
                    const label = el.previousElementSibling?.textContent?.trim();
                    if (label === 'Today\'s Visits') el.textContent = data.stats.visits_today;
                    if (label === 'Weekly Revenue') el.textContent = 'TSh ' + Number(data.stats.revenue_this_week).toLocaleString();
                    if (label === 'Pending Payments') el.textContent = data.stats.pending_payments;
                });
            }
        })
        .catch(() => {});
}, 60000);
</script>
<div id="dashboardPatientModal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm transition-opacity" onclick="closeDashboardPatientModal()"></div>
    <div class="absolute inset-y-0 right-0 w-full max-w-xl bg-white shadow-2xl transform transition-transform translate-x-full duration-300 ease-in-out flex flex-col" id="dashboardPatientSlidePanel">
        <div class="px-5 py-4 border-b border-gray-200 flex items-center gap-3 bg-white">
            <div class="w-9 h-9 rounded-md bg-slate-100 flex items-center justify-center flex-shrink-0">
                <i class="hgi-stroke hgi-user-add-01 text-xl text-slate-600"></i>
            </div>
            <div class="flex-1">
                <h3 class="text-sm font-semibold text-gray-900">Add Patient</h3>
                <p class="text-[11px] text-gray-400 mt-0.5">Register a new patient record</p>
            </div>
            <button onclick="closeDashboardPatientModal()" class="p-1.5 rounded-md hover:bg-gray-100 text-gray-400 hover:text-gray-600 transition-colors">
                <i class="hgi-stroke hgi-cancel-01 text-base"></i>
            </button>
        </div>
        <div class="flex-1 overflow-y-auto p-5">
            <form id="dashboardPatientForm" method="POST" action="{{ route('reception.patients.store') }}">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Full Name</label>
                        <x-ui.input type="text" name="name" placeholder="e.g. Amina Juma" class="w-full text-sm border border-gray-200 rounded-md px-3 py-2 outline-none focus:ring-2 focus:ring-slate-300 transition" required />
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Phone</label>
                        <x-ui.input type="text" name="phone" placeholder="e.g. 0678 233 736" class="w-full text-sm border border-gray-200 rounded-md px-3 py-2 outline-none focus:ring-2 focus:ring-slate-300 transition" />
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Email</label>
                        <x-ui.input type="email" name="email" placeholder="e.g. patient@example.com" class="w-full text-sm border border-gray-200 rounded-md px-3 py-2 outline-none focus:ring-2 focus:ring-slate-300 transition" />
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Date of Birth</label>
                            <x-ui.input type="text" data-fp-date name="date_of_birth" class="w-full text-sm border border-gray-200 rounded-md px-3 py-2 outline-none focus:ring-2 focus:ring-slate-300 transition" />
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Gender</label>
                            <x-ui.select name="gender" class="w-full text-sm border border-gray-200 rounded-md px-3 py-2 outline-none focus:ring-2 focus:ring-slate-300 transition">
                                <option value="">Select</option>
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                                <option value="other">Other</option>
                            </x-ui.select>
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Address</label>
                        <x-ui.textarea name="address" placeholder="e.g. Mlimani City, Dar es Salaam" class="w-full text-sm border border-gray-200 rounded-md px-3 py-2 outline-none focus:ring-2 focus:ring-slate-300 transition" rows="3"></x-ui.textarea>
                    </div>
                    <div class="flex items-center justify-end gap-2 pt-4 border-t border-gray-100">
                        <button type="button" onclick="closeDashboardPatientModal()" class="px-4 py-2 text-xs font-medium border border-gray-200 rounded-md text-gray-700 hover:bg-gray-100 transition-colors">Cancel</button>
                        <button type="submit" class="px-4 py-2 text-xs font-medium bg-slate-800 text-white rounded-md hover:bg-slate-700 transition-colors flex items-center gap-1.5">
                            <i class="hgi-stroke hgi-tick-02 text-sm"></i>
                            Save Patient
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
const fmt = (n) => 'TSh ' + n.toLocaleString();

const weeklyData = {{ $revenueDays->values() }};
const weeklyLabels = @json($dayLabels);
const monthlyData = {{ $monthlyDays->values() }};
const monthlyLabels = @json($monthLabels);
const visitData = {{ $visitTrend->values() }};
const visitLabels = @json($visitLabels);
const deptLabels = @json($departmentPerformance->pluck('name'));
const deptData = {{ $departmentPerformance->pluck('visits') }};

let revenueChart, visitChart, deptChart;

function initCharts() {
    const revCtx = document.getElementById('revenueChart').getContext('2d');
    const gradient = revCtx.createLinearGradient(0, 0, 0, 300);
    gradient.addColorStop(0, 'rgba(5, 150, 105, 0.08)');
    gradient.addColorStop(1, 'rgba(5, 150, 105, 0)');

    revenueChart = new Chart(revCtx, {
        type: 'line',
        data: {
            labels: weeklyLabels,
            datasets: [{
                label: 'Revenue',
                data: weeklyData,
                borderColor: '#059669',
                backgroundColor: gradient,
                borderWidth: 2,
                pointBackgroundColor: '#ffffff',
                pointBorderColor: '#059669',
                pointBorderWidth: 2,
                pointRadius: 2,
                pointHoverRadius: 4,
                fill: true,
                tension: 0.35
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false }, tooltip: { callbacks: { label: (ctx) => fmt(ctx.raw) } } },
            scales: { y: { beginAtZero: true, grid: { color: '#f1f5f9' }, ticks: { font: { size: 10 } } }, x: { grid: { display: false }, ticks: { font: { size: 10 } } } }
        }
    });

    visitChart = new Chart(document.getElementById('visitTrendChart'), {
        type: 'line',
        data: {
            labels: visitLabels,
            datasets: [{
                label: 'Visits',
                data: visitData,
                borderColor: '#94a3b8',
                backgroundColor: 'rgba(148, 163, 184, 0.08)',
                borderWidth: 2,
                borderDash: [5, 4],
                pointBackgroundColor: '#ffffff',
                pointBorderColor: '#94a3b8',
                pointBorderWidth: 2,
                pointRadius: 2,
                fill: true,
                tension: 0.35
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true, grid: { color: '#f1f5f9' }, ticks: { font: { size: 10 } } }, x: { grid: { display: false }, ticks: { font: { size: 10 } } } }
        }
    });

    deptChart = new Chart(document.getElementById('departmentChart'), {
        type: 'bar',
        data: {
            labels: deptLabels,
            datasets: [{
                label: 'Visits',
                data: deptData,
                backgroundColor: ['#334155', '#94a3b8', '#059669', '#f59e0b', '#cbd5e1', '#dc2626'],
                borderRadius: 6,
                barThickness: 24
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true, grid: { color: '#f1f5f9' }, ticks: { font: { size: 10 } } }, x: { grid: { display: false }, ticks: { font: { size: 10 } } } }
        }
    });
}

function switchRevenue(period) {
    document.getElementById('revWeeklyBtn').className = period === 'weekly'
        ? 'px-3 py-1 text-xs font-medium rounded-md bg-white text-slate-800 shadow-sm'
        : 'px-3 py-1 text-xs font-medium rounded-md text-gray-500 hover:text-gray-700';
    document.getElementById('revMonthlyBtn').className = period === 'monthly'
        ? 'px-3 py-1 text-xs font-medium rounded-md bg-white text-slate-800 shadow-sm'
        : 'px-3 py-1 text-xs font-medium rounded-md text-gray-500 hover:text-gray-700';

    revenueChart.data.labels = period === 'weekly' ? weeklyLabels : monthlyLabels;
    revenueChart.data.datasets[0].data = period === 'weekly' ? weeklyData : monthlyData;
    revenueChart.update();
}

function openDashboardPatientModal() {
    const modal = document.getElementById('dashboardPatientModal');
    const panel = document.getElementById('dashboardPatientSlidePanel');
    modal.classList.remove('hidden');
    setTimeout(() => panel.classList.remove('translate-x-full'), 10);
    document.body.style.overflow = 'hidden';
}
function closeDashboardPatientModal() {
    const modal = document.getElementById('dashboardPatientModal');
    const panel = document.getElementById('dashboardPatientSlidePanel');
    panel.classList.add('translate-x-full');
    setTimeout(() => { modal.classList.add('hidden'); document.body.style.overflow = ''; }, 300);
}

function openDashboardAppointmentModal() {
    const modal = document.getElementById('dashboardAppointmentModal');
    const panel = document.getElementById('dashboardAppointmentSlidePanel');
    modal.classList.remove('hidden');
    setTimeout(() => panel.classList.remove('translate-x-full'), 10);
    document.body.style.overflow = 'hidden';
}
function closeDashboardAppointmentModal() {
    const modal = document.getElementById('dashboardAppointmentModal');
    const panel = document.getElementById('dashboardAppointmentSlidePanel');
    panel.classList.add('translate-x-full');
    setTimeout(() => { modal.classList.add('hidden'); document.body.style.overflow = ''; }, 300);
}

document.getElementById('dashApptPatientSearch')?.addEventListener('input', function() {
    const q = this.value.toLowerCase();
    const select = document.getElementById('dashApptPatientSelect');
    Array.from(select.querySelectorAll('option')).forEach(opt => {
        if (!opt.value) { opt.style.display = 'block'; return; }
        opt.style.display = opt.dataset.search?.includes(q) ? 'block' : 'none';
    });
});

// AJAX patient form
setupAjaxForm('#dashboardPatientForm', 'Patient saved successfully.', closeDashboardPatientModal);
// AJAX appointment form
setupAjaxForm('#dashboardAppointmentForm', 'Appointment booked successfully.', closeDashboardAppointmentModal);

// Doctor filter
function filterVisitsByDoctor() {
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

// Change doctor modal
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

// Change doctor form AJAX
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

// Initialize charts
initCharts();

// Real-time refresh every 60 seconds
setInterval(() => {
    fetch('/dashboard/stats', { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
        .then(r => r.json())
        .then(data => {
            if (data.revenueDays) {
                revenueChart.data.datasets[0].data = data.revenueDays;
                revenueChart.data.labels = data.dayLabels;
                revenueChart.update();
            }
            if (data.visitTrend) {
                visitChart.data.datasets[0].data = data.visitTrend;
                visitChart.data.labels = data.visitLabels;
                visitChart.update();
            }
            if (data.stats) {
                document.querySelectorAll('.card-sm p.text-xl').forEach(el => {
                    const label = el.previousElementSibling?.textContent?.trim();
                    if (label === 'Today\'s Visits') el.textContent = data.stats.visits_today;
                    if (label === 'Weekly Revenue') el.textContent = 'TSh ' + Number(data.stats.revenue_this_week).toLocaleString();
                    if (label === 'Pending Payments') el.textContent = data.stats.pending_payments;
                });
            }
        })
        .catch(() => {});
}, 60000);
</script>
<div id="dashboardAppointmentModal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm transition-opacity" onclick="closeDashboardAppointmentModal()"></div>
    <div class="absolute inset-y-0 right-0 w-full max-w-xl bg-white shadow-2xl transform transition-transform translate-x-full duration-300 ease-in-out flex flex-col" id="dashboardAppointmentSlidePanel">
        <div class="px-5 py-4 border-b border-gray-200 flex items-center gap-3 bg-white">
            <div class="w-9 h-9 rounded-md bg-slate-100 flex items-center justify-center flex-shrink-0">
                <i class="hgi-stroke hgi-calendar-03 text-xl text-slate-600"></i>
            </div>
            <div class="flex-1">
                <h3 class="text-sm font-semibold text-gray-900">Book Appointment</h3>
                <p class="text-[11px] text-gray-400 mt-0.5">Schedule a new appointment for a patient</p>
            </div>
            <button onclick="closeDashboardAppointmentModal()" class="p-1.5 rounded-md hover:bg-gray-100 text-gray-400 hover:text-gray-600 transition-colors">
                <i class="hgi-stroke hgi-cancel-01 text-base"></i>
            </button>
        </div>
        <div class="flex-1 overflow-y-auto p-5">
            <form id="dashboardAppointmentForm" method="POST" action="{{ route('appointments.store') }}">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Patient</label>
                        <x-ui.input type="text" id="dashApptPatientSearch" placeholder="Search patient by name or phone..." class="w-full text-sm border border-gray-200 rounded-md px-3 py-2 outline-none focus:ring-2 focus:ring-slate-300 transition mb-1" />
                        <x-ui.select name="patient_id" id="dashApptPatientSelect" class="w-full text-sm border border-gray-200 rounded-md px-3 py-2 outline-none focus:ring-2 focus:ring-slate-300 transition" required>
                            <option value="">Select patient</option>
                            @foreach($patientsList as $p)
                            <option value="{{ $p->id }}" data-search="{{ strtolower($p->fullName() . ' ' . ($p->phone ?? '') . ' ' . $p->mrn) }}">{{ $p->fullName() }} ({{ $p->mrn }})</option>
                            @endforeach
                        </x-ui.select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Doctor</label>
                        <x-ui.select name="doctor_id" class="w-full text-sm border border-gray-200 rounded-md px-3 py-2 outline-none focus:ring-2 focus:ring-slate-300 transition">
                            <option value="">Select doctor</option>
                            @foreach($doctorsList as $d)
                            <option value="{{ $d->id }}">{{ $d->name }}</option>
                            @endforeach
                        </x-ui.select>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Date</label>
                            <x-ui.input type="text" data-fp-date name="appointment_date" value="{{ today()->toDateString() }}" class="w-full text-sm border border-gray-200 rounded-md px-3 py-2 outline-none focus:ring-2 focus:ring-slate-300 transition" required />
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Start Time</label>
                            <x-ui.input type="text" data-fp-time name="start_time" class="w-full text-sm border border-gray-200 rounded-md px-3 py-2 outline-none focus:ring-2 focus:ring-slate-300 transition" required />
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Notes</label>
                        <x-ui.textarea name="notes" rows="3" placeholder="Reason for visit or additional notes..." class="w-full text-sm border border-gray-200 rounded-md px-3 py-2 outline-none focus:ring-2 focus:ring-slate-300 transition"></x-ui.textarea>
                    </div>
                    <div class="flex items-center justify-end gap-2 pt-4 border-t border-gray-100">
                        <button type="button" onclick="closeDashboardAppointmentModal()" class="px-4 py-2 text-xs font-medium border border-gray-200 rounded-md text-gray-700 hover:bg-gray-100 transition-colors">Cancel</button>
                        <button type="submit" class="px-4 py-2 text-xs font-medium bg-slate-800 text-white rounded-md hover:bg-slate-700 transition-colors flex items-center gap-1.5">
                            <i class="hgi-stroke hgi-tick-02 text-sm"></i>
                            Save Appointment
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const fmt = (n) => 'TSh ' + n.toLocaleString();

const weeklyData = {{ $revenueDays->values() }};
const weeklyLabels = @json($dayLabels);
const monthlyData = {{ $monthlyDays->values() }};
const monthlyLabels = @json($monthLabels);
const visitData = {{ $visitTrend->values() }};
const visitLabels = @json($visitLabels);
const deptLabels = @json($departmentPerformance->pluck('name'));
const deptData = {{ $departmentPerformance->pluck('visits') }};

let revenueChart, visitChart, deptChart;

function initCharts() {
    const revCtx = document.getElementById('revenueChart').getContext('2d');
    const gradient = revCtx.createLinearGradient(0, 0, 0, 300);
    gradient.addColorStop(0, 'rgba(5, 150, 105, 0.08)');
    gradient.addColorStop(1, 'rgba(5, 150, 105, 0)');

    revenueChart = new Chart(revCtx, {
        type: 'line',
        data: {
            labels: weeklyLabels,
            datasets: [{
                label: 'Revenue',
                data: weeklyData,
                borderColor: '#059669',
                backgroundColor: gradient,
                borderWidth: 2,
                pointBackgroundColor: '#ffffff',
                pointBorderColor: '#059669',
                pointBorderWidth: 2,
                pointRadius: 2,
                pointHoverRadius: 4,
                fill: true,
                tension: 0.35
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false }, tooltip: { callbacks: { label: (ctx) => fmt(ctx.raw) } } },
            scales: { y: { beginAtZero: true, grid: { color: '#f1f5f9' }, ticks: { font: { size: 10 } } }, x: { grid: { display: false }, ticks: { font: { size: 10 } } } }
        }
    });

    visitChart = new Chart(document.getElementById('visitTrendChart'), {
        type: 'line',
        data: {
            labels: visitLabels,
            datasets: [{
                label: 'Visits',
                data: visitData,
                borderColor: '#94a3b8',
                backgroundColor: 'rgba(148, 163, 184, 0.08)',
                borderWidth: 2,
                borderDash: [5, 4],
                pointBackgroundColor: '#ffffff',
                pointBorderColor: '#94a3b8',
                pointBorderWidth: 2,
                pointRadius: 2,
                fill: true,
                tension: 0.35
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true, grid: { color: '#f1f5f9' }, ticks: { font: { size: 10 } } }, x: { grid: { display: false }, ticks: { font: { size: 10 } } } }
        }
    });

    deptChart = new Chart(document.getElementById('departmentChart'), {
        type: 'bar',
        data: {
            labels: deptLabels,
            datasets: [{
                label: 'Visits',
                data: deptData,
                backgroundColor: ['#334155', '#94a3b8', '#059669', '#f59e0b', '#cbd5e1', '#dc2626'],
                borderRadius: 6,
                barThickness: 24
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true, grid: { color: '#f1f5f9' }, ticks: { font: { size: 10 } } }, x: { grid: { display: false }, ticks: { font: { size: 10 } } } }
        }
    });
}

function switchRevenue(period) {
    document.getElementById('revWeeklyBtn').className = period === 'weekly'
        ? 'px-3 py-1 text-xs font-medium rounded-md bg-white text-slate-800 shadow-sm'
        : 'px-3 py-1 text-xs font-medium rounded-md text-gray-500 hover:text-gray-700';
    document.getElementById('revMonthlyBtn').className = period === 'monthly'
        ? 'px-3 py-1 text-xs font-medium rounded-md bg-white text-slate-800 shadow-sm'
        : 'px-3 py-1 text-xs font-medium rounded-md text-gray-500 hover:text-gray-700';

    revenueChart.data.labels = period === 'weekly' ? weeklyLabels : monthlyLabels;
    revenueChart.data.datasets[0].data = period === 'weekly' ? weeklyData : monthlyData;
    revenueChart.update();
}

function openDashboardPatientModal() {
    const modal = document.getElementById('dashboardPatientModal');
    const panel = document.getElementById('dashboardPatientSlidePanel');
    modal.classList.remove('hidden');
    setTimeout(() => panel.classList.remove('translate-x-full'), 10);
    document.body.style.overflow = 'hidden';
}
function closeDashboardPatientModal() {
    const modal = document.getElementById('dashboardPatientModal');
    const panel = document.getElementById('dashboardPatientSlidePanel');
    panel.classList.add('translate-x-full');
    setTimeout(() => { modal.classList.add('hidden'); document.body.style.overflow = ''; }, 300);
}

function openDashboardAppointmentModal() {
    const modal = document.getElementById('dashboardAppointmentModal');
    const panel = document.getElementById('dashboardAppointmentSlidePanel');
    modal.classList.remove('hidden');
    setTimeout(() => panel.classList.remove('translate-x-full'), 10);
    document.body.style.overflow = 'hidden';
}
function closeDashboardAppointmentModal() {
    const modal = document.getElementById('dashboardAppointmentModal');
    const panel = document.getElementById('dashboardAppointmentSlidePanel');
    panel.classList.add('translate-x-full');
    setTimeout(() => { modal.classList.add('hidden'); document.body.style.overflow = ''; }, 300);
}

document.getElementById('dashApptPatientSearch')?.addEventListener('input', function() {
    const q = this.value.toLowerCase();
    const select = document.getElementById('dashApptPatientSelect');
    Array.from(select.querySelectorAll('option')).forEach(opt => {
        if (!opt.value) { opt.style.display = 'block'; return; }
        opt.style.display = opt.dataset.search?.includes(q) ? 'block' : 'none';
    });
});

// AJAX patient form
setupAjaxForm('#dashboardPatientForm', 'Patient saved successfully.', closeDashboardPatientModal);
// AJAX appointment form
setupAjaxForm('#dashboardAppointmentForm', 'Appointment booked successfully.', closeDashboardAppointmentModal);

// Doctor filter
function filterVisitsByDoctor() {
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

// Change doctor modal
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

// Change doctor form AJAX
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

// Initialize charts
initCharts();

// Real-time refresh every 60 seconds
setInterval(() => {
    fetch('/dashboard/stats', { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
        .then(r => r.json())
        .then(data => {
            if (data.revenueDays) {
                revenueChart.data.datasets[0].data = data.revenueDays;
                revenueChart.data.labels = data.dayLabels;
                revenueChart.update();
            }
            if (data.visitTrend) {
                visitChart.data.datasets[0].data = data.visitTrend;
                visitChart.data.labels = data.visitLabels;
                visitChart.update();
            }
            if (data.stats) {
                document.querySelectorAll('.card-sm p.text-xl').forEach(el => {
                    const label = el.previousElementSibling?.textContent?.trim();
                    if (label === 'Today\'s Visits') el.textContent = data.stats.visits_today;
                    if (label === 'Weekly Revenue') el.textContent = 'TSh ' + Number(data.stats.revenue_this_week).toLocaleString();
                    if (label === 'Pending Payments') el.textContent = data.stats.pending_payments;
                });
            }
        })
        .catch(() => {});
}, 60000);
</script>
<script>
const fmt = (n) => 'TSh ' + n.toLocaleString();

const weeklyData = {{ $revenueDays->values() }};
const weeklyLabels = @json($dayLabels);
const monthlyData = {{ $monthlyDays->values() }};
const monthlyLabels = @json($monthLabels);
const visitData = {{ $visitTrend->values() }};
const visitLabels = @json($visitLabels);
const deptLabels = @json($departmentPerformance->pluck('name'));
const deptData = {{ $departmentPerformance->pluck('visits') }};

let revenueChart, visitChart, deptChart;

function initCharts() {
    const revCtx = document.getElementById('revenueChart').getContext('2d');
    const gradient = revCtx.createLinearGradient(0, 0, 0, 300);
    gradient.addColorStop(0, 'rgba(5, 150, 105, 0.08)');
    gradient.addColorStop(1, 'rgba(5, 150, 105, 0)');

    revenueChart = new Chart(revCtx, {
        type: 'line',
        data: {
            labels: weeklyLabels,
            datasets: [{
                label: 'Revenue',
                data: weeklyData,
                borderColor: '#059669',
                backgroundColor: gradient,
                borderWidth: 2,
                pointBackgroundColor: '#ffffff',
                pointBorderColor: '#059669',
                pointBorderWidth: 2,
                pointRadius: 2,
                pointHoverRadius: 4,
                fill: true,
                tension: 0.35
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false }, tooltip: { callbacks: { label: (ctx) => fmt(ctx.raw) } } },
            scales: { y: { beginAtZero: true, grid: { color: '#f1f5f9' }, ticks: { font: { size: 10 } } }, x: { grid: { display: false }, ticks: { font: { size: 10 } } } }
        }
    });

    visitChart = new Chart(document.getElementById('visitTrendChart'), {
        type: 'line',
        data: {
            labels: visitLabels,
            datasets: [{
                label: 'Visits',
                data: visitData,
                borderColor: '#94a3b8',
                backgroundColor: 'rgba(148, 163, 184, 0.08)',
                borderWidth: 2,
                borderDash: [5, 4],
                pointBackgroundColor: '#ffffff',
                pointBorderColor: '#94a3b8',
                pointBorderWidth: 2,
                pointRadius: 2,
                fill: true,
                tension: 0.35
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true, grid: { color: '#f1f5f9' }, ticks: { font: { size: 10 } } }, x: { grid: { display: false }, ticks: { font: { size: 10 } } } }
        }
    });

    deptChart = new Chart(document.getElementById('departmentChart'), {
        type: 'bar',
        data: {
            labels: deptLabels,
            datasets: [{
                label: 'Visits',
                data: deptData,
                backgroundColor: ['#334155', '#94a3b8', '#059669', '#f59e0b', '#cbd5e1', '#dc2626'],
                borderRadius: 6,
                barThickness: 24
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true, grid: { color: '#f1f5f9' }, ticks: { font: { size: 10 } } }, x: { grid: { display: false }, ticks: { font: { size: 10 } } } }
        }
    });
}

function switchRevenue(period) {
    document.getElementById('revWeeklyBtn').className = period === 'weekly'
        ? 'px-3 py-1 text-xs font-medium rounded-md bg-white text-slate-800 shadow-sm'
        : 'px-3 py-1 text-xs font-medium rounded-md text-gray-500 hover:text-gray-700';
    document.getElementById('revMonthlyBtn').className = period === 'monthly'
        ? 'px-3 py-1 text-xs font-medium rounded-md bg-white text-slate-800 shadow-sm'
        : 'px-3 py-1 text-xs font-medium rounded-md text-gray-500 hover:text-gray-700';

    revenueChart.data.labels = period === 'weekly' ? weeklyLabels : monthlyLabels;
    revenueChart.data.datasets[0].data = period === 'weekly' ? weeklyData : monthlyData;
    revenueChart.update();
}

function openDashboardPatientModal() {
    const modal = document.getElementById('dashboardPatientModal');
    const panel = document.getElementById('dashboardPatientSlidePanel');
    modal.classList.remove('hidden');
    setTimeout(() => panel.classList.remove('translate-x-full'), 10);
    document.body.style.overflow = 'hidden';
}
function closeDashboardPatientModal() {
    const modal = document.getElementById('dashboardPatientModal');
    const panel = document.getElementById('dashboardPatientSlidePanel');
    panel.classList.add('translate-x-full');
    setTimeout(() => { modal.classList.add('hidden'); document.body.style.overflow = ''; }, 300);
}

function openDashboardAppointmentModal() {
    const modal = document.getElementById('dashboardAppointmentModal');
    const panel = document.getElementById('dashboardAppointmentSlidePanel');
    modal.classList.remove('hidden');
    setTimeout(() => panel.classList.remove('translate-x-full'), 10);
    document.body.style.overflow = 'hidden';
}
function closeDashboardAppointmentModal() {
    const modal = document.getElementById('dashboardAppointmentModal');
    const panel = document.getElementById('dashboardAppointmentSlidePanel');
    panel.classList.add('translate-x-full');
    setTimeout(() => { modal.classList.add('hidden'); document.body.style.overflow = ''; }, 300);
}

document.getElementById('dashApptPatientSearch')?.addEventListener('input', function() {
    const q = this.value.toLowerCase();
    const select = document.getElementById('dashApptPatientSelect');
    Array.from(select.querySelectorAll('option')).forEach(opt => {
        if (!opt.value) { opt.style.display = 'block'; return; }
        opt.style.display = opt.dataset.search?.includes(q) ? 'block' : 'none';
    });
});

// AJAX patient form
setupAjaxForm('#dashboardPatientForm', 'Patient saved successfully.', closeDashboardPatientModal);
// AJAX appointment form
setupAjaxForm('#dashboardAppointmentForm', 'Appointment booked successfully.', closeDashboardAppointmentModal);

// Doctor filter
function filterVisitsByDoctor() {
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

// Change doctor modal
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

// Change doctor form AJAX
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

// Initialize charts
initCharts();

// Real-time refresh every 60 seconds
setInterval(() => {
    fetch('/dashboard/stats', { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
        .then(r => r.json())
        .then(data => {
            if (data.revenueDays) {
                revenueChart.data.datasets[0].data = data.revenueDays;
                revenueChart.data.labels = data.dayLabels;
                revenueChart.update();
            }
            if (data.visitTrend) {
                visitChart.data.datasets[0].data = data.visitTrend;
                visitChart.data.labels = data.visitLabels;
                visitChart.update();
            }
            if (data.stats) {
                document.querySelectorAll('.card-sm p.text-xl').forEach(el => {
                    const label = el.previousElementSibling?.textContent?.trim();
                    if (label === 'Today\'s Visits') el.textContent = data.stats.visits_today;
                    if (label === 'Weekly Revenue') el.textContent = 'TSh ' + Number(data.stats.revenue_this_week).toLocaleString();
                    if (label === 'Pending Payments') el.textContent = data.stats.pending_payments;
                });
            }
        })
        .catch(() => {});
}, 60000);
</script>
@endpush
