@extends('layouts.dashboard')

@section('title', 'Revenue Report - ' . config('app.name', 'Laravel'))
@section('page_title', 'Revenue Report')

@section('content')
<div class="bg-white rounded-xl border border-gray-100 p-5 shadow-sm mb-6">
    <form method="GET" class="flex flex-wrap gap-3 items-end">
        <div>
            <label class="block text-xs text-gray-500 mb-1">Year</label>
            <input type="number" name="year" value="{{ $year }}" class="border rounded-lg px-3 py-2 text-sm">
        </div>
        <button type="submit" class="bg-emerald-600 text-white text-sm font-medium px-4 py-2 rounded-lg hover:bg-emerald-700">Filter</button>
    </form>
</div>

<div class="bg-white rounded-xl border border-gray-100 p-5 shadow-sm mb-6">
    <h3 class="text-sm font-semibold text-gray-900 mb-4">Monthly Revenue</h3>
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

<div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-100">
        <h3 class="text-sm font-semibold text-gray-900">Monthly Summary</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left">
            <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                <tr><th class="px-6 py-3">Month</th><th class="px-6 py-3">Revenue</th></tr>
            </thead>
            <tbody>
                @foreach ($monthly as $i => $amount)
                    <tr class="border-t border-gray-100 hover:bg-gray-50/50">
                        <td class="px-6 py-3 font-medium">{{ $months[$i] }}</td>
                        <td class="px-6 py-3">TSh {{ number_format($amount, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
