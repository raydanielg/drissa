@extends('layouts.dashboard')

@section('title', 'Lab History - ' . config('app.name', 'Laravel'))
@section('page_title', 'Laboratory History')

@section('content')
<div class="space-y-5">
    @if (session('status'))
        <div class="p-3 rounded-md bg-white border-l-2 border-emerald-500 text-emerald-700 text-xs">{{ session('status') }}</div>
    @endif

    {{-- Patients List --}}
    <div class="bg-slate-800 rounded-md p-6 text-white">
        <div class="flex flex-wrap items-end justify-between gap-3">
            <div>
                <p class="text-[10px] uppercase tracking-wider text-slate-400 mb-1">Laboratory</p>
                <h1 class="text-xl font-bold">Lab History</h1>
                <p class="text-xs text-slate-400 mt-1">View all patients with completed lab tests and their history</p>
            </div>
            <a href="{{ route('lab.queue') }}" class="inline-flex items-center gap-1.5 bg-white hover:bg-gray-100 text-slate-800 text-xs font-semibold px-3 py-1.5 rounded transition-colors">
                <i class="hgi-stroke hgi-arrow-left-02 text-sm"></i>
                Back to Queue
            </a>
        </div>
    </div>

    {{-- Patients List --}}
    <form method="GET" action="{{ route('lab.history') }}" class="bg-white rounded-md border border-gray-200 p-4">
        <div class="flex gap-2">
            <x-ui.input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name, MRN, or phone..." class="flex-1 border border-gray-200 rounded px-4 py-2 text-sm focus:ring-2 focus:ring-slate-300" />
            <button type="submit" class="px-4 py-2 bg-slate-800 text-white text-sm font-medium rounded hover:bg-slate-700">Search</button>
            @if(request('search'))
            <a href="{{ route('lab.history') }}" class="px-4 py-2 border border-gray-200 text-gray-700 text-sm font-medium rounded hover:bg-gray-100">Clear</a>
            @endif
        </div>
    </form>

    {{-- Patients List --}}
    <div class="bg-white rounded-md border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-5 py-3 text-left text-[10px] font-medium text-gray-400 uppercase">Patient</th>
                        <th class="px-5 py-3 text-left text-[10px] font-medium text-gray-400 uppercase">MRN</th>
                        <th class="px-5 py-3 text-left text-[10px] font-medium text-gray-400 uppercase">Phone</th>
                        <th class="px-5 py-3 text-left text-[10px] font-medium text-gray-400 uppercase">Completed Tests</th>
                        <th class="px-5 py-3 text-right text-[10px] font-medium text-gray-400 uppercase">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($patients as $patient)
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="px-5 py-3">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-slate-100 text-slate-600 flex items-center justify-center text-xs font-bold">
                                    {{ strtoupper(substr($patient->first_name ?: 'U', 0, 1)) }}
                                </div>
                                <span class="text-xs font-medium text-gray-900">{{ $patient->fullName() }}</span>
                            </div>
                        </td>
                        <td class="px-5 py-3 text-xs text-gray-600">{{ $patient->mrn }}</td>
                        <td class="px-5 py-3 text-xs text-gray-600">{{ $patient->phone ?? '-' }}</td>
                        <td class="px-5 py-3">
                            <span class="inline-flex items-center gap-1.5 text-[10px] font-medium text-gray-600">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                {{ $patient->total_orders }} order(s)
                            </span>
                        </td>
                        <td class="px-5 py-3 text-right">
                            <a href="{{ route('lab.patient-history', $patient) }}" class="text-[10px] text-slate-600 hover:text-slate-800 font-medium">View History</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-5 py-8 text-center">
                            <div class="flex flex-col items-center gap-2">
                                <i class="hgi-stroke hgi-file-01 text-4xl text-gray-300"></i>
                                <p class="text-xs text-gray-400">No patients with lab history found</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($patients->hasPages())
        <div class="px-5 py-3 border-t border-gray-100">
            {{ $patients->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
