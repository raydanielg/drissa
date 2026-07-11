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
@endphp

{{-- Welcome Header --}}
<div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
    <div>
        <h1 class="text-xl sm:text-2xl font-bold text-gray-900 tracking-tight">Hello {{ $user->name }} </h1>
        <p class="text-sm text-gray-500 mt-0.5">{{ $roleLabels[$user->roles->first()?->name] ?? 'User' }} — Welcome to {{ config('app.name', 'Laravel') }}.</p>
    </div>
    <div class="flex items-center gap-2">
        @if($user->isReception())
            <a href="{{ route('reception.dashboard') }}" class="px-3 py-1.5 text-xs font-medium bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition-colors inline-flex items-center gap-1.5 shadow-sm">Reception</a>
        @endif
        @if($user->isDoctor())
            <a href="{{ route('doctor.queue') }}" class="px-3 py-1.5 text-xs font-medium bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition-colors inline-flex items-center gap-1.5 shadow-sm">My Queue</a>
        @endif
    </div>
</div>

{{-- KPI Stats Cards --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-6">
    @foreach([
        ['label'=>'Total Patients','value'=>number_format($stats['total_patients']),'link'=>null,'icon'=>'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z','from'=>'emerald-600','to'=>'emerald-700','border'=>'emerald-500','text'=>'emerald-100','sub'=>'emerald-200'],
        ['label'=>'Today\'s Visits','value'=>$stats['visits_today'],'link'=>null,'icon'=>'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z','from'=>'gold-400','to'=>'gold-500','border'=>'gold-300','text'=>'amber-50','sub'=>'amber-100'],
        ['label'=>'Waiting Lab','value'=>$stats['waiting_lab'],'link'=>route('lab.queue'),'icon'=>'M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z','from'=>'sky-500','to'=>'sky-600','border'=>'sky-400','text'=>'sky-100','sub'=>'sky-200'],
        ['label'=>'Pending Payments','value'=>$stats['pending_payments'],'link'=>route('reception.dashboard'),'icon'=>'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z','from'=>'violet-500','to'=>'violet-600','border'=>'violet-400','text'=>'violet-100','sub'=>'violet-200']
    ] as $card)
    <a href="{{ $card['link'] ?? '#' }}" class="card-sm block bg-gradient-to-br from-{{ $card['from'] }} to-{{ $card['to'] }} rounded-xl border border-{{ $card['border'] }} p-4 text-white relative overflow-hidden">
        <div class="absolute top-0 right-0 w-16 h-16 bg-white/10 rounded-full -mr-8 -mt-8"></div>
        <div class="relative z-10">
            <div class="flex items-start justify-between mb-2">
                <span class="text-[10px] font-medium {{ $card['text'] }}">{{ $card['label'] }}</span>
                <svg class="w-4 h-4 {{ $card['sub'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $card['icon'] }}"/></svg>
            </div>
            <p class="text-2xl font-bold tracking-tight text-white">{{ $card['value'] }}</p>
        </div>
    </a>
    @endforeach
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
@endsection
