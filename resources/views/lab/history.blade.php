@extends('layouts.dashboard')

@section('title', 'Lab History - ' . config('app.name', 'Laravel'))
@section('page_title', 'Laboratory History')

@section('content')
<div class="space-y-6">
    @if (session('status'))
        <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm animate-fade">{{ session('status') }}</div>
    @endif

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-lg font-bold text-gray-900">Lab History</h2>
            <p class="text-sm text-gray-500">View all patients with completed lab tests and their history</p>
        </div>
        <a href="{{ route('lab.queue') }}" class="inline-flex items-center gap-2 text-sm text-gray-600 hover:text-gray-900">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Back to Queue
        </a>
    </div>

    {{-- Search --}}
    <form method="GET" action="{{ route('lab.history') }}" class="bg-white rounded-xl border border-gray-100 shadow-sm p-4">
        <div class="flex gap-2">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name, MRN, or phone..." class="flex-1 border border-gray-200 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
            <button type="submit" class="px-4 py-2.5 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700">Search</button>
            @if(request('search'))
            <a href="{{ route('lab.history') }}" class="px-4 py-2.5 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200">Clear</a>
            @endif
        </div>
    </form>

    {{-- Patients List --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50/50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Patient</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">MRN</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Phone</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Completed Tests</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($patients as $patient)
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="px-6 py-3.5">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center text-xs font-bold">
                                    {{ strtoupper(substr($patient->first_name ?: 'U', 0, 1)) }}
                                </div>
                                <span class="text-sm font-medium text-gray-900">{{ $patient->fullName() }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-3.5 text-sm text-gray-700">{{ $patient->mrn }}</td>
                        <td class="px-6 py-3.5 text-sm text-gray-700">{{ $patient->phone ?? '-' }}</td>
                        <td class="px-6 py-3.5">
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700">
                                {{ $patient->total_orders }} order(s)
                            </span>
                        </td>
                        <td class="px-6 py-3.5 text-right">
                            <a href="{{ route('lab.patient-history', $patient) }}" class="text-sm text-emerald-600 hover:text-emerald-700 font-medium">View History</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center gap-2">
                                <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6 4h6"/></svg>
                                <p class="text-sm text-gray-400">No patients with lab history found</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($patients->hasPages())
        <div class="px-6 py-4 border-t border-gray-100">
            {{ $patients->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
