@extends('layouts.dashboard')

@section('title', 'New Manager Report - ' . config('app.name', 'Laravel'))
@section('page_title', 'New ' . ucfirst($type) . ' Report')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">

    {{-- Header + type switcher --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-lg font-bold text-gray-900">Manager Report</h2>
            <p class="text-sm text-gray-500">Jaza ripoti ya {{ $type === 'weekly' ? 'wiki' : 'siku' }} — idadi na taarifa kutoka kila department.</p>
        </div>
        <div class="inline-flex rounded-lg bg-gray-100 p-1">
            <a href="{{ route('manager.reports.create', ['type' => 'daily']) }}" class="px-4 py-1.5 text-xs font-semibold rounded-md transition-all {{ $type === 'daily' ? 'bg-emerald-600 text-white shadow-sm' : 'text-gray-600 hover:text-gray-900' }}">Daily</a>
            <a href="{{ route('manager.reports.create', ['type' => 'weekly']) }}" class="px-4 py-1.5 text-xs font-semibold rounded-md transition-all {{ $type === 'weekly' ? 'bg-emerald-600 text-white shadow-sm' : 'text-gray-600 hover:text-gray-900' }}">Weekly</a>
        </div>
    </div>

    <form method="POST" action="{{ route('manager.reports.store') }}">
        @csrf
        @include('manager.reports._form', ['report' => null])
    </form>
</div>
@endsection
