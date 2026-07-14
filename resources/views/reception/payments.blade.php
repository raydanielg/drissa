@extends('layouts.dashboard')

@section('title', 'Reception Payments - ' . config('app.name', 'Laravel'))
@section('page_title', 'Reception Payments')

@section('content')
<div class="space-y-6" id="receptionPayments">
    @if (session('status'))
        <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm animate-fade">{{ session('status') }}</div>
    @endif

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-gray-900">Reception Payments</h1>
            <p class="text-xs text-gray-500 mt-0.5">Record payments, close files, and complete visits after pharmacy/lab/doctor</p>
        </div>
        <div class="flex items-center gap-2">
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-emerald-50 border border-emerald-100 text-xs font-medium text-emerald-700">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span> Live
            </span>
            <span class="text-xs text-gray-400" id="lastUpdated">Updated just now</span>
        </div>
    </div>

    {{-- Payment Stats --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- KPI Cards --}}
        <div class="lg:col-span-2 grid grid-cols-2 gap-4">
            @php
                $maxAmount = max($todayTotal, 1);
                $maxPending = max($pendingCount, 20);
                $statsCards = [
                    ['label' => 'Paid Today', 'value' => 'TSh ' . number_format($todayPaidAmount), 'raw' => round($todayPaidAmount), 'icon' => 'M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z', 'bg' => 'bg-emerald-100', 'text' => 'text-emerald-600', 'bar' => 'bg-emerald-500', 'ring' => 'text-emerald-500', 'max' => $maxAmount],
                    ['label' => 'Pending Today', 'value' => 'TSh ' . number_format($todayUnpaidAmount), 'raw' => round($todayUnpaidAmount), 'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z', 'bg' => 'bg-rose-100', 'text' => 'text-rose-600', 'bar' => 'bg-rose-500', 'ring' => 'text-rose-500', 'max' => $maxAmount],
                    ['label' => 'Collection Rate', 'value' => $collectionRate . '%', 'raw' => $collectionRate, 'icon' => 'M13 7h8m0 0v8m0-8l-8 8-4-4-6 6', 'bg' => 'bg-blue-100', 'text' => 'text-blue-600', 'bar' => 'bg-blue-500', 'ring' => 'text-blue-500', 'max' => 100],
                    ['label' => 'Pending Invoices', 'value' => $pendingCount, 'raw' => $pendingCount, 'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z', 'bg' => 'bg-amber-100', 'text' => 'text-amber-600', 'bar' => 'bg-amber-500', 'ring' => 'text-amber-500', 'max' => $maxPending],
                ];
            @endphp
            @foreach ($statsCards as $card)
                <div class="bg-white rounded-xl border border-gray-100 p-4 shadow-sm hover:shadow-md transition-shadow">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-[10px] font-medium text-gray-500 uppercase tracking-wide">{{ $card['label'] }}</p>
                            <p class="text-xl font-bold text-gray-900 mt-1 kpi-counter" data-target="{{ $card['raw'] }}" data-max="{{ $card['max'] }}" data-prefix="{{ str_starts_with((string) $card['value'], 'TSh') ? 'TSh ' : '' }}" data-suffix="{{ str_ends_with((string) $card['value'], '%') ? '%' : '' }}">{{ $card['value'] }}</p>
                        </div>
                        <div class="relative w-12 h-12">
                            <svg class="w-12 h-12 transform -rotate-90" viewBox="0 0 48 48">
                                <circle cx="24" cy="24" r="20" stroke="#f3f4f6" stroke-width="5" fill="none"></circle>
                                <circle cx="24" cy="24" r="20" stroke="currentColor" stroke-width="5" fill="none" stroke-linecap="round" class="kpi-ring {{ $card['ring'] }}" stroke-dasharray="125.66" stroke-dashoffset="125.66" data-pct="{{ min(100, ($card['raw'] / $card['max']) * 100) }}"></circle>
                            </svg>
                            <div class="absolute inset-0 flex items-center justify-center">
                                <svg class="w-5 h-5 {{ $card['text'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $card['icon'] }}"/></svg>
                            </div>
                        </div>
                    </div>
                    <div class="mt-3 h-1 bg-gray-100 rounded-full overflow-hidden">
                        <div class="h-full {{ $card['bar'] }} kpi-bar" style="width: 0%" data-width="{{ min(100, ($card['raw'] / $card['max']) * 100) }}%"></div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Paid vs Pending Today --}}
        <div class="bg-white rounded-xl border border-gray-100 p-6 shadow-sm flex flex-col">
            <div class="flex items-center justify-between mb-2">
                <h2 class="text-sm font-semibold text-gray-900">Paid vs Pending Today</h2>
                <span class="text-xs text-gray-400">TSh</span>
            </div>
            <div class="relative h-44 flex-grow">
                <canvas id="paidVsPendingChart"></canvas>
            </div>
            <div class="mt-4 grid grid-cols-2 gap-2 text-xs">
                <div class="flex items-center justify-between p-2 rounded-lg bg-emerald-50">
                    <span class="flex items-center gap-1.5 text-gray-600">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span> Paid
                    </span>
                    <span class="font-semibold text-gray-900">{{ number_format($todayPaidAmount) }}</span>
                </div>
                <div class="flex items-center justify-between p-2 rounded-lg bg-rose-50">
                    <span class="flex items-center gap-1.5 text-gray-600">
                        <span class="w-2 h-2 rounded-full bg-rose-500"></span> Pending
                    </span>
                    <span class="font-semibold text-gray-900">{{ number_format($todayUnpaidAmount) }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Payment Tabs --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <h2 class="text-sm font-semibold text-gray-900">Payment Management</h2>
            <div class="flex flex-wrap gap-2" id="paymentTabs">
                <button data-tab="pending" class="payment-tab active px-3 py-1.5 text-xs font-medium rounded-lg bg-emerald-100 text-emerald-700">Pending <span class="ml-1">{{ $pendingInvoices->count() }}</span></button>
                <button data-tab="history" class="payment-tab px-3 py-1.5 text-xs font-medium rounded-lg text-gray-600 hover:bg-gray-100">History <span class="ml-1">{{ $recentPayments->count() }}</span></button>
                <button data-tab="invoices" class="payment-tab px-3 py-1.5 text-xs font-medium rounded-lg text-gray-600 hover:bg-gray-100">All Invoices <span class="ml-1">{{ $allInvoices->count() }}</span></button>
            </div>
        </div>

        <div class="overflow-x-auto p-0">
            {{-- Pending Payments --}}
            <div id="tab-pending" class="payment-panel">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                        <tr>
                            <th class="px-6 py-3">Visit #</th>
                            <th class="px-6 py-3">Patient</th>
                            <th class="px-6 py-3">Invoice #</th>
                            <th class="px-6 py-3 text-right">Total</th>
                            <th class="px-6 py-3 text-right">Paid</th>
                            <th class="px-6 py-3 text-right">Balance</th>
                            <th class="px-6 py-3">Status</th>
                            <th class="px-6 py-3">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($pendingInvoices as $invoice)
                            <tr class="border-t border-gray-100 hover:bg-gray-50/50 transition-colors">
                                <td class="px-6 py-3 font-medium">{{ $invoice->visit?->visit_number ?? '-' }}</td>
                                <td class="px-6 py-3">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-600 font-bold text-xs">
                                            {{ strtoupper(substr($invoice->patient?->first_name ?? 'U', 0, 1)) }}
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-900">{{ $invoice->patient?->fullName() ?? 'Unknown' }}</p>
                                            <p class="text-xs text-gray-500">{{ $invoice->patient?->mrn ?? '-' }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-3 text-gray-500 font-mono">{{ $invoice->invoice_number }}</td>
                                <td class="px-6 py-3 text-right font-medium">{{ number_format($invoice->total) }} TSh</td>
                                <td class="px-6 py-3 text-right text-gray-500">{{ number_format($invoice->paid) }} TSh</td>
                                <td class="px-6 py-3 text-right font-semibold text-rose-600">{{ number_format($invoice->total - $invoice->paid) }} TSh</td>
                                <td class="px-6 py-3">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $invoice->status === 'paid' ? 'bg-emerald-100 text-emerald-700' : ($invoice->status === 'partial' ? 'bg-amber-100 text-amber-700' : 'bg-rose-100 text-rose-700') }}">
                                        {{ ucfirst($invoice->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-3">
                                    <div class="flex items-center gap-2">
                                        @if($invoice->visit)
                                            <button onclick="openPaymentModal({{ $invoice->visit->id }}, '{{ $invoice->patient?->fullName() ?? 'Unknown' }}', '{{ $invoice->invoice_number }}', {{ $invoice->total }}, {{ $invoice->paid }}, {{ $invoice->total - $invoice->paid }})" class="bg-emerald-600 text-white text-xs font-medium px-3 py-1.5 rounded-lg hover:bg-emerald-700 transition-colors">Pay & Close</button>
                                            <button onclick="openPaymentModal({{ $invoice->visit->id }}, '{{ $invoice->patient?->fullName() ?? 'Unknown' }}', '{{ $invoice->invoice_number }}', {{ $invoice->total }}, {{ $invoice->paid }}, {{ $invoice->total - $invoice->paid }}, true)" class="bg-white border border-gray-200 text-gray-700 text-xs font-medium px-3 py-1.5 rounded-lg hover:bg-gray-50 transition-colors">Pay</button>
                                            <form method="POST" action="{{ route('reception.visits.close', $invoice->visit) }}" class="ajax-close-form" data-visit="{{ $invoice->visit->visit_number }}">
                                                @csrf
                                                <button type="submit" class="bg-gray-100 text-gray-600 text-xs font-medium px-3 py-1.5 rounded-lg hover:bg-gray-200 transition-colors" title="Close without payment">Close</button>
                                            </form>
                                        @endif
                                        <form method="POST" action="{{ route('reception.invoices.mark-paid', $invoice) }}" class="ajax-mark-paid-form" data-invoice="{{ $invoice->invoice_number }}">
                                            @csrf
                                            <button type="submit" class="bg-blue-100 text-blue-700 text-xs font-medium px-3 py-1.5 rounded-lg hover:bg-blue-200 transition-colors" title="Mark invoice as paid">Mark Paid</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="8" class="px-6 py-6 text-center text-gray-400">No pending invoices</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Payment History --}}
            <div id="tab-history" class="payment-panel hidden">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                        <tr>
                            <th class="px-6 py-3">Visit #</th>
                            <th class="px-6 py-3">Patient</th>
                            <th class="px-6 py-3">Amount</th>
                            <th class="px-6 py-3">Method</th>
                            <th class="px-6 py-3">Reference</th>
                            <th class="px-6 py-3">Received By</th>
                            <th class="px-6 py-3">Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($recentPayments as $payment)
                            <tr class="border-t border-gray-100 hover:bg-gray-50/50 transition-colors">
                                <td class="px-6 py-3 font-medium">{{ $payment->invoice?->visit?->visit_number ?? '-' }}</td>
                                <td class="px-6 py-3">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-600 font-bold text-xs">
                                            {{ strtoupper(substr($payment->invoice?->patient?->first_name ?? 'U', 0, 1)) }}
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-900">{{ $payment->invoice?->patient?->fullName() ?? 'Unknown' }}</p>
                                            <p class="text-xs text-gray-500">{{ $payment->invoice?->patient?->mrn ?? '-' }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-3 font-semibold text-emerald-700">{{ number_format($payment->amount) }} TSh</td>
                                <td class="px-6 py-3">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-700 capitalize">
                                        {{ $paymentMethods[$payment->method] ?? ucfirst(str_replace('_', ' ', $payment->method)) }}
                                    </span>
                                </td>
                                <td class="px-6 py-3 text-gray-500 text-xs font-mono">{{ $payment->reference ?? '-' }}</td>
                                <td class="px-6 py-3 text-xs">{{ $payment->receivedBy?->name ?? '-' }}</td>
                                <td class="px-6 py-3 text-xs text-gray-500">{{ $payment->created_at->format('M d, Y H:i') }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="px-6 py-6 text-center text-gray-400">No payments recorded</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- All Invoices --}}
            <div id="tab-invoices" class="payment-panel hidden">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                        <tr>
                            <th class="px-6 py-3">Visit #</th>
                            <th class="px-6 py-3">Patient</th>
                            <th class="px-6 py-3">Invoice #</th>
                            <th class="px-6 py-3 text-right">Total</th>
                            <th class="px-6 py-3 text-right">Paid</th>
                            <th class="px-6 py-3 text-right">Balance</th>
                            <th class="px-6 py-3">Status</th>
                            <th class="px-6 py-3">Date</th>
                            <th class="px-6 py-3">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($allInvoices as $invoice)
                            <tr class="border-t border-gray-100 hover:bg-gray-50/50 transition-colors">
                                <td class="px-6 py-3 font-medium">{{ $invoice->visit?->visit_number ?? '-' }}</td>
                                <td class="px-6 py-3">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-600 font-bold text-xs">
                                            {{ strtoupper(substr($invoice->patient?->first_name ?? 'U', 0, 1)) }}
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-900">{{ $invoice->patient?->fullName() ?? 'Unknown' }}</p>
                                            <p class="text-xs text-gray-500">{{ $invoice->patient?->mrn ?? '-' }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-3 text-gray-500 font-mono">{{ $invoice->invoice_number }}</td>
                                <td class="px-6 py-3 text-right font-medium">{{ number_format($invoice->total) }} TSh</td>
                                <td class="px-6 py-3 text-right text-gray-500">{{ number_format($invoice->paid) }} TSh</td>
                                <td class="px-6 py-3 text-right font-semibold {{ ($invoice->total - $invoice->paid) > 0 ? 'text-rose-600' : 'text-emerald-600' }}">{{ number_format($invoice->total - $invoice->paid) }} TSh</td>
                                <td class="px-6 py-3">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $invoice->status === 'paid' ? 'bg-emerald-100 text-emerald-700' : ($invoice->status === 'partial' ? 'bg-amber-100 text-amber-700' : 'bg-rose-100 text-rose-700') }}">
                                        {{ ucfirst($invoice->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-3 text-xs text-gray-500">{{ $invoice->created_at->format('M d, Y') }}</td>
                                <td class="px-6 py-3">
                                    <div class="flex items-center gap-2">
                                        @if($invoice->status !== 'paid')
                                            <form method="POST" action="{{ route('reception.invoices.mark-paid', $invoice) }}" class="ajax-mark-paid-form" data-invoice="{{ $invoice->invoice_number }}">
                                                @csrf
                                                <button type="submit" class="bg-emerald-100 text-emerald-700 text-xs font-medium px-3 py-1.5 rounded-lg hover:bg-emerald-200 transition-colors">Mark Paid</button>
                                            </form>
                                        @endif
                                        @if($invoice->status === 'paid')
                                            <form method="POST" action="{{ route('reception.invoices.mark-unpaid', $invoice) }}" class="ajax-mark-unpaid-form" data-invoice="{{ $invoice->invoice_number }}">
                                                @csrf
                                                <button type="submit" class="bg-rose-100 text-rose-700 text-xs font-medium px-3 py-1.5 rounded-lg hover:bg-rose-200 transition-colors">Mark Unpaid</button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="9" class="px-6 py-6 text-center text-gray-400">No invoices</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- Record Payment Modal --}}
<div id="paymentModal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" onclick="closePaymentModal()"></div>
    <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-full max-w-md">
        <div class="bg-white rounded-2xl shadow-2xl p-6 m-4 animate-fade">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-bold text-gray-900">Record Payment</h3>
                <button onclick="closePaymentModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="mb-4 p-3 bg-gray-50 rounded-lg space-y-1 text-sm">
                <div class="flex justify-between"><span class="text-gray-500">Patient</span><span class="font-medium text-gray-900" id="modalPatientName">-</span></div>
                <div class="flex justify-between"><span class="text-gray-500">Invoice</span><span class="font-medium text-gray-900" id="modalInvoiceNumber">-</span></div>
                <div class="flex justify-between"><span class="text-gray-500">Total</span><span class="font-medium text-gray-900" id="modalTotal">0 TSh</span></div>
                <div class="flex justify-between"><span class="text-gray-500">Paid</span><span class="font-medium text-gray-900" id="modalPaid">0 TSh</span></div>
                <div class="flex justify-between"><span class="text-gray-500">Balance</span><span class="font-medium text-rose-600" id="modalBalance">0 TSh</span></div>
            </div>
            <form id="paymentForm" method="POST" action="" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Amount (TSh)</label>
                    <input type="number" name="amount" step="0.01" id="paymentAmount" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" required>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Payment Method</label>
                    <select name="method" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" required>
                        <option value="cash">Cash</option>
                        <option value="card">Card</option>
                        <option value="mobile_money">Mobile Money</option>
                        <option value="insurance">Insurance</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Reference</label>
                    <input type="text" name="reference" placeholder="Receipt or transaction reference" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                </div>
                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" onclick="closePaymentModal()" class="px-4 py-2 text-sm text-gray-600 hover:bg-gray-100 rounded-lg">Cancel</button>
                    <button type="button" id="recordPaymentBtn" class="px-4 py-2 bg-white border border-gray-200 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50">Record Payment</button>
                    <button type="button" id="payAndCloseBtn" class="px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700">Pay & Close</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    const paidVsPendingCtx = document.getElementById('paidVsPendingChart').getContext('2d');
    const paidVsPendingChart = new Chart(paidVsPendingCtx, {
        type: 'doughnut',
        data: {
            labels: ['Paid', 'Pending'],
            datasets: [{
                data: @json(array_values($paidVsPending)),
                backgroundColor: ['#10b981', '#f43f5e'],
                borderWidth: 0,
                hoverOffset: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '70%',
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.label + ': TSh ' + context.raw.toLocaleString();
                        }
                    }
                }
            }
        }
    });

    function animateKPIs() {
        document.querySelectorAll('.kpi-counter').forEach(el => {
            const target = parseFloat(el.dataset.target) || 0;
            const prefix = el.dataset.prefix || '';
            const suffix = el.dataset.suffix || '';
            const duration = 1200;
            const startTime = performance.now();
            function update(currentTime) {
                const elapsed = currentTime - startTime;
                const progress = Math.min(elapsed / duration, 1);
                const ease = 1 - Math.pow(1 - progress, 3);
                const value = target * ease;
                el.textContent = prefix + Math.round(value).toLocaleString() + suffix;
                if (progress < 1) requestAnimationFrame(update);
            }
            requestAnimationFrame(update);
        });

        document.querySelectorAll('.kpi-bar').forEach(bar => {
            setTimeout(() => bar.style.width = bar.dataset.width, 300);
        });

        document.querySelectorAll('.kpi-ring').forEach(ring => {
            setTimeout(() => {
                const pct = parseFloat(ring.dataset.pct) || 0;
                const circumference = 125.66;
                const offset = circumference * (1 - Math.min(pct, 100) / 100);
                ring.style.strokeDashoffset = offset;
            }, 300);
        });
    }

    animateKPIs();

    // Payment tabs
    document.querySelectorAll('.payment-tab').forEach(btn => {
        btn.addEventListener('click', () => {
            document.querySelectorAll('.payment-tab').forEach(b => {
                b.classList.remove('active', 'bg-emerald-100', 'text-emerald-700');
                b.classList.add('text-gray-600', 'hover:bg-gray-100');
            });
            btn.classList.add('active', 'bg-emerald-100', 'text-emerald-700');
            btn.classList.remove('text-gray-600', 'hover:bg-gray-100');
            document.querySelectorAll('.payment-panel').forEach(p => p.classList.add('hidden'));
            document.getElementById('tab-' + btn.dataset.tab).classList.remove('hidden');
        });
    });

    // Payment modal
    let modalBalance = 0;

    function openPaymentModal(visitId, patientName, invoiceNumber, total, paid, balance, partialOnly = false) {
        modalBalance = balance;
        document.getElementById('paymentForm').action = '/reception/visits/' + visitId + '/pay';
        document.getElementById('modalPatientName').textContent = patientName;
        document.getElementById('modalInvoiceNumber').textContent = invoiceNumber;
        document.getElementById('modalTotal').textContent = total.toLocaleString() + ' TSh';
        document.getElementById('modalPaid').textContent = paid.toLocaleString() + ' TSh';
        document.getElementById('modalBalance').textContent = balance.toLocaleString() + ' TSh';
        document.getElementById('paymentAmount').value = balance > 0 ? balance.toFixed(2) : '';
        document.getElementById('payAndCloseBtn').classList.toggle('hidden', partialOnly);
        document.getElementById('paymentModal').classList.remove('hidden');
    }

    document.getElementById('recordPaymentBtn').addEventListener('click', function() {
        document.getElementById('paymentForm').dispatchEvent(new Event('submit'));
    });

    document.getElementById('payAndCloseBtn').addEventListener('click', function() {
        document.getElementById('paymentAmount').value = modalBalance.toFixed(2);
        document.getElementById('paymentForm').dispatchEvent(new Event('submit'));
    });

    function closePaymentModal() {
        document.getElementById('paymentModal').classList.add('hidden');
    }

    document.getElementById('paymentForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const form = this;
        const formData = new FormData(form);
        fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
        })
        .then(r => r.json().catch(() => ({})))
        .then(data => {
            Swal.fire({ icon: 'success', title: 'Success', text: data.message || 'Payment recorded successfully', timer: 2000, showConfirmButton: false });
            form.reset();
            closePaymentModal();
            setTimeout(() => window.location.reload(), 1200);
        })
        .catch(err => {
            Swal.fire({ icon: 'error', title: 'Error', text: 'Something went wrong. Please try again.' });
        });
    });

    // Close visit forms (without payment)
    document.querySelectorAll('.ajax-close-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const visit = this.dataset.visit;
            Swal.fire({
                title: 'Close visit?',
                text: 'Close file for ' + visit + ' without recording a payment?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, close',
                cancelButtonText: 'Cancel',
                confirmButtonColor: '#10b981'
            }).then((result) => {
                if (!result.isConfirmed) return;
                const formData = new FormData(this);
                fetch(this.action, {
                    method: 'POST',
                    body: formData,
                    headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
                })
                .then(r => r.json().catch(() => ({})))
                .then(data => {
                    Swal.fire({ icon: 'success', title: 'Closed', text: data.message || 'Visit closed', timer: 2000, showConfirmButton: false });
                    setTimeout(() => window.location.reload(), 1200);
                })
                .catch(err => {
                    Swal.fire({ icon: 'error', title: 'Error', text: 'Could not close visit.' });
                });
            });
        });
    });

    // Mark invoice as paid
    document.querySelectorAll('.ajax-mark-paid-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const invoice = this.dataset.invoice;
            Swal.fire({
                title: 'Mark as paid?',
                text: 'Mark invoice ' + invoice + ' as paid and close the visit?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Yes, mark paid',
                cancelButtonText: 'Cancel',
                confirmButtonColor: '#10b981'
            }).then((result) => {
                if (!result.isConfirmed) return;
                const formData = new FormData(this);
                fetch(this.action, {
                    method: 'POST',
                    body: formData,
                    headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
                })
                .then(r => r.json().catch(() => ({})))
                .then(data => {
                    Swal.fire({ icon: 'success', title: 'Paid', text: data.message || 'Invoice marked as paid', timer: 2000, showConfirmButton: false });
                    setTimeout(() => window.location.reload(), 1200);
                })
                .catch(err => {
                    Swal.fire({ icon: 'error', title: 'Error', text: 'Could not mark invoice as paid.' });
                });
            });
        });
    });

    // Mark invoice as unpaid
    document.querySelectorAll('.ajax-mark-unpaid-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const invoice = this.dataset.invoice;
            Swal.fire({
                title: 'Mark as unpaid?',
                text: 'Revert invoice ' + invoice + ' to unpaid? This will reset the paid amount to 0.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, mark unpaid',
                cancelButtonText: 'Cancel',
                confirmButtonColor: '#f43f5e'
            }).then((result) => {
                if (!result.isConfirmed) return;
                const formData = new FormData(this);
                fetch(this.action, {
                    method: 'POST',
                    body: formData,
                    headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
                })
                .then(r => r.json().catch(() => ({})))
                .then(data => {
                    Swal.fire({ icon: 'success', title: 'Unpaid', text: data.message || 'Invoice marked as unpaid', timer: 2000, showConfirmButton: false });
                    setTimeout(() => window.location.reload(), 1200);
                })
                .catch(err => {
                    Swal.fire({ icon: 'error', title: 'Error', text: 'Could not mark invoice as unpaid.' });
                });
            });
        });
    });

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closePaymentModal();
    });
</script>
@endpush
