@extends('layouts.dashboard')

@section('title', 'Laboratory - ' . config('app.name', 'Laravel'))
@section('page_title', 'Laboratory Workspace')

@section('content')
<div class="space-y-6">
    @if (session('status'))
        <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm animate-fade">{{ session('status') }}</div>
    @endif
    @if (session('error'))
        <div class="p-4 rounded-xl bg-red-50 border border-red-200 text-red-700 text-sm animate-fade">{{ session('error') }}</div>
    @endif

    {{-- Stats Cards --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5 animate-fade">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Pending</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['pending'] }}</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-amber-100 flex items-center justify-center">
                    <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5 animate-fade" style="animation-delay:0.05s">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Processing</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['processing'] }}</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-sky-100 flex items-center justify-center">
                    <svg class="w-6 h-6 text-sky-600 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5 animate-fade" style="animation-delay:0.1s">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Completed Today</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['completed_today'] }}</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-emerald-100 flex items-center justify-center">
                    <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5 animate-fade" style="animation-delay:0.15s">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Active Tests</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['total_tests'] }}</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-violet-100 flex items-center justify-center">
                    <svg class="w-6 h-6 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>
                </div>
            </div>
        </div>
    </div>

    {{-- Tabs --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <h2 class="text-sm font-semibold text-gray-900">Lab Orders</h2>
            <div class="flex flex-wrap gap-2" id="labTabs">
                <button data-tab="pending" class="lab-tab active px-4 py-2 text-xs font-semibold rounded-lg bg-amber-100 text-amber-700 transition-all">Pending <span class="ml-1 px-1.5 py-0.5 rounded-full bg-amber-200 text-amber-800">{{ $pendingOrders->count() }}</span></button>
                <button data-tab="processing" class="lab-tab px-4 py-2 text-xs font-semibold rounded-lg text-gray-600 hover:bg-gray-100 transition-all">Processing <span class="ml-1 px-1.5 py-0.5 rounded-full bg-gray-200 text-gray-700">{{ $processingOrders->count() }}</span></button>
                <button data-tab="completed" class="lab-tab px-4 py-2 text-xs font-semibold rounded-lg text-gray-600 hover:bg-gray-100 transition-all">Completed <span class="ml-1 px-1.5 py-0.5 rounded-full bg-gray-200 text-gray-700">{{ $completedOrders->count() }}</span></button>
            </div>
        </div>

        {{-- Pending Tab --}}
        <div id="tab-pending" class="lab-panel">
            @if ($pendingOrders->isEmpty())
                <div class="px-6 py-12 text-center">
                    <svg class="w-12 h-12 mx-auto text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <p class="text-sm text-gray-400 mt-3">No pending lab orders</p>
                </div>
            @else
                <div class="divide-y divide-gray-100">
                    @foreach ($pendingOrders as $order)
                        <div class="p-5 hover:bg-gray-50/50 transition-colors">
                            <div class="flex items-start justify-between gap-4">
                                <div class="flex items-start gap-4">
                                    <div class="w-10 h-10 rounded-xl bg-amber-100 flex items-center justify-center flex-shrink-0">
                                        <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    </div>
                                    <div>
                                        <div class="flex items-center gap-2">
                                            <span class="text-sm font-bold text-gray-900">Order #{{ $order->id }}</span>
                                            <span class="text-xs text-gray-400">•</span>
                                            <span class="text-xs text-gray-500">{{ $order->visit->visit_number }}</span>
                                        </div>
                                        <p class="text-sm font-medium text-gray-700 mt-0.5">{{ $order->visit->patient->fullName() }}</p>
                                        <p class="text-xs text-gray-400 mt-0.5">Ordered by Dr. {{ $order->doctor?->name ?? 'Unknown' }} • {{ $order->created_at->diffForHumans() }}</p>
                                    </div>
                                </div>
                                <div class="flex flex-wrap gap-1.5">
                                    @foreach ($order->items as $item)
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-violet-50 text-violet-700 border border-violet-100">{{ $item->labTest?->name ?? 'Unknown' }}</span>
                                    @endforeach
                                </div>
                            </div>
                            <div class="mt-4 pl-14">
                                <form method="POST" action="{{ route('lab.orders.start', $order) }}">
                                    @csrf
                                    <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2 bg-sky-600 hover:bg-sky-700 text-white text-xs font-semibold rounded-lg transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        Start Processing
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Processing Tab --}}
        <div id="tab-processing" class="lab-panel hidden">
            @if ($processingOrders->isEmpty())
                <div class="px-6 py-12 text-center">
                    <svg class="w-12 h-12 mx-auto text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    <p class="text-sm text-gray-400 mt-3">No orders being processed</p>
                </div>
            @else
                <div class="divide-y divide-gray-100">
                    @foreach ($processingOrders as $order)
                        <div class="p-5">
                            <div class="flex items-start justify-between gap-4">
                                <div class="flex items-start gap-4">
                                    <div class="w-10 h-10 rounded-xl bg-sky-100 flex items-center justify-center flex-shrink-0">
                                        <svg class="w-5 h-5 text-sky-600 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                    </div>
                                    <div>
                                        <div class="flex items-center gap-2">
                                            <span class="text-sm font-bold text-gray-900">Order #{{ $order->id }}</span>
                                            <span class="text-xs text-gray-400">•</span>
                                            <span class="text-xs text-gray-500">{{ $order->visit->visit_number }}</span>
                                        </div>
                                        <p class="text-sm font-medium text-gray-700 mt-0.5">{{ $order->visit->patient->fullName() }}</p>
                                        <p class="text-xs text-gray-400 mt-0.5">Processing started {{ $order->updated_at->diffForHumans() }}</p>
                                    </div>
                                </div>
                                <div class="flex flex-wrap gap-1.5">
                                    @foreach ($order->items as $item)
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-violet-50 text-violet-700 border border-violet-100">{{ $item->labTest?->name ?? 'Unknown' }}</span>
                                    @endforeach
                                </div>
                            </div>

                            {{-- Results Entry: Per-test forms --}}
                            <div class="mt-4 pl-14 space-y-4">
                                @php
                                    $processingOrders = LabOrder::with(['visit.patient', 'items.labTest', 'items.results', 'labTech'])
            ->where('status', 'processing')
            ->latest()
            ->get();
                                    $patient = $order->visit->patient;
                                    $isFemale = $patient && $patient->gender === 'female';
                                    $completedCount = 0;
                                    $totalItems = $order->items->count();
                                @endphp

                                @foreach ($order->items as $index => $item)
                                    @php
                                        $hasResults = $item->results->isNotEmpty();
                                        if ($hasResults) $completedCount++;
                                        $refRange = $item->labTest?->reference_range ?? '';
                                        if ($isFemale && $item->labTest?->reference_range_female) {
                                            $refRange = $item->labTest->reference_range_female;
                                        } elseif (!$isFemale && $item->labTest?->reference_range_male) {
                                            $refRange = $item->labTest->reference_range_male;
                                        }
                                    @endphp

                                    <div class="bg-white border rounded-xl p-4 {{ $hasResults ? 'border-emerald-200 bg-emerald-50/30' : 'border-gray-200' }}">
                                        <div class="flex items-center justify-between mb-3">
                                            <div class="flex items-center gap-2">
                                                <span class="inline-flex items-center justify-center w-6 h-6 rounded-full {{ $hasResults ? 'bg-emerald-100 text-emerald-700' : 'bg-violet-100 text-violet-700' }} text-xs font-bold">{{ $index + 1 }}</span>
                                                <span class="text-sm font-semibold text-gray-800">{{ $item->labTest?->name ?? 'Unknown Test' }}</span>
                                                @if ($item->labTest?->unit)
                                                    <span class="text-xs text-gray-400">Unit: {{ $item->labTest->unit }}</span>
                                                @endif
                                            </div>
                                            @if ($hasResults)
                                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-semibold bg-emerald-100 text-emerald-700">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                                    Result Submitted
                                                </span>
                                            @endif
                                        </div>

                                        @if ($hasResults)
                                            {{-- Show submitted results --}}
                                            <div class="overflow-hidden rounded-lg border border-emerald-100">
                                                <table class="w-full text-xs">
                                                    <thead class="bg-emerald-50/50 text-gray-500">
                                                        <tr>
                                                            <th class="px-3 py-2 text-left font-medium">Parameter</th>
                                                            <th class="px-3 py-2 text-left font-medium">Value</th>
                                                            <th class="px-3 py-2 text-left font-medium">Unit</th>
                                                            <th class="px-3 py-2 text-left font-medium">Ref Range</th>
                                                            <th class="px-3 py-2 text-left font-medium">Flag</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody class="divide-y divide-gray-50">
                                                        @foreach ($item->results as $result)
                                                            @php
                                                                $flagBg = match($result->flag) {
                                                                    'normal' => 'bg-emerald-50 text-emerald-700',
                                                                    'high', 'low' => 'bg-amber-50 text-amber-700',
                                                                    'critical' => 'bg-red-50 text-red-700',
                                                                    default => 'bg-gray-50 text-gray-700',
                                                                };
                                                            @endphp
                                                            <tr>
                                                                <td class="px-3 py-2 font-medium text-gray-800">{{ $result->parameter }}</td>
                                                                <td class="px-3 py-2 font-bold text-gray-900">{{ $result->value }}</td>
                                                                <td class="px-3 py-2 text-gray-500">{{ $result->unit ?? '-' }}</td>
                                                                <td class="px-3 py-2 text-gray-500">{{ $result->reference_range ?? '-' }}</td>
                                                                <td class="px-3 py-2"><span class="inline-flex px-1.5 py-0.5 rounded-full text-[10px] font-medium {{ $flagBg }}">{{ ucfirst($result->flag) }}</span></td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        @else
                                            {{-- Per-test result entry form --}}
                                            <form method="POST" action="{{ route('lab.orders.items.result', [$order, $item]) }}" class="space-y-3">
                                                @csrf
                                                <div class="grid grid-cols-12 gap-2 items-center">
                                                    <input type="text" name="parameter" placeholder="Parameter (e.g. Hemoglobin)" class="col-span-4 border border-gray-200 rounded-lg px-3 py-2 text-xs focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" required>
                                                    <input type="text" name="value" placeholder="Result value" class="col-span-3 border border-gray-200 rounded-lg px-3 py-2 text-xs focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" required>
                                                    <input type="text" name="unit" placeholder="Unit" value="{{ $item->labTest?->unit ?? '' }}" class="col-span-2 border border-gray-200 rounded-lg px-3 py-2 text-xs focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                                                    <input type="text" name="reference_range" placeholder="Ref range" value="{{ $refRange }}" class="col-span-3 border border-gray-200 rounded-lg px-3 py-2 text-xs focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                                                </div>
                                                @if($isFemale && ($item->labTest?->reference_range_female || $item->labTest?->reference_range_pregnant || $item->labTest?->reference_range_safe))
                                                <div class="flex flex-wrap gap-1.5 text-[10px]">
                                                    @if($item->labTest?->reference_range_female)
                                                    <button type="button" onclick="this.closest('form').querySelector('input[placeholder=Ref range]').value='{{ $item->labTest->reference_range_female }}'" class="px-2 py-0.5 rounded-full bg-pink-50 text-pink-600 border border-pink-200 hover:bg-pink-100">F: {{ $item->labTest->reference_range_female }}</button>
                                                    @endif
                                                    @if($item->labTest?->reference_range_pregnant)
                                                    <button type="button" onclick="this.closest('form').querySelector('input[placeholder=Ref range]').value='{{ $item->labTest->reference_range_pregnant }}'" class="px-2 py-0.5 rounded-full bg-purple-50 text-purple-600 border border-purple-200 hover:bg-purple-100">Preg: {{ $item->labTest->reference_range_pregnant }}</button>
                                                    @endif
                                                    @if($item->labTest?->reference_range_safe)
                                                    <button type="button" onclick="this.closest('form').querySelector('input[placeholder=Ref range]').value='{{ $item->labTest->reference_range_safe }}'" class="px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-600 border border-emerald-200 hover:bg-emerald-100">Safe: {{ $item->labTest->reference_range_safe }}</button>
                                                    @endif
                                                </div>
                                                @elseif(!$isFemale && $item->labTest?->reference_range_male)
                                                <div class="flex flex-wrap gap-1.5 text-[10px]">
                                                    <button type="button" onclick="this.closest('form').querySelector('input[placeholder=Ref range]').value='{{ $item->labTest->reference_range_male }}'" class="px-2 py-0.5 rounded-full bg-blue-50 text-blue-600 border border-blue-200 hover:bg-blue-100">M: {{ $item->labTest->reference_range_male }}</button>
                                                </div>
                                                @endif
                                                <div class="flex items-center justify-between">
                                                    <select name="flag" class="border border-gray-200 rounded-lg px-3 py-2 text-xs focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                                                        <option value="normal">🟢 Normal</option>
                                                        <option value="high">🟡 High</option>
                                                        <option value="low">🟡 Low</option>
                                                        <option value="critical">🔴 Critical</option>
                                                    </select>
                                                    <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold rounded-lg transition-colors">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                                        Submit Result
                                                    </button>
                                                </div>
                                            </form>
                                        @endif
                                    </div>
                                @endforeach

                                {{-- Progress bar --}}
                                @php $progress = $totalItems > 0 ? round(($completedCount / $totalItems) * 100) : 0; @endphp
                                <div class="bg-gray-50 border border-gray-200 rounded-xl p-3">
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="text-xs font-semibold text-gray-700">Progress: {{ $completedCount }}/{{ $totalItems }} tests completed</span>
                                        <span class="text-xs font-bold {{ $progress == 100 ? 'text-emerald-600' : 'text-amber-600' }}">{{ $progress }}%</span>
                                    </div>
                                    <div class="w-full h-2 bg-gray-200 rounded-full overflow-hidden">
                                        <div class="h-full rounded-full transition-all duration-500 {{ $progress == 100 ? 'bg-emerald-500' : 'bg-amber-500' }}" style="width: {{ $progress }}%"></div>
                                    </div>
                                </div>

                                {{-- File Upload (for all at once) --}}
                                <form method="POST" action="{{ route('lab.orders.results', $order) }}" enctype="multipart/form-data" class="border-2 border-dashed border-gray-200 rounded-lg p-4 hover:border-emerald-400 transition-colors">
                                    @csrf
                                    <div class="flex items-center gap-3">
                                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                                        <div class="flex-1">
                                            <p class="text-xs font-medium text-gray-700">Attach Report (PDF, Image)</p>
                                            <p class="text-xs text-gray-400">Optional — Max 5MB</p>
                                        </div>
                                        <input type="file" name="report" class="hidden" accept=".pdf,.jpg,.jpeg,.png" id="report-{{ $order->id }}">
                                        <label for="report-{{ $order->id }}" class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-medium rounded-lg cursor-pointer transition-colors">Choose File</label>
                                        <button type="submit" class="px-4 py-1.5 bg-sky-600 hover:bg-sky-700 text-white text-xs font-semibold rounded-lg transition-colors" onclick="return confirm('Upload attachment only? Results are submitted per test above.')">Upload</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Completed Tab --}}
        <div id="tab-completed" class="lab-panel hidden">
            @if ($completedOrders->isEmpty())
                <div class="px-6 py-12 text-center">
                    <svg class="w-12 h-12 mx-auto text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <p class="text-sm text-gray-400 mt-3">No completed lab orders</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                            <tr>
                                <th class="px-6 py-3">Order #</th>
                                <th class="px-6 py-3">Patient</th>
                                <th class="px-6 py-3">Tests</th>
                                <th class="px-6 py-3">Results</th>
                                <th class="px-6 py-3">Completed</th>
                                <th class="px-6 py-3">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($completedOrders as $order)
                                <tr class="border-t border-gray-100 hover:bg-gray-50/50 transition-colors">
                                    <td class="px-6 py-3 font-medium text-gray-900">#{{ $order->id }}</td>
                                    <td class="px-6 py-3">{{ $order->visit->patient->fullName() }}</td>
                                    <td class="px-6 py-3">
                                        <div class="flex flex-wrap gap-1">
                                            @foreach ($order->items as $item)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-violet-50 text-violet-700">{{ $item->labTest?->name ?? 'Unknown' }}</span>
                                            @endforeach
                                        </div>
                                    </td>
                                    <td class="px-6 py-3">
                                        @php
                                            $normalCount = $order->results->where('flag', 'normal')->count();
                                            $abnormalCount = $order->results->count() - $normalCount;
                                        @endphp
                                        <span class="inline-flex items-center gap-1.5">
                                            <span class="text-xs font-medium text-gray-700">{{ $order->results->count() }} parameters</span>
                                            @if ($abnormalCount > 0)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-700">{{ $abnormalCount }} abnormal</span>
                                            @else
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700">All normal</span>
                                            @endif
                                        </span>
                                    </td>
                                    <td class="px-6 py-3 text-xs text-gray-500">{{ $order->completed_at?->diffForHumans() }}</td>
                                    <td class="px-6 py-3">
                                        <div class="flex items-center gap-2">
                                            <a href="{{ route('lab.patient-history', $order->visit->patient) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-amber-50 hover:bg-amber-100 text-amber-700 text-xs font-medium rounded-lg transition-colors" title="View patient lab history">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                History
                                            </a>
                                            <a href="{{ route('lab.orders.show', $order) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-medium rounded-lg transition-colors">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                                View
                                            </a>
                                            <a href="{{ route('lab.orders.show', $order) }}" onclick="window.open(this.href, '_blank'); setTimeout(() => window.print(), 500); return false;" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-100 hover:bg-emerald-200 text-emerald-700 text-xs font-medium rounded-lg transition-colors">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                                                Print
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
document.querySelectorAll('.lab-tab').forEach(btn => {
    btn.addEventListener('click', () => {
        document.querySelectorAll('.lab-tab').forEach(b => {
            b.classList.remove('active', 'bg-amber-100', 'text-amber-700');
            b.classList.add('text-gray-600', 'hover:bg-gray-100');
        });
        btn.classList.add('active', 'bg-amber-100', 'text-amber-700');
        btn.classList.remove('text-gray-600', 'hover:bg-gray-100');
        document.querySelectorAll('.lab-panel').forEach(p => p.classList.add('hidden'));
        document.getElementById('tab-' + btn.dataset.tab).classList.remove('hidden');
    });
});

{{-- SweetAlert for form submissions --}}
document.querySelectorAll('form[method="POST"]').forEach(form => {
    form.addEventListener('submit', function(e) {
        if (this.dataset.swalHandled) return;
        @if (session('status'))
            Swal.fire({ icon: 'success', title: 'Success', text: '{{ session('status') }}', timer: 2000, showConfirmButton: false, toast: true, position: 'top-end' });
        @endif
    });
});
</script>
@endsection
