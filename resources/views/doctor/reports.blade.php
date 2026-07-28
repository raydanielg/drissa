@extends('layouts.dashboard')

@section('title', 'My Reports - ' . config('app.name', 'Laravel'))
@section('page_title', 'My Reports')

@section('content')
<div class="space-y-6">

    {{-- Summary Stats --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <p class="text-xs text-gray-500 uppercase tracking-wider mb-1">Total Visits</p>
            <p class="text-2xl font-bold text-gray-900">{{ $totalVisits }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <p class="text-xs text-gray-500 uppercase tracking-wider mb-1">Completed</p>
            <p class="text-2xl font-bold text-green-600">{{ $completed }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <p class="text-xs text-gray-500 uppercase tracking-wider mb-1">Cancelled</p>
            <p class="text-2xl font-bold text-red-600">{{ $cancelled }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <p class="text-xs text-gray-500 uppercase tracking-wider mb-1">Success Rate</p>
            <p class="text-2xl font-bold text-emerald-600">{{ $successRate }}%</p>
        </div>
    </div>

    {{-- Activity Stats --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 rounded-lg bg-rose-100 flex items-center justify-center">
                    <svg class="w-5 h-5 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5"/></svg>
                </div>
                <div>
                    <p class="text-sm font-semibold text-gray-900">Prescriptions Written</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $prescriptionCount }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 rounded-lg bg-cyan-100 flex items-center justify-center">
                    <svg class="w-5 h-5 text-cyan-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>
                </div>
                <div>
                    <p class="text-sm font-semibold text-gray-900">Lab Orders</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $labOrderCount }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Monthly Visits Chart --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
        <h3 class="text-sm font-semibold text-gray-900 mb-4">Monthly Visits (Last 6 Months)</h3>
        <div class="flex items-end justify-between gap-2 h-48">
            @foreach($monthlyStats as $i => $count)
                @php($maxVal = max($monthlyStats->max(), 1))
                <div class="flex-1 flex flex-col items-center gap-2">
                    <div class="w-full bg-gradient-to-t from-emerald-500 to-emerald-400 rounded-t-lg transition-all hover:from-emerald-600 hover:to-emerald-500" style="height: {{ max(($count / $maxVal) * 100, 2) }}%; min-height: 4px;" title="{{ $count }} visits"></div>
                    <span class="text-[10px] text-gray-500 text-center">{{ $monthLabels[$i] }}</span>
                    <span class="text-xs font-bold text-gray-700">{{ $count }}</span>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Top Diagnoses --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100">
            <h3 class="text-sm font-semibold text-gray-900">Top Diagnoses</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-xs text-gray-500 border-b border-gray-100">
                        <th class="px-5 py-3 font-medium">#</th>
                        <th class="px-5 py-3 font-medium">Diagnosis</th>
                        <th class="px-5 py-3 font-medium">Cases</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($topDiagnoses as $i => $diag)
                        <tr class="border-b border-gray-50 hover:bg-gray-50/50">
                            <td class="px-5 py-3 text-gray-400">{{ $i + 1 }}</td>
                            <td class="px-5 py-3 font-medium text-gray-900">{{ $diag->diagnosis }}</td>
                            <td class="px-5 py-3">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-medium bg-emerald-100 text-emerald-700">{{ $diag->total }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="px-5 py-8 text-center text-gray-400">No diagnosis data yet</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
