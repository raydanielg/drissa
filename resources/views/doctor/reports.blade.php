@extends('layouts.dashboard')

@section('title', 'My Reports - ' . config('app.name', 'Laravel'))
@section('page_title', 'My Reports')

@section('content')
<div class="space-y-5">

    {{-- Top Diagnoses --}}
    <div class="bg-slate-800 rounded-md p-6 text-white">
        <p class="text-[10px] uppercase tracking-wider text-slate-400 mb-1">Doctor</p>
        <h1 class="text-xl font-bold">My Reports</h1>
        <p class="text-xs text-slate-400 mt-1">Your visit, prescription, and lab order activity.</p>
    </div>

    {{-- Top Diagnoses --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
        <div class="bg-white rounded-md border border-gray-200 p-4">
            <p class="text-[10px] font-medium text-gray-500 uppercase tracking-wide">Total Visits</p>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ $totalVisits }}</p>
        </div>
        <div class="bg-white rounded-md border border-emerald-200 p-4">
            <p class="text-[10px] font-medium text-gray-500 uppercase tracking-wide">Completed</p>
            <p class="text-2xl font-bold text-emerald-600 mt-1">{{ $completed }}</p>
        </div>
        <div class="bg-white rounded-md border border-red-200 p-4">
            <p class="text-[10px] font-medium text-gray-500 uppercase tracking-wide">Cancelled</p>
            <p class="text-2xl font-bold text-red-600 mt-1">{{ $cancelled }}</p>
        </div>
        <div class="bg-white rounded-md border border-gray-200 p-4">
            <p class="text-[10px] font-medium text-gray-500 uppercase tracking-wide">Success Rate</p>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ $successRate }}%</p>
        </div>
    </div>

    {{-- Top Diagnoses --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
        <div class="bg-white rounded-md border border-gray-200 p-5">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded bg-slate-100 flex items-center justify-center">
                    <i class="hgi-stroke hgi-test-tube-01 text-xl text-slate-500"></i>
                </div>
                <div>
                    <p class="text-sm font-semibold text-gray-900">Prescriptions Written</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $prescriptionCount }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-md border border-gray-200 p-5">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded bg-slate-100 flex items-center justify-center">
                    <i class="hgi-stroke hgi-test-tube-01 text-xl text-slate-500"></i>
                </div>
                <div>
                    <p class="text-sm font-semibold text-gray-900">Lab Orders</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $labOrderCount }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Top Diagnoses --}}
    <div class="bg-white rounded-md border border-gray-200 p-5">
        <h3 class="text-sm font-bold text-gray-900 mb-4">Monthly Visits (Last 6 Months)</h3>
        <div class="flex items-end justify-between gap-2 h-48">
            @foreach($monthlyStats as $i => $count)
                @php($maxVal = max($monthlyStats->max(), 1))
                <div class="flex-1 flex flex-col items-center gap-2">
                    <div class="w-full bg-slate-500 rounded-t" style="height: {{ max(($count / $maxVal) * 100, 2) }}%; min-height: 4px;" title="{{ $count }} visits"></div>
                    <span class="text-[10px] text-gray-500 text-center">{{ $monthLabels[$i] }}</span>
                    <span class="text-xs font-bold text-gray-700">{{ $count }}</span>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Top Diagnoses --}}
    <div class="bg-white rounded-md border border-gray-200 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100">
            <h3 class="text-sm font-bold text-gray-900">Top Diagnoses</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 text-[10px] uppercase text-gray-400 border-b border-gray-100">
                        <th class="px-5 py-3 font-medium text-left">#</th>
                        <th class="px-5 py-3 font-medium text-left">Diagnosis</th>
                        <th class="px-5 py-3 font-medium text-left">Cases</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($topDiagnoses as $i => $diag)
                        <tr class="hover:bg-gray-50/50">
                            <td class="px-5 py-3 text-xs text-gray-400">{{ $i + 1 }}</td>
                            <td class="px-5 py-3 text-xs font-medium text-gray-900">{{ $diag->diagnosis }}</td>
                            <td class="px-5 py-3 text-xs text-gray-600">{{ $diag->total }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="px-5 py-8 text-center text-gray-400 text-xs">No diagnosis data yet</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
