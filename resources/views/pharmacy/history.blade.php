@extends('layouts.dashboard')

@section('title', 'Prescription History - ' . config('app.name', 'Laravel'))
@section('page_title', 'Prescription History')

@section('content')
<div class="space-y-6">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100">
            <h3 class="text-sm font-semibold text-gray-900">Dispensed & Cancelled Prescriptions</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-xs text-gray-500 border-b border-gray-100">
                        <th class="px-5 py-3 font-medium">Date</th>
                        <th class="px-5 py-3 font-medium">Patient</th>
                        <th class="px-5 py-3 font-medium">Doctor</th>
                        <th class="px-5 py-3 font-medium">Items</th>
                        <th class="px-5 py-3 font-medium">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($prescriptions as $rx)
                        <tr class="border-b border-gray-50 hover:bg-gray-50/50">
                            <td class="px-5 py-3 text-gray-500">{{ $rx->created_at->format('M j, Y H:i') }}</td>
                            <td class="px-5 py-3 font-medium text-gray-900">{{ $rx->visit?->patient?->fullName() ?? 'Unknown' }}</td>
                            <td class="px-5 py-3 text-gray-500">{{ $rx->doctor?->name ?? '-' }}</td>
                            <td class="px-5 py-3">
                                @foreach($rx->items as $item)
                                    <div class="text-xs">{{ $item->medication?->name ?? 'Unknown' }} ×{{ $item->quantity }}</div>
                                @endforeach
                            </td>
                            <td class="px-5 py-3">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-medium {{ $rx->status === 'dispensed' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                    {{ ucfirst($rx->status) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-5 py-8 text-center text-gray-400">No history found</td></tr>
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
