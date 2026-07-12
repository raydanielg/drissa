@extends('layouts.dashboard')

@section('title', 'Lab Results - ' . config('app.name', 'Laravel'))
@section('page_title', 'Lab Results')

@section('content')
<div class="space-y-6">
    @if (session('status'))
        <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm">{{ session('status') }}</div>
    @endif

    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <h2 class="text-sm font-semibold text-gray-900">Patients Sent to Lab</h2>
            <p class="text-xs text-gray-500 mt-1">Track lab orders, review completed results, and continue treatment.</p>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                    <tr>
                        <th class="px-6 py-3">Visit #</th>
                        <th class="px-6 py-3">Patient</th>
                        <th class="px-6 py-3">Status</th>
                        <th class="px-6 py-3">Lab Tests</th>
                        <th class="px-6 py-3">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($visits as $visit)
                        <tr class="border-t border-gray-100">
                            <td class="px-6 py-3 align-top">{{ $visit->visit_number }}</td>
                            <td class="px-6 py-3 align-top">
                                <div class="font-medium text-gray-900">{{ $visit->patient->fullName() }}</div>
                                <div class="text-xs text-gray-500">{{ $visit->patient->phone ?? 'No phone' }}</div>
                            </td>
                            <td class="px-6 py-3 align-top">
                                @php
                                    $statusClass = match($visit->status) {
                                        \App\Enums\VisitStatus::WaitingForLab->value => 'bg-amber-100 text-amber-700',
                                        \App\Enums\VisitStatus::InLab->value => 'bg-sky-100 text-sky-700',
                                        \App\Enums\VisitStatus::LabCompleted->value => 'bg-emerald-100 text-emerald-700',
                                        default => 'bg-gray-100 text-gray-700',
                                    };
                                @endphp
                                <span class="inline-flex px-2 py-1 rounded-full text-xs font-medium {{ $statusClass }}">
                                    {{ str_replace('_', ' ', $visit->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-3 align-top">
                                @forelse ($visit->labOrders as $order)
                                    <div class="mb-3 last:mb-0 border-l-2 border-emerald-200 pl-3">
                                        <p class="text-xs font-medium text-gray-700">Order #{{ $order->id }}</p>
                                        <ul class="text-xs text-gray-600 list-disc list-inside">
                                            @foreach ($order->items as $item)
                                                <li>{{ $item->labTest->name ?? 'Unknown test' }}</li>
                                            @endforeach
                                        </ul>

                                        @if ($order->results->isNotEmpty())
                                            <div class="mt-2 space-y-1">
                                                @foreach ($order->results as $result)
                                                    <div class="flex items-center gap-2 text-xs">
                                                        <span class="font-medium text-gray-700">{{ $result->parameter }}:</span>
                                                        <span class="text-gray-900">{{ $result->value }} {{ $result->unit }}</span>
                                                        @php
                                                            $flagClass = match($result->flag) {
                                                                'normal' => 'bg-emerald-100 text-emerald-700',
                                                                'high', 'low' => 'bg-amber-100 text-amber-700',
                                                                'critical' => 'bg-red-100 text-red-700',
                                                                default => 'bg-gray-100 text-gray-700',
                                                            };
                                                        @endphp
                                                        <span class="px-1.5 py-0.5 rounded text-[10px] font-medium {{ $flagClass }}">{{ ucfirst($result->flag) }}</span>
                                                        @if ($result->reference_range)
                                                            <span class="text-gray-400">Ref: {{ $result->reference_range }}</span>
                                                        @endif
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif

                                        @if ($order->attachments->isNotEmpty())
                                            <div class="mt-2 flex flex-wrap gap-2">
                                                @foreach ($order->attachments as $attachment)
                                                    <a href="{{ asset('storage/' . $attachment->file_path) }}" target="_blank" class="inline-flex items-center gap-1 px-2 py-1 bg-gray-100 hover:bg-gray-200 rounded text-xs text-gray-700">
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                                        Report
                                                    </a>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                @empty
                                    <span class="text-xs text-gray-400">No lab orders found.</span>
                                @endforelse
                            </td>
                            <td class="px-6 py-3 align-top space-y-3 min-w-[220px]">
                                @if ($visit->status === \App\Enums\VisitStatus::LabCompleted->value)
                                    <form method="POST" action="{{ route('doctor.visits.lab-return', $visit) }}">
                                        @csrf
                                        <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-medium px-3 py-2 rounded-lg flex items-center justify-center gap-1">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
                                            Receive & Review
                                        </button>
                                    </form>
                                @endif

                                @if ($visit->status === \App\Enums\VisitStatus::WithDoctor->value)
                                    {{-- Prescription Form --}}
                                    <form method="POST" action="{{ route('doctor.visits.prescribe', $visit) }}" class="border rounded-lg p-3 bg-gray-50">
                                        @csrf
                                        <p class="text-xs font-medium text-gray-700 mb-2">Write Prescription</p>
                                        <div class="space-y-2" id="prescription-items-{{ $visit->id }}">
                                            <div class="grid grid-cols-6 gap-2">
                                                <select name="items[0][medication_id]" class="col-span-2 border rounded-lg px-2 py-1 text-xs" required>
                                                    <option value="">Drug</option>
                                                    @foreach ($medications as $med)
                                                        <option value="{{ $med->id }}">{{ $med->name }}</option>
                                                    @endforeach
                                                </select>
                                                <input type="number" name="items[0][quantity]" placeholder="Qty" class="border rounded-lg px-2 py-1 text-xs" required>
                                                <input type="text" name="items[0][dosage]" placeholder="Dose" class="border rounded-lg px-2 py-1 text-xs" required>
                                                <input type="text" name="items[0][frequency]" placeholder="Freq" class="border rounded-lg px-2 py-1 text-xs" required>
                                                <input type="text" name="items[0][duration]" placeholder="Duration" class="border rounded-lg px-2 py-1 text-xs" required>
                                            </div>
                                        </div>
                                        <textarea name="items[0][instructions]" placeholder="Instructions (optional)" class="w-full border rounded-lg px-2 py-1 text-xs mt-2" rows="2"></textarea>
                                        <button type="submit" class="w-full bg-violet-500 hover:bg-violet-600 text-white text-xs font-medium px-3 py-2 rounded-lg mt-2 flex items-center justify-center gap-1">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>
                                            Send to Pharmacy
                                        </button>
                                    </form>
                                @endif

                                @if (in_array($visit->status, [\App\Enums\VisitStatus::WaitingForLab->value, \App\Enums\VisitStatus::InLab->value]))
                                    <span class="inline-flex items-center gap-1 text-xs text-amber-600 bg-amber-50 px-2 py-1.5 rounded-lg">
                                        <svg class="w-3.5 h-3.5 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                        Waiting for lab results
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-6 py-6 text-center text-gray-400">No patients in lab queue</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
