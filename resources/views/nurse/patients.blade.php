@extends('layouts.dashboard')

@section('title', 'Patients - ' . config('app.name', 'Laravel'))
@section('page_title', 'Patients')

@section('content')
<div class="space-y-6">

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-xs text-gray-500 border-b border-gray-100">
                        <th class="px-5 py-3 font-medium">MRN</th>
                        <th class="px-5 py-3 font-medium">Name</th>
                        <th class="px-5 py-3 font-medium">Gender</th>
                        <th class="px-5 py-3 font-medium">Phone</th>
                        <th class="px-5 py-3 font-medium">Visits</th>
                        <th class="px-5 py-3 font-medium">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($patients as $patient)
                        <tr class="border-b border-gray-50 hover:bg-gray-50/50">
                            <td class="px-5 py-3 font-medium text-gray-900">{{ $patient->mrn }}</td>
                            <td class="px-5 py-3">{{ $patient->fullName() }}</td>
                            <td class="px-5 py-3">{{ ucfirst($patient->gender ?? '-') }}</td>
                            <td class="px-5 py-3 text-gray-500">{{ $patient->phone ?? '-' }}</td>
                            <td class="px-5 py-3">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-medium bg-emerald-100 text-emerald-700">{{ $patient->visits_count }}</span>
                            </td>
                            <td class="px-5 py-3">
                                <a href="{{ route('patients.show', $patient) }}" class="text-emerald-600 hover:text-emerald-700 text-xs font-medium">View</a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-5 py-8 text-center text-gray-400">No patients found</td></tr>
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
