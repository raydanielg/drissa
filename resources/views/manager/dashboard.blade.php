@extends('layouts.dashboard')

@section('title', 'Manager Dashboard - ' . config('app.name', 'Laravel'))
@section('page_title', 'Clinic Manager Dashboard')

@section('content')
<div class="space-y-6">

    {{-- Welcome + quick actions --}}
    <div class="bg-gradient-to-r from-emerald-900 to-emerald-700 rounded-2xl p-6 text-white shadow-lg">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div>
                <p class="text-[10px] uppercase tracking-widest text-gold-400 font-bold">Clinic Manager Workspace</p>
                <h2 class="text-2xl font-extrabold mt-1">Karibu, {{ auth()->user()->name }}</h2>
                <p class="text-sm text-emerald-200/80 mt-1">{{ now()->format('l, d F Y') }} · Manage daily & weekly reports for the whole clinic.</p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('manager.reports.create', ['type' => 'daily']) }}" class="inline-flex items-center gap-2 bg-gold-500 hover:bg-gold-400 text-emerald-900 text-sm font-bold px-4 py-2.5 rounded-lg shadow transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Daily Report
                </a>
                <a href="{{ route('manager.reports.create', ['type' => 'weekly']) }}" class="inline-flex items-center gap-2 bg-white/10 hover:bg-white/20 text-white text-sm font-semibold px-4 py-2.5 rounded-lg border border-white/20 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    Weekly Report
                </a>
            </div>
        </div>
    </div>

    {{-- Report status --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="bg-white rounded-xl shadow-sm border {{ $todayReport ? 'border-emerald-200' : 'border-amber-200' }} p-5 flex items-center gap-4">
            <div class="w-11 h-11 rounded-xl {{ $todayReport ? 'bg-emerald-100' : 'bg-amber-100' }} flex items-center justify-center flex-shrink-0">
                @if($todayReport)
                    <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                @else
                    <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                @endif
            </div>
            <div class="flex-1">
                <p class="text-sm font-semibold text-gray-900">Today's Daily Report</p>
                <p class="text-xs text-gray-500">{{ $todayReport ? 'Submitted at ' . $todayReport->created_at->format('H:i') : 'Haijawasilishwa bado — not submitted yet' }}</p>
            </div>
            @if($todayReport)
                <a href="{{ route('manager.reports.show', $todayReport) }}" class="text-xs font-medium text-emerald-600 hover:text-emerald-700">View</a>
            @else
                <a href="{{ route('manager.reports.create', ['type' => 'daily']) }}" class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-medium rounded-lg transition-all">Fill Now</a>
            @endif
        </div>
        <div class="bg-white rounded-xl shadow-sm border {{ $weekReport ? 'border-emerald-200' : 'border-amber-200' }} p-5 flex items-center gap-4">
            <div class="w-11 h-11 rounded-xl {{ $weekReport ? 'bg-emerald-100' : 'bg-amber-100' }} flex items-center justify-center flex-shrink-0">
                @if($weekReport)
                    <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                @else
                    <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                @endif
            </div>
            <div class="flex-1">
                <p class="text-sm font-semibold text-gray-900">This Week's Weekly Report</p>
                <p class="text-xs text-gray-500">{{ $weekReport ? 'Submitted' : 'Week ends ' . now()->endOfWeek()->format('d M') . ' — not submitted yet' }}</p>
            </div>
            @if($weekReport)
                <a href="{{ route('manager.reports.show', $weekReport) }}" class="text-xs font-medium text-emerald-600 hover:text-emerald-700">View</a>
            @else
                <a href="{{ route('manager.reports.create', ['type' => 'weekly']) }}" class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-medium rounded-lg transition-all">Fill Now</a>
            @endif
        </div>
    </div>

    {{-- Clinic KPIs --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-medium text-gray-500 uppercase tracking-wider">Revenue Today</span>
                <div class="w-8 h-8 rounded-lg bg-emerald-100 flex items-center justify-center">
                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
            <p class="text-xl font-bold text-gray-900">{{ number_format($stats['revenue_today']) }} <span class="text-xs font-medium text-gray-400">TSh</span></p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-medium text-gray-500 uppercase tracking-wider">Revenue This Week</span>
                <div class="w-8 h-8 rounded-lg bg-gold-100 flex items-center justify-center">
                    <svg class="w-4 h-4 text-gold-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                </div>
            </div>
            <p class="text-xl font-bold text-gray-900">{{ number_format($stats['revenue_this_week']) }} <span class="text-xs font-medium text-gray-400">TSh</span></p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-medium text-gray-500 uppercase tracking-wider">Visits Today</span>
                <div class="w-8 h-8 rounded-lg bg-sky-100 flex items-center justify-center">
                    <svg class="w-4 h-4 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </div>
            </div>
            <p class="text-xl font-bold text-gray-900">{{ $stats['visits_today'] }}</p>
            <p class="text-[10px] text-gray-400 mt-1">{{ $stats['completed_today'] }} completed · {{ $stats['active_visits'] }} active</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-medium text-gray-500 uppercase tracking-wider">New Patients (Week)</span>
                <div class="w-8 h-8 rounded-lg bg-purple-100 flex items-center justify-center">
                    <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                </div>
            </div>
            <p class="text-xl font-bold text-gray-900">{{ $stats['new_patients_week'] }}</p>
            <p class="text-[10px] text-gray-400 mt-1">{{ $stats['reports_this_week'] }} reports this week · {{ $stats['reports_total'] }} total</p>
        </div>
    </div>

    {{-- Recent reports --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
            <div>
                <h3 class="text-sm font-semibold text-gray-900">Recent Manager Reports</h3>
                <p class="text-xs text-gray-400">Daily & weekly reports submitted {{ auth()->user()->isAdmin() ? 'by all managers' : 'by you' }}</p>
            </div>
            <a href="{{ route('manager.reports.index') }}" class="text-xs font-medium text-emerald-600 hover:text-emerald-700">View All</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                    <tr>
                        <th class="px-5 py-3">Type</th>
                        <th class="px-5 py-3">Period</th>
                        <th class="px-5 py-3 text-right">Revenue</th>
                        <th class="px-5 py-3 text-right">Clients</th>
                        @if(auth()->user()->isAdmin())<th class="px-5 py-3">Manager</th>@endif
                        <th class="px-5 py-3">Submitted</th>
                        <th class="px-5 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($recentReports as $report)
                    <tr class="hover:bg-emerald-50/40 transition-colors">
                        <td class="px-5 py-3">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium {{ $report->type === 'weekly' ? 'bg-gold-100 text-gold-700' : 'bg-emerald-100 text-emerald-700' }}">
                                {{ ucfirst($report->type) }}
                            </span>
                        </td>
                        <td class="px-5 py-3 text-gray-700 text-xs font-medium">{{ $report->periodLabel() }}</td>
                        <td class="px-5 py-3 text-right text-xs font-medium text-gray-700">{{ $report->revenue_overall !== null ? number_format($report->revenue_overall) . ' TSh' : '—' }}</td>
                        <td class="px-5 py-3 text-right text-xs text-gray-500">{{ ($report->new_clients ?? 0) + ($report->returning_clients ?? 0) }}</td>
                        @if(auth()->user()->isAdmin())<td class="px-5 py-3 text-xs text-gray-600">{{ $report->user->name }}</td>@endif
                        <td class="px-5 py-3 text-xs text-gray-500">{{ $report->created_at->format('d M Y H:i') }}</td>
                        <td class="px-5 py-3">
                            <div class="flex items-center justify-end gap-1">
                                <a href="{{ route('manager.reports.show', $report) }}" class="p-1.5 text-emerald-600 hover:bg-emerald-100 rounded-lg transition-colors" title="View">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </a>
                                <a href="{{ route('manager.reports.edit', $report) }}" class="p-1.5 text-gray-500 hover:bg-gray-100 rounded-lg transition-colors" title="Edit">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.43-9.525l-9.17 9.17a2 2 0 00-.586 1.414V17a1 1 0 001 1h2.828a2 2 0 001.414-.586l9.17-9.17a2 2 0 000-2.828l-1.414-1.414a2 2 0 00-2.828 0z"/></svg>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="px-5 py-10 text-center text-gray-400 text-sm">No reports submitted yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
