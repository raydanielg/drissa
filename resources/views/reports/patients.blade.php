@extends('layouts.dashboard')

@section('title', 'Patient Report - ' . config('app.name', 'Laravel'))
@section('page_title', 'Patient Report')

@section('content')
<div class="space-y-5">
{{-- Header --}}
<div class="bg-slate-800 rounded-md p-6 text-white">
    <p class="text-[10px] uppercase tracking-wider text-slate-400 mb-1">Reports</p>
    <h1 class="text-xl font-bold">Patient Report</h1>
    <p class="text-xs text-slate-400 mt-1">New patient registrations by date range.</p>
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
        <h3 class="text-sm font-bold text-gray-900">New Patients ({{ $patients->total() }})</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left">
            <thead class="bg-gray-50 text-[10px] uppercase text-gray-400">
                <tr>
                    <th class="px-5 py-3">MRN</th>
                    <th class="px-5 py-3">Name</th>
                    <th class="px-5 py-3">Gender</th>
                    <th class="px-5 py-3">Phone</th>
                    <th class="px-5 py-3">Registered</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse ($patients as $patient)
                    <tr class="hover:bg-gray-50/50">
                        <td class="px-5 py-3 text-xs font-medium text-gray-900">{{ $patient->mrn }}</td>
                        <td class="px-5 py-3 text-xs text-gray-600">{{ $patient->fullName() }}</td>
                        <td class="px-5 py-3 text-xs text-gray-600 capitalize">{{ $patient->gender }}</td>
                        <td class="px-5 py-3 text-xs text-gray-600">{{ $patient->phone ?? '-' }}</td>
                        <td class="px-5 py-3 text-xs text-gray-500">{{ $patient->created_at->format('d M Y') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-5 py-8 text-center text-gray-400 text-xs">No patients in this range</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-5 py-3 border-t border-gray-100">
        {{ $patients->links() }}
    </div>
</div>
</div>
@endsection
