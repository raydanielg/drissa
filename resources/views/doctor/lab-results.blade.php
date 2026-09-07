@extends('layouts.dashboard')

@section('title', 'Lab Results - ' . config('app.name', 'Laravel'))
@section('page_title', 'Lab Results')

@section('content')
<div class="space-y-6">
    @if (session('status'))
        <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm">{{ session('status') }}</div>
    @endif

    @php
        $flagStyles = [
            'normal' => ['bg' => 'bg-emerald-50', 'text' => 'text-emerald-700', 'border' => 'border-emerald-200', 'icon' => '🟢', 'label' => 'Normal'],
            'high' => ['bg' => 'bg-amber-50', 'text' => 'text-amber-700', 'border' => 'border-amber-200', 'icon' => '🟡', 'label' => 'High'],
            'low' => ['bg' => 'bg-amber-50', 'text' => 'text-amber-700', 'border' => 'border-amber-200', 'icon' => '🟡', 'label' => 'Low'],
            'critical' => ['bg' => 'bg-red-50', 'text' => 'text-red-700', 'border' => 'border-red-200', 'icon' => '🔴', 'label' => 'Critical'],
        ];
    @endphp

    @forelse ($visits as $visit)
        @php
            $statusClass = match($visit->status) {
                \App\Enums\VisitStatus::WaitingForLab->value => 'bg-amber-100 text-amber-700',
                \App\Enums\VisitStatus::InLab->value => 'bg-sky-100 text-sky-700',
                \App\Enums\VisitStatus::LabCompleted->value => 'bg-emerald-100 text-emerald-700',
                \App\Enums\VisitStatus::WaitingForUltrasound->value => 'bg-amber-100 text-amber-700',
                \App\Enums\VisitStatus::InUltrasound->value => 'bg-sky-100 text-sky-700',
                \App\Enums\VisitStatus::UltrasoundCompleted->value => 'bg-emerald-100 text-emerald-700',
                default => 'bg-gray-100 text-gray-700',
            };
        @endphp

        {{-- Patient Card --}}
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
            {{-- Patient Header --}}
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between bg-gradient-to-r from-emerald-50/50 to-transparent">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-emerald-100 flex items-center justify-center">
                        <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-gray-900">{{ $visit->patient->fullName() }}</h3>
                        <p class="text-xs text-gray-500">{{ $visit->visit_number }} • {{ $visit->patient->phone ?? 'No phone' }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('doctor.patients.history', $visit->patient) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-sky-50 hover:bg-sky-100 text-sky-700 text-xs font-semibold rounded-lg transition-colors">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Full History
                    </a>
                    <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-medium {{ $statusClass }}">
                        {{ str_replace('_', ' ', $visit->status) }}
                    </span>
                    @if ($visit->status === \App\Enums\VisitStatus::LabCompleted->value)
                        <form method="POST" action="{{ route('doctor.visits.lab-return', $visit) }}" class="inline">
                            @csrf
                            <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-medium rounded-lg transition-colors">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
                                Receive & Review
                            </button>
                        </form>
                    @endif
                    @if ($visit->status === \App\Enums\VisitStatus::UltrasoundCompleted->value)
                        <form method="POST" action="{{ route('doctor.visits.lab-return', $visit) }}" class="inline">
                            @csrf
                            <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-medium rounded-lg transition-colors">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
                                Receive & Review
                            </button>
                        </form>
                    @endif
                </div>
            </div>

            {{-- Lab Orders --}}
            <div class="divide-y divide-gray-100">
                @forelse ($visit->labOrders as $order)
                    <div class="p-5">
                        {{-- Order Header --}}
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center gap-2">
                                <span class="text-xs font-bold text-gray-700">Order #{{ $order->id }}</span>
                                <span class="text-xs text-gray-400">•</span>
                                <span class="text-xs text-gray-500">{{ $order->created_at->format('M d, Y H:i') }}</span>
                                @if($order->status === 'completed')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-emerald-100 text-emerald-700">Completed</span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-amber-100 text-amber-700">{{ ucfirst($order->status) }}</span>
                                @endif
                            </div>
                            @if($order->status === 'completed')
                                <a href="{{ route('lab.orders.show', $order) }}" target="_blank" class="text-xs text-emerald-600 hover:text-emerald-700 font-medium inline-flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    Full Report
                                </a>
                            @endif
                        </div>

                        {{-- Tests List --}}
                        <div class="flex flex-wrap gap-1.5 mb-4">
                            @foreach ($order->items as $item)
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-violet-50 text-violet-700 border border-violet-100">{{ $item->labTest?->name ?? 'Unknown' }}</span>
                            @endforeach
                        </div>

                        {{-- Results --}}
                        @if ($order->results->isNotEmpty())
                            @php
                                $groupedResults = $order->results->groupBy('lab_order_item_id');
                            @endphp
                            @foreach ($groupedResults as $itemId => $results)
                                @php
                                    $testItem = $order->items->firstWhere('id', $itemId);
                                    $abnormalCount = $results->whereNotIn('flag', ['normal'])->count();
                                @endphp
                                <div class="border border-gray-200 rounded-xl overflow-hidden mb-3 last:mb-0">
                                    {{-- Test Name Header --}}
                                    <div class="px-4 py-2.5 bg-gray-50/80 border-b border-gray-200 flex items-center justify-between">
                                        <span class="text-sm font-semibold text-gray-800">{{ $testItem?->labTest?->name ?? 'Unknown Test' }}</span>
                                        @if($abnormalCount > 0)
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-amber-100 text-amber-700">
                                                {{ $abnormalCount }} Abnormal
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-100 text-emerald-700">
                                                All Normal
                                            </span>
                                        @endif
                                    </div>
                                    {{-- Results Table --}}
                                    <table class="w-full text-sm text-left">
                                        <thead class="bg-white text-xs text-gray-500">
                                            <tr>
                                                <th class="px-4 py-2 font-medium">Parameter</th>
                                                <th class="px-4 py-2 font-medium">Result</th>
                                                <th class="px-4 py-2 font-medium">Unit</th>
                                                <th class="px-4 py-2 font-medium">Ref Range</th>
                                                <th class="px-4 py-2 font-medium">Flag</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-100">
                                            @foreach ($results as $result)
                                                @php $style = $flagStyles[$result->flag] ?? $flagStyles['normal']; @endphp
                                                <tr>
                                                    <td class="px-4 py-2.5 font-medium text-gray-800">{{ $result->parameter }}</td>
                                                    <td class="px-4 py-2.5">
                                                        <span class="font-bold {{ $result->flag === 'critical' ? 'text-red-600' : ($result->flag !== 'normal' ? 'text-amber-600' : 'text-gray-900') }}">{{ $result->value }}</span>
                                                    </td>
                                                    <td class="px-4 py-2.5 text-gray-600 text-xs">{{ $result->unit ?? '-' }}</td>
                                                    <td class="px-4 py-2.5 text-gray-500 text-xs">{{ $result->reference_range ?? '-' }}</td>
                                                    <td class="px-4 py-2.5">
                                                        <span class="inline-flex items-center gap-0.5 px-1.5 py-0.5 rounded-full text-[10px] font-medium {{ $style['bg'] }} {{ $style['text'] }} border {{ $style['border'] }}">
                                                            {{ $style['icon'] }} {{ $style['label'] }}
                                                        </span>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endforeach
                        @elseif ($order->status !== 'completed')
                            <div class="flex items-center gap-2 text-xs text-amber-600 bg-amber-50 px-3 py-2 rounded-lg">
                                <svg class="w-3.5 h-3.5 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                Waiting for lab results...
                            </div>
                        @endif

                        {{-- Attachments --}}
                        @if ($order->attachments->isNotEmpty())
                            <div class="mt-3 flex flex-wrap gap-2">
                                @foreach ($order->attachments as $attachment)
                                    <a href="{{ asset('storage/' . $attachment->file_path) }}" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-50 hover:bg-blue-100 text-blue-700 rounded-lg text-xs font-medium transition-colors">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                        {{ $attachment->file_name }}
                                    </a>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="p-5 text-center text-sm text-gray-400">No lab orders found.</div>
                @endforelse
            </div>

            {{-- Prescription Form (if with doctor) --}}
            @if ($visit->status === \App\Enums\VisitStatus::WithDoctor->value)
                <div class="px-5 pb-5">
                    <form method="POST" action="{{ route('doctor.visits.prescribe', $visit) }}" class="border rounded-xl p-4 bg-gray-50/50">
                        @csrf
                        <p class="text-xs font-semibold text-gray-700 mb-3 flex items-center gap-2">
                            <svg class="w-4 h-4 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>
                            Write Prescription
                        </p>
                        <div class="space-y-2" id="prescription-items-{{ $visit->id }}">
                            <div class="grid grid-cols-6 gap-2">
                                <select name="items[0][medication_id]" class="col-span-2 border rounded-lg px-2 py-1.5 text-xs" required>
                                    <option value="">Drug</option>
                                    @foreach ($medications as $med)
                                        <option value="{{ $med->id }}">{{ $med->name }}</option>
                                    @endforeach
                                </select>
                                <input type="number" name="items[0][quantity]" placeholder="Qty" class="border rounded-lg px-2 py-1.5 text-xs" required>
                                <input type="text" name="items[0][dosage]" placeholder="Dose" class="border rounded-lg px-2 py-1.5 text-xs" required>
                                <input type="text" name="items[0][frequency]" placeholder="Freq" class="border rounded-lg px-2 py-1.5 text-xs" required>
                                <input type="text" name="items[0][duration]" placeholder="Duration" class="border rounded-lg px-2 py-1.5 text-xs" required>
                            </div>
                        </div>
                        <textarea name="items[0][instructions]" placeholder="Instructions (optional)" class="w-full border rounded-lg px-2 py-1.5 text-xs mt-2" rows="2"></textarea>
                        <button type="submit" class="w-full bg-violet-500 hover:bg-violet-600 text-white text-xs font-medium px-3 py-2 rounded-lg mt-2 flex items-center justify-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Send to Pharmacy
                        </button>
                    </form>
                </div>
            @endif
        </div>
    @empty
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-12 text-center">
            <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>
            <p class="text-sm text-gray-400">No patients in lab queue</p>
        </div>
    @endforelse
</div>
@endsection
