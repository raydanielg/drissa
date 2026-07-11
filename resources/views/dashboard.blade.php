@extends('layouts.dashboard')

@section('title', 'Dashboard - ' . config('app.name', 'Laravel'))
@section('page_title', 'Dashboard Overview')

@section('content')
<style>
    .card-sm { transition: all 0.2s cubic-bezier(0.4,0,0.2,1); }
    .card-sm:hover { transform: translateY(-3px); box-shadow: 0 12px 30px -8px rgba(0,0,0,0.15); }
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

{{-- Welcome Header --}}
<div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
    <div>
        <h1 class="text-xl sm:text-2xl font-bold text-gray-900 tracking-tight">Hello {{ $user->name }} </h1>
        <p class="text-sm text-gray-500 mt-0.5">{{ $roleLabels[$user->roles->first()?->name] ?? 'User' }} — Here's what's happening at {{ config('app.name', 'Laravel') }}.</p>
    </div>
    <div class="flex items-center gap-2">
        @if($user->isReception() || $user->isAdmin())
            <button type="button" onclick="openDashboardPatientModal()" class="px-3 py-1.5 text-xs font-medium border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors inline-flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                Add Patient
            </button>
            <button type="button" onclick="openDashboardAppointmentModal()" class="px-3 py-1.5 text-xs font-medium bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition-colors inline-flex items-center gap-1.5 shadow-sm">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                New Appointment
            </button>
        @endif
        @if($user->isDoctor())
            <a href="{{ route('doctor.queue') }}" class="px-3 py-1.5 text-xs font-medium bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition-colors inline-flex items-center gap-1.5 shadow-sm">My Queue</a>
        @endif
    </div>
</div>

{{-- KPI Stats Cards --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-6">
    @foreach([
        ['label'=>'Total Patients','value'=>number_format($stats['total_patients']),'change'=>'+'.$stats['total_visits'].' visits','link'=>null,'icon'=>'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z','from'=>'emerald-600','to'=>'emerald-700','border'=>'emerald-500','text'=>'emerald-100','sub'=>'emerald-200'],
        ['label'=>'Today\'s Visits','value'=>$stats['visits_today'],'change'=>$todayAppointments->count().' appts today','link'=>null,'icon'=>'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z','from'=>'gold-400','to'=>'gold-500','border'=>'gold-300','text'=>'amber-50','sub'=>'amber-100'],
        ['label'=>'Weekly Revenue','value'=>$fmt($stats['revenue_this_week']),'change'=>'Today: '.$fmt($stats['revenue_today']),'link'=>route('invoices.index'),'icon'=>'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z','from'=>'violet-500','to'=>'violet-600','border'=>'violet-400','text'=>'violet-100','sub'=>'violet-200'],
        ['label'=>'Pending Payments','value'=>$stats['pending_payments'],'change'=>$stats['waiting_pharmacy'].' pharmacy','link'=>route('reception.dashboard'),'icon'=>'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z','from'=>'sky-500','to'=>'sky-600','border'=>'sky-400','text'=>'sky-100','sub'=>'sky-200']
    ] as $card)
    <a href="{{ $card['link'] ?? '#' }}" class="card-sm block bg-gradient-to-br from-{{ $card['from'] }} to-{{ $card['to'] }} rounded-xl border border-{{ $card['border'] }} p-4 text-white relative overflow-hidden">
        <div class="absolute top-0 right-0 w-16 h-16 bg-white/10 rounded-full -mr-8 -mt-8"></div>
        <div class="relative z-10">
            <div class="flex items-start justify-between mb-2">
                <span class="text-[10px] font-medium {{ $card['text'] }}">{{ $card['label'] }}</span>
                <svg class="w-4 h-4 {{ $card['sub'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $card['icon'] }}"/></svg>
            </div>
            <p class="text-xl font-bold tracking-tight text-white">{{ $card['value'] }}</p>
            <p class="text-[10px] {{ $card['sub'] }} font-medium mt-1">{{ $card['change'] }}</p>
        </div>
    </a>
    @endforeach
</div>

{{-- Charts & Performance Row --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
    {{-- Weekly Revenue Bar Chart --}}
    <div class="lg:col-span-2 bg-white rounded-xl border border-gray-100 p-5 shadow-sm">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="text-sm font-semibold text-gray-900">Weekly Revenue</h3>
                <p class="text-xs text-gray-400">Last 7 days</p>
            </div>
            <div class="text-right">
                <div class="text-lg font-semibold text-gray-900">{{ $fmt($stats['revenue_this_week']) }}</div>
                <div class="text-xs text-emerald-600 font-medium">+12.5%</div>
            </div>
        </div>
        @php $maxRev = $revenueDays->max() ?: 1; @endphp
        <div class="flex items-end gap-[6px] h-52">
            @foreach($revenueDays as $i => $rev)
            @php $pct = min(100, ($rev / $maxRev) * 100); $isToday = $i === count($revenueDays) - 1; @endphp
            <div class="flex-1 flex flex-col items-center gap-1.5 group cursor-pointer" title="{{ $dayLabels[$i] }}: {{ $fmt($rev) }}">
                <div class="w-full bg-gray-50 rounded-t-md relative h-44 overflow-hidden">
                    <div class="absolute bottom-0 left-0 right-0 rounded-t-md transition-all duration-300 {{ $isToday ? 'bg-emerald-500' : 'bg-emerald-300 group-hover:bg-emerald-400' }}" style="height: {{ max($pct, 4) }}%"></div>
                </div>
                <span class="text-[10px] text-gray-400 font-medium">{{ $dayLabels[$i] }}</span>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Performance Rings --}}
    <div class="bg-white rounded-xl border border-gray-100 p-5 shadow-sm space-y-6">
        <h3 class="text-sm font-semibold text-gray-900">Performance</h3>
        @php
            $circles = [
                ['label' => 'Success Rate', 'value' => $successRate, 'color' => '#10b981'],
                ['label' => 'Clinic Occupancy', 'value' => $occupancyRate, 'color' => '#f59e0b'],
                ['label' => 'Monthly Target', 'value' => $treatmentTargetPct, 'color' => '#8b5cf6'],
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
    </div>
</div>

{{-- Doctor Queue Preview --}}
@if($waitingForDoctor->isNotEmpty())
<div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden mb-6">
    <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
        <h3 class="text-sm font-semibold text-gray-900">Patients Waiting for Doctor</h3>
        <a href="{{ route('doctor.queue') }}" class="text-xs font-medium text-emerald-600 hover:text-emerald-700">View Queue</a>
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
                    <tr class="border-t border-gray-100 hover:bg-gray-50/50">
                        <td class="px-6 py-3 font-medium">{{ $visit->visit_number }}</td>
                        <td class="px-6 py-3">{{ $visit->patient->fullName() }}</td>
                        <td class="px-6 py-3"><span class="px-2 py-0.5 rounded-full text-[10px] font-medium bg-gold-100 text-gold-700">Waiting</span></td>
                        <td class="px-6 py-3 text-gray-500">{{ $visit->registered_at->diffForHumans() }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

{{-- Admin / Reception Layout --}}
@if($user->isAdmin() || $user->isReception())
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
            <h3 class="text-sm font-semibold text-gray-900">Today's Appointments</h3>
            <a href="{{ route('appointments.index') }}" class="text-xs font-medium text-emerald-600 hover:text-emerald-700">View All</a>
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
                    <tr class="border-t border-gray-100 hover:bg-gray-50/50 transition-colors">
                        <td class="px-5 py-2.5 text-xs text-gray-600 font-medium">{{ optional($appt->start_time)?->format('H:i') }}</td>
                        <td class="px-5 py-2.5 text-xs text-gray-900">{{ $appt->patient?->name ?? '-' }}</td>
                        <td class="px-5 py-2.5 text-xs text-gray-600">{{ $appt->doctor?->name ?? '-' }}</td>
                        <td class="px-5 py-2.5">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium capitalize
                                {{ $appt->status === 'completed' ? 'bg-emerald-100 text-emerald-700' : ($appt->status === 'cancelled' ? 'bg-red-100 text-red-700' : 'bg-gold-100 text-gold-700') }}">{{ str_replace('_', ' ', $appt->status) }}</span>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="px-5 py-6 text-center text-gray-400 text-xs">No appointments today</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
            <h3 class="text-sm font-semibold text-gray-900">Recent Patients</h3>
            <a href="{{ route('reception.dashboard') }}" class="text-xs font-medium text-emerald-600 hover:text-emerald-700">View All</a>
        </div>
        <div class="p-5 space-y-3">
            @forelse($recentPatients as $p)
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-700 font-bold text-xs">
                    {{ strtoupper(substr($p->name, 0, 1)) }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-gray-900 truncate">{{ $p->name }}</p>
                    <p class="text-xs text-gray-500">{{ $p->phone ?? $p->mrn }}</p>
                </div>
                <span class="text-[10px] text-gray-400 shrink-0">{{ $p->created_at->diffForHumans() }}</span>
            </div>
            @empty
            <p class="text-sm text-gray-400 text-center py-4">No patients yet</p>
            @endforelse
        </div>
    </div>
</div>
@endif

{{-- Recent Visits --}}
<div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
        <h3 class="text-sm font-semibold text-gray-900">Recent Visits</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left">
            <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                <tr>
                    <th class="px-6 py-3">Visit #</th>
                    <th class="px-6 py-3">Patient</th>
                    <th class="px-6 py-3">Status</th>
                    <th class="px-6 py-3">Date</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($recentVisits as $visit)
                    <tr class="border-t border-gray-100 hover:bg-gray-50/50 transition-colors">
                        <td class="px-6 py-3 font-medium">{{ $visit->visit_number }}</td>
                        <td class="px-6 py-3">{{ $visit->patient->fullName() }}</td>
                        <td class="px-6 py-3">
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-medium capitalize
                                {{ $visit->status === 'completed' ? 'bg-emerald-100 text-emerald-700' : 'bg-gold-100 text-gold-700' }}">
                                {{ str_replace('_', ' ', $visit->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-3 text-gray-500">{{ $visit->registered_at->format('d M Y H:i') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="px-6 py-6 text-center text-gray-400">No visits yet</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Add Patient Slide-over --}}
<div id="dashboardPatientModal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm transition-opacity" onclick="closeDashboardPatientModal()"></div>
    <div class="absolute inset-y-0 right-0 w-full max-w-xl bg-white shadow-2xl transform transition-transform translate-x-full duration-300 ease-in-out flex flex-col" id="dashboardPatientSlidePanel">
        <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between bg-white">
            <h3 class="text-sm font-semibold text-gray-900">Add Patient</h3>
            <button onclick="closeDashboardPatientModal()" class="p-1.5 rounded-lg hover:bg-gray-100 text-gray-500">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div class="flex-1 overflow-y-auto p-5">
            <form id="dashboardPatientForm" method="POST" action="{{ route('reception.patients.store') }}">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Full Name</label>
                        <input type="text" name="name" class="w-full text-sm border border-gray-200 rounded-lg px-3 py-2 outline-none focus:border-emerald-500" required>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Phone</label>
                        <input type="text" name="phone" class="w-full text-sm border border-gray-200 rounded-lg px-3 py-2 outline-none focus:border-emerald-500">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Email</label>
                        <input type="email" name="email" class="w-full text-sm border border-gray-200 rounded-lg px-3 py-2 outline-none focus:border-emerald-500">
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Date of Birth</label>
                            <input type="date" name="date_of_birth" class="w-full text-sm border border-gray-200 rounded-lg px-3 py-2 outline-none focus:border-emerald-500">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Gender</label>
                            <select name="gender" class="w-full text-sm border border-gray-200 rounded-lg px-3 py-2 outline-none focus:border-emerald-500">
                                <option value="">Select</option>
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Address</label>
                        <textarea name="address" class="w-full text-sm border border-gray-200 rounded-lg px-3 py-2 outline-none focus:border-emerald-500" rows="3"></textarea>
                    </div>
                    <div>
                        <button type="submit" class="px-4 py-2 text-xs font-medium bg-emerald-600 text-white rounded-lg hover:bg-emerald-700">Save Patient</button>
                        <button type="button" onclick="closeDashboardPatientModal()" class="px-4 py-2 text-xs font-medium border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">Cancel</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Add Appointment Slide-over --}}
<div id="dashboardAppointmentModal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm transition-opacity" onclick="closeDashboardAppointmentModal()"></div>
    <div class="absolute inset-y-0 right-0 w-full max-w-xl bg-white shadow-2xl transform transition-transform translate-x-full duration-300 ease-in-out flex flex-col" id="dashboardAppointmentSlidePanel">
        <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between bg-white">
            <h3 class="text-sm font-semibold text-gray-900">Book Appointment</h3>
            <button onclick="closeDashboardAppointmentModal()" class="p-1.5 rounded-lg hover:bg-gray-100 text-gray-500">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div class="flex-1 overflow-y-auto p-5">
            <form id="dashboardAppointmentForm" method="POST" action="{{ route('appointments.store') }}">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Patient</label>
                        <input type="text" id="dashApptPatientSearch" placeholder="Search patient by name or phone..." class="w-full text-sm border border-gray-200 rounded-lg px-3 py-2 outline-none focus:border-emerald-500 mb-1">
                        <select name="patient_id" id="dashApptPatientSelect" class="w-full text-sm border border-gray-200 rounded-lg px-3 py-2 outline-none focus:border-emerald-500" required>
                            <option value="">Select patient</option>
                            @foreach($patientsList as $p)
                            <option value="{{ $p->id }}" data-search="{{ strtolower($p->name . ' ' . ($p->phone ?? '') . ' ' . $p->mrn) }}">{{ $p->name }} ({{ $p->mrn }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Doctor</label>
                        <select name="doctor_id" class="w-full text-sm border border-gray-200 rounded-lg px-3 py-2 outline-none focus:border-emerald-500">
                            <option value="">Select doctor</option>
                            @foreach($doctorsList as $d)
                            <option value="{{ $d->id }}">{{ $d->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Date</label>
                            <input type="date" name="appointment_date" value="{{ today()->toDateString() }}" class="w-full text-sm border border-gray-200 rounded-lg px-3 py-2 outline-none focus:border-emerald-500" required>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Start Time</label>
                            <input type="time" name="start_time" class="w-full text-sm border border-gray-200 rounded-lg px-3 py-2 outline-none focus:border-emerald-500" required>
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Notes</label>
                        <textarea name="notes" rows="3" class="w-full text-sm border border-gray-200 rounded-lg px-3 py-2 outline-none focus:border-emerald-500"></textarea>
                    </div>
                    <div>
                        <button type="submit" class="px-4 py-2 text-xs font-medium bg-emerald-600 text-white rounded-lg hover:bg-emerald-700">Save Appointment</button>
                        <button type="button" onclick="closeDashboardAppointmentModal()" class="px-4 py-2 text-xs font-medium border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">Cancel</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
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
</script>
@endpush
