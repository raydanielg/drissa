@extends('layouts.dashboard')

@section('title', 'Prescription History - ' . config('app.name', 'Laravel'))
@section('page_title', 'Prescription History')

@section('content')
<div class="space-y-5">
    {{-- Header --}}
    <div class="bg-slate-800 rounded-md p-6 text-white">
        <p class="text-[10px] uppercase tracking-wider text-slate-400 mb-1">Pharmacy</p>
        <h1 class="text-xl font-bold">Prescription History</h1>
        <p class="text-xs text-slate-400 mt-1">Dispensed and cancelled prescriptions.</p>
    </div>

    <div class="bg-white rounded-md border border-gray-200 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100">
            <h3 class="text-sm font-bold text-gray-900">Dispensed & Cancelled Prescriptions</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-[10px] uppercase text-gray-400 border-b border-gray-100 bg-gray-50">
                        <th class="px-5 py-3 font-medium">Date</th>
                        <th class="px-5 py-3 font-medium">Patient</th>
                        <th class="px-5 py-3 font-medium">Doctor</th>
                        <th class="px-5 py-3 font-medium">Items</th>
                        <th class="px-5 py-3 font-medium">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($prescriptions as $rx)
                        <tr class="hover:bg-gray-50/50">
                            <td class="px-5 py-3 text-gray-500 text-xs">{{ $rx->created_at->format('M j, Y H:i') }}</td>
                            <td class="px-5 py-3 font-medium text-gray-900 text-xs">{{ $rx->visit?->patient?->fullName() ?? 'Unknown' }}</td>
                            <td class="px-5 py-3 text-gray-500 text-xs">{{ $rx->doctor?->name ?? '-' }}</td>
                            <td class="px-5 py-3">
                                @foreach($rx->items as $item)
                                    <div class="text-xs text-gray-600">{{ $item->medication?->name ?? 'Unknown' }} ×{{ $item->quantity }}</div>
                                @endforeach
                            </td>
                            <td class="px-5 py-3">
                                <span class="inline-flex items-center gap-1.5 text-[10px] font-medium text-gray-600">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $rx->status === 'dispensed' ? 'bg-emerald-500' : 'bg-red-500' }}"></span>
                                    {{ ucfirst($rx->status) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-5 py-8 text-center text-gray-400 text-xs">No history found</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($prescriptions->hasPages())
        <div class="px-5 py-3 border-t border-gray-100">
            {{ $prescriptions->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
