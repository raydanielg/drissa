@extends('layouts.dashboard')

@section('title', 'Edit Manager Report - ' . config('app.name', 'Laravel'))
@section('page_title', 'Edit ' . ucfirst($report->type) . ' Report')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-lg font-bold text-gray-900">Edit {{ ucfirst($report->type) }} Report</h2>
            <p class="text-sm text-gray-500">{{ $report->periodLabel() }} · by {{ $report->user->name }}</p>
        </div>
        <a href="{{ route('manager.reports.show', $report) }}" class="inline-flex items-center gap-2 text-xs font-medium text-emerald-600 hover:text-emerald-700">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
            View Report
        </a>
    </div>

    <form method="POST" action="{{ route('manager.reports.update', $report) }}">
        @csrf
        @method('PUT')
        @include('manager.reports._form', ['type' => $report->type])
    </form>
</div>
@endsection
