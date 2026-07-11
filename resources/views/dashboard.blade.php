@extends('layouts.dashboard')

@section('title', 'Dashboard - ' . config('app.name', 'Laravel'))
@section('page_title', 'Dashboard Overview')

@section('content')
<style>
    .card-sm { transition: all 0.2s cubic-bezier(0.4,0,0.2,1); }
    .card-sm:hover { transform: translateY(-2px); box-shadow: 0 8px 30px -8px rgba(0,0,0,0.12); }
    .circular-chart { display: block; margin: 0 auto; max-width: 100%; max-height: 140px; }
    .circle-bg { fill: none; stroke: #e5e7eb; stroke-width: 3; }
    .circle { fill: none; stroke-width: 3; stroke-linecap: round; animation: progress 1s ease-out forwards; }
    @keyframes progress { 0% { stroke-dasharray: 0 100; } }
    .percentage { fill: #111827; font-weight: 700; font-size: 0.5rem; }
    .label { fill: #6b7280; font-size: 0.22rem; }
</style>

@php
    $user = auth()->user();
    $totalUsers = \App\Models\User::count();
@endphp

{{-- Welcome Header --}}
<div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
    <div>
        <h1 class="text-xl sm:text-2xl font-bold text-gray-900 tracking-tight">Hello {{ $user->name }} </h1>
        <p class="text-sm text-gray-500 mt-0.5">Welcome to {{ config('app.name', 'Laravel') }} dashboard.</p>
    </div>
    <div class="flex items-center gap-2">
        <button type="button" class="px-3 py-1.5 text-xs font-medium bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition-colors inline-flex items-center gap-1.5 shadow-sm">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            New Action
        </button>
    </div>
</div>

{{-- KPI Stats Cards --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-6">
    @foreach([
        ['label'=>'Total Users','value'=>number_format($totalUsers),'change'=>'Active accounts','icon'=>'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z','from'=>'emerald-600','to'=>'emerald-700','border'=>'emerald-500','text'=>'emerald-100','sub'=>'emerald-200'],
        ['label'=>'Sessions','value'=>'24','change'=>'This week','icon'=>'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z','from'=>'gold-400','to'=>'gold-500','border'=>'gold-300','text'=>'amber-50','sub'=>'amber-100'],
        ['label'=>'Tasks Done','value'=>'12','change'=>'Monthly target','icon'=>'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2','from'=>'sky-500','to'=>'sky-600','border'=>'sky-400','text'=>'sky-100','sub'=>'sky-200'],
        ['label'=>'Revenue','value'=>'TSh 0','change'=>'Today: TSh 0','icon'=>'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z','from'=>'violet-500','to'=>'violet-600','border'=>'violet-400','text'=>'violet-100','sub'=>'violet-200']
    ] as $card)
    <div class="card-sm bg-gradient-to-br from-{{ $card['from'] }} to-{{ $card['to'] }} rounded-xl border border-{{ $card['border'] }} p-4 text-white relative overflow-hidden">
        <div class="absolute top-0 right-0 w-16 h-16 bg-white/10 rounded-full -mr-8 -mt-8"></div>
        <div class="relative z-10">
            <div class="flex items-start justify-between mb-2">
                <span class="text-[10px] font-medium {{ $card['text'] }}">{{ $card['label'] }}</span>
                <svg class="w-4 h-4 {{ $card['sub'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $card['icon'] }}"/></svg>
            </div>
            <p class="text-xl font-bold tracking-tight text-white">{{ $card['value'] }}</p>
            <p class="text-[10px] {{ $card['sub'] }} font-medium mt-1">{{ $card['change'] }}</p>
        </div>
    </div>
    @endforeach
</div>

{{-- Charts & Progress Row --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
    {{-- Activity Bar Chart --}}
    <div class="lg:col-span-2 bg-white rounded-xl border border-gray-100 p-5 shadow-sm">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="text-sm font-semibold text-gray-900">Weekly Activity</h3>
                <p class="text-xs text-gray-400">Last 7 days</p>
            </div>
        </div>
        <div class="flex items-end gap-[6px] h-52">
            @foreach(['Mon','Tue','Wed','Thu','Fri','Sat','Sun'] as $i => $day)
            @php $pct = [35, 55, 40, 70, 45, 60, 30][$i]; @endphp
            <div class="flex-1 flex flex-col items-center gap-1.5 group cursor-pointer" title="{{ $day }}">
                <div class="w-full bg-gray-50 rounded-t-md relative h-44 overflow-hidden">
                    <div class="absolute bottom-0 left-0 right-0 rounded-t-md transition-all duration-300 {{ $i === 6 ? 'bg-emerald-500' : 'bg-emerald-300 group-hover:bg-emerald-400' }}" style="height: {{ $pct }}%"></div>
                </div>
                <span class="text-[10px] text-gray-400 font-medium">{{ $day }}</span>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Circle Progress Cards --}}
    <div class="bg-white rounded-xl border border-gray-100 p-5 shadow-sm space-y-6">
        <h3 class="text-sm font-semibold text-gray-900">Performance</h3>

        @php
            $circles = [
                ['label' => 'Success Rate', 'value' => 96, 'color' => '#10b981'],
                ['label' => 'Occupancy', 'value' => 75, 'color' => '#f59e0b'],
                ['label' => 'Target', 'value' => 60, 'color' => '#8b5cf6'],
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

{{-- Recent Activity --}}
<div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
    <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
        <h3 class="text-sm font-semibold text-gray-900">Recent Activity</h3>
    </div>
    <div class="p-5 text-center text-sm text-gray-400">
        No recent activity to display.
    </div>
</div>
@endsection
