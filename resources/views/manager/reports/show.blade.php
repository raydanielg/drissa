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
<div class="max-w-4xl mx-auto space-y-5">

    {{-- Signature --}}
    <div class="bg-slate-800 rounded-md p-6 text-white no-print">
        <div class="flex flex-wrap items-end justify-between gap-3">
            <div>
                <p class="text-[10px] uppercase tracking-wider text-slate-400 mb-1">Manager</p>
                <h1 class="text-xl font-bold">{{ ucfirst($report->type) }} Manager Report</h1>
                <p class="text-xs text-slate-400 mt-1">{{ $report->periodLabel() }}</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('manager.reports.index') }}" class="inline-flex items-center gap-1.5 bg-white/10 hover:bg-white/20 border border-slate-600 text-white text-xs font-medium px-3 py-1.5 rounded transition-colors">
                    <i class="hgi-stroke hgi-arrow-left-02 text-sm"></i>
                    All Reports
                </a>
                <button onclick="window.print()" class="inline-flex items-center gap-1.5 bg-white/10 hover:bg-white/20 border border-slate-600 text-white text-xs font-medium px-3 py-1.5 rounded transition-colors">
                    <i class="hgi-stroke hgi-printer text-sm"></i>
                    Print
                </button>
                <a href="{{ route('manager.reports.edit', $report) }}" class="inline-flex items-center gap-1.5 bg-white hover:bg-gray-100 text-slate-800 text-xs font-semibold px-3 py-1.5 rounded transition-colors">
                    <i class="hgi-stroke hgi-pencil-edit-01 text-sm"></i>
                    Edit
                </a>
            </div>
        </div>
    </div>

    {{-- Signature --}}
    <div class="bg-white rounded-md border border-gray-200 overflow-hidden print-card">
        {{-- Signature --}}
        <div class="px-8 pt-8 pb-4 border-b-2 border-slate-800">
            <div class="flex items-start justify-between">
                <div class="flex items-center gap-3">
                    <img src="{{ asset('logo.png') }}" alt="Logo" class="w-12 h-12 rounded object-cover">
                    <div>
                        <h1 class="text-lg font-extrabold text-slate-800 uppercase tracking-wide">{{ config('app.name', 'Laravel') }}</h1>
                        <p class="text-[10px] text-gray-500 uppercase tracking-widest">Ultra Lab — Consult — Report From Every Department</p>
                    </div>
                </div>
                <div class="text-right text-[10px] text-gray-500">
                    <p>Report #{{ str_pad($report->id, 5, '0', STR_PAD_LEFT) }}</p>
                    <p>{{ $report->created_at->format('d M Y H:i') }}</p>
                </div>
            </div>
            <div class="mt-4 flex items-center justify-between">
                <h2 class="text-base font-extrabold text-gray-900 uppercase tracking-wider">{{ $report->type === 'weekly' ? 'Weekly Manager Report' : 'Daily Manager Report' }}</h2>
                <span class="inline-flex items-center gap-1.5 text-[10px] font-medium text-gray-600"><span class="w-1.5 h-1.5 rounded-full {{ $report->type === 'weekly' ? 'bg-amber-500' : 'bg-slate-500' }}"></span>{{ $report->periodLabel() }}</span>
            </div>
        </div>

        {{-- Signature --}}
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

            {{-- Signature --}}
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

            {{-- Signature --}}
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
