@extends('layouts.dashboard')

@section('title', 'Laboratory - ' . config('app.name', 'Laravel'))
@section('page_title', 'Laboratory Workspace')

@section('content')
<div class="space-y-5">
    @if (session('status'))
        <div class="p-3 rounded-md bg-white border-l-2 border-emerald-500 text-emerald-700 text-xs">{{ session('status') }}</div>
    @endif
    @if (session('error'))
        <div class="p-3 rounded-md bg-white border-l-2 border-red-500 text-red-700 text-xs">{{ session('error') }}</div>
    @endif

    <script>
function swalConfirmSubmit(event, message) {
    event.preventDefault();
    const form = event.target.closest('form') || event.target;
    Swal.fire({
        icon: 'question',
        title: 'Confirm',
        text: message,
        showCancelButton: true,
        confirmButtonText: 'Yes',
        cancelButtonText: 'Cancel',
        confirmButtonColor: '#0f172a',
    }).then((result) => {
        if (result.isConfirmed) form.submit();
    });
    return false;
}

document.querySelectorAll('.lab-tab').forEach(btn => {
    btn.addEventListener('click', () => {
        document.querySelectorAll('.lab-tab').forEach(b => {
            b.classList.remove('active', 'bg-slate-800', 'text-white');
            b.classList.add('text-gray-600', 'hover:bg-gray-100');
        });
        btn.classList.add('active', 'bg-slate-800', 'text-white');
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
    <div class="bg-slate-800 rounded-md p-6 text-white">
        <div>
            <p class="text-[10px] uppercase tracking-wider text-slate-400 mb-1">Laboratory</p>
            <h1 class="text-xl font-bold">Laboratory Workspace</h1>
            <p class="text-xs text-slate-400 mt-1">Process lab orders and record results.</p>
        </div>
    </div>

    <script>
function swalConfirmSubmit(event, message) {
    event.preventDefault();
    const form = event.target.closest('form') || event.target;
    Swal.fire({
        icon: 'question',
        title: 'Confirm',
        text: message,
        showCancelButton: true,
        confirmButtonText: 'Yes',
        cancelButtonText: 'Cancel',
        confirmButtonColor: '#0f172a',
    }).then((result) => {
        if (result.isConfirmed) form.submit();
    });
    return false;
}

document.querySelectorAll('.lab-tab').forEach(btn => {
    btn.addEventListener('click', () => {
        document.querySelectorAll('.lab-tab').forEach(b => {
            b.classList.remove('active', 'bg-slate-800', 'text-white');
            b.classList.add('text-gray-600', 'hover:bg-gray-100');
        });
        btn.classList.add('active', 'bg-slate-800', 'text-white');
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
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
        <div class="bg-white rounded-md border border-amber-200 p-4">
            <div class="flex items-start justify-between">
                <p class="text-[10px] font-medium text-gray-500 uppercase tracking-wide">Pending</p>
                <i class="hgi-stroke hgi-clock-01 text-base text-gray-400"></i>
            </div>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['pending'] }}</p>
        </div>
        <div class="bg-white rounded-md border border-gray-200 p-4">
            <div class="flex items-start justify-between">
                <p class="text-[10px] font-medium text-gray-500 uppercase tracking-wide">Processing</p>
                <i class="hgi-stroke hgi-refresh text-base text-gray-400"></i>
            </div>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['processing'] }}</p>
        </div>
        <div class="bg-white rounded-md border border-emerald-200 p-4">
            <div class="flex items-start justify-between">
                <p class="text-[10px] font-medium text-gray-500 uppercase tracking-wide">Completed Today</p>
                <i class="hgi-stroke hgi-checkmark-circle-02 text-base text-gray-400"></i>
            </div>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['completed_today'] }}</p>
        </div>
        <div class="bg-white rounded-md border border-gray-200 p-4">
            <div class="flex items-start justify-between">
                <p class="text-[10px] font-medium text-gray-500 uppercase tracking-wide">Active Tests</p>
                <i class="hgi-stroke hgi-test-tube-01 text-base text-gray-400"></i>
            </div>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['total_tests'] }}</p>
        </div>
    </div>

    <script>
function swalConfirmSubmit(event, message) {
    event.preventDefault();
    const form = event.target.closest('form') || event.target;
    Swal.fire({
        icon: 'question',
        title: 'Confirm',
        text: message,
        showCancelButton: true,
        confirmButtonText: 'Yes',
        cancelButtonText: 'Cancel',
        confirmButtonColor: '#0f172a',
    }).then((result) => {
        if (result.isConfirmed) form.submit();
    });
    return false;
}

document.querySelectorAll('.lab-tab').forEach(btn => {
    btn.addEventListener('click', () => {
        document.querySelectorAll('.lab-tab').forEach(b => {
            b.classList.remove('active', 'bg-slate-800', 'text-white');
            b.classList.add('text-gray-600', 'hover:bg-gray-100');
        });
        btn.classList.add('active', 'bg-slate-800', 'text-white');
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
    <div class="bg-white rounded-md border border-gray-200 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <h2 class="text-sm font-bold text-gray-900">Lab Orders</h2>
            <div class="flex flex-wrap gap-2" id="labTabs">
                <button data-tab="pending" class="lab-tab active px-4 py-2 text-xs font-semibold rounded bg-slate-800 text-white transition-all">Pending <span class="ml-1 px-1.5 py-0.5 rounded bg-slate-600 text-slate-100">{{ $pendingOrders->count() }}</span></button>
                <button data-tab="processing" class="lab-tab px-4 py-2 text-xs font-semibold rounded text-gray-600 hover:bg-gray-100 transition-all">Processing <span class="ml-1 px-1.5 py-0.5 rounded bg-gray-200 text-gray-700">{{ $processingOrders->count() }}</span></button>
                <button data-tab="completed" class="lab-tab px-4 py-2 text-xs font-semibold rounded text-gray-600 hover:bg-gray-100 transition-all">Completed <span class="ml-1 px-1.5 py-0.5 rounded bg-gray-200 text-gray-700">{{ $completedOrders->count() }}</span></button>
            </div>
        </div>

        <script>
function swalConfirmSubmit(event, message) {
    event.preventDefault();
    const form = event.target.closest('form') || event.target;
    Swal.fire({
        icon: 'question',
        title: 'Confirm',
        text: message,
        showCancelButton: true,
        confirmButtonText: 'Yes',
        cancelButtonText: 'Cancel',
        confirmButtonColor: '#0f172a',
    }).then((result) => {
        if (result.isConfirmed) form.submit();
    });
    return false;
}

document.querySelectorAll('.lab-tab').forEach(btn => {
    btn.addEventListener('click', () => {
        document.querySelectorAll('.lab-tab').forEach(b => {
            b.classList.remove('active', 'bg-slate-800', 'text-white');
            b.classList.add('text-gray-600', 'hover:bg-gray-100');
        });
        btn.classList.add('active', 'bg-slate-800', 'text-white');
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
        <div id="tab-pending" class="lab-panel">
            @if ($pendingOrders->isEmpty())
                <div class="px-6 py-12 text-center">
                    <i class="hgi-stroke hgi-checkmark-circle-02 text-6xl mx-auto text-gray-300"></i>
                    <p class="text-sm text-gray-400 mt-3">No pending lab orders</p>
                </div>
            @else
                <div class="divide-y divide-gray-100">
                    @foreach ($pendingOrders as $order)
                        <div class="p-5 hover:bg-gray-50/50 transition-colors">
                            <div class="flex items-start justify-between gap-4">
                                <div class="flex items-start gap-4">
                                    <div class="w-10 h-10 rounded bg-amber-50 border border-amber-200 flex items-center justify-center flex-shrink-0">
                                        <i class="hgi-stroke hgi-clock-01 text-xl text-amber-600"></i>
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
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-600">{{ $item->labTest?->name ?? 'Unknown' }}</span>
                                    @endforeach
                                </div>
                            </div>
                            <div class="mt-4 pl-14">
                                <form method="POST" action="{{ route('lab.orders.start', $order) }}">
                                    @csrf
                                    <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2 bg-slate-800 hover:bg-slate-700 text-white text-xs font-semibold rounded transition-colors">
                                        <i class="hgi-stroke hgi-play-circle text-base"></i>
                                        Start Processing
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <script>
function swalConfirmSubmit(event, message) {
    event.preventDefault();
    const form = event.target.closest('form') || event.target;
    Swal.fire({
        icon: 'question',
        title: 'Confirm',
        text: message,
        showCancelButton: true,
        confirmButtonText: 'Yes',
        cancelButtonText: 'Cancel',
        confirmButtonColor: '#0f172a',
    }).then((result) => {
        if (result.isConfirmed) form.submit();
    });
    return false;
}

document.querySelectorAll('.lab-tab').forEach(btn => {
    btn.addEventListener('click', () => {
        document.querySelectorAll('.lab-tab').forEach(b => {
            b.classList.remove('active', 'bg-slate-800', 'text-white');
            b.classList.add('text-gray-600', 'hover:bg-gray-100');
        });
        btn.classList.add('active', 'bg-slate-800', 'text-white');
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
        <div id="tab-processing" class="lab-panel hidden">
            @if ($processingOrders->isEmpty())
                <div class="px-6 py-12 text-center">
                    <i class="hgi-stroke hgi-refresh text-6xl mx-auto text-gray-300"></i>
                    <p class="text-sm text-gray-400 mt-3">No orders being processed</p>
                </div>
            @else
                <div class="divide-y divide-gray-100">
                    @foreach ($processingOrders as $order)
                        <div class="p-5">
                            <div class="flex items-start justify-between gap-4">
                                <div class="flex items-start gap-4">
                                    <div class="w-10 h-10 rounded bg-slate-100 flex items-center justify-center flex-shrink-0">
                                        <i class="hgi-stroke hgi-refresh text-xl text-slate-600"></i>
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
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-600">{{ $item->labTest?->name ?? 'Unknown' }}</span>
                                    @endforeach
                                </div>
                            </div>

                            <script>
function swalConfirmSubmit(event, message) {
    event.preventDefault();
    const form = event.target.closest('form') || event.target;
    Swal.fire({
        icon: 'question',
        title: 'Confirm',
        text: message,
        showCancelButton: true,
        confirmButtonText: 'Yes',
        cancelButtonText: 'Cancel',
        confirmButtonColor: '#0f172a',
    }).then((result) => {
        if (result.isConfirmed) form.submit();
    });
    return false;
}

document.querySelectorAll('.lab-tab').forEach(btn => {
    btn.addEventListener('click', () => {
        document.querySelectorAll('.lab-tab').forEach(b => {
            b.classList.remove('active', 'bg-slate-800', 'text-white');
            b.classList.add('text-gray-600', 'hover:bg-gray-100');
        });
        btn.classList.add('active', 'bg-slate-800', 'text-white');
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
                            <div class="mt-4 pl-14 space-y-4">
                                @php
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

                                    <div class="bg-white border rounded p-4 {{ $hasResults ? 'border-emerald-200' : 'border-gray-200' }}">
                                        <div class="flex items-center justify-between mb-3">
                                            <div class="flex items-center gap-2">
                                                <span class="inline-flex items-center justify-center w-6 h-6 rounded-full {{ $hasResults ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-600' }} text-xs font-bold">{{ $index + 1 }}</span>
                                                <span class="text-sm font-semibold text-gray-800">{{ $item->labTest?->name ?? 'Unknown Test' }}</span>
                                                @if ($item->labTest?->unit)
                                                    <span class="text-xs text-gray-400">Unit: {{ $item->labTest->unit }}</span>
                                                @endif
                                            </div>
                                            @if ($hasResults)
                                                <span class="inline-flex items-center gap-1 text-[10px] font-medium text-gray-600">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                                    Result Submitted
                                                </span>
                                            @endif
                                        </div>

                                        @if ($hasResults)
                                            <script>
function swalConfirmSubmit(event, message) {
    event.preventDefault();
    const form = event.target.closest('form') || event.target;
    Swal.fire({
        icon: 'question',
        title: 'Confirm',
        text: message,
        showCancelButton: true,
        confirmButtonText: 'Yes',
        cancelButtonText: 'Cancel',
        confirmButtonColor: '#0f172a',
    }).then((result) => {
        if (result.isConfirmed) form.submit();
    });
    return false;
}

document.querySelectorAll('.lab-tab').forEach(btn => {
    btn.addEventListener('click', () => {
        document.querySelectorAll('.lab-tab').forEach(b => {
            b.classList.remove('active', 'bg-slate-800', 'text-white');
            b.classList.add('text-gray-600', 'hover:bg-gray-100');
        });
        btn.classList.add('active', 'bg-slate-800', 'text-white');
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
                                            <div class="overflow-hidden rounded border border-gray-200">
                                                <table class="w-full text-xs">
                                                    <thead class="bg-gray-50 text-gray-500">
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
                                            <script>
function swalConfirmSubmit(event, message) {
    event.preventDefault();
    const form = event.target.closest('form') || event.target;
    Swal.fire({
        icon: 'question',
        title: 'Confirm',
        text: message,
        showCancelButton: true,
        confirmButtonText: 'Yes',
        cancelButtonText: 'Cancel',
        confirmButtonColor: '#0f172a',
    }).then((result) => {
        if (result.isConfirmed) form.submit();
    });
    return false;
}

document.querySelectorAll('.lab-tab').forEach(btn => {
    btn.addEventListener('click', () => {
        document.querySelectorAll('.lab-tab').forEach(b => {
            b.classList.remove('active', 'bg-slate-800', 'text-white');
            b.classList.add('text-gray-600', 'hover:bg-gray-100');
        });
        btn.classList.add('active', 'bg-slate-800', 'text-white');
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
                                            <form method="POST" action="{{ route('lab.orders.items.result', [$order, $item]) }}" class="space-y-3">
                                                @csrf
                                                <div class="grid grid-cols-12 gap-2 items-center">
                                                    <x-ui.input type="text" name="parameter" placeholder="Parameter (e.g. Hemoglobin)" class="col-span-4 border border-gray-200 rounded px-3 py-2 text-xs focus:ring-2 focus:ring-slate-300" required />
                                                    <x-ui.input type="text" name="value" placeholder="Result value" class="col-span-3 border border-gray-200 rounded px-3 py-2 text-xs focus:ring-2 focus:ring-slate-300" required />
                                                    <x-ui.input type="text" name="unit" placeholder="Unit" value="{{ $item->labTest?->unit ?? '' }}" class="col-span-2 border border-gray-200 rounded px-3 py-2 text-xs focus:ring-2 focus:ring-slate-300" />
                                                    <x-ui.input type="text" name="reference_range" placeholder="Ref range" value="{{ $refRange }}" class="col-span-3 border border-gray-200 rounded px-3 py-2 text-xs focus:ring-2 focus:ring-slate-300" />
                                                </div>
                                                @if($isFemale && ($item->labTest?->reference_range_female || $item->labTest?->reference_range_pregnant || $item->labTest?->reference_range_safe))
                                                <div class="flex flex-wrap gap-1.5 text-[10px]">
                                                    @if($item->labTest?->reference_range_female)
                                                    <button type="button" onclick="this.closest('form').querySelector('input[placeholder=Ref range]').value='{{ $item->labTest->reference_range_female }}'" class="px-2 py-0.5 rounded bg-gray-100 text-gray-600 border border-gray-200 hover:bg-gray-200">F: {{ $item->labTest->reference_range_female }}</button>
                                                    @endif
                                                    @if($item->labTest?->reference_range_pregnant)
                                                    <button type="button" onclick="this.closest('form').querySelector('input[placeholder=Ref range]').value='{{ $item->labTest->reference_range_pregnant }}'" class="px-2 py-0.5 rounded bg-gray-100 text-gray-600 border border-gray-200 hover:bg-gray-200">Preg: {{ $item->labTest->reference_range_pregnant }}</button>
                                                    @endif
                                                    @if($item->labTest?->reference_range_safe)
                                                    <button type="button" onclick="this.closest('form').querySelector('input[placeholder=Ref range]').value='{{ $item->labTest->reference_range_safe }}'" class="px-2 py-0.5 rounded bg-emerald-50 text-emerald-700 border border-emerald-200 hover:bg-emerald-100">Safe: {{ $item->labTest->reference_range_safe }}</button>
                                                    @endif
                                                </div>
                                                @elseif(!$isFemale && $item->labTest?->reference_range_male)
                                                <div class="flex flex-wrap gap-1.5 text-[10px]">
                                                    <button type="button" onclick="this.closest('form').querySelector('input[placeholder=Ref range]').value='{{ $item->labTest->reference_range_male }}'" class="px-2 py-0.5 rounded bg-gray-100 text-gray-600 border border-gray-200 hover:bg-gray-200">M: {{ $item->labTest->reference_range_male }}</button>
                                                </div>
                                                @endif
                                                <div class="flex items-center justify-between">
                                                    <x-ui.select name="flag" class="border border-gray-200 rounded px-3 py-2 text-xs focus:ring-2 focus:ring-slate-300">
                                                        <option value="normal">Normal</option>
                                                        <option value="high">High</option>
                                                        <option value="low">Low</option>
                                                        <option value="critical">Critical</option>
                                                    </x-ui.select>
                                                    <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2 bg-slate-800 hover:bg-slate-700 text-white text-xs font-semibold rounded transition-colors">
                                                        <i class="hgi-stroke hgi-tick-02 text-sm"></i>
                                                        Submit Result
                                                    </button>
                                                </div>
                                            </form>
                                        @endif
                                    </div>
                                @endforeach

                                <script>
function swalConfirmSubmit(event, message) {
    event.preventDefault();
    const form = event.target.closest('form') || event.target;
    Swal.fire({
        icon: 'question',
        title: 'Confirm',
        text: message,
        showCancelButton: true,
        confirmButtonText: 'Yes',
        cancelButtonText: 'Cancel',
        confirmButtonColor: '#0f172a',
    }).then((result) => {
        if (result.isConfirmed) form.submit();
    });
    return false;
}

document.querySelectorAll('.lab-tab').forEach(btn => {
    btn.addEventListener('click', () => {
        document.querySelectorAll('.lab-tab').forEach(b => {
            b.classList.remove('active', 'bg-slate-800', 'text-white');
            b.classList.add('text-gray-600', 'hover:bg-gray-100');
        });
        btn.classList.add('active', 'bg-slate-800', 'text-white');
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
                                @php $progress = $totalItems > 0 ? round(($completedCount / $totalItems) * 100) : 0; @endphp
                                <div class="bg-gray-50 border border-gray-200 rounded p-3">
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="text-xs font-semibold text-gray-700">Progress: {{ $completedCount }}/{{ $totalItems }} tests completed</span>
                                        <span class="text-xs font-bold {{ $progress == 100 ? 'text-emerald-600' : 'text-gray-500' }}">{{ $progress }}%</span>
                                    </div>
                                    <div class="w-full h-1.5 bg-gray-100 rounded-full overflow-hidden">
                                        <div class="h-full rounded-full bg-emerald-600 transition-all duration-500" style="width: {{ $progress }}%"></div>
                                    </div>
                                </div>

                                <script>
function swalConfirmSubmit(event, message) {
    event.preventDefault();
    const form = event.target.closest('form') || event.target;
    Swal.fire({
        icon: 'question',
        title: 'Confirm',
        text: message,
        showCancelButton: true,
        confirmButtonText: 'Yes',
        cancelButtonText: 'Cancel',
        confirmButtonColor: '#0f172a',
    }).then((result) => {
        if (result.isConfirmed) form.submit();
    });
    return false;
}

document.querySelectorAll('.lab-tab').forEach(btn => {
    btn.addEventListener('click', () => {
        document.querySelectorAll('.lab-tab').forEach(b => {
            b.classList.remove('active', 'bg-slate-800', 'text-white');
            b.classList.add('text-gray-600', 'hover:bg-gray-100');
        });
        btn.classList.add('active', 'bg-slate-800', 'text-white');
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
                                <form method="POST" action="{{ route('lab.orders.results', $order) }}" enctype="multipart/form-data" class="border-2 border-dashed border-gray-200 rounded p-4 hover:border-slate-400 transition-colors">
                                    @csrf
                                    <div class="flex items-center gap-3">
                                        <i class="hgi-stroke hgi-cloud-upload text-xl text-gray-400"></i>
                                        <div class="flex-1">
                                            <p class="text-xs font-medium text-gray-700">Attach Report (PDF, Image)</p>
                                            <p class="text-xs text-gray-400">Optional — Max 5MB</p>
                                        </div>
                                        <x-ui.input type="file" name="report" class="hidden" accept=".pdf,.jpg,.jpeg,.png" id="report-{{ $order->id }}" />
                                        <label for="report-{{ $order->id }}" class="px-3 py-1.5 bg-white border border-gray-200 hover:bg-gray-100 text-gray-700 text-xs font-medium rounded cursor-pointer transition-colors">Choose File</label>
                                        <button type="submit" class="px-4 py-1.5 bg-slate-800 hover:bg-slate-700 text-white text-xs font-semibold rounded transition-colors" onclick="return swalConfirmSubmit(event, 'Upload attachment only? Results are submitted per test above.')">Upload</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <script>
function swalConfirmSubmit(event, message) {
    event.preventDefault();
    const form = event.target.closest('form') || event.target;
    Swal.fire({
        icon: 'question',
        title: 'Confirm',
        text: message,
        showCancelButton: true,
        confirmButtonText: 'Yes',
        cancelButtonText: 'Cancel',
        confirmButtonColor: '#0f172a',
    }).then((result) => {
        if (result.isConfirmed) form.submit();
    });
    return false;
}

document.querySelectorAll('.lab-tab').forEach(btn => {
    btn.addEventListener('click', () => {
        document.querySelectorAll('.lab-tab').forEach(b => {
            b.classList.remove('active', 'bg-slate-800', 'text-white');
            b.classList.add('text-gray-600', 'hover:bg-gray-100');
        });
        btn.classList.add('active', 'bg-slate-800', 'text-white');
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
        <div id="tab-completed" class="lab-panel hidden">
            @if ($completedOrders->isEmpty())
                <div class="px-6 py-12 text-center">
                    <i class="hgi-stroke hgi-checkmark-circle-02 text-6xl mx-auto text-gray-300"></i>
                    <p class="text-sm text-gray-400 mt-3">No completed lab orders</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-gray-50 text-[10px] uppercase text-gray-400">
                            <tr>
                                <th class="px-5 py-3">Order #</th>
                                <th class="px-5 py-3">Patient</th>
                                <th class="px-5 py-3">Tests</th>
                                <th class="px-5 py-3">Results</th>
                                <th class="px-5 py-3">Completed</th>
                                <th class="px-5 py-3 text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($completedOrders as $order)
                                <tr class="border-t border-gray-50 hover:bg-gray-50/50 transition-colors">
                                    <td class="px-5 py-3 text-xs font-semibold text-gray-900">#{{ $order->id }}</td>
                                    <td class="px-5 py-3 text-xs text-gray-700">{{ $order->visit->patient->fullName() }}</td>
                                    <td class="px-5 py-3">
                                        <div class="flex flex-wrap gap-1">
                                            @foreach ($order->items as $item)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-600">{{ $item->labTest?->name ?? 'Unknown' }}</span>
                                            @endforeach
                                        </div>
                                    </td>
                                    <td class="px-5 py-3">
                                        @php
                                            $normalCount = $order->results->where('flag', 'normal')->count();
                                            $abnormalCount = $order->results->count() - $normalCount;
                                        @endphp
                                        <span class="inline-flex items-center gap-1.5">
                                            <span class="text-xs font-medium text-gray-700">{{ $order->results->count() }} parameters</span>
                                            @if ($abnormalCount > 0)
                                                <span class="inline-flex items-center gap-1 text-[10px] font-medium text-gray-600"><span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>{{ $abnormalCount }} abnormal</span>
                                            @else
                                                <span class="inline-flex items-center gap-1 text-[10px] font-medium text-gray-600"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>All normal</span>
                                            @endif
                                        </span>
                                    </td>
                                    <td class="px-5 py-3 text-xs text-gray-500">{{ $order->completed_at?->diffForHumans() }}</td>
                                    <td class="px-5 py-3">
                                        <div class="flex items-center justify-end gap-2">
                                            <a href="{{ route('lab.patient-history', $order->visit->patient) }}" class="inline-flex items-center gap-1 px-2.5 py-1.5 border border-gray-200 hover:bg-gray-100 text-gray-700 text-[10px] font-medium rounded transition-colors" title="View patient lab history">
                                                <i class="hgi-stroke hgi-clock-01 text-xs"></i>
                                                History
                                            </a>
                                            <a href="{{ route('lab.orders.show', $order) }}" class="inline-flex items-center gap-1 px-2.5 py-1.5 border border-gray-200 hover:bg-gray-100 text-gray-700 text-[10px] font-medium rounded transition-colors">
                                                <i class="hgi-stroke hgi-view text-xs"></i>
                                                View
                                            </a>
                                            <a href="{{ route('lab.orders.show', $order) }}" onclick="window.open(this.href, '_blank'); setTimeout(() => window.print(), 500); return false;" class="inline-flex items-center gap-1 px-2.5 py-1.5 bg-slate-800 hover:bg-slate-700 text-white text-[10px] font-medium rounded transition-colors">
                                                <i class="hgi-stroke hgi-printer text-xs"></i>
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
function swalConfirmSubmit(event, message) {
    event.preventDefault();
    const form = event.target.closest('form') || event.target;
    Swal.fire({
        icon: 'question',
        title: 'Confirm',
        text: message,
        showCancelButton: true,
        confirmButtonText: 'Yes',
        cancelButtonText: 'Cancel',
        confirmButtonColor: '#0f172a',
    }).then((result) => {
        if (result.isConfirmed) form.submit();
    });
    return false;
}

document.querySelectorAll('.lab-tab').forEach(btn => {
    btn.addEventListener('click', () => {
        document.querySelectorAll('.lab-tab').forEach(b => {
            b.classList.remove('active', 'bg-slate-800', 'text-white');
            b.classList.add('text-gray-600', 'hover:bg-gray-100');
        });
        btn.classList.add('active', 'bg-slate-800', 'text-white');
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
