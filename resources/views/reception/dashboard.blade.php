@extends('layouts.dashboard')

@section('title', 'Reception - ' . config('app.name', 'Laravel'))
@section('page_title', 'Reception Dashboard')

@section('content')
<div class="space-y-6" id="receptionDashboard">
    @if (session('status'))
        <div class="p-3 rounded-md bg-white border-l-2 border-emerald-500 text-emerald-700 text-xs animate-fade">{{ session('status') }}</div>
    @endif

    <div class="bg-slate-800 rounded-md p-6 text-white">
        <div class="flex flex-wrap items-end justify-between gap-3">
            <div>
                <p class="text-[10px] uppercase tracking-wider text-slate-400 mb-1">Reception</p>
                <h1 class="text-xl font-bold">Reception Command Center</h1>
                <p class="text-xs text-slate-400 mt-1">{{ today()->format('l, F j, Y') }}</p>
            </div>
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded bg-slate-700/60 text-xs font-medium text-slate-200">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Live Queue
                </span>
                <span class="text-[10px] text-slate-400" id="lastUpdated">Updated just now</span>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        @php
            $kpiCards = [
                ['label' => 'Today Visits', 'value' => $kpis['today_visits'], 'icon' => 'hgi-file-01', 'text' => 'text-gray-400', 'bar' => 'bg-emerald-600', 'ring' => 'text-emerald-600', 'max' => 50],
                ['label' => 'Waiting Payment', 'value' => $kpis['waiting_payment'], 'icon' => 'hgi-money-03', 'text' => 'text-gray-400', 'bar' => 'bg-amber-500', 'ring' => 'text-amber-500', 'max' => 20],
                ['label' => 'Waiting Doctor', 'value' => $kpis['waiting_doctor'], 'icon' => 'hgi-user', 'text' => 'text-gray-400', 'bar' => 'bg-amber-500', 'ring' => 'text-amber-500', 'max' => 20],
                ['label' => 'With Doctor', 'value' => $kpis['with_doctor'], 'icon' => 'hgi-checkmark-circle-02', 'text' => 'text-gray-400', 'bar' => 'bg-slate-400', 'ring' => 'text-slate-400', 'max' => 20],
                ['label' => 'Today Revenue', 'value' => 'TSh ' . number_format($kpis['today_revenue']), 'raw' => round($kpis['today_revenue']), 'icon' => 'hgi-message-multiple-02', 'text' => 'text-gray-400', 'bar' => 'bg-emerald-600', 'ring' => 'text-emerald-600', 'max' => 500000],
                ['label' => 'New Patients', 'value' => $kpis['today_patients'], 'icon' => 'hgi-user-add-01', 'text' => 'text-gray-400', 'bar' => 'bg-slate-400', 'ring' => 'text-slate-400', 'max' => 20],
                ['label' => 'Appointments', 'value' => $kpis['appointments_today'], 'icon' => 'hgi-calendar-03', 'text' => 'text-gray-400', 'bar' => 'bg-slate-400', 'ring' => 'text-slate-400', 'max' => 30],
                ['label' => 'Avg Wait', 'value' => $kpis['avg_wait_minutes'] . 'm', 'raw' => $kpis['avg_wait_minutes'], 'icon' => 'hgi-clock-01', 'text' => 'text-gray-400', 'bar' => 'bg-amber-500', 'ring' => 'text-amber-500', 'max' => 60],
            ];
        @endphp
        @foreach ($kpiCards as $card)
            <div class="bg-white rounded-md border border-gray-200 p-4">
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
                            <i class="hgi-stroke {{ $card['icon'] }} text-xl {{ $card['text'] }}"></i>
                        </div>
                    </div>
                </div>
                <div class="mt-3 h-1.5 bg-gray-100 rounded-full overflow-hidden">
                    <div class="h-full {{ $card['bar'] }} kpi-bar" style="width: 0%" data-width="{{ min(100, (($card['raw'] ?? $card['value']) / $card['max']) * 100) }}%"></div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 grid grid-cols-2 sm:grid-cols-4 gap-3">
            <button onclick="openRegisterPatientModal()" class="group flex flex-col items-center gap-2 p-4 bg-slate-800 rounded-md text-white hover:bg-slate-700 transition-colors">
                <div class="w-10 h-10 rounded-md bg-white/10 flex items-center justify-center">
                    <i class="hgi-stroke hgi-user-add-01 text-xl"></i>
                </div>
                <span class="text-xs font-semibold">Register Patient</span>
            </button>
            <button onclick="openOpenVisitModal()" class="group flex flex-col items-center gap-2 p-4 bg-white rounded-md border border-gray-200 text-gray-700 hover:border-gray-300 transition-colors">
                <div class="w-10 h-10 rounded-md bg-gray-100 text-slate-600 flex items-center justify-center">
                    <i class="hgi-stroke hgi-file-01 text-xl"></i>
                </div>
                <span class="text-xs font-medium">Open Visit</span>
            </button>
            <a href="{{ route('patients.index') }}" class="group flex flex-col items-center gap-2 p-4 bg-white rounded-md border border-gray-200 text-gray-700 hover:border-gray-300 transition-colors">
                <div class="w-10 h-10 rounded-md bg-gray-100 text-slate-600 flex items-center justify-center">
                    <i class="hgi-stroke hgi-user-multiple-02 text-xl"></i>
                </div>
                <span class="text-xs font-medium">Patients</span>
            </a>
            <a href="{{ route('appointments.index') }}" class="group flex flex-col items-center gap-2 p-4 bg-white rounded-md border border-gray-200 text-gray-700 hover:border-gray-300 transition-colors">
                <div class="w-10 h-10 rounded-md bg-gray-100 text-slate-600 flex items-center justify-center">
                    <i class="hgi-stroke hgi-calendar-03 text-xl"></i>
                </div>
                <span class="text-xs font-medium">Appointments</span>
            </a>
        </div>
        <div class="bg-white rounded-md border border-gray-200 p-4 flex flex-col justify-center">
            <label class="text-xs font-medium text-gray-700 mb-1.5">Quick Patient Search</label>
            <div class="relative">
                <x-ui.input type="text" id="globalPatientSearch" placeholder="Type name, phone or MRN..." class="w-full border border-gray-200 rounded pl-9 pr-3 py-2 text-sm focus:ring-2 focus:ring-slate-300 outline-none" />
                <i class="hgi-stroke hgi-search-01 text-base text-gray-400 absolute left-3 top-2.5"></i>
            </div>
            <div id="globalSearchResults" class="hidden mt-2 max-h-40 overflow-y-auto border border-gray-100 rounded-lg bg-white"></div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 bg-white rounded-md border border-gray-200 p-5">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-sm font-bold text-gray-900">Visit Trend (Last 7 Days)</h2>
                <span class="text-xs text-gray-400 live-indicator flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span> Live
                </span>
            </div>
            <div class="relative h-64">
                <canvas id="visitTrendChart"></canvas>
            </div>
        </div>

        <div class="bg-white rounded-md border border-gray-200 p-5">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-sm font-bold text-gray-900">Status Distribution</h2>
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
                        'registered' => 'bg-slate-400',
                        'waiting_for_doctor' => 'bg-amber-500',
                        'with_doctor' => 'bg-slate-700',
                        'waiting_for_lab' => 'bg-slate-500',
                        'waiting_for_pharmacy' => 'bg-slate-600',
                        'waiting_for_payment' => 'bg-amber-500',
                        'completed' => 'bg-emerald-500',
                        'cancelled' => 'bg-red-500',
                    ];
                @endphp
                @foreach ($statusCounts as $status => $count)
                    <div class="flex items-center justify-between px-3 py-2 rounded bg-gray-50">
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

    <div class="bg-white rounded-md border border-gray-200 overflow-hidden" id="liveQueue">
        <div class="px-5 py-4 border-b border-gray-200 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <h2 class="text-sm font-bold text-gray-900">Live Reception Queue</h2>
            <div class="flex flex-wrap gap-2" id="queueTabs">
                <button data-tab="need-doctor" class="queue-tab active px-3 py-1.5 text-xs font-medium rounded bg-slate-800 text-white">Check-in & Pay <span class="ml-1 tab-count" data-count="registered">{{ $registeredVisits->count() }}</span></button>
                <button data-tab="waiting-doctor" class="queue-tab px-3 py-1.5 text-xs font-medium rounded text-gray-600 hover:bg-gray-100">Waiting Doctor <span class="ml-1 tab-count" data-count="waiting_for_doctor">{{ $waitingForDoctor->count() }}</span></button>
                <button data-tab="with-doctor" class="queue-tab px-3 py-1.5 text-xs font-medium rounded text-gray-600 hover:bg-gray-100">With Doctor <span class="ml-1 tab-count" data-count="with_doctor">{{ $withDoctorVisits->count() }}</span></button>
                <button data-tab="payment" class="queue-tab px-3 py-1.5 text-xs font-medium rounded text-gray-600 hover:bg-gray-100">Payment <span class="ml-1 tab-count" data-count="waiting_for_payment">{{ $waitingForPayment->count() }}</span></button>
            </div>
        </div>
        <div class="overflow-x-auto p-0">
            @php
                $statusDots = [
                    'registered' => 'bg-slate-400',
                    'waiting_for_doctor' => 'bg-amber-500',
                    'with_doctor' => 'bg-slate-700',
                    'waiting_for_lab' => 'bg-slate-500',
                    'waiting_for_pharmacy' => 'bg-slate-600',
                    'waiting_for_payment' => 'bg-amber-500',
                    'completed' => 'bg-emerald-500',
                    'cancelled' => 'bg-red-500',
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
                                <td class="px-6 py-3 text-xs font-semibold text-gray-900 text-right">{{ number_format($invoice?->total ?? 0) }} TSh</td>
                                <td class="px-6 py-3">
                                    @if ($isPaid)
                                        <span class="inline-flex items-center gap-1.5 text-[10px] font-medium text-gray-600"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>Paid</span>
                                    @elseif ($invoice && $invoice->paid > 0)
                                        <span class="inline-flex items-center gap-1.5 text-[10px] font-medium text-gray-600"><span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>Partial</span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 text-[10px] font-medium text-gray-600"><span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>Unpaid</span>
                                    @endif
                                </td>
                                <td class="px-6 py-3">
                                    @if (! $isPaid)
                                        <div class="flex items-center gap-2">
                                            <form method="POST" action="{{ route('reception.visits.pay', $visit) }}" class="ajax-pay-form">
                                                @csrf
                                                <input type="hidden" name="amount" value="{{ $balance }}">
                                                <input type="hidden" name="method" value="cash">
                                                <button type="submit" class="btn-submit inline-flex items-center gap-1.5 bg-slate-800 text-white text-xs font-semibold px-3 py-1.5 rounded hover:bg-slate-700 transition-colors">
                                                    <i class="hgi-stroke hgi-tick-02 text-sm"></i>
                                                    Pay {{ number_format($balance) }}
                                                </button>
                                            </form>
                                            <button type="button" onclick="openPaymentModal({{ $visit->id }}, '{{ $visit->visit_number }}', '{{ $visit->patient->fullName() }}', {{ $invoice?->total ?? 0 }}, {{ $invoice?->paid ?? 0 }}, {{ $balance }})" class="inline-flex items-center gap-1 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-medium px-3 py-1.5 rounded-lg transition-colors">
                                                <i class="hgi-stroke hgi-plus-sign text-sm"></i>
                                                Custom
                                            </button>
                                        </div>
                                    @elseif (! $visit->doctor_id)
                                        <form method="POST" action="{{ route('reception.visits.assign', $visit) }}" class="ajax-assign-form flex gap-2">
                                            @csrf
                                            <x-ui.select name="doctor_id" class="border border-gray-200 rounded text-xs px-2 py-1.5 focus:ring-2 focus:ring-slate-300 outline-none" required>
                                                <option value="">Select Doctor</option>
                                                @foreach ($doctors as $doctor)
                                                    <option value="{{ $doctor->id }}">{{ $doctor->name }}</option>
                                                @endforeach
                                            </x-ui.select>
                                            <button type="submit" class="btn-submit bg-slate-800 text-white text-xs font-medium px-3 py-1.5 rounded hover:bg-slate-700 transition-colors">Send to Doctor</button>
                                        </form>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 text-xs text-emerald-600 font-medium"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>Ready for doctor</span>
                                    @endif
                                </td>
                                <td class="px-6 py-3 text-right">
                                    <button type="button" onclick="cancelVisit({{ $visit->id }}, '{{ $visit->visit_number }}')" class="inline-flex items-center justify-center w-7 h-7 rounded bg-red-50 hover:bg-red-100 text-red-600 hover:text-red-700 transition-colors" title="Cancel visit">
                                        <i class="hgi-stroke hgi-delete-02 text-sm"></i>
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
                                        <x-ui.select name="doctor_id" class="border border-gray-200 rounded text-xs px-2 py-1 focus:ring-2 focus:ring-slate-300 outline-none" required>
                                            @foreach ($doctors as $doctor)
                                                <option value="{{ $doctor->id }}" {{ $visit->doctor_id == $doctor->id ? 'selected' : '' }}>{{ $doctor->name }}</option>
                                            @endforeach
                                        </x-ui.select>
                                        <button type="submit" class="btn-submit bg-slate-800 text-white text-xs font-medium px-3 py-1 rounded hover:bg-slate-700 transition-colors">Change</button>
                                    </form>
                                </td>
                                <td class="px-6 py-3 text-right">
                                    <button type="button" onclick="cancelVisit({{ $visit->id }}, '{{ $visit->visit_number }}')" class="inline-flex items-center justify-center w-7 h-7 rounded bg-red-50 hover:bg-red-100 text-red-600 hover:text-red-700 transition-colors" title="Cancel visit">
                                        <i class="hgi-stroke hgi-delete-02 text-sm"></i>
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
                                <td class="px-6 py-3"><span class="inline-flex items-center gap-1.5 text-[10px] font-medium text-gray-600"><span class="w-1.5 h-1.5 rounded-full {{ $statusDots[$visit->status] ?? 'bg-slate-400' }}"></span>{{ str_replace('_', ' ', $visit->status) }}</span></td>
                                <td class="px-6 py-3 text-right">
                                    <button type="button" onclick="cancelVisit({{ $visit->id }}, '{{ $visit->visit_number }}')" class="inline-flex items-center justify-center w-7 h-7 rounded bg-red-50 hover:bg-red-100 text-red-600 hover:text-red-700 transition-colors" title="Cancel visit">
                                        <i class="hgi-stroke hgi-delete-02 text-sm"></i>
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
                                <td class="px-6 py-3 text-xs font-semibold text-gray-900 text-right">{{ number_format($visit->invoice?->total ?? 0) }} TSh</td>
                                <td class="px-6 py-3">
                                    <div class="flex items-center gap-2">
                                        <form method="POST" action="{{ route('reception.visits.pay', $visit) }}" class="ajax-pay-form">
                                            @csrf
                                            <input type="hidden" name="amount" value="{{ $balance }}">
                                            <input type="hidden" name="method" value="cash">
                                            <button type="submit" class="btn-submit inline-flex items-center gap-1.5 bg-slate-800 text-white text-xs font-semibold px-3 py-1.5 rounded hover:bg-slate-700 transition-colors">
                                                <i class="hgi-stroke hgi-tick-02 text-sm"></i>
                                                Pay {{ number_format($balance) }}
                                            </button>
                                        </form>
                                        <button type="button" onclick="openPaymentModal({{ $visit->id }}, '{{ $visit->visit_number }}', '{{ $visit->patient->fullName() }}', {{ $visit->invoice?->total ?? 0 }}, {{ $visit->invoice?->paid ?? 0 }}, {{ $balance }})" class="inline-flex items-center gap-1 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-medium px-3 py-1.5 rounded-lg transition-colors">
                                            <i class="hgi-stroke hgi-plus-sign text-sm"></i>
                                            Custom
                                        </button>
                                    </div>
                                </td>
                                <td class="px-6 py-3 text-right">
                                    <button type="button" onclick="cancelVisit({{ $visit->id }}, '{{ $visit->visit_number }}')" class="inline-flex items-center justify-center w-7 h-7 rounded bg-red-50 hover:bg-red-100 text-red-600 hover:text-red-700 transition-colors" title="Cancel visit">
                                        <i class="hgi-stroke hgi-delete-02 text-sm"></i>
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

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-md border border-gray-200 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <h2 class="text-sm font-bold text-gray-900">Today's Appointments</h2>
                <span class="text-[10px] font-semibold bg-slate-100 text-slate-700 px-2 py-0.5 rounded" id="appointmentsTodayCount">{{ $todayAppointments->count() }}</span>
            </div>
            <div class="p-5 space-y-3 max-h-80 overflow-y-auto">
                @forelse ($todayAppointments as $appt)
                    <div class="flex items-center gap-3 p-2 rounded-lg hover:bg-gray-50 transition-colors cursor-pointer" onclick="window.location.href='{{ route('appointments.edit', $appt) }}'">
                        <div class="w-10 h-10 rounded bg-slate-100 text-slate-700 flex items-center justify-center text-xs font-bold">{{ substr($appt->startTime(), 0, 2) }}</div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 truncate">{{ $appt->patient?->name ?? '-' }}</p>
                            <p class="text-xs text-gray-500">{{ $appt->startTime() }} &bull; {{ $appt->doctor?->name ?? 'Unassigned' }}</p>
                        </div>
                        <span class="inline-flex items-center gap-1.5 text-[10px] font-medium capitalize text-gray-600">
                            <span class="w-1.5 h-1.5 rounded-full {{ $appt->status === 'completed' ? 'bg-emerald-500' : ($appt->status === 'cancelled' ? 'bg-red-500' : 'bg-amber-500') }}"></span>
                            {{ str_replace('_', ' ', $appt->status) }}
                        </span>
                    </div>
                @empty
                    <p class="text-sm text-gray-400 text-center py-4">No appointments today</p>
                @endforelse
            </div>
        </div>

        <div class="bg-white rounded-md border border-gray-200 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <h2 class="text-sm font-bold text-gray-900">Recent Patients</h2>
                <a href="{{ route('patients.index') }}" class="text-xs text-slate-600 hover:text-slate-800 font-medium">View All</a>
            </div>
            <div class="p-5 space-y-3 max-h-80 overflow-y-auto">
                @forelse ($recentPatients as $p)
                    <div class="flex items-center gap-3 p-2 rounded-lg hover:bg-gray-50 transition-colors cursor-pointer" onclick="window.location.href='{{ route('patients.show', $p) }}'">
                        <div class="w-10 h-10 rounded-full bg-slate-100 text-slate-600 flex items-center justify-center font-bold text-sm">{{ strtoupper(substr($p->first_name, 0, 1)) }}</div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 truncate">{{ $p->fullName() }}</p>
                            <p class="text-xs text-gray-500">{{ $p->mrn }} &bull; {{ $p->phone ?? 'No phone' }}</p>
                        </div>
                        <div class="text-right">
                            <span class="text-[10px] text-gray-400">{{ $p->created_at->diffForHumans() }}</span>
                            <div class="text-[10px] text-gray-400 font-medium">{{ $p->visits_count ?? 0 }} visits</div>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-gray-400 text-center py-4">No patients yet</p>
                @endforelse
            </div>
        </div>
    </div>
</div>

<div id="registerPatientModal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" onclick="closeRegisterPatientModal()"></div>
    <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-full max-w-2xl">
        <div class="bg-white rounded-md shadow-2xl p-6 m-4 animate-fade">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-bold text-gray-900">Register New Patient</h3>
                <button onclick="closeRegisterPatientModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="hgi-stroke hgi-cancel-01 text-2xl"></i>
                </button>
            </div>
            <form id="registerPatientForm" method="POST" action="{{ route('reception.patients.store') }}" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                @csrf
                <x-ui.input type="text" name="first_name" placeholder="First name" class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-slate-300" required />
                <x-ui.input type="text" name="last_name" placeholder="Last name" class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-slate-300" required />
                <x-ui.input type="text" data-fp-date name="date_of_birth" class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-slate-300" required />
                <x-ui.select name="gender" class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-slate-300" required>
                    <option value="">Gender</option>
                    <option value="male">Male</option>
                    <option value="female">Female</option>
                    <option value="other">Other</option>
                </x-ui.select>
                <x-ui.input type="text" name="phone" placeholder="Phone" class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-slate-300" />
                <x-ui.input type="text" name="national_id" placeholder="National ID" class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-slate-300" />
                <x-ui.input type="text" name="blood_group" placeholder="Blood group" class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-slate-300" />
                <x-ui.textarea name="address" placeholder="Address" class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-slate-300" rows="2"></x-ui.textarea>
                <x-ui.textarea name="allergies" placeholder="Allergies" class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-slate-300" rows="2"></x-ui.textarea>
                <div class="sm:col-span-2 flex justify-end gap-2 pt-2">
                    <button type="button" onclick="closeRegisterPatientModal()" class="px-4 py-2 text-sm text-gray-600 hover:bg-gray-100 rounded-lg">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-slate-800 text-white text-sm font-medium rounded hover:bg-slate-700">Register Patient</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div id="openVisitModal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" onclick="closeOpenVisitModal()"></div>
    <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-full max-w-lg">
        <div class="bg-white rounded-md shadow-2xl p-6 m-4 animate-fade">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-bold text-gray-900">Open New Visit</h3>
                <button onclick="closeOpenVisitModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="hgi-stroke hgi-cancel-01 text-2xl"></i>
                </button>
            </div>
            <form id="openVisitForm" method="POST" action="{{ route('reception.visits.store') }}" class="space-y-4">
                @csrf
                <div class="relative" id="patientSearchContainer">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Select Patient</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="hgi-stroke hgi-search-01 text-xl text-gray-400"></i>
                        </div>
                        <x-ui.input type="text" id="patientSearchInput" autocomplete="off" class="w-full pl-10 pr-4 py-2.5 bg-white border border-gray-300 rounded-lg text-gray-900 focus:outline-none focus:ring-2 focus:ring-slate-300 transition-all" placeholder="Search by name, MRN or phone..." />
                        <input type="hidden" name="patient_id" id="selectedPatientId" required>
                        
                        <div id="selectedPatientIndicator" class="hidden absolute inset-y-0 left-0 right-10 flex items-center pl-3 bg-slate-100 rounded pointer-events-none">
                            <div class="flex items-center gap-2">
                                <div class="w-6 h-6 rounded-full bg-slate-800 flex items-center justify-center text-white text-[10px] font-bold" id="indicatorInitial"></div>
                                <span class="text-sm font-medium text-slate-800" id="indicatorName"></span>
                                <span class="text-xs text-slate-500" id="indicatorMRN"></span>
                            </div>
                        </div>
                        
                        <button type="button" id="clearPatientBtn" class="hidden absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600">
                            <i class="hgi-stroke hgi-cancel-01 text-xl"></i>
                        </button>
                    </div>
                    
                    <div id="patientSearchResults" class="hidden absolute z-[60] left-0 right-0 mt-1 bg-white border border-gray-200 rounded-md shadow-xl max-h-64 overflow-y-auto overflow-x-hidden animate-fade">
                        <div class="p-4 text-center text-gray-500 text-sm" id="searchPlaceholder">Start typing to search...</div>
                        <div class="p-4 text-center text-gray-500 text-sm hidden" id="searchLoading">Searching...</div>
                        <div class="p-4 text-center text-gray-500 text-sm hidden" id="noResults">No patients found</div>
                        <div class="divide-y divide-gray-50" id="resultsList"></div>
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Visit Type</label>
                    <x-ui.select name="type" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-slate-300" required>
                        <option value="outpatient">Outpatient</option>
                        <option value="emergency">Emergency</option>
                        <option value="followup">Follow-up</option>
                    </x-ui.select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Assign Doctor <span class="font-normal text-gray-400">(optional)</span></label>
                    <x-ui.select name="doctor_id" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-slate-300">
                        <option value="">Keep in reception queue</option>
                        @foreach ($doctors as $doctor)
                            <option value="{{ $doctor->id }}">{{ $doctor->name }}</option>
                        @endforeach
                    </x-ui.select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Chief Complaint</label>
                    <x-ui.textarea name="chief_complaint" placeholder="Chief complaint" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-slate-300" rows="3"></x-ui.textarea>
                </div>
                <div class="bg-gray-50 border border-gray-200 rounded p-3">
                    <div class="flex items-center justify-between mb-2">
                        <div>
                            <p class="text-xs font-medium text-gray-700">Consultation Fee</p>
                            <p class="text-xs text-gray-500">Adjust amount if needed, then collect from patient</p>
                        </div>
                    </div>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-sm font-medium text-slate-600">TSh</span>
                        <x-ui.input type="number" name="consultation_fee" value="{{ \App\Models\Setting::get('consultation_fee', 10000) }}" min="0" step="1000" class="w-full pl-12 pr-4 py-2.5 border border-gray-200 rounded text-sm font-semibold text-gray-900 bg-white focus:outline-none focus:ring-2 focus:ring-slate-300 transition-all" placeholder="10000" />
                    </div>
                </div>
                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" onclick="closeOpenVisitModal()" class="px-4 py-2 text-sm text-gray-600 hover:bg-gray-100 rounded-lg">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-slate-800 text-white text-sm font-medium rounded hover:bg-slate-700">Open Visit</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div id="paymentModal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" onclick="closePaymentModal()"></div>
    <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-full max-w-md">
        <div class="bg-white rounded-md shadow-2xl m-4 animate-fade overflow-hidden">
            <div class="px-6 py-4 bg-slate-800 rounded-t-md flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded bg-slate-700/60 flex items-center justify-center">
                        <i class="hgi-stroke hgi-message-multiple-02 text-xl text-slate-200"></i>
                    </div>
                    <div>
                        <h3 class="text-white font-bold text-base">Collect Payment</h3>
                        <p class="text-slate-400 text-xs" id="paymentModalVisit">Visit #</p>
                    </div>
                </div>
                <button onclick="closePaymentModal()" class="text-white/70 hover:text-white">
                    <i class="hgi-stroke hgi-cancel-01 text-xl"></i>
                </button>
            </div>
            <div class="p-6 space-y-4">
                <div class="bg-gray-50 rounded p-3 flex items-center justify-between">
                    <div>
                        <p class="text-xs text-gray-500">Patient</p>
                        <p class="text-sm font-medium text-gray-900" id="paymentModalPatient">-</p>
                    </div>
                    <div class="text-right">
                        <p class="text-xs text-gray-500">Balance Due</p>
                        <p class="text-lg font-bold text-gray-900" id="paymentModalBalance">0 TSh</p>
                    </div>
                </div>
                <form id="paymentModalForm" method="POST" action="" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1.5">Amount (TSh) <span class="text-red-500">*</span></label>
                        <x-ui.input type="number" name="amount" id="paymentModalAmount" step="0.01" min="0.01" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-slate-300" required />
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1.5">Payment Method <span class="text-red-500">*</span></label>
                        <div class="grid grid-cols-4 gap-2">
                            <button type="button" data-method="cash" class="method-btn flex flex-col items-center gap-1 p-2.5 border-2 border-slate-700 bg-slate-50 rounded text-xs font-medium text-slate-800 transition-all">
                                <i class="hgi-stroke hgi-money-03 text-xl"></i>
                                Cash
                            </button>
                            <button type="button" data-method="card" class="method-btn flex flex-col items-center gap-1 p-2.5 border-2 border-gray-200 rounded text-xs font-medium text-gray-600 hover:border-slate-400 hover:text-slate-700 transition-all">
                                <i class="hgi-stroke hgi-credit-card text-xl"></i>
                                Card
                            </button>
                            <button type="button" data-method="mobile_money" class="method-btn flex flex-col items-center gap-1 p-2.5 border-2 border-gray-200 rounded text-xs font-medium text-gray-600 hover:border-slate-400 hover:text-slate-700 transition-all">
                                <i class="hgi-stroke hgi-smart-phone-01 text-xl"></i>
                                Mobile
                            </button>
                            <button type="button" data-method="insurance" class="method-btn flex flex-col items-center gap-1 p-2.5 border-2 border-gray-200 rounded text-xs font-medium text-gray-600 hover:border-slate-400 hover:text-slate-700 transition-all">
                                <i class="hgi-stroke hgi-checkmark-badge-01 text-xl"></i>
                                Insurance
                            </button>
                        </div>
                        <input type="hidden" name="method" id="paymentModalMethod" value="cash">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1.5">Reference <span class="font-normal text-gray-400">(optional)</span></label>
                        <x-ui.input type="text" name="reference" id="paymentModalReference" placeholder="Transaction ID, cheque no, etc." class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-slate-300" />
                    </div>
                    <div class="flex justify-end gap-2 pt-2">
                        <button type="button" onclick="closePaymentModal()" class="px-4 py-2.5 text-sm text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">Cancel</button>
                        <button type="submit" class="btn-submit inline-flex items-center gap-2 px-5 py-2.5 bg-slate-800 text-white text-sm font-semibold rounded hover:bg-slate-700 transition-colors">
                            <i class="hgi-stroke hgi-tick-02 text-base"></i>
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
@endpush

@push('scripts')
<script>
    const visitTrendCtx = document.getElementById('visitTrendChart').getContext('2d');
    const gradient = visitTrendCtx.createLinearGradient(0, 0, 0, 250);
    gradient.addColorStop(0, 'rgba(5, 150, 105, 0.08)');
    gradient.addColorStop(1, 'rgba(5, 150, 105, 0)');

    const visitTrendData = {
        labels: @json($visitLabels),
        datasets: [{
            label: 'Visits',
            data: @json($visitTrend),
            borderColor: '#059669',
            backgroundColor: gradient,
            borderWidth: 2,
            tension: 0.35,
            fill: true,
            pointBackgroundColor: '#fff',
            pointBorderColor: '#059669',
            pointHoverBackgroundColor: '#fff',
            pointHoverBorderColor: '#059669',
            pointRadius: 2,
            pointHoverRadius: 4,
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
        'registered': '#94a3b8',
        'waiting_for_doctor': '#f59e0b',
        'with_doctor': '#334155',
        'waiting_for_lab': '#64748b',
        'waiting_for_pharmacy': '#475569',
        'waiting_for_payment': '#d97706',
        'completed': '#059669',
        'cancelled': '#dc2626'
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
    function openOpenVisitModal() { 
        document.getElementById('openVisitModal').classList.remove('hidden'); 
        setTimeout(() => document.getElementById('patientSearchInput').focus(), 100);
    }
    function closeOpenVisitModal() { 
        document.getElementById('openVisitModal').classList.add('hidden'); 
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
        if (!selectedPatientId) return;
        selectedPatientId.value = '';
        searchInput.value = '';
        searchInput.classList.remove('hidden');
        selectedPatientIndicator.classList.add('hidden');
        clearPatientBtn.classList.add('hidden');
        resultsDropdown.classList.add('hidden');
    }

    clearPatientBtn?.addEventListener('click', clearPatientSelection);

    searchInput?.addEventListener('input', function(e) {
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
                        row.className = 'w-full text-left p-3 hover:bg-gray-50 transition-colors flex items-center justify-between group border-b border-gray-50 last:border-0';
                        row.innerHTML = `
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center text-slate-600 font-bold text-xs group-hover:bg-slate-800 group-hover:text-white transition-colors">
                                    ${patient.text.charAt(0)}
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-gray-900">${patient.text}</p>
                                    <p class="text-[10px] text-gray-500">${patient.phone || 'No phone'} • ${patient.gender} • ${patient.age} yrs</p>
                                </div>
                            </div>
                            <i class="hgi-stroke hgi-tick-02 text-base text-slate-400 opacity-0 group-hover:opacity-100 transition-opacity"></i>
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
        if (!document.getElementById('patientSearchContainer')?.contains(e.target)) {
            resultsDropdown?.classList.add('hidden');
        }
    });

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
            b.classList.remove('border-slate-700', 'bg-slate-50', 'text-slate-800');
            b.classList.add('border-gray-200', 'text-gray-600');
        });
        document.querySelector('.method-btn[data-method="cash"]').classList.add('border-slate-700', 'bg-slate-50', 'text-slate-800');
        document.querySelector('.method-btn[data-method="cash"]').classList.remove('border-gray-200', 'text-gray-600');
        document.getElementById('paymentModal').classList.remove('hidden');
    }
    function closePaymentModal() { document.getElementById('paymentModal').classList.add('hidden'); }

    // Method button selection
    document.querySelectorAll('.method-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            document.querySelectorAll('.method-btn').forEach(b => {
                b.classList.remove('border-slate-700', 'bg-slate-50', 'text-slate-800');
                b.classList.add('border-gray-200', 'text-gray-600');
            });
            this.classList.add('border-slate-700', 'bg-slate-50', 'text-slate-800');
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
                btn.innerHTML = '<i class="hgi-stroke hgi-refresh text-sm animate-spin inline"></i> Saving...';
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
                b.classList.remove('active', 'bg-slate-800', 'text-white');
                b.classList.add('text-gray-600', 'hover:bg-gray-100');
            });
            btn.classList.add('active', 'bg-slate-800', 'text-white');
            btn.classList.remove('text-gray-600', 'hover:bg-gray-100');
            document.querySelectorAll('.queue-panel').forEach(p => p.classList.add('hidden'));
            document.getElementById('tab-' + btn.dataset.tab).classList.remove('hidden');
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
