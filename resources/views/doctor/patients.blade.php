@extends('layouts.dashboard')

@section('title', 'My Patients - ' . config('app.name', 'Laravel'))
@section('page_title', 'My Patients')

@section('content')
<div class="space-y-5">

    <script>
document.getElementById('patientSearch').addEventListener('input', function(e) {
    const term = e.target.value.toLowerCase();
    document.querySelectorAll('.patient-row').forEach(function(row) {
        row.style.display = row.dataset.search.includes(term) ? '' : 'none';
    });
});
</script>
    <div class="bg-slate-800 rounded-md p-6 text-white">
        <p class="text-[10px] uppercase tracking-wider text-slate-400 mb-1">Doctor</p>
        <h1 class="text-xl font-bold">My Patients</h1>
        <p class="text-xs text-slate-400 mt-1">Patients you have attended.</p>
    </div>

    <script>
document.getElementById('patientSearch').addEventListener('input', function(e) {
    const term = e.target.value.toLowerCase();
    document.querySelectorAll('.patient-row').forEach(function(row) {
        row.style.display = row.dataset.search.includes(term) ? '' : 'none';
    });
});
</script>
    <div class="bg-white rounded-md border border-gray-200 p-4">
        <div class="flex items-center gap-3">
            <div class="flex-1 relative">
                <i class="hgi-stroke hgi-search-01 text-base text-gray-400 absolute left-3 top-1/2 -translate-y-1/2"></i>
                <x-ui.input type="text" id="patientSearch" placeholder="Search patient by name or MRN..." class="w-full pl-10 pr-4 py-2 rounded border border-gray-200 text-sm focus:ring-2 focus:ring-slate-300 outline-none transition-all" />
            </div>
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
    <div class="bg-white rounded-md border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm" id="patientsTable">
                <thead>
                    <tr class="text-left text-[10px] uppercase text-gray-400 border-b border-gray-100 bg-gray-50">
                        <th class="px-5 py-3 font-medium">MRN</th>
                        <th class="px-5 py-3 font-medium">Name</th>
                        <th class="px-5 py-3 font-medium">Gender</th>
                        <th class="px-5 py-3 font-medium">Phone</th>
                        <th class="px-5 py-3 font-medium">My Visits</th>
                        <th class="px-5 py-3 font-medium">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($patients as $patient)
                        <tr class="hover:bg-gray-50/50 patient-row" data-search="{{ strtolower($patient->fullName() . ' ' . ($patient->phone ?? '') . ' ' . $patient->mrn) }}">
                            <td class="px-5 py-3 font-medium text-gray-900 text-xs">{{ $patient->mrn }}</td>
                            <td class="px-5 py-3 text-xs text-gray-600">{{ $patient->fullName() }}</td>
                            <td class="px-5 py-3 text-xs text-gray-600">{{ ucfirst($patient->gender ?? '-') }}</td>
                            <td class="px-5 py-3 text-gray-500 text-xs">{{ $patient->phone ?? '-' }}</td>
                            <td class="px-5 py-3 text-xs font-semibold text-gray-900">{{ $patient->my_visits }}</td>
                            <td class="px-5 py-3">
                                <a href="{{ route('patients.show', $patient) }}" class="text-[10px] text-slate-600 hover:text-slate-800 font-medium">View</a>
                                <a href="{{ route('patients.history', $patient) }}" class="text-[10px] text-slate-600 hover:text-slate-800 font-medium ml-3">History</a>
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

<script>
document.getElementById('patientSearch').addEventListener('input', function(e) {
    const term = e.target.value.toLowerCase();
    document.querySelectorAll('.patient-row').forEach(function(row) {
        row.style.display = row.dataset.search.includes(term) ? '' : 'none';
    });
});
</script>
@endsection
