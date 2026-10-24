@extends('layouts.dashboard')

@section('title', 'Pharmacy - ' . config('app.name', 'Laravel'))
@section('page_title', 'Pharmacy Queue')

@section('content')
<div class="space-y-5">
    @if (session('status'))
        <div class="p-3 rounded-md bg-white border-l-2 border-emerald-500 text-emerald-700 text-xs">{{ session('status') }}</div>
    @endif

    {{-- Header --}}
    <div class="bg-slate-800 rounded-md p-6 text-white">
        <div>
            <p class="text-[10px] uppercase tracking-wider text-slate-400 mb-1">Pharmacy</p>
            <h1 class="text-xl font-bold">Pharmacy Queue</h1>
            <p class="text-xs text-slate-400 mt-1">Pending prescriptions to dispense.</p>
        </div>
    </div>

    <div class="bg-white rounded-md border border-gray-200 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100">
            <h2 class="text-sm font-bold text-gray-900">Pending Prescriptions</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-50 text-[10px] uppercase text-gray-400">
                    <tr>
                        <th class="px-5 py-3">Prescription ID</th>
                        <th class="px-5 py-3">Patient</th>
                        <th class="px-5 py-3">Items</th>
                        <th class="px-5 py-3 text-right">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($prescriptions as $prescription)
                        <tr class="border-t border-gray-50 hover:bg-gray-50/50 transition-colors">
                            <td class="px-5 py-3 text-xs font-semibold text-gray-900">#{{ $prescription->id }}</td>
                            <td class="px-5 py-3 text-xs text-gray-700">{{ $prescription->visit->patient->fullName() }}</td>
                            <td class="px-5 py-3">
                                <ul class="list-disc list-inside text-xs text-gray-600">
                                    @foreach ($prescription->items as $item)
                                        <li>{{ $item->medication->name }} x{{ $item->quantity }}</li>
                                    @endforeach
                                </ul>
                            </td>
                            <td class="px-5 py-3 text-right">
                                <form method="POST" action="{{ route('pharmacy.prescriptions.dispense', $prescription) }}" data-ajax data-confirm="Dispense these drugs?">
                                    @csrf
                                    <button type="submit" class="bg-slate-800 hover:bg-slate-700 text-white text-xs font-medium px-3 py-1.5 rounded transition-colors">Dispense</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="px-5 py-8 text-center text-gray-400 text-xs">No pending prescriptions</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
