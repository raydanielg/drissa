@extends('layouts.dashboard')

@section('title', 'Ultrasound - ' . config('app.name', 'Laravel'))
@section('page_title', 'Ultrasound Workspace')

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
                <div class="w-12 h-12 rounded-xl bg-blue-100 flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="animation-duration:3s"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
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
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Total Scans</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['total_services'] }}</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-purple-100 flex items-center justify-center">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                </div>
            </div>
        </div>
    </div>

    {{-- Pending Orders --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <h3 class="text-sm font-semibold text-gray-900 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                Pending Orders
            </h3>
            <a href="{{ route('ultrasound-services.index') }}" class="text-xs text-emerald-600 hover:text-emerald-700 font-medium">Manage Services</a>
        </div>
        @if($pendingOrders->isNotEmpty())
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50/50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Patient</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Services</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ordered By</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Visit Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Notes</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($pendingOrders as $order)
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="px-6 py-3.5">
                            <div class="text-sm font-medium text-gray-900">{{ $order->patient?->fullName() }}</div>
                            <div class="text-xs text-gray-500">{{ $order->patient?->mrn }}</div>
                        </td>
                        <td class="px-6 py-3.5">
                            @foreach($order->items as $item)
                                <span class="inline-block px-2 py-0.5 bg-purple-50 text-purple-700 text-xs rounded-full mr-1 mb-1">{{ $item->ultrasoundService?->name }}</span>
                            @endforeach
                        </td>
                        <td class="px-6 py-3.5 text-sm text-gray-700">{{ $order->doctor?->name ?? 'N/A' }}</td>
                        <td class="px-6 py-3.5">
                            @if($order->visit)
                                @if($order->visit->status === 'waiting_for_ultrasound')
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-700">Waiting</span>
                                @elseif($order->visit->status === 'in_ultrasound')
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-700">In Scan</span>
                                @elseif($order->visit->status === 'ultrasound_completed')
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700">Completed</span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-700">{{ $order->visit->status }}</span>
                                @endif
                            @else
                                <span class="text-xs text-gray-400">N/A</span>
                            @endif
                        </td>
                        <td class="px-6 py-3.5 text-xs text-gray-500">{{ $order->clinical_notes ?? '-' }}</td>
                        <td class="px-6 py-3.5 text-right">
                            <form method="POST" action="{{ route('ultrasound.orders.start', $order) }}" class="inline">
                                @csrf
                                <button type="submit" class="px-3 py-1.5 bg-blue-600 text-white text-xs font-medium rounded-lg hover:bg-blue-700 transition-colors">Start</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="text-center py-12">
            <p class="text-gray-500 text-sm">No pending ultrasound orders</p>
        </div>
        @endif
    </div>

    {{-- Processing Orders --}}
    @if($processingOrders->isNotEmpty())
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <h3 class="text-sm font-semibold text-gray-900 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-blue-500 animate-pulse"></span>
                In Progress
            </h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50/50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Patient</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Services</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tech</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($processingOrders as $order)
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="px-6 py-3.5">
                            <div class="text-sm font-medium text-gray-900">{{ $order->patient?->fullName() }}</div>
                            <div class="text-xs text-gray-500">{{ $order->patient?->mrn }}</div>
                        </td>
                        <td class="px-6 py-3.5">
                            @foreach($order->items as $item)
                                <span class="inline-block px-2 py-0.5 bg-purple-50 text-purple-700 text-xs rounded-full mr-1 mb-1">{{ $item->ultrasoundService?->name }}</span>
                            @endforeach
                        </td>
                        <td class="px-6 py-3.5 text-sm text-gray-700">{{ $order->ultrasoundTech?->name ?? 'N/A' }}</td>
                        <td class="px-6 py-3.5 text-right">
                            <button onclick="openResultsModal({{ $order->id }}, '{{ $order->patient?->fullName() }}')" class="px-3 py-1.5 bg-emerald-600 text-white text-xs font-medium rounded-lg hover:bg-emerald-700 transition-colors">Submit Results</button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    {{-- Completed Orders --}}
    @if($completedOrders->isNotEmpty())
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <h3 class="text-sm font-semibold text-gray-900 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                Recently Completed
            </h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50/50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Patient</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Services</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Completed</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Attachments</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($completedOrders as $order)
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="px-6 py-3.5">
                            <div class="text-sm font-medium text-gray-900">{{ $order->patient?->fullName() }}</div>
                            <div class="text-xs text-gray-500">{{ $order->patient?->mrn }}</div>
                        </td>
                        <td class="px-6 py-3.5">
                            @foreach($order->items as $item)
                                <span class="inline-block px-2 py-0.5 bg-purple-50 text-purple-700 text-xs rounded-full mr-1 mb-1">{{ $item->ultrasoundService?->name }}</span>
                            @endforeach
                        </td>
                        <td class="px-6 py-3.5 text-xs text-gray-500">{{ $order->completed_at?->format('M d, Y H:i') }}</td>
                        <td class="px-6 py-3.5">
                            @if($order->attachments->isNotEmpty())
                                <span class="inline-flex items-center gap-1 text-xs text-blue-600">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                                    {{ $order->attachments->count() }} file(s)
                                </span>
                            @else
                                <span class="text-xs text-gray-400">No files</span>
                            @endif
                        </td>
                        <td class="px-6 py-3.5 text-right">
                            <a href="{{ route('ultrasound.orders.show', $order) }}" class="text-xs text-emerald-600 hover:text-emerald-700 font-medium">View Results</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
</div>

{{-- Submit Results Modal --}}
<div id="resultsModal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" onclick="closeResultsModal()"></div>
    <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-full max-w-2xl">
        <div class="bg-white rounded-2xl shadow-2xl p-6 m-4 animate-fade max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-bold text-gray-900">Submit Ultrasound Results</h3>
                <button onclick="closeResultsModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <form id="resultsForm" method="POST" action="" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Findings</label>
                    <textarea name="findings" rows="4" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" placeholder="Describe ultrasound findings..."></textarea>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Impression</label>
                    <textarea name="impression" rows="3" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" placeholder="Clinical impression/conclusion..."></textarea>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Attach Files (Images, PDF, DICOM)</label>
                    <input type="file" name="attachments[]" multiple accept=".pdf,.jpg,.jpeg,.png,.dicom" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                    <p class="text-xs text-gray-400 mt-1">You can select multiple files. Max 10MB each.</p>
                </div>
                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" onclick="closeResultsModal()" class="px-4 py-2 text-sm text-gray-600 hover:bg-gray-100 rounded-lg">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700">Submit Results</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function openResultsModal(orderId, patientName) {
        document.getElementById('resultsForm').action = '{{ url("ultrasound/orders") }}/' + orderId + '/results';
        document.getElementById('resultsModal').classList.remove('hidden');
    }
    function closeResultsModal() {
        document.getElementById('resultsModal').classList.add('hidden');
    }
</script>
@endsection
