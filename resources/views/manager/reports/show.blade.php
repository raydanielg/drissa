@extends('layouts.dashboard')

@section('title', ucfirst($report->type) . ' Manager Report - ' . config('app.name', 'Laravel'))
@section('page_title', ucfirst($report->type) . ' Manager Report')

@push('styles')
<style>
    @media print {
        #dashboardSidebar, header, #mobileOverlay, .no-print { display: none !important; }
        .lg\:ml-64 { margin-left: 0 !important; }
        main { padding: 0 !important; }
        body { background: #fff !important; }
        .print-card { box-shadow: none !important; border: 1px solid #e5e7eb !important; }
    }
</style>
@endpush

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    {{-- Actions --}}
    <div class="flex items-center justify-between no-print">
        <a href="{{ route('manager.reports.index') }}" class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-gray-700">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            All Reports
        </a>
        <div class="flex items-center gap-2">
            <button onclick="window.print()" class="inline-flex items-center gap-2 bg-white border border-gray-200 hover:border-emerald-300 text-gray-700 text-sm font-medium px-4 py-2 rounded-lg shadow-sm transition-all">
                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4H7v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                Print
            </button>
            <a href="{{ route('manager.reports.edit', $report) }}" class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium px-4 py-2 rounded-lg shadow-sm transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.43-9.525l-9.17 9.17a2 2 0 00-.586 1.414V17a1 1 0 001 1h2.828a2 2 0 001.414-.586l9.17-9.17a2 2 0 000-2.828l-1.414-1.414a2 2 0 00-2.828 0z"/></svg>
                Edit
            </a>
        </div>
    </div>

    {{-- Report document --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden print-card">
        {{-- Letterhead --}}
        <div class="px-8 pt-8 pb-4 border-b-2 border-emerald-800">
            <div class="flex items-start justify-between">
                <div class="flex items-center gap-3">
                    <img src="{{ asset('logo.png') }}" alt="Logo" class="w-12 h-12 rounded-lg object-cover">
                    <div>
                        <h1 class="text-lg font-extrabold text-emerald-900 uppercase tracking-wide">{{ config('app.name', 'Laravel') }}</h1>
                        <p class="text-[10px] text-gray-500 uppercase tracking-widest">Ultra Lab · Consult · Report From Every Department</p>
                    </div>
                </div>
                <div class="text-right text-[10px] text-gray-500">
                    <p>Report #{{ str_pad($report->id, 5, '0', STR_PAD_LEFT) }}</p>
                    <p>{{ $report->created_at->format('d M Y H:i') }}</p>
                </div>
            </div>
            <div class="mt-4 flex items-center justify-between">
                <h2 class="text-base font-extrabold text-gray-900 uppercase tracking-wider">{{ $report->type === 'weekly' ? 'Weekly Manager Report' : 'Daily Manager Report' }}</h2>
                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider {{ $report->type === 'weekly' ? 'bg-gold-100 text-gold-700' : 'bg-emerald-100 text-emerald-700' }}">{{ $report->periodLabel() }}</span>
            </div>
        </div>

        {{-- Items table --}}
        <div class="px-8 py-6">
            <table class="w-full text-sm border border-gray-200 rounded-lg overflow-hidden">
                <thead>
                    <tr class="bg-gray-50 text-left">
                        <th class="px-4 py-2.5 text-[11px] font-bold uppercase tracking-wider text-gray-600 border-b border-gray-200 w-1/2">Item</th>
                        <th class="px-4 py-2.5 text-[11px] font-bold uppercase tracking-wider text-gray-600 border-b border-gray-200">{{ $report->type === 'weekly' ? "This Week's Report" : "Today's Report" }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <tr><td class="px-4 py-2.5 font-medium text-gray-700">Revenue Overall</td><td class="px-4 py-2.5 text-gray-900 font-semibold">{{ $report->revenue_overall !== null ? number_format($report->revenue_overall) . ' TSh' : '—' }}</td></tr>
                    <tr><td class="px-4 py-2.5 font-medium text-gray-700">Total Supplement Sales PPV</td><td class="px-4 py-2.5 text-gray-900 font-semibold">{{ $report->supplement_sales_ppv !== null ? number_format($report->supplement_sales_ppv) . ' TSh' : '—' }}</td></tr>
                    <tr><td class="px-4 py-2.5 font-medium text-gray-700">Total Products Sales [Costs]</td><td class="px-4 py-2.5 text-gray-900 font-semibold">{{ $report->product_sales_costs !== null ? number_format($report->product_sales_costs) . ' TSh' : '—' }}</td></tr>
                    <tr><td class="px-4 py-2.5 font-medium text-gray-700">Total New Clients [Check up]</td><td class="px-4 py-2.5 text-gray-900 font-semibold">{{ $report->new_clients ?? '—' }}</td></tr>
                    <tr><td class="px-4 py-2.5 font-medium text-gray-700">Returning Clients [Follow up]</td><td class="px-4 py-2.5 text-gray-900 font-semibold">{{ $report->returning_clients ?? '—' }}</td></tr>
                    <tr><td class="px-4 py-2.5 font-medium text-gray-700">Laboratory Tools Expenses</td><td class="px-4 py-2.5 text-gray-900 font-semibold">{{ $report->lab_tools_expenses !== null ? number_format($report->lab_tools_expenses) . ' TSh' : '—' }}</td></tr>
                    <tr><td class="px-4 py-2.5 font-medium text-gray-700">Office Expenses [Matumizi]</td><td class="px-4 py-2.5 text-gray-900 font-semibold">{{ $report->office_expenses !== null ? number_format($report->office_expenses) . ' TSh' : '—' }}</td></tr>
                </tbody>
            </table>

            {{-- Department reports --}}
            @if(!empty($report->department_reports))
            <div class="mt-6">
                <h3 class="text-[11px] font-bold uppercase tracking-wider text-gray-600 mb-2">Report From Every Department</h3>
                <table class="w-full text-sm border border-gray-200 rounded-lg overflow-hidden">
                    <thead>
                        <tr class="bg-gray-50 text-left">
                            <th class="px-4 py-2 text-[11px] font-bold uppercase tracking-wider text-gray-600 border-b border-gray-200 w-48">Department</th>
                            <th class="px-4 py-2 text-[11px] font-bold uppercase tracking-wider text-gray-600 border-b border-gray-200">Report</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($report->department_reports as $deptReport)
                        <tr>
                            <td class="px-4 py-2.5 font-medium text-gray-700">{{ $deptReport['name'] ?? '—' }}</td>
                            <td class="px-4 py-2.5 text-gray-600">{{ $deptReport['report'] ?? '—' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif

            {{-- Narrative --}}
            <div class="mt-6 space-y-4">
                <div>
                    <h3 class="text-[11px] font-bold uppercase tracking-wider text-gray-600">Staff Performance For Every Department</h3>
                    <p class="mt-1 text-sm text-gray-700 whitespace-pre-line">{{ $report->staff_performance ?: '—' }}</p>
                </div>
                <div>
                    <h3 class="text-[11px] font-bold uppercase tracking-wider text-gray-600">Challenges Happened</h3>
                    <p class="mt-1 text-sm text-gray-700 whitespace-pre-line">{{ $report->challenges ?: '—' }}</p>
                </div>
                <div>
                    <h3 class="text-[11px] font-bold uppercase tracking-wider text-gray-600">Action Plan Taken</h3>
                    <p class="mt-1 text-sm text-gray-700 whitespace-pre-line">{{ $report->action_plan ?: '—' }}</p>
                </div>
                <div>
                    <h3 class="text-[11px] font-bold uppercase tracking-wider text-gray-600">{{ $report->type === 'weekly' ? 'Weekly Meeting Reports And Comments' : 'Meeting Reports And Comments' }}</h3>
                    <p class="mt-1 text-sm text-gray-700 whitespace-pre-line">{{ $report->meeting_reports ?: '—' }}</p>
                </div>
            </div>

            {{-- Signature --}}
            <div class="mt-8 pt-6 border-t border-gray-200 flex items-end justify-between">
                <div>
                    <p class="text-[10px] uppercase tracking-wider text-gray-400">Manager Signature</p>
                    <p class="mt-1 text-lg font-bold italic text-gray-800">{{ $report->manager_signature ?: $report->user->name }}</p>
                    <p class="text-xs text-gray-500">{{ $report->user->name }} · Clinic Manager</p>
                </div>
                <div class="text-right">
                    <p class="text-[10px] uppercase tracking-wider text-gray-400">Submitted</p>
                    <p class="text-xs font-medium text-gray-600">{{ $report->created_at->format('d M Y, H:i') }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
