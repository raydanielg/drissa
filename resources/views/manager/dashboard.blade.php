@extends('layouts.dashboard')

@section('title', 'Manager Dashboard - ' . config('app.name', 'Laravel'))
@section('page_title', 'Clinic Manager Dashboard')

@section('content')
<div class="space-y-6">

    {{-- Recent reports --}}
    <div class="bg-slate-800 rounded-md p-6 text-white">
        <div class="flex flex-wrap items-end justify-between gap-3">
            <div>
                <p class="text-[10px] uppercase tracking-wider text-slate-400 mb-1">Clinic Manager</p>
                <h2 class="text-xl font-bold">Karibu, {{ auth()->user()->name }}</h2>
                <p class="text-xs text-slate-400 mt-1">{{ now()->format('l, d F Y') }} · Manage daily & weekly reports for the whole clinic.</p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('manager.reports.create', ['type' => 'daily']) }}" class="inline-flex items-center gap-1.5 bg-white hover:bg-gray-100 text-slate-800 text-xs font-semibold px-3 py-1.5 rounded transition-colors">
                    <i class="hgi-stroke hgi-add-01 text-sm"></i>
                    Daily Report
                </a>
                <a href="{{ route('manager.reports.create', ['type' => 'weekly']) }}" class="inline-flex items-center gap-1.5 bg-slate-700/60 hover:bg-slate-700 text-slate-100 text-xs font-medium px-3 py-1.5 rounded border border-slate-600 transition-colors">
                    <i class="hgi-stroke hgi-calendar-03 text-sm"></i>
                    Weekly Report
                </a>
            </div>
        </div>
    </div>

    {{-- Recent reports --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
        <div class="bg-white rounded-md border {{ $todayReport ? 'border-emerald-200' : 'border-amber-200' }} p-4 flex items-center gap-3">
            <div class="w-9 h-9 rounded-md {{ $todayReport ? 'bg-emerald-50 text-emerald-600' : 'bg-amber-50 text-amber-600' }} flex items-center justify-center flex-shrink-0">
                @if($todayReport)
                    <i class="hgi-stroke hgi-checkmark-circle-02 text-base"></i>
                @else
                    <i class="hgi-stroke hgi-clock-01 text-base"></i>
                @endif
            </div>
            <div class="flex-1">
                <p class="text-sm font-semibold text-gray-900">Today's Daily Report</p>
                <p class="text-xs text-gray-500">{{ $todayReport ? 'Submitted at ' . $todayReport->created_at->format('H:i') : 'Haijawasilishwa bado — not submitted yet' }}</p>
            </div>
            @if($todayReport)
                <a href="{{ route('manager.reports.show', $todayReport) }}" class="text-xs font-medium text-slate-600 hover:text-slate-800">View</a>
            @else
                <a href="{{ route('manager.reports.create', ['type' => 'daily']) }}" class="px-3 py-1.5 bg-slate-800 hover:bg-slate-700 text-white text-xs font-medium rounded transition-colors">Fill Now</a>
            @endif
        </div>
        <div class="bg-white rounded-md border {{ $weekReport ? 'border-emerald-200' : 'border-amber-200' }} p-4 flex items-center gap-3">
            <div class="w-9 h-9 rounded-md {{ $weekReport ? 'bg-emerald-50 text-emerald-600' : 'bg-amber-50 text-amber-600' }} flex items-center justify-center flex-shrink-0">
                @if($weekReport)
                    <i class="hgi-stroke hgi-checkmark-circle-02 text-base"></i>
                @else
                    <i class="hgi-stroke hgi-calendar-03 text-base"></i>
                @endif
            </div>
            <div class="flex-1">
                <p class="text-sm font-semibold text-gray-900">This Week's Weekly Report</p>
                <p class="text-xs text-gray-500">{{ $weekReport ? 'Submitted' : 'Week ends ' . now()->endOfWeek()->format('d M') . ' — not submitted yet' }}</p>
            </div>
            @if($weekReport)
                <a href="{{ route('manager.reports.show', $weekReport) }}" class="text-xs font-medium text-slate-600 hover:text-slate-800">View</a>
            @else
                <a href="{{ route('manager.reports.create', ['type' => 'weekly']) }}" class="px-3 py-1.5 bg-slate-800 hover:bg-slate-700 text-white text-xs font-medium rounded transition-colors">Fill Now</a>
            @endif
        </div>
    </div>

    {{-- Recent reports --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
        <div class="bg-white rounded-md border border-gray-200 p-4">
            <div class="flex items-start justify-between">
                <p class="text-[10px] font-medium text-gray-500 uppercase tracking-wide">Revenue Today</p>
                <i class="hgi-stroke hgi-money-03 text-base text-gray-400"></i>
            </div>
            <p class="text-lg font-bold text-gray-900 mt-1">{{ number_format($stats['revenue_today']) }} <span class="text-[10px] font-medium text-gray-400">TSh</span></p>
        </div>
        <div class="bg-white rounded-md border border-gray-200 p-4">
            <div class="flex items-start justify-between">
                <p class="text-[10px] font-medium text-gray-500 uppercase tracking-wide">Revenue This Week</p>
                <i class="hgi-stroke hgi-analytics-02 text-base text-gray-400"></i>
            </div>
            <p class="text-lg font-bold text-gray-900 mt-1">{{ number_format($stats['revenue_this_week']) }} <span class="text-[10px] font-medium text-gray-400">TSh</span></p>
        </div>
        <div class="bg-white rounded-md border border-gray-200 p-4">
            <div class="flex items-start justify-between">
                <p class="text-[10px] font-medium text-gray-500 uppercase tracking-wide">Visits Today</p>
                <i class="hgi-stroke hgi-user-multiple-02 text-base text-gray-400"></i>
            </div>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['visits_today'] }}</p>
            <p class="text-[10px] text-gray-400 mt-0.5">{{ $stats['completed_today'] }} completed · {{ $stats['active_visits'] }} active</p>
        </div>
        <div class="bg-white rounded-md border border-gray-200 p-4">
            <div class="flex items-start justify-between">
                <p class="text-[10px] font-medium text-gray-500 uppercase tracking-wide">New Patients (Week)</p>
                <i class="hgi-stroke hgi-user-add-01 text-base text-gray-400"></i>
            </div>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['new_patients_week'] }}</p>
            <p class="text-[10px] text-gray-400 mt-0.5">{{ $stats['reports_this_week'] }} reports this week · {{ $stats['reports_total'] }} total</p>
        </div>
    </div>

    {{-- Charts --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
        <div class="lg:col-span-2 bg-white rounded-md border border-gray-200 p-5">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-sm font-bold text-gray-900">Revenue & Visits — Last 7 Days</h3>
                    <p class="text-[10px] text-gray-400 mt-0.5">TSh collected vs patients seen per day</p>
                </div>
                <div class="flex items-center gap-3 text-[10px] text-gray-500">
                    <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-emerald-600"></span>Revenue</span>
                    <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-slate-400"></span>Visits</span>
                </div>
            </div>
            <div class="h-56"><canvas id="managerTrendChart"></canvas></div>
        </div>
        <div class="bg-white rounded-md border border-gray-200 p-5">
            <h3 class="text-sm font-bold text-gray-900 mb-4">Visit Status</h3>
            <div class="h-40"><canvas id="managerStatusChart"></canvas></div>
            <div class="grid grid-cols-2 gap-1.5 mt-4">
                @php
                    $statusLabels = [
                        'registered' => 'Registered', 'waiting_for_doctor' => 'Waiting Doctor',
                        'with_doctor' => 'With Doctor', 'waiting_for_lab' => 'Waiting Lab',
                        'waiting_for_pharmacy' => 'Pharmacy', 'waiting_for_payment' => 'Payment',
                        'completed' => 'Completed', 'cancelled' => 'Cancelled',
                    ];
                    $statusDots = [
                        'registered' => 'bg-slate-400', 'waiting_for_doctor' => 'bg-amber-500',
                        'with_doctor' => 'bg-slate-700', 'waiting_for_lab' => 'bg-slate-500',
                        'waiting_for_pharmacy' => 'bg-slate-600', 'waiting_for_payment' => 'bg-amber-600',
                        'completed' => 'bg-emerald-500', 'cancelled' => 'bg-red-500',
                    ];
                @endphp
                @foreach ($visitStatusCounts as $status => $count)
                    <div class="flex items-center justify-between text-[10px]">
                        <span class="flex items-center gap-1 text-gray-500"><span class="w-1.5 h-1.5 rounded-full {{ $statusDots[$status] ?? 'bg-gray-400' }}"></span>{{ $statusLabels[$status] ?? ucwords(str_replace('_', ' ', $status)) }}</span>
                        <span class="font-semibold text-gray-900">{{ $count }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Recent reports --}}
    <div class="bg-white rounded-md border border-gray-200 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
            <div>
                <h3 class="text-sm font-bold text-gray-900">Recent Manager Reports</h3>
                <p class="text-xs text-gray-400">Daily & weekly reports submitted {{ auth()->user()->isAdmin() ? 'by all managers' : 'by you' }}</p>
            </div>
            <a href="{{ route('manager.reports.index') }}" class="text-xs font-medium text-slate-600 hover:text-slate-800">View All</a>
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
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="px-5 py-3">
                            <span class="inline-flex items-center gap-1.5 text-[10px] font-medium capitalize text-gray-600">
                                <span class="w-1.5 h-1.5 rounded-full {{ $report->type === 'weekly' ? 'bg-amber-500' : 'bg-slate-500' }}"></span>
                                {{ ucfirst($report->type) }}
                            </span>
                        </td>
                        <td class="px-5 py-3 text-gray-700 text-xs font-medium">{{ $report->periodLabel() }}</td>
                        <td class="px-5 py-3 text-right text-xs font-semibold text-gray-900">{{ $report->revenue_overall !== null ? number_format($report->revenue_overall) . ' TSh' : '—' }}</td>
                        <td class="px-5 py-3 text-right text-xs text-gray-500">{{ ($report->new_clients ?? 0) + ($report->returning_clients ?? 0) }}</td>
                        @if(auth()->user()->isAdmin())<td class="px-5 py-3 text-xs text-gray-600">{{ $report->user->name }}</td>@endif
                        <td class="px-5 py-3 text-xs text-gray-500">{{ $report->created_at->format('d M Y H:i') }}</td>
                        <td class="px-5 py-3">
                            <div class="flex items-center justify-end gap-1">
                                <a href="{{ route('manager.reports.show', $report) }}" class="p-1.5 text-slate-600 hover:bg-gray-100 rounded transition-colors" title="View">
                                    <i class="hgi-stroke hgi-view text-base"></i>
                                </a>
                                <a href="{{ route('manager.reports.edit', $report) }}" class="p-1.5 text-gray-500 hover:bg-gray-100 rounded-lg transition-colors" title="Edit">
                                    <i class="hgi-stroke hgi-pencil-edit-01 text-base"></i>
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

@push('scripts')
<script>
    (function () {
        var trendCanvas = document.getElementById('managerTrendChart');
        if (trendCanvas && window.Chart) {
            var ctx = trendCanvas.getContext('2d');
            var gradient = ctx.createLinearGradient(0, 0, 0, 220);
            gradient.addColorStop(0, 'rgba(5, 150, 105, 0.08)');
            gradient.addColorStop(1, 'rgba(5, 150, 105, 0)');
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: @json($revenueLabels),
                    datasets: [
                        {
                            label: 'Revenue (TSh)',
                            data: @json($revenueTrend),
                            borderColor: '#059669',
                            backgroundColor: gradient,
                            borderWidth: 2,
                            tension: 0.35,
                            fill: true,
                            pointRadius: 2,
                            pointHoverRadius: 4,
                            pointBackgroundColor: '#fff',
                            pointBorderColor: '#059669',
                            yAxisID: 'y'
                        },
                        {
                            label: 'Visits',
                            data: @json($visitsTrend),
                            borderColor: '#94a3b8',
                            borderDash: [5, 4],
                            borderWidth: 2,
                            tension: 0.35,
                            fill: false,
                            pointRadius: 2,
                            pointHoverRadius: 4,
                            pointBackgroundColor: '#fff',
                            pointBorderColor: '#94a3b8',
                            yAxisID: 'y1'
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: { intersect: false, mode: 'index' },
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: 'rgba(17, 24, 39, 0.9)',
                            padding: 10,
                            cornerRadius: 8,
                            callbacks: {
                                label: function (c) {
                                    return c.dataset.yAxisID === 'y'
                                        ? 'Revenue: ' + Number(c.raw).toLocaleString() + ' TSh'
                                        : 'Visits: ' + c.raw;
                                }
                            }
                        }
                    },
                    scales: {
                        y: { beginAtZero: true, grid: { color: '#f1f5f9' }, ticks: { font: { size: 10 }, callback: function (v) { return (v / 1000) + 'k'; } } },
                        y1: { beginAtZero: true, position: 'right', grid: { display: false }, ticks: { font: { size: 10 }, precision: 0 } },
                        x: { grid: { display: false }, ticks: { font: { size: 10 } } }
                    }
                }
            });
        }

        var statusCanvas = document.getElementById('managerStatusChart');
        if (statusCanvas && window.Chart) {
            var statusColorMap = {
                'registered': '#94a3b8', 'waiting_for_doctor': '#f59e0b', 'with_doctor': '#334155',
                'waiting_for_lab': '#64748b', 'waiting_for_pharmacy': '#475569', 'waiting_for_payment': '#d97706',
                'completed': '#059669', 'cancelled': '#dc2626'
            };
            var statusLabels = @json(array_keys($visitStatusCounts));
            new Chart(statusCanvas.getContext('2d'), {
                type: 'doughnut',
                data: {
                    labels: statusLabels.map(function (s) { return s.replace(/_/g, ' '); }),
                    datasets: [{
                        data: @json(array_values($visitStatusCounts)),
                        backgroundColor: statusLabels.map(function (s) { return statusColorMap[s] || '#9ca3af'; }),
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
                        tooltip: { backgroundColor: 'rgba(17, 24, 39, 0.9)', padding: 10, cornerRadius: 8 }
                    }
                }
            });
        }
    })();
</script>
@endpush
