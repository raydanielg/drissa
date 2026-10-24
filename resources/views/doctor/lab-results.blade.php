@extends('layouts.dashboard')

@section('title', 'Results - ' . config('app.name', 'Laravel'))
@section('page_title', 'Lab & Ultrasound Results')

@section('content')
<div class="space-y-5">
    @if (session('status'))
        <div class="p-3 rounded-md bg-white border-l-2 border-emerald-500 text-emerald-700 text-xs">{{ session('status') }}</div>
    @endif

    {{-- Ultrasound Orders --}}
    <div class="bg-slate-800 rounded-md p-6 text-white">
        <p class="text-[10px] uppercase tracking-wider text-slate-400 mb-1">Doctor</p>
        <h1 class="text-xl font-bold">Lab & Ultrasound Results</h1>
        <p class="text-xs text-slate-400 mt-1">Results awaiting review and completed orders.</p>
    </div>

    @php
        $flagStyles = [
            'normal' => ['dot' => 'bg-emerald-500', 'label' => 'Normal'],
            'high' => ['dot' => 'bg-amber-500', 'label' => 'High'],
            'low' => ['dot' => 'bg-amber-500', 'label' => 'Low'],
            'critical' => ['dot' => 'bg-red-500', 'label' => 'Critical'],
        ];
    @endphp

    @php
        $labStatuses = [
            \App\Enums\VisitStatus::WaitingForLab->value,
            \App\Enums\VisitStatus::InLab->value,
            \App\Enums\VisitStatus::LabCompleted->value,
        ];
        $ultrasoundStatuses = [
            \App\Enums\VisitStatus::WaitingForUltrasound->value,
            \App\Enums\VisitStatus::InUltrasound->value,
            \App\Enums\VisitStatus::UltrasoundCompleted->value,
        ];
        $filter = request('filter', 'all');
        $filteredVisits = $visits->filter(function($visit) use ($filter, $labStatuses, $ultrasoundStatuses) {
            if ($filter === 'lab') return in_array($visit->status, $labStatuses);
            if ($filter === 'ultrasound') return in_array($visit->status, $ultrasoundStatuses);
            return true;
        });
    @endphp

    {{-- Ultrasound Orders --}}
    <div class="flex items-center gap-2">
        <a href="{{ route('doctor.lab-results') }}" class="px-4 py-2 text-sm font-medium rounded transition-colors {{ $filter === 'all' ? 'bg-slate-800 text-white' : 'bg-white border border-gray-200 text-gray-700 hover:bg-gray-100' }}">
            All ({{ $visits->count() }})
        </a>
        <a href="{{ route('doctor.lab-results', ['filter' => 'lab']) }}" class="px-4 py-2 text-sm font-medium rounded transition-colors {{ $filter === 'lab' ? 'bg-slate-800 text-white' : 'bg-white border border-gray-200 text-gray-700 hover:bg-gray-100' }}">
            Lab ({{ $visits->filter(fn($v) => in_array($v->status, $labStatuses))->count() }})
        </a>
        <a href="{{ route('doctor.lab-results', ['filter' => 'ultrasound']) }}" class="px-4 py-2 text-sm font-medium rounded transition-colors {{ $filter === 'ultrasound' ? 'bg-slate-800 text-white' : 'bg-white border border-gray-200 text-gray-700 hover:bg-gray-100' }}">
            Ultrasound ({{ $visits->filter(fn($v) => in_array($v->status, $ultrasoundStatuses))->count() }})
        </a>
    </div>

    @forelse ($filteredVisits as $visit)
        @php
            $statusDot = match($visit->status) {
                \App\Enums\VisitStatus::WaitingForLab->value, \App\Enums\VisitStatus::WaitingForUltrasound->value => 'bg-amber-500',
                \App\Enums\VisitStatus::InLab->value, \App\Enums\VisitStatus::InUltrasound->value => 'bg-slate-500',
                \App\Enums\VisitStatus::LabCompleted->value, \App\Enums\VisitStatus::UltrasoundCompleted->value => 'bg-emerald-500',
                default => 'bg-slate-400',
            };
        @endphp

        {{-- Ultrasound Orders --}}
        <div class="bg-white rounded-md border border-gray-200 overflow-hidden">
            {{-- Ultrasound Orders --}}
            <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded bg-slate-100 flex items-center justify-center">
                        <i class="hgi-stroke hgi-user text-xl text-slate-600"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-gray-900">{{ $visit->patient->fullName() }}</h3>
                        <p class="text-xs text-gray-500">{{ $visit->visit_number }} • {{ $visit->patient->phone ?? 'No phone' }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('doctor.patients.history', $visit->patient) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 border border-gray-200 hover:bg-gray-100 text-gray-700 text-xs font-medium rounded transition-colors">
                        <i class="hgi-stroke hgi-clock-01 text-sm"></i>
                        Full History
                    </a>
                    <span class="inline-flex items-center gap-1.5 text-[10px] font-medium text-gray-600 capitalize">
                        <span class="w-1.5 h-1.5 rounded-full {{ $statusDot }}"></span>
                        {{ str_replace('_', ' ', $visit->status) }}
                    </span>
                    @if ($visit->status === \App\Enums\VisitStatus::LabCompleted->value)
                        <form method="POST" action="{{ route('doctor.visits.lab-return', $visit) }}" class="inline">
                            @csrf
                            <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-slate-800 hover:bg-slate-700 text-white text-xs font-medium rounded transition-colors">
                                <i class="hgi-stroke hgi-login-02 text-sm"></i>
                                Receive & Review
                            </button>
                        </form>
                    @endif
                    @if ($visit->status === \App\Enums\VisitStatus::UltrasoundCompleted->value)
                        <form method="POST" action="{{ route('doctor.visits.lab-return', $visit) }}" class="inline">
                            @csrf
                            <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-slate-800 hover:bg-slate-700 text-white text-xs font-medium rounded transition-colors">
                                <i class="hgi-stroke hgi-login-02 text-sm"></i>
                                Receive & Review
                            </button>
                        </form>
                    @endif
                </div>
            </div>

            {{-- Ultrasound Orders --}}
            <div class="divide-y divide-gray-100">
                @forelse ($visit->labOrders as $order)
                    <div class="p-5">
                        {{-- Ultrasound Orders --}}
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center gap-2">
                                <span class="text-xs font-bold text-gray-700">Order #{{ $order->id }}</span>
                                <span class="text-xs text-gray-400">•</span>
                                <span class="text-xs text-gray-500">{{ $order->created_at->format('M d, Y H:i') }}</span>
                                @if($order->status === 'completed')
                                    <span class="inline-flex items-center gap-1 text-[10px] font-medium text-gray-600"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>Completed</span>
                                @else
                                    <span class="inline-flex items-center gap-1 text-[10px] font-medium text-gray-600"><span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>{{ ucfirst($order->status) }}</span>
                                @endif
                            </div>
                            @if($order->status === 'completed')
                                <a href="{{ route('lab.orders.show', $order) }}" target="_blank" class="text-[10px] text-slate-600 hover:text-slate-800 font-medium inline-flex items-center gap-1">
                                    <i class="hgi-stroke hgi-view text-sm"></i>
                                    Full Report
                                </a>
                            @endif
                        </div>

                        {{-- Ultrasound Orders --}}
                        <div class="flex flex-wrap gap-1.5 mb-4">
                            @foreach ($order->items as $item)
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium bg-gray-100 text-gray-700 border border-gray-200">{{ $item->labTest?->name ?? 'Unknown' }}</span>
                            @endforeach
                        </div>

                        {{-- Ultrasound Orders --}}
                        @if ($order->results->isNotEmpty())
                            @php
                                $groupedResults = $order->results->groupBy('lab_order_item_id');
                            @endphp
                            @foreach ($groupedResults as $itemId => $results)
                                @php
                                    $testItem = $order->items->firstWhere('id', $itemId);
                                    $abnormalCount = $results->whereNotIn('flag', ['normal'])->count();
                                @endphp
                                <div class="border border-gray-200 rounded overflow-hidden mb-3 last:mb-0">
                                    {{-- Ultrasound Orders --}}
                                    <div class="px-4 py-2.5 bg-gray-50 border-b border-gray-200 flex items-center justify-between">
                                        <span class="text-xs font-semibold text-gray-800">{{ $testItem?->labTest?->name ?? 'Unknown Test' }}</span>
                                        @if($abnormalCount > 0)
                                            <span class="inline-flex items-center gap-1 text-[10px] font-medium text-gray-600"><span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>{{ $abnormalCount }} Abnormal</span>
                                        @else
                                            <span class="inline-flex items-center gap-1 text-[10px] font-medium text-gray-600"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>All Normal</span>
                                        @endif
                                    </div>
                                    {{-- Ultrasound Orders --}}
                                    <table class="w-full text-sm text-left">
                                        <thead class="bg-white text-[10px] uppercase text-gray-400">
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
                                                        <span class="inline-flex items-center gap-1.5 text-[10px] font-medium text-gray-600">
                                                            <span class="w-1.5 h-1.5 rounded-full {{ $style['dot'] }}"></span>
                                                            {{ $style['label'] }}
                                                        </span>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endforeach
                        @elseif ($order->status !== 'completed')
                            <div class="flex items-center gap-2 text-xs text-gray-600 bg-white border-l-2 border-amber-500 px-3 py-2">
                                <i class="hgi-stroke hgi-refresh text-sm animate-spin text-amber-500"></i>
                                Waiting for lab results...
                            </div>
                        @endif

                        {{-- Ultrasound Orders --}}
                        @if ($order->attachments->isNotEmpty())
                            <div class="mt-3 flex flex-wrap gap-2">
                                @foreach ($order->attachments as $attachment)
                                    <a href="{{ route('file.serve', $attachment->file_path) }}" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded text-xs font-medium transition-colors">
                                        <i class="hgi-stroke hgi-file-01 text-sm"></i>
                                        {{ $attachment->file_name }}
                                    </a>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="p-5 text-center text-xs text-gray-400">No lab orders found.</div>
                @endforelse
            </div>

            {{-- Ultrasound Orders --}}
            @if ($visit->ultrasoundOrders->isNotEmpty())
                <div class="divide-y divide-gray-100 border-t border-gray-100">
                    @foreach ($visit->ultrasoundOrders as $usOrder)
                        <div class="p-5">
                            <div class="flex items-center justify-between mb-3">
                                <div class="flex items-center gap-2">
                                    <span class="text-xs font-bold text-gray-700">Ultrasound Order #{{ $usOrder->id }}</span>
                                    <span class="text-xs text-gray-400">•</span>
                                    <span class="text-xs text-gray-500">{{ $usOrder->created_at->format('M d, Y H:i') }}</span>
                                    @if($usOrder->status === 'completed')
                                        <span class="inline-flex items-center gap-1 text-[10px] font-medium text-gray-600"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>Completed</span>
                                    @else
                                        <span class="inline-flex items-center gap-1 text-[10px] font-medium text-gray-600"><span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>{{ ucfirst($usOrder->status) }}</span>
                                    @endif
                                </div>
                                @if($usOrder->status === 'completed')
                                    <a href="{{ route('ultrasound.orders.show', $usOrder) }}" target="_blank" class="text-[10px] text-slate-600 hover:text-slate-800 font-medium inline-flex items-center gap-1">
                                        <i class="hgi-stroke hgi-view text-sm"></i>
                                        Full Report
                                    </a>
                                @endif
                            </div>
                            <div class="flex flex-wrap gap-1.5 mb-3">
                                @foreach ($usOrder->items as $item)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium bg-gray-100 text-gray-700 border border-gray-200">{{ $item->ultrasoundService?->name ?? 'Unknown' }}</span>
                                @endforeach
                            </div>
                            @if ($usOrder->status === 'completed')
                                <div class="space-y-2">
                                    @if ($usOrder->findings)
                                        <div>
                                            <p class="text-[10px] font-semibold text-gray-500 uppercase">Findings</p>
                                            <p class="text-sm text-gray-700 whitespace-pre-wrap">{{ $usOrder->findings }}</p>
                                        </div>
                                    @endif
                                    @if ($usOrder->impression)
                                        <div>
                                            <p class="text-[10px] font-semibold text-gray-500 uppercase">Impression</p>
                                            <p class="text-sm text-gray-700 whitespace-pre-wrap">{{ $usOrder->impression }}</p>
                                        </div>
                                    @endif
                                </div>
                                @if ($usOrder->attachments->isNotEmpty())
                                    <div class="mt-3 flex flex-wrap gap-2">
                                        @foreach ($usOrder->attachments as $attachment)
                                            <a href="{{ route('file.serve', $attachment->file_path) }}" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded text-xs font-medium transition-colors">
                                                <i class="hgi-stroke hgi-file-01 text-sm"></i>
                                                {{ $attachment->file_name }}
                                            </a>
                                        @endforeach
                                    </div>
                                @endif
                            @else
                                <div class="flex items-center gap-2 text-xs text-gray-600 bg-white border-l-2 border-amber-500 px-3 py-2">
                                    <i class="hgi-stroke hgi-refresh text-sm animate-spin text-amber-500"></i>
                                    Waiting for ultrasound results...
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
            @if ($visit->status === \App\Enums\VisitStatus::WithDoctor->value)
                <div class="px-5 pb-5">
                    <form method="POST" action="{{ route('doctor.visits.prescribe', $visit) }}" class="border border-gray-200 rounded p-4 bg-gray-50">
                        @csrf
                        <p class="text-xs font-semibold text-gray-700 mb-3 flex items-center gap-2">
                            <i class="hgi-stroke hgi-test-tube-01 text-base text-gray-400"></i>
                            Write Prescription
                        </p>
                        <div class="space-y-2" id="prescription-items-{{ $visit->id }}">
                            <div class="grid grid-cols-6 gap-2">
                                <x-ui.select name="items[0][medication_id]" class="col-span-2 border rounded-lg px-2 py-1.5 text-xs" required>
                                    <option value="">Drug</option>
                                    @foreach ($medications as $med)
                                        <option value="{{ $med->id }}">{{ $med->name }}</option>
                                    @endforeach
                                </x-ui.select>
                                <x-ui.input type="number" name="items[0][quantity]" placeholder="Qty" class="border rounded-lg px-2 py-1.5 text-xs" required />
                                <x-ui.input type="text" name="items[0][dosage]" placeholder="Dose" class="border rounded-lg px-2 py-1.5 text-xs" required />
                                <x-ui.input type="text" name="items[0][frequency]" placeholder="Freq" class="border rounded-lg px-2 py-1.5 text-xs" required />
                                <x-ui.input type="text" name="items[0][duration]" placeholder="Duration" class="border rounded-lg px-2 py-1.5 text-xs" required />
                            </div>
                        </div>
                        <x-ui.textarea name="items[0][instructions]" placeholder="Instructions (optional)" class="w-full border rounded-lg px-2 py-1.5 text-xs mt-2" rows="2"></x-ui.textarea>
                        <button type="submit" class="w-full bg-slate-800 hover:bg-slate-700 text-white text-xs font-medium px-3 py-2 rounded mt-2 flex items-center justify-center gap-1">
                            <i class="hgi-stroke hgi-tick-02 text-sm"></i>
                            Send to Pharmacy
                        </button>
                    </form>
                </div>
            @endif
        </div>
    @empty
        <div class="bg-white rounded-md border border-gray-200 p-12 text-center">
            <i class="hgi-stroke hgi-test-tube-01 text-6xl mx-auto text-gray-300 mb-3"></i>
            <p class="text-xs text-gray-400">No results pending. All patients are in your queue.</p>
            <a href="{{ route('doctor.queue') }}" class="inline-flex items-center gap-2 mt-4 px-4 py-2 bg-slate-800 hover:bg-slate-700 text-white text-sm font-medium rounded transition-colors">
                Back to Queue
            </a>
        </div>
    @endforelse
</div>
@endsection
