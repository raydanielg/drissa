@extends('layouts.dashboard')

@section('title', 'Reception - ' . config('app.name', 'Laravel'))
@section('page_title', 'Reception Dashboard')

@section('content')
<div class="space-y-6" id="receptionDashboard">
    @if (session('status'))
        <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm animate-fade">{{ session('status') }}</div>
    @endif

    {{-- KPI Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
        <div class="bg-white rounded-xl border border-gray-100 p-5 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Today Visits</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1 kpi-counter" data-target="{{ $kpis['today_visits'] }}">0</p>
                </div>
                <div class="w-10 h-10 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6 4h6"/></svg>
                </div>
            </div>
            <div class="mt-3 h-1 bg-gray-100 rounded-full overflow-hidden">
                <div class="h-full bg-emerald-500 kpi-bar" style="width: 0%" data-width="{{ min(100, $kpis['today_visits'] * 10) }}%"></div>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-100 p-5 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Waiting Payment</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1 kpi-counter" data-target="{{ $kpis['waiting_payment'] }}">0</p>
                </div>
                <div class="w-10 h-10 rounded-full bg-amber-100 flex items-center justify-center text-amber-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
            <div class="mt-3 h-1 bg-gray-100 rounded-full overflow-hidden">
                <div class="h-full bg-amber-500 kpi-bar" style="width: 0%" data-width="{{ min(100, $kpis['waiting_payment'] * 20) }}%"></div>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-100 p-5 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Today Revenue</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1"><span class="kpi-counter" data-target="{{ round($kpis['today_revenue']) }}">0</span></p>
                </div>
                <div class="w-10 h-10 rounded-full bg-gold-100 flex items-center justify-center text-gold-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                </div>
            </div>
            <div class="mt-3 h-1 bg-gray-100 rounded-full overflow-hidden">
                <div class="h-full bg-gold-500 kpi-bar" style="width: 0%" data-width="{{ min(100, $kpis['today_revenue'] / 1000) }}%"></div>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-100 p-5 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">New Patients</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1 kpi-counter" data-target="{{ $kpis['today_patients'] }}">0</p>
                </div>
                <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                </div>
            </div>
            <div class="mt-3 h-1 bg-gray-100 rounded-full overflow-hidden">
                <div class="h-full bg-blue-500 kpi-bar" style="width: 0%" data-width="{{ min(100, $kpis['today_patients'] * 20) }}%"></div>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-100 p-5 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Avg Wait</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1"><span class="kpi-counter" data-target="{{ $kpis['avg_wait_minutes'] }}">0</span><span class="text-sm font-normal text-gray-500">min</span></p>
                </div>
                <div class="w-10 h-10 rounded-full bg-purple-100 flex items-center justify-center text-purple-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
            <div class="mt-3 h-1 bg-gray-100 rounded-full overflow-hidden">
                <div class="h-full bg-purple-500 kpi-bar" style="width: 0%" data-width="{{ min(100, $kpis['avg_wait_minutes'] * 4) }}%"></div>
            </div>
        </div>
    </div>

    {{-- Charts & Quick Actions --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Visit Trend --}}
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

        {{-- Status Distribution --}}
        <div class="bg-white rounded-xl border border-gray-100 p-6 shadow-sm">
            <h2 class="text-sm font-semibold text-gray-900 mb-4">Visit Status</h2>
            <div class="space-y-3">
                @foreach ($statusCounts as $status => $count)
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-600 capitalize">{{ str_replace('_', ' ', $status) }}</span>
                        <span class="font-semibold text-gray-900 status-count" data-status="{{ $status }}">{{ $count }}</span>
                    </div>
                    <div class="h-2 bg-gray-100 rounded-full overflow-hidden">
                        @php
                            $max = max($statusCounts) ?: 1;
                            $pct = round(($count / $max) * 100);
                            $colors = ['registered' => 'bg-gray-400', 'with_doctor' => 'bg-emerald-500', 'waiting_for_lab' => 'bg-blue-500', 'waiting_for_payment' => 'bg-amber-500', 'completed' => 'bg-gold-500'];
                        @endphp
                        <div class="h-full {{ $colors[$status] }} transition-all duration-700" style="width: 0%" data-width="{{ $pct }}%"></div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Quick Actions --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <button onclick="openRegisterPatientModal()" class="group bg-gradient-to-br from-emerald-600 to-emerald-700 rounded-xl p-5 text-left text-white shadow-sm hover:shadow-lg hover:-translate-y-0.5 transition-all">
            <div class="w-10 h-10 rounded-lg bg-white/20 flex items-center justify-center mb-3">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
            </div>
            <p class="font-semibold text-sm">Register Patient</p>
            <p class="text-xs text-emerald-100 mt-0.5">Add new patient record</p>
        </button>

        <button onclick="openOpenVisitModal()" class="group bg-gradient-to-br from-blue-600 to-blue-700 rounded-xl p-5 text-left text-white shadow-sm hover:shadow-lg hover:-translate-y-0.5 transition-all">
            <div class="w-10 h-10 rounded-lg bg-white/20 flex items-center justify-center mb-3">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6 4h6"/></svg>
            </div>
            <p class="font-semibold text-sm">Open Visit</p>
            <p class="text-xs text-blue-100 mt-0.5">Create a new visit</p>
        </button>

        <a href="{{ route('patients.index') }}" class="group bg-gradient-to-br from-purple-600 to-purple-700 rounded-xl p-5 text-left text-white shadow-sm hover:shadow-lg hover:-translate-y-0.5 transition-all">
            <div class="w-10 h-10 rounded-lg bg-white/20 flex items-center justify-center mb-3">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            </div>
            <p class="font-semibold text-sm">Patients</p>
            <p class="text-xs text-purple-100 mt-0.5">View patient registry</p>
        </a>

        <a href="{{ route('appointments.index') }}" class="group bg-gradient-to-br from-gold-500 to-gold-600 rounded-xl p-5 text-left text-white shadow-sm hover:shadow-lg hover:-translate-y-0.5 transition-all">
            <div class="w-10 h-10 rounded-lg bg-white/20 flex items-center justify-center mb-3">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            </div>
            <p class="font-semibold text-sm">Appointments</p>
            <p class="text-xs text-gold-100 mt-0.5">Manage bookings</p>
        </a>
    </div>

    {{-- Today's Visits & Waiting for Payment --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <h2 class="text-sm font-semibold text-gray-900">Today's Visits</h2>
                <span class="text-xs bg-emerald-100 text-emerald-700 px-2 py-1 rounded-full" id="todayVisitsCount">{{ $todayVisits->count() }}</span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                        <tr>
                            <th class="px-6 py-3">Visit #</th>
                            <th class="px-6 py-3">Patient</th>
                            <th class="px-6 py-3">Status</th>
                            <th class="px-6 py-3">Action</th>
                        </tr>
                    </thead>
                    <tbody id="todayVisitsBody">
                        @forelse ($todayVisits as $visit)
                            <tr class="border-t border-gray-100 hover:bg-gray-50/50 transition-colors">
                                <td class="px-6 py-3 font-medium">{{ $visit->visit_number }}</td>
                                <td class="px-6 py-3">{{ $visit->patient->fullName() }}</td>
                                <td class="px-6 py-3">
                                    @php
                                        $badgeColors = [
                                            'registered' => 'bg-gray-100 text-gray-700',
                                            'with_doctor' => 'bg-emerald-100 text-emerald-700',
                                            'waiting_for_lab' => 'bg-blue-100 text-blue-700',
                                            'waiting_for_payment' => 'bg-amber-100 text-amber-700',
                                            'completed' => 'bg-gold-100 text-gold-700',
                                        ];
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $badgeColors[$visit->status] ?? 'bg-gray-100 text-gray-700' }}">
                                        {{ str_replace('_', ' ', $visit->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-3">
                                    @if ($visit->status === 'registered')
                                        <form method="POST" action="{{ route('reception.visits.assign', $visit) }}" class="ajax-assign-form flex gap-2">
                                            @csrf
                                            <select name="doctor_id" class="border rounded-lg text-xs px-2 py-1" required>
                                                <option value="">Doctor</option>
                                                @foreach ($doctors as $doctor)
                                                    <option value="{{ $doctor->id }}">{{ $doctor->name }}</option>
                                                @endforeach
                                            </select>
                                            <button type="submit" class="bg-emerald-600 text-white text-xs font-medium px-3 py-1 rounded-lg hover:bg-emerald-700">Assign</button>
                                        </form>
                                    @else
                                        <span class="text-gray-400 text-xs">-</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr id="noTodayVisitsRow"><td colspan="4" class="px-6 py-6 text-center text-gray-400">No visits today</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <h2 class="text-sm font-semibold text-gray-900">Waiting for Payment</h2>
                <span class="text-xs bg-amber-100 text-amber-700 px-2 py-1 rounded-full" id="waitingPaymentCount">{{ $waitingForPayment->count() }}</span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                        <tr>
                            <th class="px-6 py-3">Visit #</th>
                            <th class="px-6 py-3">Patient</th>
                            <th class="px-6 py-3">Total</th>
                            <th class="px-6 py-3">Action</th>
                        </tr>
                    </thead>
                    <tbody id="waitingPaymentBody">
                        @forelse ($waitingForPayment as $visit)
                            <tr class="border-t border-gray-100 hover:bg-gray-50/50 transition-colors">
                                <td class="px-6 py-3 font-medium">{{ $visit->visit_number }}</td>
                                <td class="px-6 py-3">{{ $visit->patient->fullName() }}</td>
                                <td class="px-6 py-3 font-medium text-emerald-700">{{ number_format($visit->invoice?->total ?? 0) }} TSh</td>
                                <td class="px-6 py-3">
                                    <form method="POST" action="{{ route('reception.visits.pay', $visit) }}" class="ajax-pay-form flex gap-2">
                                        @csrf
                                        <input type="number" name="amount" step="0.01" value="{{ $visit->invoice?->total ?? 0 }}" class="border rounded-lg text-xs px-2 py-1 w-24" required>
                                        <select name="method" class="border rounded-lg text-xs px-2 py-1" required>
                                            <option value="cash">Cash</option>
                                            <option value="card">Card</option>
                                            <option value="mobile_money">Mobile Money</option>
                                            <option value="insurance">Insurance</option>
                                        </select>
                                        <button type="submit" class="bg-emerald-600 text-white text-xs font-medium px-3 py-1 rounded-lg hover:bg-emerald-700">Pay</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr id="noWaitingPaymentRow"><td colspan="4" class="px-6 py-6 text-center text-gray-400">No pending payments</td></tr>
                        @endforelse
                    </tbody>
                </table>
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
                <select name="patient_id" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" required>
                    <option value="">Select patient</option>
                    @foreach (\App\Models\Patient::orderBy('first_name')->get() as $patient)
                        <option value="{{ $patient->id }}">{{ $patient->fullName() }} ({{ $patient->mrn }})</option>
                    @endforeach
                </select>
                <select name="type" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" required>
                    <option value="outpatient">Outpatient</option>
                    <option value="emergency">Emergency</option>
                    <option value="followup">Follow-up</option>
                </select>
                <textarea name="chief_complaint" placeholder="Chief complaint" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" rows="3"></textarea>
                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" onclick="closeOpenVisitModal()" class="px-4 py-2 text-sm text-gray-600 hover:bg-gray-100 rounded-lg">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700">Open Visit</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const visitTrendCtx = document.getElementById('visitTrendChart').getContext('2d');
    const gradient = visitTrendCtx.createLinearGradient(0, 0, 0, 250);
    gradient.addColorStop(0, 'rgba(2, 73, 56, 0.25)');
    gradient.addColorStop(1, 'rgba(2, 73, 56, 0)');

    const visitTrendData = {
        labels: @json($visitLabels),
        datasets: [{
            label: 'Visits',
            data: @json($visitTrend),
            borderColor: '#024938',
            backgroundColor: gradient,
            borderWidth: 2,
            tension: 0.4,
            fill: true,
            pointBackgroundColor: '#f9ac00',
            pointBorderColor: '#fff',
            pointHoverBackgroundColor: '#fff',
            pointHoverBorderColor: '#f9ac00',
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
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, grid: { color: '#f3f4f6' }, ticks: { precision: 0 } },
                x: { grid: { display: false } }
            },
            interaction: { intersect: false, mode: 'index' },
        }
    });

    // KPI counter animation
    function animateKPIs() {
        document.querySelectorAll('.kpi-counter').forEach(el => {
            const target = parseFloat(el.dataset.target) || 0;
            const duration = 1200;
            const start = 0;
            const startTime = performance.now();
            function update(currentTime) {
                const elapsed = currentTime - startTime;
                const progress = Math.min(elapsed / duration, 1);
                const ease = 1 - Math.pow(1 - progress, 3);
                const value = start + (target - start) * ease;
                el.textContent = Number.isInteger(target) ? Math.round(value).toLocaleString() : value.toLocaleString(undefined, {maximumFractionDigits: 0});
                if (progress < 1) requestAnimationFrame(update);
            }
            requestAnimationFrame(update);
        });

        document.querySelectorAll('.kpi-bar').forEach(bar => {
            setTimeout(() => bar.style.width = bar.dataset.width, 300);
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

    // AJAX form submissions
    function submitFormAjax(form, modalCloseFn) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(form);
            fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
            })
            .then(r => r.json().catch(() => ({})))
            .then(data => {
                Swal.fire({ icon: 'success', title: 'Success', text: data.message || 'Saved successfully', timer: 2000, showConfirmButton: false });
                form.reset();
                if (modalCloseFn) modalCloseFn();
                refreshStats();
            })
            .catch(err => {
                Swal.fire({ icon: 'error', title: 'Error', text: 'Something went wrong. Please try again.' });
            });
        });
    }

    submitFormAjax(document.getElementById('registerPatientForm'), closeRegisterPatientModal);
    submitFormAjax(document.getElementById('openVisitForm'), closeOpenVisitModal);

    document.querySelectorAll('.ajax-assign-form').forEach(form => submitFormAjax(form, null));
    document.querySelectorAll('.ajax-pay-form').forEach(form => submitFormAjax(form, null));

    // Real-time stats polling
    async function refreshStats() {
        try {
            const res = await fetch('{{ route("reception.stats") }}', { headers: { 'Accept': 'application/json' } });
            const data = await res.json();

            document.querySelectorAll('.kpi-counter').forEach(el => {
                if (el.parentElement.querySelector('p:first-child').textContent.includes('Today Visits')) el.dataset.target = data.today_visits;
                if (el.parentElement.querySelector('p:first-child').textContent.includes('Waiting Payment')) el.dataset.target = data.waiting_payment;
                if (el.parentElement.querySelector('p:first-child').textContent.includes('Today Revenue')) el.dataset.target = Math.round(data.today_revenue);
                if (el.parentElement.querySelector('p:first-child').textContent.includes('New Patients')) el.dataset.target = data.today_patients;
            });
            animateKPIs();

            visitTrendChart.data.datasets[0].data = data.visit_trend;
            visitTrendChart.data.labels = data.visit_labels;
            visitTrendChart.update();

            document.querySelectorAll('.status-count').forEach(el => {
                el.textContent = data.status_counts[el.dataset.status] ?? 0;
            });

            document.getElementById('todayVisitsCount').textContent = data.today_visits;
            document.getElementById('waitingPaymentCount').textContent = data.waiting_payment;
        } catch (e) { console.error('Stats refresh failed', e); }
    }

    setInterval(refreshStats, 30000);
</script>
@endpush
