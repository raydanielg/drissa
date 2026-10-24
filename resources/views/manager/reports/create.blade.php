@extends('layouts.dashboard')

@section('title', 'New Manager Report - ' . config('app.name', 'Laravel'))
@section('page_title', 'New ' . ucfirst($type) . ' Report')

@section('content')
<div class="max-w-5xl mx-auto space-y-5">

    {{-- Header + type switcher --}}
    <div class="bg-slate-800 rounded-md p-6 text-white">
        <div class="flex flex-wrap items-end justify-between gap-3">
            <div>
                <p class="text-[10px] uppercase tracking-wider text-slate-400 mb-1">Manager</p>
                <h1 class="text-xl font-bold">Manager Report</h1>
                <p class="text-xs text-slate-400 mt-1">Jaza ripoti ya {{ $type === 'weekly' ? 'wiki' : 'siku' }} — idadi na taarifa kutoka kila department.</p>
            </div>
            <div class="inline-flex rounded bg-slate-700 p-1">
                <a href="{{ route('manager.reports.create', ['type' => 'daily']) }}" class="px-4 py-1.5 text-xs font-semibold rounded transition-all {{ $type === 'daily' ? 'bg-white text-slate-800' : 'text-slate-300 hover:text-white' }}">Daily</a>
                <a href="{{ route('manager.reports.create', ['type' => 'weekly']) }}" class="px-4 py-1.5 text-xs font-semibold rounded transition-all {{ $type === 'weekly' ? 'bg-white text-slate-800' : 'text-slate-300 hover:text-white' }}">Weekly</a>
            </div>
        </div>
    </div>

    <form method="POST" action="{{ route('manager.reports.store') }}">
        @csrf
        @include('manager.reports._form', ['report' => null])
    </form>
</div>
@endsection
