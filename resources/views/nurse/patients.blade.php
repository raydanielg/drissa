@extends('layouts.dashboard')

@section('title', 'Patients - ' . config('app.name', 'Laravel'))
@section('page_title', 'Patients')

@section('content')
<div class="space-y-5">

    {{-- Header --}}
    <div class="bg-slate-800 rounded-md p-6 text-white">
        <p class="text-[10px] uppercase tracking-wider text-slate-400 mb-1">Nursing</p>
        <h1 class="text-xl font-bold">Patients</h1>
        <p class="text-xs text-slate-400 mt-1">Registered patient list.</p>
    </div>

    <div class="bg-white rounded-md border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-[10px] uppercase text-gray-400 border-b border-gray-100 bg-gray-50">
                        <th class="px-5 py-3 font-medium">MRN</th>
                        <th class="px-5 py-3 font-medium">Name</th>
                        <th class="px-5 py-3 font-medium">Gender</th>
                        <th class="px-5 py-3 font-medium">Phone</th>
                        <th class="px-5 py-3 font-medium">Visits</th>
                        <th class="px-5 py-3 font-medium">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($patients as $patient)
                        <tr class="hover:bg-gray-50/50">
                            <td class="px-5 py-3 font-medium text-gray-900 text-xs">{{ $patient->mrn }}</td>
                            <td class="px-5 py-3 text-xs text-gray-600">{{ $patient->fullName() }}</td>
                            <td class="px-5 py-3 text-xs text-gray-600">{{ ucfirst($patient->gender ?? '-') }}</td>
                            <td class="px-5 py-3 text-gray-500 text-xs">{{ $patient->phone ?? '-' }}</td>
                            <td class="px-5 py-3 text-xs font-semibold text-gray-900">{{ $patient->visits_count }}</td>
                            <td class="px-5 py-3">
                                <a href="{{ route('patients.show', $patient) }}" class="text-[10px] text-slate-600 hover:text-slate-800 font-medium">View</a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-5 py-8 text-center text-gray-400 text-xs">No patients found</td></tr>
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
