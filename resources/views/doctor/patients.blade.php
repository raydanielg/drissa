@extends('layouts.dashboard')

@section('title', 'My Patients - ' . config('app.name', 'Laravel'))
@section('page_title', 'My Patients')

@section('content')
<div class="space-y-6">

    {{-- Search --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
        <div class="flex items-center gap-3">
            <div class="flex-1 relative">
                <svg class="w-5 h-5 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input type="text" id="patientSearch" placeholder="Search patient by name or MRN..." class="w-full pl-10 pr-4 py-2.5 rounded-lg border border-gray-200 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all">
            </div>
        </div>
    </div>

    {{-- Patients Table --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm" id="patientsTable">
                <thead>
                    <tr class="text-left text-xs text-gray-500 border-b border-gray-100">
                        <th class="px-5 py-3 font-medium">MRN</th>
                        <th class="px-5 py-3 font-medium">Name</th>
                        <th class="px-5 py-3 font-medium">Gender</th>
                        <th class="px-5 py-3 font-medium">Phone</th>
                        <th class="px-5 py-3 font-medium">My Visits</th>
                        <th class="px-5 py-3 font-medium">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($patients as $patient)
                        <tr class="border-b border-gray-50 hover:bg-gray-50/50 patient-row" data-search="{{ strtolower($patient->fullName() . ' ' . ($patient->phone ?? '') . ' ' . $patient->mrn) }}">
                            <td class="px-5 py-3 font-medium text-gray-900">{{ $patient->mrn }}</td>
                            <td class="px-5 py-3">{{ $patient->fullName() }}</td>
                            <td class="px-5 py-3">{{ ucfirst($patient->gender ?? '-') }}</td>
                            <td class="px-5 py-3 text-gray-500">{{ $patient->phone ?? '-' }}</td>
                            <td class="px-5 py-3">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-medium bg-emerald-100 text-emerald-700">{{ $patient->my_visits }}</span>
                            </td>
                            <td class="px-5 py-3">
                                <a href="{{ route('patients.show', $patient) }}" class="text-emerald-600 hover:text-emerald-700 text-xs font-medium">View</a>
                                <a href="{{ route('patients.history', $patient) }}" class="text-sky-600 hover:text-sky-700 text-xs font-medium ml-3">History</a>
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

<script>
document.getElementById('patientSearch').addEventListener('input', function(e) {
    const term = e.target.value.toLowerCase();
    document.querySelectorAll('.patient-row').forEach(function(row) {
        row.style.display = row.dataset.search.includes(term) ? '' : 'none';
    });
});
</script>
@endsection
