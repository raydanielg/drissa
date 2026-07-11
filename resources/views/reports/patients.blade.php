@extends('layouts.dashboard')

@section('title', 'Patient Report - ' . config('app.name', 'Laravel'))
@section('page_title', 'Patient Report')

@section('content')
<div class="bg-white rounded-xl border border-gray-100 p-5 shadow-sm mb-6">
    <form method="GET" class="flex flex-wrap gap-3 items-end">
        <div>
            <label class="block text-xs text-gray-500 mb-1">From</label>
            <input type="date" name="from" value="{{ $from }}" class="border rounded-lg px-3 py-2 text-sm">
        </div>
        <div>
            <label class="block text-xs text-gray-500 mb-1">To</label>
            <input type="date" name="to" value="{{ $to }}" class="border rounded-lg px-3 py-2 text-sm">
        </div>
        <button type="submit" class="bg-emerald-600 text-white text-sm font-medium px-4 py-2 rounded-lg hover:bg-emerald-700">Filter</button>
    </form>
</div>

<div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-100">
        <h3 class="text-sm font-semibold text-gray-900">New Patients ({{ $patients->total() }})</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left">
            <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                <tr>
                    <th class="px-6 py-3">MRN</th>
                    <th class="px-6 py-3">Name</th>
                    <th class="px-6 py-3">Gender</th>
                    <th class="px-6 py-3">Phone</th>
                    <th class="px-6 py-3">Registered</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($patients as $patient)
                    <tr class="border-t border-gray-100 hover:bg-gray-50/50">
                        <td class="px-6 py-3 font-medium">{{ $patient->mrn }}</td>
                        <td class="px-6 py-3">{{ $patient->fullName() }}</td>
                        <td class="px-6 py-3 capitalize">{{ $patient->gender }}</td>
                        <td class="px-6 py-3">{{ $patient->phone ?? '-' }}</td>
                        <td class="px-6 py-3 text-gray-500">{{ $patient->created_at->format('d M Y') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-6 py-6 text-center text-gray-400">No patients in this range</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-6 py-4 border-t border-gray-100">
        {{ $patients->links() }}
    </div>
</div>
@endsection
