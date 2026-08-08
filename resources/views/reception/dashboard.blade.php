@extends('layouts.dashboard')

@section('title', 'Reception - ' . config('app.name', 'Laravel'))
@section('page_title', 'Reception Dashboard')

@section('content')
<div class="space-y-6" id="receptionDashboard">
    @if (session('status'))
        <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm animate-fade">{{ session('status') }}</div>
    @endif

    {{-- Call Notification Banner --}}
    <div id="callNotificationBanner" class="hidden p-4 rounded-xl bg-amber-50 border-2 border-amber-300 animate-fade">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-full bg-amber-100 flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-amber-600 animate-bounce" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
            </div>
            <div class="flex-1">
                <p class="text-sm font-bold text-amber-900" id="callNotificationTitle">Patient Called</p>
                <p class="text-xs text-amber-700" id="callNotificationText"></p>
            </div>
            <button onclick="dismissCallNotification()" class="text-amber-500 hover:text-amber-700 p-1">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
    </div>

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-gray-900">Reception Command Center</h1>
            <p class="text-xs text-gray-500 mt-0.5">{{ today()->format('l, F j, Y') }}</p>
        </div>
        <div class="flex items-center gap-2">
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-emerald-50 border border-emerald-100 text-xs font-medium text-emerald-700">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span> Live Queue
            </span>
            <span class="text-xs text-gray-400" id="lastUpdated">Updated just now</span>
        </div>
    </div>

    {{-- KPI Cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        @php
            $kpiCards = [
                ['label' => 'Today Visits', 'value' => $kpis['today_visits'], 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6 4h6', 'bg' => 'bg-emerald-100', 'text' => 'text-emerald-600', 'bar' => 'bg-emerald-500', 'ring' => 'text-emerald-500', 'max' => 50],
                ['label' => 'Waiting Payment', 'value' => $kpis['waiting_payment'], 'icon' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z', 'bg' => 'bg-amber-100', 'text' => 'text-amber-600', 'bar' => 'bg-amber-500', 'ring' => 'text-amber-500', 'max' => 20],
                ['label' => 'Waiting Doctor', 'value' => $kpis['waiting_doctor'], 'icon' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z', 'bg' => 'bg-blue-100', 'text' => 'text-blue-600', 'bar' => 'bg-blue-500', 'ring' => 'text-blue-500', 'max' => 20],
                ['label' => 'With Doctor', 'value' => $kpis['with_doctor'], 'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z', 'bg' => 'bg-purple-100', 'text' => 'text-purple-600', 'bar' => 'bg-purple-500', 'ring' => 'text-purple-500', 'max' => 20],
                ['label' => 'Today Revenue', 'value' => 'TSh ' . number_format($kpis['today_revenue']), 'raw' => round($kpis['today_revenue']), 'icon' => 'M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z', 'bg' => 'bg-amber-100', 'text' => 'text-amber-600', 'bar' => 'bg-amber-500', 'ring' => 'text-amber-500', 'max' => 500000],
                ['label' => 'New Patients', 'value' => $kpis['today_patients'], 'icon' => 'M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z', 'bg' => 'bg-sky-100', 'text' => 'text-sky-600', 'bar' => 'bg-sky-500', 'ring' => 'text-sky-500', 'max' => 20],
                ['label' => 'Appointments', 'value' => $kpis['appointments_today'], 'icon' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z', 'bg' => 'bg-rose-100', 'text' => 'text-rose-600', 'bar' => 'bg-rose-500', 'ring' => 'text-rose-500', 'max' => 30],
                ['label' => 'Avg Wait', 'value' => $kpis['avg_wait_minutes'] . 'm', 'raw' => $kpis['avg_wait_minutes'], 'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z', 'bg' => 'bg-indigo-100', 'text' => 'text-indigo-600', 'bar' => 'bg-indigo-500', 'ring' => 'text-indigo-500', 'max' => 60],
            ];
        @endphp
        @foreach ($kpiCards as $card)
            <div class="bg-white rounded-xl border border-gray-100 p-4 shadow-sm hover:shadow-md transition-shadow">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-[10px] font-medium text-gray-500 uppercase tracking-wide">{{ $card['label'] }}</p>
                        <p class="text-xl font-bold text-gray-900 mt-1 kpi-counter" data-target="{{ $card['raw'] ?? $card['value'] }}" data-max="{{ $card['max'] }}" data-prefix="{{ str_starts_with((string) $card['value'], 'TSh') ? 'TSh ' : '' }}" data-suffix="{{ str_ends_with((string) $card['value'], 'm') ? 'm' : '' }}">{{ $card['value'] }}</p>
                    </div>
                    <div class="relative w-12 h-12">
                        <svg class="w-12 h-12 transform -rotate-90" viewBox="0 0 48 48">
                            <circle cx="24" cy="24" r="20" stroke="#f3f4f6" stroke-width="5" fill="none"></circle>
                            <circle cx="24" cy="24" r="20" stroke="currentColor" stroke-width="5" fill="none" stroke-linecap="round" class="kpi-ring {{ $card['ring'] }}" stroke-dasharray="125.66" stroke-dashoffset="125.66" data-pct="{{ min(100, (($card['raw'] ?? $card['value']) / $card['max']) * 100) }}"></circle>
                        </svg>
                        <div class="absolute inset-0 flex items-center justify-center">
                            <svg class="w-5 h-5 {{ $card['text'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $card['icon'] }}"/></svg>
                        </div>
                    </div>
                </div>
                <div class="mt-3 h-1 bg-gray-100 rounded-full overflow-hidden">
                    <div class="h-full {{ $card['bar'] }} kpi-bar" style="width: 0%" data-width="{{ min(100, (($card['raw'] ?? $card['value']) / $card['max']) * 100) }}%"></div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Quick Actions & Search --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 grid grid-cols-2 sm:grid-cols-4 gap-3">
            <button onclick="openRegisterPatientModal()" class="group flex flex-col items-center gap-2 p-4 bg-gradient-to-br from-emerald-600 to-emerald-700 rounded-xl text-white shadow-sm hover:shadow-lg hover:-translate-y-0.5 transition-all">
                <div class="w-10 h-10 rounded-lg bg-white/20 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                </div>
                <span class="text-xs font-semibold">Register Patient</span>
            </button>
            <button onclick="openOpenVisitModal()" class="group flex flex-col items-center gap-2 p-4 bg-gradient-to-br from-blue-600 to-blue-700 rounded-xl text-white shadow-sm hover:shadow-lg hover:-translate-y-0.5 transition-all">
                <div class="w-10 h-10 rounded-lg bg-white/20 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6 4h6"/></svg>
                </div>
                <span class="text-xs font-semibold">Open Visit</span>
            </button>
            <a href="{{ route('patients.index') }}" class="group flex flex-col items-center gap-2 p-4 bg-gradient-to-br from-purple-600 to-purple-700 rounded-xl text-white shadow-sm hover:shadow-lg hover:-translate-y-0.5 transition-all">
                <div class="w-10 h-10 rounded-lg bg-white/20 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </div>
                <span class="text-xs font-semibold">Patients</span>
            </a>
            <a href="{{ route('appointments.index') }}" class="group flex flex-col items-center gap-2 p-4 bg-gradient-to-br from-amber-500 to-amber-600 rounded-xl text-white shadow-sm hover:shadow-lg hover:-translate-y-0.5 transition-all">
                <div class="w-10 h-10 rounded-lg bg-white/20 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </div>
                <span class="text-xs font-semibold">Appointments</span>
            </a>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 p-4 shadow-sm flex flex-col justify-center">
            <label class="text-xs font-medium text-gray-700 mb-1.5">Quick Patient Search</label>
            <div class="relative">
                <input type="text" id="globalPatientSearch" placeholder="Type name, phone or MRN..." class="w-full border border-gray-200 rounded-lg pl-9 pr-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                <svg class="w-4 h-4 text-gray-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>
            <div id="globalSearchResults" class="hidden mt-2 max-h-40 overflow-y-auto border border-gray-100 rounded-lg bg-white"></div>
        </div>
    </div>

    {{-- Charts --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 bg-white rounded-xl border border-gray-100 p-6 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-sm font-semibold text-gray-900">Visit Trend (Last 7 Days)</h2>
                <span class="text-xs text-gray-400 live-indicator flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span> Live
                </span>
            </div>
            <div class="relative h-64">
                <canvas id="visitTrendChart"></canvas>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-100 p-6 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-sm font-semibold text-gray-900">Status Distribution</h2>
                <span class="text-xs text-gray-400 live-indicator flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span> Live
                </span>
            </div>
            <div class="relative h-64">
                <canvas id="statusDoughnutChart"></canvas>
            </div>
            <div class="mt-4 grid grid-cols-2 gap-2 text-xs">
                @php
                    $statusLabels = [
                        'registered' => 'Registered',
                        'waiting_for_doctor' => 'Waiting Doctor',
                        'with_doctor' => 'With Doctor',
                        'waiting_for_lab' => 'Waiting Lab',
                        'waiting_for_pharmacy' => 'Waiting Pharmacy',
                        'waiting_for_payment' => 'Waiting Payment',
                        'completed' => 'Completed',
                        'cancelled' => 'Cancelled',
                    ];
                    $statusDotColors = [
                        'registered' => 'bg-gray-400',
                        'waiting_for_doctor' => 'bg-blue-500',
                        'with_doctor' => 'bg-purple-500',
                        'waiting_for_lab' => 'bg-cyan-500',
                        'waiting_for_pharmacy' => 'bg-pink-500',
                        'waiting_for_payment' => 'bg-amber-500',
                        'completed' => 'bg-emerald-500',
                        'cancelled' => 'bg-red-500',
                    ];
                @endphp
                @foreach ($statusCounts as $status => $count)
                    <div class="flex items-center justify-between p-2 rounded-lg bg-gray-50">
                        <span class="flex items-center gap-1.5 text-gray-600">
                            <span class="w-2 h-2 rounded-full {{ $statusDotColors[$status] ?? 'bg-gray-400' }}"></span>
                            {{ $statusLabels[$status] ?? ucwords(str_replace('_', ' ', $status)) }}
                        </span>
                        <span class="font-semibold text-gray-900 status-count" data-status="{{ $status }}">{{ $count }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Live Queue Tabs --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden" id="liveQueue">
        <div class="px-6 py-4 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <h2 class="text-sm font-semibold text-gray-900">Live Reception Queue</h2>
            <div class="flex flex-wrap gap-2" id="queueTabs">
                <button data-tab="need-doctor" class="queue-tab active px-3 py-1.5 text-xs font-medium rounded-lg bg-emerald-100 text-emerald-700">Check-in & Pay <span class="ml-1 tab-count" data-count="registered">{{ $registeredVisits->count() }}</span></button>
                <button data-tab="waiting-doctor" class="queue-tab px-3 py-1.5 text-xs font-medium rounded-lg text-gray-600 hover:bg-gray-100">Waiting Doctor <span class="ml-1 tab-count" data-count="waiting_for_doctor">{{ $waitingForDoctor->count() }}</span></button>
                <button data-tab="with-doctor" class="queue-tab px-3 py-1.5 text-xs font-medium rounded-lg text-gray-600 hover:bg-gray-100">With Doctor <span class="ml-1 tab-count" data-count="with_doctor">{{ $withDoctorVisits->count() }}</span></button>
                <button data-tab="payment" class="queue-tab px-3 py-1.5 text-xs font-medium rounded-lg text-gray-600 hover:bg-gray-100">Payment <span class="ml-1 tab-count" data-count="waiting_for_payment">{{ $waitingForPayment->count() }}</span></button>
            </div>
        </div>
        <div class="overflow-x-auto p-0">
            @php
                $badgeColors = [
                    'registered' => 'bg-gray-100 text-gray-700',
                    'waiting_for_doctor' => 'bg-blue-100 text-blue-700',
                    'with_doctor' => 'bg-purple-100 text-purple-700',
                    'waiting_for_lab' => 'bg-cyan-100 text-cyan-700',
                    'waiting_for_pharmacy' => 'bg-pink-100 text-pink-700',
                    'waiting_for_payment' => 'bg-amber-100 text-amber-700',
                    'completed' => 'bg-emerald-100 text-emerald-700',
                    'cancelled' => 'bg-red-100 text-red-700',
                ];
            @endphp
            <div id="tab-need-doctor" class="queue-panel">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50 text-xs uppercase text-gray-500"><tr><th class="px-6 py-3">Visit #</th><th class="px-6 py-3">Patient</th><th class="px-6 py-3">Fee</th><th class="px-6 py-3">Pay Status</th><th class="px-6 py-3">Action</th><th class="px-6 py-3 text-right">Cancel</th></tr></thead>
                    <tbody>
                        @forelse ($registeredVisits as $visit)
                            @php
                                $invoice = $visit->invoice;
                                $isPaid = $invoice && $invoice->status === 'paid';
                                $balance = ($invoice?->total ?? 0) - ($invoice?->paid ?? 0);
                            @endphp
                            <tr class="border-t border-gray-100 hover:bg-gray-50/50 transition-colors" id="visit-row-{{ $visit->id }}">
                                <td class="px-6 py-3 font-medium">{{ $visit->visit_number }}</td>
                                <td class="px-6 py-3">{{ $visit->patient->fullName() }}</td>
                                <td class="px-6 py-3 font-medium text-emerald-700">{{ number_format($invoice?->total ?? 0) }} TSh</td>
                                <td class="px-6 py-3">
                                    @if ($isPaid)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700">Paid</span>
                                    @elseif ($invoice && $invoice->paid > 0)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-700">Partial</span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-700">Unpaid</span>
                                    @endif
                                </td>
                                <td class="px-6 py-3">
                                    @if (! $isPaid)
                                        <div class="flex items-center gap-2">
                                            {{-- Quick Pay Full Amount --}}
                                            <form method="POST" action="{{ route('reception.visits.pay', $visit) }}" class="ajax-pay-form">
                                                @csrf
                                                <input type="hidden" name="amount" value="{{ $balance }}">
                                                <input type="hidden" name="method" value="cash">
                                                <button type="submit" class="btn-submit inline-flex items-center gap-1.5 bg-emerald-600 text-white text-xs font-semibold px-3 py-1.5 rounded-lg hover:bg-emerald-700 transition-colors shadow-sm">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                                    Pay {{ number_format($balance) }}
                                                </button>
                                            </form>
                                            {{-- Detailed Payment Button --}}
                                            <button type="button" onclick="openPaymentModal({{ $visit->id }}, '{{ $visit->visit_number }}', '{{ $visit->patient->fullName() }}', {{ $invoice?->total ?? 0 }}, {{ $invoice?->paid ?? 0 }}, {{ $balance }})" class="inline-flex items-center gap-1 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-medium px-3 py-1.5 rounded-lg transition-colors">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                                                Custom
                                            </button>
                                        </div>
                                    @elseif (! $visit->doctor_id)
                                        {{-- Paid but no doctor: assign doctor --}}
                                        <form method="POST" action="{{ route('reception.visits.assign', $visit) }}" class="ajax-assign-form flex gap-2">
                                            @csrf
                                            <select name="doctor_id" class="border border-gray-200 rounded-lg text-xs px-2 py-1.5 focus:ring-emerald-500 focus:border-emerald-500" required>
                                                <option value="">Select Doctor</option>
                                                @foreach ($doctors as $doctor)
                                                    <option value="{{ $doctor->id }}">{{ $doctor->name }}</option>
                                                @endforeach
                                            </select>
                                            <button type="submit" class="btn-submit bg-blue-600 text-white text-xs font-medium px-3 py-1.5 rounded-lg hover:bg-blue-700 transition-colors">Send to Doctor</button>
                                        </form>
                                    @else
                                        <span class="text-xs text-emerald-600 font-medium">Ready for doctor</span>
                                    @endif
                                </td>
                                <td class="px-6 py-3 text-right">
                                    <button type="button" onclick="cancelVisit({{ $visit->id }}, '{{ $visit->visit_number }}')" class="inline-flex items-center justify-center w-7 h-7 rounded-lg bg-red-50 hover:bg-red-100 text-red-500 hover:text-red-700 transition-colors" title="Cancel visit">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="px-6 py-6 text-center text-gray-400">No visits waiting for payment</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div id="tab-waiting-doctor" class="queue-panel hidden">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50 text-xs uppercase text-gray-500"><tr><th class="px-6 py-3">Visit #</th><th class="px-6 py-3">Patient</th><th class="px-6 py-3">Doctor</th><th class="px-6 py-3">Waiting</th><th class="px-6 py-3">Action</th><th class="px-6 py-3 text-right">Cancel</th></tr></thead>
                    <tbody>
                        @forelse ($waitingForDoctor as $visit)
                            <tr class="border-t border-gray-100 hover:bg-gray-50/50 transition-colors" id="visit-row-{{ $visit->id }}">
                                <td class="px-6 py-3 font-medium">{{ $visit->visit_number }}</td>
                                <td class="px-6 py-3">{{ $visit->patient->fullName() }}</td>
                                <td class="px-6 py-3 text-xs">{{ $visit->doctor?->name ?? 'Unassigned' }}</td>
                                <td class="px-6 py-3 text-gray-500 text-xs">{{ $visit->registered_at->diffForHumans() }}</td>
                                <td class="px-6 py-3">
                                    <form method="POST" action="{{ route('reception.visits.change-doctor', $visit) }}" class="ajax-change-form flex gap-2">
                                        @csrf
                                        <select name="doctor_id" class="border border-gray-200 rounded-lg text-xs px-2 py-1 focus:ring-emerald-500 focus:border-emerald-500" required>
                                            @foreach ($doctors as $doctor)
                                                <option value="{{ $doctor->id }}" {{ $visit->doctor_id == $doctor->id ? 'selected' : '' }}>{{ $doctor->name }}</option>
                                            @endforeach
                                        </select>
                                        <button type="submit" class="btn-submit bg-blue-600 text-white text-xs font-medium px-3 py-1 rounded-lg hover:bg-blue-700 transition-colors">Change</button>
                                    </form>
                                </td>
                                <td class="px-6 py-3 text-right">
                                    <button type="button" onclick="cancelVisit({{ $visit->id }}, '{{ $visit->visit_number }}')" class="inline-flex items-center justify-center w-7 h-7 rounded-lg bg-red-50 hover:bg-red-100 text-red-500 hover:text-red-700 transition-colors" title="Cancel visit">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="px-6 py-6 text-center text-gray-400">No patients waiting for doctor</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div id="tab-with-doctor" class="queue-panel hidden">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50 text-xs uppercase text-gray-500"><tr><th class="px-6 py-3">Visit #</th><th class="px-6 py-3">Patient</th><th class="px-6 py-3">Doctor</th><th class="px-6 py-3">Status</th><th class="px-6 py-3 text-right">Cancel</th></tr></thead>
                    <tbody>
                        @forelse ($withDoctorVisits as $visit)
                            <tr class="border-t border-gray-100 hover:bg-gray-50/50 transition-colors" id="visit-row-{{ $visit->id }}">
                                <td class="px-6 py-3 font-medium">{{ $visit->visit_number }}</td>
                                <td class="px-6 py-3">{{ $visit->patient->fullName() }}</td>
                                <td class="px-6 py-3 text-xs">{{ $visit->doctor?->name ?? 'Unassigned' }}</td>
                                <td class="px-6 py-3"><span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $badgeColors[$visit->status] ?? 'bg-gray-100 text-gray-700' }}">{{ str_replace('_', ' ', $visit->status) }}</span></td>
                                <td class="px-6 py-3 text-right">
                                    <button type="button" onclick="cancelVisit({{ $visit->id }}, '{{ $visit->visit_number }}')" class="inline-flex items-center justify-center w-7 h-7 rounded-lg bg-red-50 hover:bg-red-100 text-red-500 hover:text-red-700 transition-colors" title="Cancel visit">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="px-6 py-6 text-center text-gray-400">No patients currently with doctor</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div id="tab-payment" class="queue-panel hidden">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50 text-xs uppercase text-gray-500"><tr><th class="px-6 py-3">Visit #</th><th class="px-6 py-3">Patient</th><th class="px-6 py-3">Total</th><th class="px-6 py-3">Action</th><th class="px-6 py-3 text-right">Cancel</th></tr></thead>
                    <tbody>
                        @forelse ($waitingForPayment as $visit)
                            @php
                                $balance = ($visit->invoice?->total ?? 0) - ($visit->invoice?->paid ?? 0);
                            @endphp
                            <tr class="border-t border-gray-100 hover:bg-gray-50/50 transition-colors" id="visit-row-{{ $visit->id }}">
                                <td class="px-6 py-3 font-medium">{{ $visit->visit_number }}</td>
                                <td class="px-6 py-3">{{ $visit->patient->fullName() }}</td>
                                <td class="px-6 py-3 font-medium text-emerald-700">{{ number_format($visit->invoice?->total ?? 0) }} TSh</td>
                                <td class="px-6 py-3">
                                    <div class="flex items-center gap-2">
                                        <form method="POST" action="{{ route('reception.visits.pay', $visit) }}" class="ajax-pay-form">
                                            @csrf
                                            <input type="hidden" name="amount" value="{{ $balance }}">
                                            <input type="hidden" name="method" value="cash">
                                            <button type="submit" class="btn-submit inline-flex items-center gap-1.5 bg-emerald-600 text-white text-xs font-semibold px-3 py-1.5 rounded-lg hover:bg-emerald-700 transition-colors shadow-sm">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                                Pay {{ number_format($balance) }}
                                            </button>
                                        </form>
                                        <button type="button" onclick="openPaymentModal({{ $visit->id }}, '{{ $visit->visit_number }}', '{{ $visit->patient->fullName() }}', {{ $visit->invoice?->total ?? 0 }}, {{ $visit->invoice?->paid ?? 0 }}, {{ $balance }})" class="inline-flex items-center gap-1 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-medium px-3 py-1.5 rounded-lg transition-colors">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                                            Custom
                                        </button>
                                    </div>
                                </td>
                                <td class="px-6 py-3 text-right">
                                    <button type="button" onclick="cancelVisit({{ $visit->id }}, '{{ $visit->visit_number }}')" class="inline-flex items-center justify-center w-7 h-7 rounded-lg bg-red-50 hover:bg-red-100 text-red-500 hover:text-red-700 transition-colors" title="Cancel visit">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="px-6 py-6 text-center text-gray-400">No pending payments</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Appointments & Recent Patients --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <h2 class="text-sm font-semibold text-gray-900">Today's Appointments</h2>
                <span class="text-xs bg-rose-100 text-rose-700 px-2 py-1 rounded-full" id="appointmentsTodayCount">{{ $todayAppointments->count() }}</span>
            </div>
            <div class="p-5 space-y-3 max-h-80 overflow-y-auto">
                @forelse ($todayAppointments as $appt)
                    <div class="flex items-center gap-3 p-2 rounded-lg hover:bg-gray-50 transition-colors cursor-pointer" onclick="window.location.href='{{ route('appointments.edit', $appt) }}'">
                        <div class="w-10 h-10 rounded-full bg-rose-100 text-rose-700 flex items-center justify-center text-xs font-bold">{{ substr($appt->startTime(), 0, 2) }}</div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 truncate">{{ $appt->patient?->name ?? '-' }}</p>
                            <p class="text-xs text-gray-500">{{ $appt->startTime() }} &bull; {{ $appt->doctor?->name ?? 'Unassigned' }}</p>
                        </div>
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-medium capitalize {{ $appt->status === 'completed' ? 'bg-emerald-100 text-emerald-700' : ($appt->status === 'cancelled' ? 'bg-red-100 text-red-700' : ($appt->status === 'confirmed' ? 'bg-blue-100 text-blue-700' : 'bg-amber-100 text-amber-700')) }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ $appt->status === 'completed' ? 'bg-emerald-500' : ($appt->status === 'cancelled' ? 'bg-red-500' : ($appt->status === 'confirmed' ? 'bg-blue-500' : 'bg-amber-500')) }}"></span>
                            {{ str_replace('_', ' ', $appt->status) }}
                        </span>
                    </div>
                @empty
                    <p class="text-sm text-gray-400 text-center py-4">No appointments today</p>
                @endforelse
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <h2 class="text-sm font-semibold text-gray-900">Recent Patients</h2>
                <a href="{{ route('patients.index') }}" class="text-xs text-emerald-600 hover:text-emerald-700 font-medium">View All</a>
            </div>
            <div class="p-5 space-y-3 max-h-80 overflow-y-auto">
                @forelse ($recentPatients as $p)
                    <div class="flex items-center gap-3 p-2 rounded-lg hover:bg-gray-50 transition-colors cursor-pointer" onclick="window.location.href='{{ route('patients.show', $p) }}'">
                        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-emerald-500 to-emerald-700 flex items-center justify-center text-white font-bold text-sm shadow-sm">{{ strtoupper(substr($p->first_name, 0, 1)) }}</div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 truncate">{{ $p->fullName() }}</p>
                            <p class="text-xs text-gray-500">{{ $p->mrn }} &bull; {{ $p->phone ?? 'No phone' }}</p>
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
</div>

{{-- Register Patient Modal --}}
<div id="registerPatientModal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" onclick="closeRegisterPatientModal()"></div>
    <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-full max-w-2xl">
        <div class="bg-white rounded-2xl shadow-2xl p-6 m-4 animate-fade">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-bold text-gray-900">Register New Patient</h3>
                <button onclick="closeRegisterPatientModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <form id="registerPatientForm" method="POST" action="{{ route('reception.patients.store') }}" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                @csrf
                <input type="text" name="first_name" placeholder="First name" class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" required>
                <input type="text" name="last_name" placeholder="Last name" class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" required>
                <input type="date" name="date_of_birth" class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" required>
                <select name="gender" class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" required>
                    <option value="">Gender</option>
                    <option value="male">Male</option>
                    <option value="female">Female</option>
                    <option value="other">Other</option>
                </select>
                <input type="text" name="phone" placeholder="Phone" class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                <input type="text" name="national_id" placeholder="National ID" class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                <input type="text" name="blood_group" placeholder="Blood group" class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                <textarea name="address" placeholder="Address" class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" rows="2"></textarea>
                <textarea name="allergies" placeholder="Allergies" class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" rows="2"></textarea>
                <div class="sm:col-span-2 flex justify-end gap-2 pt-2">
                    <button type="button" onclick="closeRegisterPatientModal()" class="px-4 py-2 text-sm text-gray-600 hover:bg-gray-100 rounded-lg">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700">Register Patient</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Open Visit Modal --}}
<div id="openVisitModal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" onclick="closeOpenVisitModal()"></div>
    <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-full max-w-lg">
        <div class="bg-white rounded-2xl shadow-2xl p-6 m-4 animate-fade">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-bold text-gray-900">Open New Visit</h3>
                <button onclick="closeOpenVisitModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <form id="openVisitForm" method="POST" action="{{ route('reception.visits.store') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Patient</label>
                    <input type="text" id="openVisitPatientSearch" placeholder="Search patient by name, phone or MRN..." class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 mb-1">
                    <select name="patient_id" id="openVisitPatientSelect" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" required>
                        <option value="">Select patient</option>
                        @foreach ($patientsList as $patient)
                            <option value="{{ $patient->id }}" data-search="{{ strtolower($patient->fullName() . ' ' . ($patient->phone ?? '') . ' ' . $patient->mrn) }}">{{ $patient->fullName() }} ({{ $patient->mrn }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Visit Type</label>
                    <select name="type" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" required>
                        <option value="outpatient">Outpatient</option>
                        <option value="emergency">Emergency</option>
                        <option value="followup">Follow-up</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Assign Doctor <span class="font-normal text-gray-400">(optional)</span></label>
                    <select name="doctor_id" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                        <option value="">Keep in reception queue</option>
                        @foreach ($doctors as $doctor)
                            <option value="{{ $doctor->id }}">{{ $doctor->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Chief Complaint</label>
                    <textarea name="chief_complaint" placeholder="Chief complaint" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" rows="3"></textarea>
                </div>
                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" onclick="closeOpenVisitModal()" class="px-4 py-2 text-sm text-gray-600 hover:bg-gray-100 rounded-lg">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700">Open Visit</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Custom Payment Modal --}}
<div id="paymentModal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" onclick="closePaymentModal()"></div>
    <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-full max-w-md">
        <div class="bg-white rounded-2xl shadow-2xl m-4 animate-fade overflow-hidden">
            <div class="px-6 py-4 bg-gradient-to-r from-emerald-600 to-emerald-700 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg bg-white/20 flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    </div>
                    <div>
                        <h3 class="text-white font-bold text-base">Collect Payment</h3>
                        <p class="text-emerald-100 text-xs" id="paymentModalVisit">Visit #</p>
                    </div>
                </div>
                <button onclick="closePaymentModal()" class="text-white/70 hover:text-white">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="p-6 space-y-4">
                <div class="bg-gray-50 rounded-xl p-3 flex items-center justify-between">
                    <div>
                        <p class="text-xs text-gray-500">Patient</p>
                        <p class="text-sm font-medium text-gray-900" id="paymentModalPatient">-</p>
                    </div>
                    <div class="text-right">
                        <p class="text-xs text-gray-500">Balance Due</p>
                        <p class="text-lg font-bold text-emerald-700" id="paymentModalBalance">0 TSh</p>
                    </div>
                </div>
                <form id="paymentModalForm" method="POST" action="" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1.5">Amount (TSh) <span class="text-red-500">*</span></label>
                        <input type="number" name="amount" id="paymentModalAmount" step="0.01" min="0.01" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" required>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1.5">Payment Method <span class="text-red-500">*</span></label>
                        <div class="grid grid-cols-4 gap-2">
                            <button type="button" data-method="cash" class="method-btn flex flex-col items-center gap-1 p-2.5 border-2 border-emerald-500 bg-emerald-50 rounded-lg text-xs font-medium text-emerald-700 transition-all">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                Cash
                            </button>
                            <button type="button" data-method="card" class="method-btn flex flex-col items-center gap-1 p-2.5 border-2 border-gray-200 rounded-lg text-xs font-medium text-gray-600 hover:border-emerald-400 hover:text-emerald-600 transition-all">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                                Card
                            </button>
                            <button type="button" data-method="mobile_money" class="method-btn flex flex-col items-center gap-1 p-2.5 border-2 border-gray-200 rounded-lg text-xs font-medium text-gray-600 hover:border-emerald-400 hover:text-emerald-600 transition-all">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                Mobile
                            </button>
                            <button type="button" data-method="insurance" class="method-btn flex flex-col items-center gap-1 p-2.5 border-2 border-gray-200 rounded-lg text-xs font-medium text-gray-600 hover:border-emerald-400 hover:text-emerald-600 transition-all">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                Insurance
                            </button>
                        </div>
                        <input type="hidden" name="method" id="paymentModalMethod" value="cash">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1.5">Reference <span class="font-normal text-gray-400">(optional)</span></label>
                        <input type="text" name="reference" id="paymentModalReference" placeholder="Transaction ID, cheque no, etc." class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                    </div>
                    <div class="flex justify-end gap-2 pt-2">
                        <button type="button" onclick="closePaymentModal()" class="px-4 py-2.5 text-sm text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">Cancel</button>
                        <button type="submit" class="btn-submit inline-flex items-center gap-2 px-5 py-2.5 bg-emerald-600 text-white text-sm font-semibold rounded-lg hover:bg-emerald-700 transition-colors shadow-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Record Payment
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const visitTrendCtx = document.getElementById('visitTrendChart').getContext('2d');
    const gradient = visitTrendCtx.createLinearGradient(0, 0, 0, 250);
    gradient.addColorStop(0, 'rgba(16, 185, 129, 0.25)');
    gradient.addColorStop(1, 'rgba(16, 185, 129, 0)');

    const visitTrendData = {
        labels: @json($visitLabels),
        datasets: [{
            label: 'Visits',
            data: @json($visitTrend),
            borderColor: '#10b981',
            backgroundColor: gradient,
            borderWidth: 2,
            tension: 0.4,
            fill: true,
            pointBackgroundColor: '#10b981',
            pointBorderColor: '#fff',
            pointHoverBackgroundColor: '#fff',
            pointHoverBorderColor: '#10b981',
            pointRadius: 4,
            pointHoverRadius: 6,
        }]
    };

    const visitTrendChart = new Chart(visitTrendCtx, {
        type: 'line',
        data: visitTrendData,
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: 'rgba(17, 24, 39, 0.9)',
                    padding: 10,
                    cornerRadius: 8,
                    displayColors: false,
                    callbacks: {
                        label: function(context) {
                            return 'Visits: ' + context.raw;
                        }
                    }
                }
            },
            scales: {
                y: { beginAtZero: true, grid: { color: '#f3f4f6' }, ticks: { precision: 0 } },
                x: { grid: { display: false } }
            },
            interaction: { intersect: false, mode: 'index' },
        }
    });

    const statusDoughnutCtx = document.getElementById('statusDoughnutChart').getContext('2d');
    const statusColorMap = {
        'registered': '#9ca3af',
        'waiting_for_doctor': '#3b82f6',
        'with_doctor': '#a855f7',
        'waiting_for_lab': '#06b6d4',
        'waiting_for_pharmacy': '#ec4899',
        'waiting_for_payment': '#f59e0b',
        'completed': '#10b981',
        'cancelled': '#ef4444'
    };
    const statusLabels = @json(array_keys($statusCounts));
    const statusData = @json(array_values($statusCounts));

    const statusDoughnutChart = new Chart(statusDoughnutCtx, {
        type: 'doughnut',
        data: {
            labels: statusLabels.map(s => s.replace(/_/g, ' ')),
            datasets: [{
                data: statusData,
                backgroundColor: statusLabels.map(s => statusColorMap[s] || '#9ca3af'),
                borderWidth: 0,
                hoverOffset: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '65%',
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: 'rgba(17, 24, 39, 0.9)',
                    padding: 10,
                    cornerRadius: 8,
                    callbacks: {
                        label: function(context) {
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const pct = total ? Math.round((context.raw / total) * 100) : 0;
                            return context.label + ': ' + context.raw + ' (' + pct + '%)';
                        }
                    }
                }
            }
        }
    });

    // KPI counter animation
    function animateKPIs() {
        document.querySelectorAll('.kpi-counter').forEach(el => {
            const target = parseFloat(el.dataset.target) || 0;
            const prefix = el.dataset.prefix || '';
            const suffix = el.dataset.suffix || '';
            const duration = 1200;
            const startTime = performance.now();
            function update(currentTime) {
                const elapsed = currentTime - startTime;
                const progress = Math.min(elapsed / duration, 1);
                const ease = 1 - Math.pow(1 - progress, 3);
                const value = target * ease;
                el.textContent = prefix + (Number.isInteger(target) ? Math.round(value).toLocaleString() : Math.round(value).toLocaleString()) + suffix;
                if (progress < 1) requestAnimationFrame(update);
            }
            requestAnimationFrame(update);
        });

        document.querySelectorAll('.kpi-bar').forEach(bar => {
            setTimeout(() => bar.style.width = bar.dataset.width, 300);
        });

        document.querySelectorAll('.kpi-ring').forEach(ring => {
            setTimeout(() => {
                const pct = parseFloat(ring.dataset.pct) || 0;
                const circumference = 125.66;
                const offset = circumference * (1 - Math.min(pct, 100) / 100);
                ring.style.strokeDashoffset = offset;
            }, 300);
        });

        document.querySelectorAll('[data-width]').forEach(bar => {
            if (!bar.classList.contains('kpi-bar')) {
                setTimeout(() => bar.style.width = bar.dataset.width, 300);
            }
        });
    }

    animateKPIs();

    // Modal helpers
    function openRegisterPatientModal() { document.getElementById('registerPatientModal').classList.remove('hidden'); }
    function closeRegisterPatientModal() { document.getElementById('registerPatientModal').classList.add('hidden'); }
    function openOpenVisitModal() { document.getElementById('openVisitModal').classList.remove('hidden'); }
    function closeOpenVisitModal() { document.getElementById('openVisitModal').classList.add('hidden'); }

    // Payment modal
    function openPaymentModal(visitId, visitNumber, patientName, total, paid, balance) {
        const url = '{{ route("reception.visits.pay", "__ID__") }}'.replace('__ID__', visitId);
        document.getElementById('paymentModalForm').action = url;
        document.getElementById('paymentModalVisit').textContent = 'Visit #' + visitNumber;
        document.getElementById('paymentModalPatient').textContent = patientName;
        document.getElementById('paymentModalBalance').textContent = balance.toLocaleString() + ' TSh';
        document.getElementById('paymentModalAmount').value = balance;
        document.getElementById('paymentModalReference').value = '';
        // Reset method to cash
        document.getElementById('paymentModalMethod').value = 'cash';
        document.querySelectorAll('.method-btn').forEach(b => {
            b.classList.remove('border-emerald-500', 'bg-emerald-50', 'text-emerald-700');
            b.classList.add('border-gray-200', 'text-gray-600');
        });
        document.querySelector('.method-btn[data-method="cash"]').classList.add('border-emerald-500', 'bg-emerald-50', 'text-emerald-700');
        document.querySelector('.method-btn[data-method="cash"]').classList.remove('border-gray-200', 'text-gray-600');
        document.getElementById('paymentModal').classList.remove('hidden');
    }
    function closePaymentModal() { document.getElementById('paymentModal').classList.add('hidden'); }

    // Method button selection
    document.querySelectorAll('.method-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            document.querySelectorAll('.method-btn').forEach(b => {
                b.classList.remove('border-emerald-500', 'bg-emerald-50', 'text-emerald-700');
                b.classList.add('border-gray-200', 'text-gray-600');
            });
            this.classList.add('border-emerald-500', 'bg-emerald-50', 'text-emerald-700');
            this.classList.remove('border-gray-200', 'text-gray-600');
            document.getElementById('paymentModalMethod').value = this.dataset.method;
        });
    });

    // Submit payment modal form via AJAX
    submitFormAjax(document.getElementById('paymentModalForm'), closePaymentModal);

    const receptionAction = new URLSearchParams(window.location.search).get('action');
    if (receptionAction === 'register') openRegisterPatientModal();
    if (receptionAction === 'visit') openOpenVisitModal();

    // AJAX form submissions
    function submitFormAjax(form, modalCloseFn) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const btn = form.querySelector('.btn-submit');
            const originalHTML = btn ? btn.innerHTML : '';
            if (btn) {
                btn.disabled = true;
                btn.innerHTML = '<svg class="w-3.5 h-3.5 animate-spin inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg> Saving...';
                btn.classList.add('opacity-75', 'cursor-not-allowed');
            }
            const formData = new FormData(form);
            fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
            })
            .then(r => r.json().then(data => ({ ok: r.ok, data })))
            .then(({ ok, data }) => {
                if (ok) {
                    Swal.fire({ icon: 'success', title: 'Success', text: data.message || 'Saved successfully', timer: 1500, showConfirmButton: false });
                    form.reset();
                    if (modalCloseFn) modalCloseFn();
                    refreshStats();
                } else {
                    Swal.fire({ icon: 'error', title: 'Error', text: data.message || 'Something went wrong.' });
                }
                if (btn) {
                    btn.disabled = false;
                    btn.innerHTML = originalHTML;
                    btn.classList.remove('opacity-75', 'cursor-not-allowed');
                }
            })
            .catch(() => {
                Swal.fire({ icon: 'error', title: 'Error', text: 'Something went wrong. Please try again.' });
                if (btn) {
                    btn.disabled = false;
                    btn.innerHTML = originalHTML;
                    btn.classList.remove('opacity-75', 'cursor-not-allowed');
                }
            });
        });
    }

    submitFormAjax(document.getElementById('registerPatientForm'), closeRegisterPatientModal);
    submitFormAjax(document.getElementById('openVisitForm'), closeOpenVisitModal);

    document.querySelectorAll('.ajax-assign-form').forEach(form => submitFormAjax(form, null));
    document.querySelectorAll('.ajax-change-form').forEach(form => submitFormAjax(form, null));
    document.querySelectorAll('.ajax-pay-form').forEach(form => submitFormAjax(form, null));

    // Queue tabs
    document.querySelectorAll('.queue-tab').forEach(btn => {
        btn.addEventListener('click', () => {
            document.querySelectorAll('.queue-tab').forEach(b => {
                b.classList.remove('active', 'bg-emerald-100', 'text-emerald-700');
                b.classList.add('text-gray-600', 'hover:bg-gray-100');
            });
            btn.classList.add('active', 'bg-emerald-100', 'text-emerald-700');
            btn.classList.remove('text-gray-600', 'hover:bg-gray-100');
            document.querySelectorAll('.queue-panel').forEach(p => p.classList.add('hidden'));
            document.getElementById('tab-' + btn.dataset.tab).classList.remove('hidden');
        });
    });

    // Patient search in open visit modal
    document.getElementById('openVisitPatientSearch')?.addEventListener('input', function() {
        const q = this.value.toLowerCase();
        const select = document.getElementById('openVisitPatientSelect');
        Array.from(select.querySelectorAll('option')).forEach(opt => {
            if (!opt.value) { opt.style.display = 'block'; return; }
            opt.style.display = (opt.dataset.search || '').includes(q) ? 'block' : 'none';
        });
    });

    // Global patient search
    const globalSearch = document.getElementById('globalPatientSearch');
    const globalResults = document.getElementById('globalSearchResults');
    const patients = @json($patientSearchData);

    globalSearch?.addEventListener('input', function() {
        const q = this.value.toLowerCase().trim();
        globalResults.innerHTML = '';
        if (!q) { globalResults.classList.add('hidden'); return; }
        const matches = patients.filter(p => (p.name + ' ' + (p.phone || '') + ' ' + p.mrn).toLowerCase().includes(q)).slice(0, 8);
        if (matches.length === 0) { globalResults.classList.add('hidden'); return; }
        matches.forEach(p => {
            const div = document.createElement('a');
            div.href = p.url;
            div.className = 'block px-3 py-2 text-xs text-gray-700 hover:bg-gray-50 border-b border-gray-50 last:border-0';
            div.innerHTML = `<span class="font-medium">${p.name}</span> <span class="text-gray-400">&bull; ${p.mrn}</span>`;
            globalResults.appendChild(div);
        });
        globalResults.classList.remove('hidden');
    });

    document.addEventListener('click', (e) => {
        if (!globalSearch?.contains(e.target) && !globalResults?.contains(e.target)) {
            globalResults?.classList.add('hidden');
        }
    });

    // Real-time stats polling
    async function refreshStats() {
        try {
            const res = await fetch('{{ route("reception.stats") }}', { headers: { 'Accept': 'application/json' } });
            const data = await res.json();

            const kpiMap = {
                'Today Visits': data.today_visits,
                'Waiting Payment': data.waiting_payment,
                'Waiting Doctor': data.waiting_doctor,
                'With Doctor': data.with_doctor,
                'Today Revenue': Math.round(data.today_revenue),
                'New Patients': data.today_patients,
                'Appointments': data.appointments_today,
                'Avg Wait': data.avg_wait_minutes ?? 12,
            };
            document.querySelectorAll('.kpi-counter').forEach(el => {
                const label = el.parentElement.querySelector('p:first-child')?.textContent?.trim();
                if (label && kpiMap[label] !== undefined) {
                    const newVal = kpiMap[label];
                    const max = parseFloat(el.dataset.max) || 1;
                    const pct = Math.min(100, (newVal / max) * 100);
                    el.dataset.target = newVal;
                    const card = el.closest('.bg-white');
                    if (card) {
                        const bar = card.querySelector('.kpi-bar');
                        const ring = card.querySelector('.kpi-ring');
                        if (bar) bar.dataset.width = pct + '%';
                        if (ring) ring.dataset.pct = pct;
                    }
                }
            });
            animateKPIs();

            visitTrendChart.data.datasets[0].data = data.visit_trend;
            visitTrendChart.data.labels = data.visit_labels;
            visitTrendChart.update();

            const newStatusLabels = Object.keys(data.status_counts);
            statusDoughnutChart.data.labels = newStatusLabels.map(s => s.replace(/_/g, ' '));
            statusDoughnutChart.data.datasets[0].data = Object.values(data.status_counts);
            statusDoughnutChart.data.datasets[0].backgroundColor = newStatusLabels.map(s => statusColorMap[s] || '#9ca3af');
            statusDoughnutChart.update();

            document.querySelectorAll('.status-count').forEach(el => {
                el.textContent = data.status_counts[el.dataset.status] ?? 0;
            });

            document.querySelectorAll('.tab-count').forEach(el => {
                const key = el.dataset.count;
                if (data.status_counts && data.status_counts[key] !== undefined) el.textContent = data.status_counts[key];
            });

            document.getElementById('appointmentsTodayCount').textContent = data.appointments_today;
            document.getElementById('lastUpdated').textContent = 'Updated ' + new Date().toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
        } catch (e) { console.error('Stats refresh failed', e); }
    }

    setInterval(refreshStats, 30000);

    // Call notification polling
    let lastCallCheck = new Date().toISOString();
    let shownNotificationIds = new Set();
    const alarmSound = new Audio('{{ asset("mixkit-alarm-tone-996.wav") }}');
    alarmSound.volume = 0.7;

    function dismissCallNotification() {
        document.getElementById('callNotificationBanner').classList.add('hidden');
    }

    async function pollCallNotifications() {
        try {
            const res = await fetch('{{ route("reception.call-notifications") }}?since=' + encodeURIComponent(lastCallCheck), {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            });
            const data = await res.json();
            lastCallCheck = data.server_time;

            const newNotifications = data.notifications.filter(n => !shownNotificationIds.has(n.id));
            if (newNotifications.length > 0) {
                newNotifications.forEach(n => shownNotificationIds.add(n.id));
                const latest = newNotifications[0];
                const banner = document.getElementById('callNotificationBanner');
                document.getElementById('callNotificationTitle').textContent = 'Dr. ' + latest.doctor_name + ' is calling ' + latest.patient_name;
                document.getElementById('callNotificationText').textContent = 'Visit #' + latest.visit_number + ' - Please direct the patient to the doctor\'s room. Called at ' + latest.time;
                banner.classList.remove('hidden');

                // Play sound
                alarmSound.currentTime = 0;
                alarmSound.play().catch(e => console.log('Audio play blocked:', e));

                // Auto-dismiss after 30 seconds
                setTimeout(() => banner.classList.add('hidden'), 30000);
            }
        } catch (e) { console.error('Call notification poll failed', e); }
    }

    setInterval(pollCallNotifications, 10000);
    setTimeout(pollCallNotifications, 3000);

    // Cancel visit with confirmation
    function cancelVisit(visitId, visitNumber) {
        Swal.fire({
            title: 'Cancel Visit?',
            text: 'Are you sure you want to cancel visit ' + visitNumber + '? This cannot be undone.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Yes, cancel it',
            cancelButtonText: 'No, keep it',
            reverseButtons: true,
        }).then((result) => {
            if (result.isConfirmed) {
                const cancelUrl = '{{ route("reception.visits.cancel", "__ID__") }}'.replace('__ID__', visitId);
                fetch(cancelUrl, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || document.querySelector('input[name="_token"]')?.value,
                    },
                })
                .then(r => r.json().catch(() => ({})))
                .then(data => {
                    if (data.success !== false) {
                        const row = document.getElementById('visit-row-' + visitId);
                        if (row) {
                            row.style.transition = 'opacity 0.3s, transform 0.3s';
                            row.style.opacity = '0';
                            row.style.transform = 'translateX(20px)';
                            setTimeout(() => row.remove(), 300);
                        }
                        Swal.fire({ icon: 'success', title: 'Cancelled', text: data.message || 'Visit cancelled successfully.', timer: 1500, showConfirmButton: false });
                        refreshStats();
                    } else {
                        Swal.fire({ icon: 'error', title: 'Error', text: data.message || 'Could not cancel visit.' });
                    }
                })
                .catch(() => {
                    Swal.fire({ icon: 'error', title: 'Error', text: 'Something went wrong. Please try again.' });
                });
            }
        });
    }
</script>
@endpush
