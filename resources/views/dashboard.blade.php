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
            <a href="{{ route('doctor.queue') }}" class="px-3 py-1.5 text-xs font-medium bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition-colors inline-flex items-center gap-1.5 shadow-sm">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                My Queue
            </a>
        @endif
    </div>
</div>

{{-- DOCTOR DASHBOARD --}}
@if($user->isDoctor())
@php
    $doctorId = auth()->id();
    $todayDoctorVisits = \App\Models\Visit::where('doctor_id', $doctorId)->whereDate('registered_at', today())->count();
    $pendingConsultations = \App\Models\Visit::where('doctor_id', $doctorId)->whereIn('status', ['waiting_for_doctor', 'with_doctor'])->count();
    $completedToday = \App\Models\Visit::where('doctor_id', $doctorId)->whereDate('completed_at', today())->where('status', 'completed')->count();
    $pendingLabResults = \App\Models\Visit::where('doctor_id', $doctorId)->whereIn('status', ['waiting_for_lab', 'in_lab', 'lab_completed'])->count();
    $doctorAppointments = \App\Models\Appointment::whereDate('appointment_date', today())->where('doctor_id', $doctorId)->with('patient')->orderBy('start_time')->get();
    $recentDoctorPatients = \App\Models\Visit::where('doctor_id', $doctorId)->with('patient')->latest()->limit(5)->get();
@endphp

{{-- Doctor Welcome Banner --}}
<div class="bg-gradient-to-r from-emerald-600 via-emerald-700 to-teal-700 rounded-2xl p-6 mb-6 shadow-xl relative overflow-hidden">
    <div class="absolute top-0 right-0 w-64 h-64 bg-white/5 rounded-full -mr-20 -mt-20"></div>
    <div class="absolute bottom-0 left-0 w-32 h-32 bg-white/5 rounded-full -ml-10 -mb-10"></div>
    <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h2 class="text-lg font-bold text-white mb-1">Doctor Workspace</h2>
            <p class="text-emerald-100 text-sm">Manage your patients, consultations, and appointments efficiently.</p>
        </div>
        <div class="flex items-center gap-3">
            <div class="text-center px-4 py-2 bg-white/10 rounded-lg backdrop-blur-sm">
                <div class="text-2xl font-bold text-white">{{ $pendingConsultations }}</div>
                <div class="text-[10px] text-emerald-100 uppercase tracking-wide">Waiting</div>
            </div>
            <div class="text-center px-4 py-2 bg-white/10 rounded-lg backdrop-blur-sm">
                <div class="text-2xl font-bold text-white">{{ $completedToday }}</div>
                <div class="text-[10px] text-emerald-100 uppercase tracking-wide">Done Today</div>
            </div>
        </div>
    </div>
</div>

{{-- Doctor KPI Cards --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <a href="{{ route('doctor.queue') }}" class="card-sm block bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl p-4 text-white relative overflow-hidden shadow-lg hover:shadow-xl">
        <div class="absolute top-0 right-0 w-14 h-14 bg-white/10 rounded-full -mr-6 -mt-6"></div>
        <div class="relative z-10">
            <div class="flex items-center justify-between mb-2">
                <svg class="w-5 h-5 text-blue-100" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            </div>
            <p class="text-xl font-bold tracking-tight">{{ $todayDoctorVisits }}</p>
            <p class="text-[10px] text-blue-100 font-medium mt-1">Today's Patients</p>
        </div>
    </a>
    <a href="{{ route('doctor.queue') }}" class="card-sm block bg-gradient-to-br from-amber-500 to-amber-600 rounded-xl p-4 text-white relative overflow-hidden shadow-lg hover:shadow-xl">
        <div class="absolute top-0 right-0 w-14 h-14 bg-white/10 rounded-full -mr-6 -mt-6"></div>
        <div class="relative z-10">
            <div class="flex items-center justify-between mb-2">
                <svg class="w-5 h-5 text-amber-100" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                @if($pendingConsultations > 0)
                <span class="flex h-2 w-2">
                    <span class="animate-ping absolute inline-flex h-2 w-2 rounded-full bg-red-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2 w-2 bg-red-500"></span>
                </span>
                @endif
            </div>
            <p class="text-xl font-bold tracking-tight">{{ $pendingConsultations }}</p>
            <p class="text-[10px] text-amber-100 font-medium mt-1">Pending Consultations</p>
        </div>
    </a>
    <a href="{{ route('doctor.lab-results') }}" class="card-sm block bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl p-4 text-white relative overflow-hidden shadow-lg hover:shadow-xl">
        <div class="absolute top-0 right-0 w-14 h-14 bg-white/10 rounded-full -mr-6 -mt-6"></div>
        <div class="relative z-10">
            <div class="flex items-center justify-between mb-2">
                <svg class="w-5 h-5 text-purple-100" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>
            </div>
            <p class="text-xl font-bold tracking-tight">{{ $pendingLabResults }}</p>
            <p class="text-[10px] text-purple-100 font-medium mt-1">Waiting Lab Results</p>
        </div>
    </a>
    <div class="card-sm block bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-xl p-4 text-white relative overflow-hidden shadow-lg hover:shadow-xl">
        <div class="absolute top-0 right-0 w-14 h-14 bg-white/10 rounded-full -mr-6 -mt-6"></div>
        <div class="relative z-10">
            <div class="flex items-center justify-between mb-2">
                <svg class="w-5 h-5 text-emerald-100" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <p class="text-xl font-bold tracking-tight">{{ $completedToday }}</p>
            <p class="text-[10px] text-emerald-100 font-medium mt-1">Completed Today</p>
        </div>
    </div>
</div>

{{-- Doctor Schedule & Recent Patients --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    {{-- Today's Appointments --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
            <div>
                <h3 class="text-sm font-semibold text-gray-900">Today's Schedule</h3>
                <p class="text-xs text-gray-400">{{ today()->format('D, M j') }}</p>
            </div>
            <a href="{{ route('appointments.index') }}" class="text-xs font-medium text-emerald-600 hover:text-emerald-700">View All</a>
        </div>
        <div class="p-5">
            @forelse($doctorAppointments as $appt)
                <div class="flex items-center gap-3 py-3 {{ !$loop->last ? 'border-b border-gray-50' : '' }}">
                    <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-emerald-500 to-emerald-700 flex flex-col items-center justify-center text-white shadow-sm">
                        <span class="text-[10px] font-medium">{{ date('H:i', strtotime($appt->start_time)) }}</span>
                    </div>
                    <div class="flex-1">
                        <div class="text-sm font-medium text-gray-900">{{ $appt->patient?->name ?? 'Unknown' }}</div>
                        <div class="text-xs text-gray-500">{{ ucfirst($appt->status) }}</div>
                    </div>
                    <span class="px-2 py-1 rounded-full text-[10px] font-medium capitalize
                        {{ $appt->status === 'completed' ? 'bg-emerald-100 text-emerald-700' : ($appt->status === 'cancelled' ? 'bg-red-100 text-red-700' : 'bg-amber-100 text-amber-700') }}">
                        {{ $appt->status }}
                    </span>
                </div>
            @empty
                <div class="text-center py-8 text-gray-400">
                    <svg class="w-10 h-10 mx-auto text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    <p class="text-xs">No appointments today</p>
                </div>
            @endforelse
        </div>
    </div>

    {{-- Recent Patients --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
            <div>
                <h3 class="text-sm font-semibold text-gray-900">Recent Patients</h3>
                <p class="text-xs text-gray-400">Last 5 consultations</p>
            </div>
            <a href="{{ route('patients.index') }}" class="text-xs font-medium text-emerald-600 hover:text-emerald-700">All Patients</a>
        </div>
        <div class="p-5">
            @forelse($recentDoctorPatients as $visit)
                <div class="flex items-center gap-3 py-3 {{ !$loop->last ? 'border-b border-gray-50' : '' }}">
                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-cyan-500 to-cyan-700 flex items-center justify-center text-white text-sm font-bold shadow-sm">
                        {{ strtoupper(substr($visit->patient->name ?? 'U', 0, 1)) }}
                    </div>
                    <div class="flex-1">
                        <div class="text-sm font-medium text-gray-900">{{ $visit->patient->fullName() ?? 'Unknown' }}</div>
                        <div class="text-xs text-gray-500">{{ $visit->visit_number }} • {{ $visit->registered_at->format('M j, H:i') }}</div>
                    </div>
                    <span class="px-2 py-1 rounded-full text-[10px] font-medium capitalize bg-gray-100 text-gray-700">
                        {{ str_replace('_', ' ', $visit->status) }}
                    </span>
                </div>
            @empty
                <div class="text-center py-8 text-gray-400">
                    <svg class="w-10 h-10 mx-auto text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    <p class="text-xs">No recent patients</p>
                </div>
            @endforelse
        </div>
    </div>
</div>

{{-- Quick Actions --}}
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
    <a href="{{ route('doctor.queue') }}" class="flex flex-col items-center gap-2 p-4 bg-white rounded-xl border border-gray-100 shadow-sm hover:shadow-md hover:border-emerald-200 transition-all">
        <div class="w-10 h-10 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
        </div>
        <span class="text-xs font-medium text-gray-700">My Queue</span>
    </a>
    <a href="{{ route('appointments.index') }}" class="flex flex-col items-center gap-2 p-4 bg-white rounded-xl border border-gray-100 shadow-sm hover:shadow-md hover:border-emerald-200 transition-all">
        <div class="w-10 h-10 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
        </div>
        <span class="text-xs font-medium text-gray-700">Appointments</span>
    </a>
    <a href="{{ route('patients.index') }}" class="flex flex-col items-center gap-2 p-4 bg-white rounded-xl border border-gray-100 shadow-sm hover:shadow-md hover:border-emerald-200 transition-all">
        <div class="w-10 h-10 rounded-full bg-purple-100 text-purple-700 flex items-center justify-center">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
        </div>
        <span class="text-xs font-medium text-gray-700">Patients</span>
    </a>
    <a href="{{ route('clinical-records.index') }}" class="flex flex-col items-center gap-2 p-4 bg-white rounded-xl border border-gray-100 shadow-sm hover:shadow-md hover:border-emerald-200 transition-all">
        <div class="w-10 h-10 rounded-full bg-amber-100 text-amber-700 flex items-center justify-center">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
        </div>
        <span class="text-xs font-medium text-gray-700">Records</span>
    </a>
</div>
@endif

{{-- Admin/Reception KPI Stats Cards --}}
@if(!$user->isDoctor())
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
    {{-- Revenue Line Chart --}}
    <div class="lg:col-span-2 bg-white rounded-xl border border-gray-100 p-5 shadow-sm">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="text-sm font-semibold text-gray-900">Revenue Trend</h3>
                <p class="text-xs text-gray-400">Live financial overview</p>
            </div>
            <div class="flex items-center gap-3">
                <div class="text-right">
                    <div class="text-lg font-semibold text-gray-900">{{ $fmt($stats['revenue_this_week']) }}</div>
                    <div class="text-xs text-emerald-600 font-medium">This Week</div>
                </div>
                <div class="flex bg-gray-100 rounded-lg p-0.5">
                    <button type="button" onclick="switchRevenue('weekly')" id="revWeeklyBtn" class="px-3 py-1 text-xs font-medium rounded-md bg-white text-emerald-700 shadow-sm">7 Days</button>
                    <button type="button" onclick="switchRevenue('monthly')" id="revMonthlyBtn" class="px-3 py-1 text-xs font-medium rounded-md text-gray-500 hover:text-gray-700">30 Days</button>
                </div>
            </div>
        </div>
        <div class="relative h-64">
            <canvas id="revenueChart"></canvas>
        </div>
    </div>

    {{-- Performance Rings --}}
    <div class="bg-white rounded-xl border border-gray-100 p-5 shadow-sm space-y-6">
        <div class="flex items-center justify-between">
            <h3 class="text-sm font-semibold text-gray-900">Performance</h3>
            <span class="text-[10px] text-gray-400">Live</span>
        </div>
        @php
            $circles = [
                ['label' => 'Success Rate', 'value' => $successRate, 'color' => '#10b981'],
                ['label' => 'Occupancy', 'value' => $occupancyRate, 'color' => '#f59e0b'],
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

        <div class="border-t border-gray-100 pt-4 space-y-3">
            <h4 class="text-xs font-semibold text-gray-700">Top Doctors</h4>
            @foreach($topDoctors as $doc)
                <div class="flex items-center justify-between text-sm">
                    <div class="flex items-center gap-2">
                        <div class="w-6 h-6 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center text-[10px] font-bold">{{ strtoupper(substr($doc->name,0,1)) }}</div>
                        <span class="text-xs text-gray-700 truncate max-w-[120px]">{{ $doc->name }}</span>
                    </div>
                    <span class="text-xs font-medium text-emerald-600">{{ $doc->completed_visits }} visits</span>
                </div>
            @endforeach
        </div>
    </div>
</div>

{{-- Performance Analytics Row --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    {{-- Visit Trend Line Chart --}}
    <div class="bg-white rounded-xl border border-gray-100 p-5 shadow-sm">
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

    {{-- Department Performance --}}
    <div class="bg-white rounded-xl border border-gray-100 p-5 shadow-sm">
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
            <div>
                <h3 class="text-sm font-semibold text-gray-900">Today's Appointments</h3>
                <p class="text-xs text-gray-400">{{ today()->format('D, M j') }}</p>
            </div>
            <a href="{{ route('appointments.index') }}" class="text-xs font-medium text-emerald-600 hover:text-emerald-700">View All</a>
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
                <div class="text-center p-3 bg-gradient-to-br from-amber-50 to-amber-100 rounded-lg border border-amber-200 hover:shadow-md transition-shadow">
                    <div class="text-xl font-bold text-amber-600">{{ $apptStats['scheduled'] }}</div>
                    <div class="text-[10px] text-amber-700 font-medium">Scheduled</div>
                </div>
                <div class="text-center p-3 bg-gradient-to-br from-blue-50 to-blue-100 rounded-lg border border-blue-200 hover:shadow-md transition-shadow">
                    <div class="text-xl font-bold text-blue-600">{{ $apptStats['confirmed'] }}</div>
                    <div class="text-[10px] text-blue-700 font-medium">Confirmed</div>
                </div>
                <div class="text-center p-3 bg-gradient-to-br from-emerald-50 to-emerald-100 rounded-lg border border-emerald-200 hover:shadow-md transition-shadow">
                    <div class="text-xl font-bold text-emerald-600">{{ $apptStats['completed'] }}</div>
                    <div class="text-[10px] text-emerald-700 font-medium">Completed</div>
                </div>
                <div class="text-center p-3 bg-gradient-to-br from-red-50 to-red-100 rounded-lg border border-red-200 hover:shadow-md transition-shadow">
                    <div class="text-xl font-bold text-red-600">{{ $apptStats['cancelled'] }}</div>
                    <div class="text-[10px] text-red-700 font-medium">Cancelled</div>
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
                    <tr class="border-t border-gray-100 hover:bg-gray-50/50 transition-colors cursor-pointer" onclick="window.location.href='{{ route('appointments.edit', $appt) }}'">
                        <td class="px-5 py-2.5">
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center text-xs font-bold">
                                    {{ substr($appt->startTime(), 0, 2) }}
                                </div>
                                <span class="text-xs text-gray-900 font-medium">{{ $appt->startTime() }}</span>
                            </div>
                        </td>
                        <td class="px-5 py-2.5">
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-purple-500 to-purple-700 flex items-center justify-center text-white text-xs font-bold shadow-sm">
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
                                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-cyan-500 to-cyan-700 flex items-center justify-center text-white text-xs font-bold shadow-sm">
                                    {{ strtoupper(substr($appt->doctor?->name ?? '', 0, 1)) }}
                                </div>
                                <span class="text-xs text-gray-700">{{ $appt->doctor?->name ?? 'Unassigned' }}</span>
                            </div>
                        </td>
                        <td class="px-5 py-2.5">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-medium capitalize
                                {{ $appt->status === 'completed' ? 'bg-emerald-100 text-emerald-700' : ($appt->status === 'cancelled' ? 'bg-red-100 text-red-700' : ($appt->status === 'confirmed' ? 'bg-blue-100 text-blue-700' : 'bg-amber-100 text-amber-700')) }}">
                                <span class="w-1.5 h-1.5 rounded-full {{ $appt->status === 'completed' ? 'bg-emerald-500' : ($appt->status === 'cancelled' ? 'bg-red-500' : ($appt->status === 'confirmed' ? 'bg-blue-500' : 'bg-amber-500')) }}"></span>
                                {{ str_replace('_', ' ', $appt->status) }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="px-5 py-10 text-center text-gray-400 text-xs">
                        <div class="flex flex-col items-center gap-2">
                            <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            <p>No appointments today</p>
                        </div>
                    </td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
            <h3 class="text-sm font-semibold text-gray-900">Recent Patients</h3>
            <a href="{{ route('patients.index') }}" class="text-xs font-medium text-emerald-600 hover:text-emerald-700">View All</a>
        </div>
        <div class="p-5 space-y-3">
            @forelse($recentPatients as $p)
            <div class="flex items-center gap-3 p-2 rounded-lg hover:bg-gray-50 transition-colors cursor-pointer" onclick="window.location.href='{{ route('patients.show', $p) }}'">
                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-emerald-500 to-emerald-700 flex items-center justify-center text-white font-bold text-sm shadow-sm">
                    {{ strtoupper(substr($p->name, 0, 1)) }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-gray-900 truncate">{{ $p->name }}</p>
                    <p class="text-xs text-gray-500">{{ $p->mrn }} • {{ $p->phone ?? 'No phone' }}</p>
                </div>
                <div class="text-right">
                    <span class="text-[10px] text-gray-400">{{ $p->created_at->diffForHumans() }}</span>
                    <div class="text-[10px] text-emerald-600 font-medium">{{ $p->visits_count ?? 0 }} visits</div>
                </div>
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
        <div class="flex items-center gap-2">
            <select id="doctorFilter" onchange="filterVisitsByDoctor()" class="text-xs border border-gray-200 rounded-lg px-2 py-1.5 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 bg-white">
                <option value="">All Doctors</option>
                @foreach($doctorsList as $d)
                    <option value="{{ $d->id }}">{{ $d->name }}</option>
                @endforeach
            </select>
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
                    <tr class="border-t border-gray-100 hover:bg-gray-50/50 transition-colors visit-row" data-doctor-id="{{ $visit->doctor_id ?? '' }}">
                        <td class="px-6 py-3 font-medium">{{ $visit->visit_number }}</td>
                        <td class="px-6 py-3">
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center text-xs font-bold">
                                    {{ strtoupper(substr($visit->patient->name, 0, 1)) }}
                                </div>
                                <span>{{ $visit->patient->fullName() }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-3">
                            <div class="flex items-center gap-2">
                                <span class="text-xs text-gray-700">{{ $visit->doctor?->name ?? 'Unassigned' }}</span>
                                @if($visit->status !== 'completed')
                                    <button type="button" onclick="openChangeDoctorModal({{ $visit->id }}, '{{ $visit->doctor_id ?? '' }}')" class="text-emerald-600 hover:text-emerald-700">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                                    </button>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-3">
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-medium capitalize
                                {{ $visit->status === 'completed' ? 'bg-emerald-100 text-emerald-700' : ($visit->status === 'in_progress' ? 'bg-blue-100 text-blue-700' : 'bg-amber-100 text-amber-700') }}">
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
                            @foreach($doctorsList as $d)
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
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
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
    gradient.addColorStop(0, 'rgba(16, 185, 129, 0.25)');
    gradient.addColorStop(1, 'rgba(16, 185, 129, 0.0)');

    revenueChart = new Chart(revCtx, {
        type: 'line',
        data: {
            labels: weeklyLabels,
            datasets: [{
                label: 'Revenue',
                data: weeklyData,
                borderColor: '#10b981',
                backgroundColor: gradient,
                borderWidth: 3,
                pointBackgroundColor: '#ffffff',
                pointBorderColor: '#10b981',
                pointBorderWidth: 2,
                pointRadius: 4,
                pointHoverRadius: 6,
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false }, tooltip: { callbacks: { label: (ctx) => fmt(ctx.raw) } } },
            scales: { y: { beginAtZero: true, grid: { color: '#f3f4f6' } }, x: { grid: { display: false } } }
        }
    });

    visitChart = new Chart(document.getElementById('visitTrendChart'), {
        type: 'line',
        data: {
            labels: visitLabels,
            datasets: [{
                label: 'Visits',
                data: visitData,
                borderColor: '#8b5cf6',
                backgroundColor: 'rgba(139, 92, 246, 0.1)',
                borderWidth: 3,
                pointBackgroundColor: '#ffffff',
                pointBorderColor: '#8b5cf6',
                pointBorderWidth: 2,
                pointRadius: 4,
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true, grid: { color: '#f3f4f6' } }, x: { grid: { display: false } } }
        }
    });

    deptChart = new Chart(document.getElementById('departmentChart'), {
        type: 'bar',
        data: {
            labels: deptLabels,
            datasets: [{
                label: 'Visits',
                data: deptData,
                backgroundColor: ['#10b981', '#f59e0b', '#8b5cf6', '#0ea5e9', '#ec4899', '#6366f1'],
                borderRadius: 6,
                barThickness: 24
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true, grid: { color: '#f3f4f6' } }, x: { grid: { display: false } } }
        }
    });
}

function switchRevenue(period) {
    document.getElementById('revWeeklyBtn').className = period === 'weekly'
        ? 'px-3 py-1 text-xs font-medium rounded-md bg-white text-emerald-700 shadow-sm'
        : 'px-3 py-1 text-xs font-medium rounded-md text-gray-500 hover:text-gray-700';
    document.getElementById('revMonthlyBtn').className = period === 'monthly'
        ? 'px-3 py-1 text-xs font-medium rounded-md bg-white text-emerald-700 shadow-sm'
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
