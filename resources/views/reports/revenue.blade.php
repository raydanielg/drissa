@extends('layouts.dashboard')

@section('title', 'Revenue Report - ' . config('app.name', 'Laravel'))
@section('page_title', 'Revenue Report')

@section('content')
<div class="space-y-5">
{{-- Header --}}
<div class="bg-slate-800 rounded-md p-6 text-white">
    <p class="text-[10px] uppercase tracking-wider text-slate-400 mb-1">Reports</p>
    <h1 class="text-xl font-bold">Revenue Report</h1>
    <p class="text-xs text-slate-400 mt-1">Monthly revenue for {{ $year }}.</p>
</div>

<div class="bg-white rounded-md border border-gray-200 p-5">
    <form method="GET" class="flex flex-wrap gap-3 items-end">
        <div>
            <label class="block text-xs text-gray-500 mb-1">Year</label>
            <x-ui.input type="number" name="year" value="{{ $year }}" class="border border-gray-200 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-slate-300" />
        </div>
        <button type="submit" class="bg-slate-800 text-white text-sm font-medium px-4 py-2 rounded hover:bg-slate-700">Filter</button>
    </form>
</div>

<div class="bg-white rounded-md border border-gray-200 p-5">
    <h3 class="text-sm font-bold text-gray-900 mb-4">Monthly Revenue</h3>
    <div class="flex items-end gap-2 h-48">
        @php $months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec']; @endphp
        @foreach ($monthly as $i => $amount)
            @php $max = max($monthly) ?: 1; $height = ($amount / $max) * 100; @endphp
            <div class="flex-1 flex flex-col items-center gap-1 group">
                <div class="w-full bg-gray-100 rounded-t relative h-40 overflow-hidden">
                    <div class="absolute bottom-0 left-0 right-0 bg-emerald-500 rounded-t transition-all" style="height: {{ $height }}%"></div>
                </div>
                <span class="text-[10px] text-gray-500">{{ $months[$i] }}</span>
                <span class="text-[10px] font-medium text-gray-700">TSh {{ number_format($amount, 0) }}</span>
            </div>
        @endforeach
    </div>
</div>

<div class="bg-white rounded-md border border-gray-200 overflow-hidden">
    <div class="px-5 py-4 border-b border-gray-100">
        <h3 class="text-sm font-bold text-gray-900">Monthly Summary</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left">
            <thead class="bg-gray-50 text-[10px] uppercase text-gray-400">
                <tr><th class="px-5 py-3">Month</th><th class="px-5 py-3 text-right">Revenue</th></tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @foreach ($monthly as $i => $amount)
                    <tr class="hover:bg-gray-50/50">
                        <td class="px-5 py-3 text-xs font-medium text-gray-900">{{ $months[$i] }}</td>
                        <td class="px-5 py-3 text-xs font-semibold text-gray-900 text-right">TSh {{ number_format($amount, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
