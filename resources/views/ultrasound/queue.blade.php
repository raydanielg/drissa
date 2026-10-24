@extends('layouts.dashboard')

@section('title', 'Ultrasound - ' . config('app.name', 'Laravel'))
@section('page_title', 'Ultrasound Workspace')

@section('content')
<div class="space-y-5">
    @if (session('status'))
        <div class="p-3 rounded-md bg-white border-l-2 border-emerald-500 text-emerald-700 text-xs">{{ session('status') }}</div>
    @endif
    @if (session('error'))
        <div class="p-3 rounded-md bg-white border-l-2 border-red-500 text-red-700 text-xs">{{ session('error') }}</div>
    @endif

    <script>
    function openResultsModal(orderId, patientName) {
        document.getElementById('resultsForm').action = '{{ url("ultrasound/orders") }}/' + orderId + '/results';
        document.getElementById('resultsModal').classList.remove('hidden');
    }
    function closeResultsModal() {
        document.getElementById('resultsModal').classList.add('hidden');
    }
</script>
    <div class="bg-slate-800 rounded-md p-6 text-white">
        <div class="flex flex-wrap items-end justify-between gap-3">
            <div>
                <p class="text-[10px] uppercase tracking-wider text-slate-400 mb-1">Imaging</p>
                <h1 class="text-xl font-bold">Ultrasound Workspace</h1>
                <p class="text-xs text-slate-400 mt-1">Manage ultrasound orders and results.</p>
            </div>
            <a href="{{ route('ultrasound-services.index') }}" class="inline-flex items-center gap-1.5 bg-white hover:bg-gray-100 text-slate-800 text-xs font-semibold px-3 py-1.5 rounded transition-colors">
                Manage Services
            </a>
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
                <p class="text-[10px] font-medium text-gray-500 uppercase tracking-wide">Total Scans</p>
                <i class="hgi-stroke hgi-analytics-02 text-base text-gray-400"></i>
            </div>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['total_services'] }}</p>
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
    <div class="bg-white rounded-md border border-gray-200 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
            <h3 class="text-sm font-bold text-gray-900 flex items-center gap-2">
                <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                Pending Orders
            </h3>
        </div>
        @if($pendingOrders->isNotEmpty())
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-5 py-3 text-left text-[10px] font-medium text-gray-400 uppercase">Patient</th>
                        <th class="px-5 py-3 text-left text-[10px] font-medium text-gray-400 uppercase">Services</th>
                        <th class="px-5 py-3 text-left text-[10px] font-medium text-gray-400 uppercase">Ordered By</th>
                        <th class="px-5 py-3 text-left text-[10px] font-medium text-gray-400 uppercase">Visit Status</th>
                        <th class="px-5 py-3 text-left text-[10px] font-medium text-gray-400 uppercase">Notes</th>
                        <th class="px-5 py-3 text-right text-[10px] font-medium text-gray-400 uppercase">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($pendingOrders as $order)
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="px-5 py-3">
                            <div class="text-sm font-medium text-gray-900">{{ $order->patient?->fullName() }}</div>
                            <div class="text-xs text-gray-500">{{ $order->patient?->mrn }}</div>
                        </td>
                        <td class="px-5 py-3">
                            @foreach($order->items as $item)
                                <span class="inline-block px-2 py-0.5 bg-gray-100 text-gray-600 text-xs rounded mr-1 mb-1">{{ $item->ultrasoundService?->name }}</span>
                            @endforeach
                        </td>
                        <td class="px-5 py-3 text-sm text-gray-700">{{ $order->doctor?->name ?? 'N/A' }}</td>
                        <td class="px-5 py-3">
                            @if($order->visit)
                                @if($order->visit->status === 'waiting_for_ultrasound')
                                    <span class="inline-flex items-center gap-1.5 text-[10px] font-medium text-gray-600"><span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>Waiting</span>
                                @elseif($order->visit->status === 'in_ultrasound')
                                    <span class="inline-flex items-center gap-1.5 text-[10px] font-medium text-gray-600"><span class="w-1.5 h-1.5 rounded-full bg-slate-600"></span>In Scan</span>
                                @elseif($order->visit->status === 'ultrasound_completed')
                                    <span class="inline-flex items-center gap-1.5 text-[10px] font-medium text-gray-600"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>Completed</span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 text-[10px] font-medium text-gray-600"><span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>{{ $order->visit->status }}</span>
                                @endif
                            @else
                                <span class="text-xs text-gray-400">N/A</span>
                            @endif
                        </td>
                        <td class="px-5 py-3 text-xs text-gray-500">{{ $order->clinical_notes ?? '-' }}</td>
                        <td class="px-5 py-3 text-right">
                            <form method="POST" action="{{ route('ultrasound.orders.start', $order) }}" class="inline">
                                @csrf
                                <button type="submit" class="px-3 py-1.5 bg-slate-800 text-white text-xs font-medium rounded hover:bg-slate-700 transition-colors">Start</button>
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

    <script>
    function openResultsModal(orderId, patientName) {
        document.getElementById('resultsForm').action = '{{ url("ultrasound/orders") }}/' + orderId + '/results';
        document.getElementById('resultsModal').classList.remove('hidden');
    }
    function closeResultsModal() {
        document.getElementById('resultsModal').classList.add('hidden');
    }
</script>
    @if($processingOrders->isNotEmpty())
    <div class="bg-white rounded-md border border-gray-200 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100">
            <h3 class="text-sm font-bold text-gray-900 flex items-center gap-2">
                <span class="w-1.5 h-1.5 rounded-full bg-slate-600"></span>
                In Progress
            </h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-5 py-3 text-left text-[10px] font-medium text-gray-400 uppercase">Patient</th>
                        <th class="px-5 py-3 text-left text-[10px] font-medium text-gray-400 uppercase">Services</th>
                        <th class="px-5 py-3 text-left text-[10px] font-medium text-gray-400 uppercase">Tech</th>
                        <th class="px-5 py-3 text-right text-[10px] font-medium text-gray-400 uppercase">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($processingOrders as $order)
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="px-5 py-3">
                            <div class="text-sm font-medium text-gray-900">{{ $order->patient?->fullName() }}</div>
                            <div class="text-xs text-gray-500">{{ $order->patient?->mrn }}</div>
                        </td>
                        <td class="px-5 py-3">
                            @foreach($order->items as $item)
                                <span class="inline-block px-2 py-0.5 bg-gray-100 text-gray-600 text-xs rounded mr-1 mb-1">{{ $item->ultrasoundService?->name }}</span>
                            @endforeach
                        </td>
                        <td class="px-5 py-3 text-sm text-gray-700">{{ $order->ultrasoundTech?->name ?? 'N/A' }}</td>
                        <td class="px-5 py-3 text-right">
                            <button onclick="openResultsModal({{ $order->id }}, '{{ $order->patient?->fullName() }}')" class="px-3 py-1.5 bg-slate-800 text-white text-xs font-medium rounded hover:bg-slate-700 transition-colors">Submit Results</button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <script>
    function openResultsModal(orderId, patientName) {
        document.getElementById('resultsForm').action = '{{ url("ultrasound/orders") }}/' + orderId + '/results';
        document.getElementById('resultsModal').classList.remove('hidden');
    }
    function closeResultsModal() {
        document.getElementById('resultsModal').classList.add('hidden');
    }
</script>
    @if($completedOrders->isNotEmpty())
    <div class="bg-white rounded-md border border-gray-200 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100">
            <h3 class="text-sm font-bold text-gray-900 flex items-center gap-2">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                Recently Completed
            </h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-5 py-3 text-left text-[10px] font-medium text-gray-400 uppercase">Patient</th>
                        <th class="px-5 py-3 text-left text-[10px] font-medium text-gray-400 uppercase">Services</th>
                        <th class="px-5 py-3 text-left text-[10px] font-medium text-gray-400 uppercase">Completed</th>
                        <th class="px-5 py-3 text-left text-[10px] font-medium text-gray-400 uppercase">Attachments</th>
                        <th class="px-5 py-3 text-right text-[10px] font-medium text-gray-400 uppercase">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($completedOrders as $order)
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="px-5 py-3">
                            <div class="text-sm font-medium text-gray-900">{{ $order->patient?->fullName() }}</div>
                            <div class="text-xs text-gray-500">{{ $order->patient?->mrn }}</div>
                        </td>
                        <td class="px-5 py-3">
                            @foreach($order->items as $item)
                                <span class="inline-block px-2 py-0.5 bg-gray-100 text-gray-600 text-xs rounded mr-1 mb-1">{{ $item->ultrasoundService?->name }}</span>
                            @endforeach
                        </td>
                        <td class="px-5 py-3 text-xs text-gray-500">{{ $order->completed_at?->format('M d, Y H:i') }}</td>
                        <td class="px-5 py-3">
                            @if($order->attachments->isNotEmpty())
                                <span class="inline-flex items-center gap-1 text-xs text-slate-600">
                                    <i class="hgi-stroke hgi-attachment-01 text-sm"></i>
                                    {{ $order->attachments->count() }} file(s)
                                </span>
                            @else
                                <span class="text-xs text-gray-400">No files</span>
                            @endif
                        </td>
                        <td class="px-5 py-3 text-right">
                            <a href="{{ route('ultrasound.orders.show', $order) }}" class="text-xs text-slate-600 hover:text-slate-800 font-medium">View Results</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
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
<div id="resultsModal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" onclick="closeResultsModal()"></div>
    <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-full max-w-2xl">
        <div class="bg-white rounded-md shadow-xl p-6 m-4 max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-bold text-gray-900">Submit Ultrasound Results</h3>
                <button onclick="closeResultsModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="hgi-stroke hgi-cancel-01 text-2xl"></i>
                </button>
            </div>
            <form id="resultsForm" method="POST" action="" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Findings</label>
                    <x-ui.textarea name="findings" rows="4" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-slate-300" placeholder="Describe ultrasound findings..."></x-ui.textarea>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Impression</label>
                    <x-ui.textarea name="impression" rows="3" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-slate-300" placeholder="Clinical impression/conclusion..."></x-ui.textarea>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Attach Files (Images, PDF, DICOM)</label>
                    <x-ui.input type="file" name="attachments[]" multiple accept=".pdf,.jpg,.jpeg,.png,.dicom" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-slate-300" />
                    <p class="text-xs text-gray-400 mt-1">You can select multiple files. Max 10MB each.</p>
                </div>
                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" onclick="closeResultsModal()" class="px-4 py-2 text-sm text-gray-700 border border-gray-200 hover:bg-gray-100 rounded">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-slate-800 text-white text-sm font-medium rounded hover:bg-slate-700">Submit Results</button>
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
