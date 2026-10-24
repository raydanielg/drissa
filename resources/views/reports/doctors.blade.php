@extends('layouts.dashboard')

@section('title', 'Doctor Performance - ' . config('app.name', 'Laravel'))
@section('page_title', 'Doctor Performance')

@section('content')
<div class="space-y-5">
{{-- Header --}}
<div class="bg-slate-800 rounded-md p-6 text-white">
    <p class="text-[10px] uppercase tracking-wider text-slate-400 mb-1">Reports</p>
    <h1 class="text-xl font-bold">Doctor Performance</h1>
    <p class="text-xs text-slate-400 mt-1">Visits attended by each doctor.</p>
</div>

<div class="bg-white rounded-md border border-gray-200 p-5">
    <form method="GET" class="flex flex-wrap gap-3 items-end">
        <div>
            <label class="block text-xs text-gray-500 mb-1">From</label>
            <x-ui.input type="text" data-fp-date name="from" value="{{ $from }}" class="border border-gray-200 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-slate-300" />
        </div>
        <div>
            <label class="block text-xs text-gray-500 mb-1">To</label>
            <x-ui.input type="text" data-fp-date name="to" value="{{ $to }}" class="border border-gray-200 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-slate-300" />
        </div>
        <button type="submit" class="bg-slate-800 text-white text-sm font-medium px-4 py-2 rounded hover:bg-slate-700">Filter</button>
    </form>
</div>

<div class="bg-white rounded-md border border-gray-200 overflow-hidden">
    <div class="px-5 py-4 border-b border-gray-100">
        <h3 class="text-sm font-bold text-gray-900">Doctor Visits</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left">
            <thead class="bg-gray-50 text-[10px] uppercase text-gray-400">
                <tr>
                    <th class="px-5 py-3">Doctor</th>
                    <th class="px-5 py-3">Email</th>
                    <th class="px-5 py-3">Visits Attended</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse ($doctors as $doctor)
                    <tr class="hover:bg-gray-50/50">
                        <td class="px-5 py-3 text-xs font-medium text-gray-900">{{ $doctor->name }}</td>
                        <td class="px-5 py-3 text-xs text-gray-600">{{ $doctor->email }}</td>
                        <td class="px-5 py-3 text-xs text-gray-600">{{ $doctor->visits_as_doctor_count }}</td>
                    </tr>
                @empty
                    <tr><td colspan="3" class="px-5 py-8 text-center text-gray-400 text-xs">No doctors found</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
</div>
@endsection
