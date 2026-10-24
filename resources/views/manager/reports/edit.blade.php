@extends('layouts.dashboard')

@section('title', 'Edit Manager Report - ' . config('app.name', 'Laravel'))
@section('page_title', 'Edit ' . ucfirst($report->type) . ' Report')

@section('content')
<div class="max-w-5xl mx-auto space-y-5">

    <div class="bg-slate-800 rounded-md p-6 text-white">
        <div class="flex flex-wrap items-end justify-between gap-3">
            <div>
                <p class="text-[10px] uppercase tracking-wider text-slate-400 mb-1">Manager</p>
                <h1 class="text-xl font-bold">Edit {{ ucfirst($report->type) }} Report</h1>
                <p class="text-xs text-slate-400 mt-1">{{ $report->periodLabel() }} — by {{ $report->user->name }}</p>
            </div>
            <a href="{{ route('manager.reports.show', $report) }}" class="inline-flex items-center gap-1.5 bg-white/10 hover:bg-white/20 border border-slate-600 text-white text-xs font-medium px-3 py-1.5 rounded transition-colors">
                <i class="hgi-stroke hgi-view text-sm"></i>
                View Report
            </a>
        </div>
    </div>

    <form method="POST" action="{{ route('manager.reports.update', $report) }}">
        @csrf
        @method('PUT')
        @include('manager.reports._form', ['type' => $report->type])
    </form>
</div>
@endsection
