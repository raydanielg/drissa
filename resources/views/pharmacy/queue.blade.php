@extends('layouts.dashboard')

@section('title', 'Pharmacy - ' . config('app.name', 'Laravel'))
@section('page_title', 'Pharmacy Queue')

@section('content')
<div class="space-y-6">
    @if (session('status'))
        <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm">{{ session('status') }}</div>
    @endif

    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <h2 class="text-sm font-semibold text-gray-900">Pending Prescriptions</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                    <tr>
                        <th class="px-6 py-3">Prescription ID</th>
                        <th class="px-6 py-3">Patient</th>
                        <th class="px-6 py-3">Items</th>
                        <th class="px-6 py-3">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($prescriptions as $prescription)
                        <tr class="border-t border-gray-100">
                            <td class="px-6 py-3">#{{ $prescription->id }}</td>
                            <td class="px-6 py-3">{{ $prescription->visit->patient->fullName() }}</td>
                            <td class="px-6 py-3">
                                <ul class="list-disc list-inside">
                                    @foreach ($prescription->items as $item)
                                        <li>{{ $item->medication->name }} x{{ $item->quantity }}</li>
                                    @endforeach
                                </ul>
                            </td>
                            <td class="px-6 py-3">
                                <form method="POST" action="{{ route('pharmacy.prescriptions.dispense', $prescription) }}" data-ajax data-confirm="Dispense these drugs?">
                                    @csrf
                                    <button type="submit" class="bg-emerald-600 text-white text-xs font-medium px-3 py-1 rounded-lg">Dispense</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="px-6 py-6 text-center text-gray-400">No pending prescriptions</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
