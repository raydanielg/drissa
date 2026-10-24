@extends('layouts.dashboard')

@section('title', 'Invoices - ' . config('app.name', 'Laravel'))
@section('page_title', 'Financial Management - Invoices')

@section('content')
<div class="space-y-5">
    @if (session('status'))
        <div class="p-3 rounded-md bg-white border-l-2 border-emerald-500 text-emerald-700 text-xs">{{ session('status') }}</div>
    @endif

    <script>
function viewReceipt(invoiceId) {
    const modal = document.getElementById('receiptSlideOver');
    const panel = document.getElementById('receiptPanel');
    const content = document.getElementById('receiptContent');
    
    content.innerHTML = '<div class="flex items-center justify-center py-10"><div class="animate-spin rounded-full h-8 w-8 border-b-2 border-slate-700"></div></div>';
    
    modal.classList.remove('hidden');
    setTimeout(() => {
        panel.classList.remove('translate-x-full');
    }, 10);
    document.body.style.overflow = 'hidden';
    
    fetch('/invoices/' + invoiceId)
        .then(r => r.text())
        .then(html => {
            content.innerHTML = html;
        })
        .catch(err => {
            content.innerHTML = '<div class="text-center text-red-600">Failed to load receipt</div>';
        });
}

function closeReceipt() {
    const modal = document.getElementById('receiptSlideOver');
    const panel = document.getElementById('receiptPanel');
    panel.classList.add('translate-x-full');
    setTimeout(() => {
        modal.classList.add('hidden');
        document.body.style.overflow = '';
    }, 300);
}
</script>
    <div class="bg-slate-800 rounded-md p-6 text-white">
        <div class="flex flex-wrap items-end justify-between gap-3">
            <div>
                <p class="text-[10px] uppercase tracking-wider text-slate-400 mb-1">Billing</p>
                <h1 class="text-xl font-bold">Invoices</h1>
                <p class="text-xs text-slate-400 mt-1">Manage patient billing and receipts.</p>
            </div>
            <a href="{{ route('invoices.download-all') }}" class="inline-flex items-center gap-1.5 bg-white hover:bg-gray-100 text-slate-800 text-xs font-semibold px-3 py-1.5 rounded transition-colors">
                <i class="hgi-stroke hgi-download-02 text-sm"></i>
                Download All PDF
            </a>
        </div>
    </div>

    <script>
function viewReceipt(invoiceId) {
    const modal = document.getElementById('receiptSlideOver');
    const panel = document.getElementById('receiptPanel');
    const content = document.getElementById('receiptContent');
    
    content.innerHTML = '<div class="flex items-center justify-center py-10"><div class="animate-spin rounded-full h-8 w-8 border-b-2 border-slate-700"></div></div>';
    
    modal.classList.remove('hidden');
    setTimeout(() => {
        panel.classList.remove('translate-x-full');
    }, 10);
    document.body.style.overflow = 'hidden';
    
    fetch('/invoices/' + invoiceId)
        .then(r => r.text())
        .then(html => {
            content.innerHTML = html;
        })
        .catch(err => {
            content.innerHTML = '<div class="text-center text-red-600">Failed to load receipt</div>';
        });
}

function closeReceipt() {
    const modal = document.getElementById('receiptSlideOver');
    const panel = document.getElementById('receiptPanel');
    panel.classList.add('translate-x-full');
    setTimeout(() => {
        modal.classList.add('hidden');
        document.body.style.overflow = '';
    }, 300);
}
</script>
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
        <div class="bg-white rounded-md border border-gray-200 p-4">
            <div class="flex items-start justify-between">
                <p class="text-[10px] font-medium text-gray-500 uppercase tracking-wide">Total Invoices</p>
                <i class="hgi-stroke hgi-file-02 text-base text-gray-400"></i>
            </div>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['total'] ?? 0 }}</p>
        </div>
        <div class="bg-white rounded-md border border-emerald-200 p-4">
            <div class="flex items-start justify-between">
                <p class="text-[10px] font-medium text-gray-500 uppercase tracking-wide">Total Paid</p>
                <i class="hgi-stroke hgi-checkmark-circle-02 text-base text-emerald-500"></i>
            </div>
            <p class="text-2xl font-bold text-emerald-600 mt-1">TSh {{ number_format($stats['paid'] ?? 0, 0) }}</p>
        </div>
        <div class="bg-white rounded-md border border-amber-200 p-4">
            <div class="flex items-start justify-between">
                <p class="text-[10px] font-medium text-gray-500 uppercase tracking-wide">Pending Balance</p>
                <i class="hgi-stroke hgi-money-03 text-base text-amber-500"></i>
            </div>
            <p class="text-2xl font-bold text-amber-600 mt-1">TSh {{ number_format($stats['pending'] ?? 0, 0) }}</p>
        </div>
        <div class="bg-white rounded-md border border-red-200 p-4">
            <div class="flex items-start justify-between">
                <p class="text-[10px] font-medium text-gray-500 uppercase tracking-wide">Overdue</p>
                <i class="hgi-stroke hgi-cancel-circle text-base text-red-500"></i>
            </div>
            <p class="text-2xl font-bold text-red-600 mt-1">{{ $stats['overdue'] ?? 0 }}</p>
        </div>
    </div>

    <script>
function viewReceipt(invoiceId) {
    const modal = document.getElementById('receiptSlideOver');
    const panel = document.getElementById('receiptPanel');
    const content = document.getElementById('receiptContent');
    
    content.innerHTML = '<div class="flex items-center justify-center py-10"><div class="animate-spin rounded-full h-8 w-8 border-b-2 border-slate-700"></div></div>';
    
    modal.classList.remove('hidden');
    setTimeout(() => {
        panel.classList.remove('translate-x-full');
    }, 10);
    document.body.style.overflow = 'hidden';
    
    fetch('/invoices/' + invoiceId)
        .then(r => r.text())
        .then(html => {
            content.innerHTML = html;
        })
        .catch(err => {
            content.innerHTML = '<div class="text-center text-red-600">Failed to load receipt</div>';
        });
}

function closeReceipt() {
    const modal = document.getElementById('receiptSlideOver');
    const panel = document.getElementById('receiptPanel');
    panel.classList.add('translate-x-full');
    setTimeout(() => {
        modal.classList.add('hidden');
        document.body.style.overflow = '';
    }, 300);
}
</script>
    <div class="bg-white rounded-md border border-gray-200 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
            <h2 class="text-sm font-bold text-gray-900">Invoices Management</h2>
            <div class="flex items-center gap-2">
                <x-ui.select class="text-xs border border-gray-200 rounded px-3 py-1.5 focus:ring-2 focus:ring-slate-300 focus:outline-none bg-white">
                    <option value="">All Status</option>
                    <option value="paid">Paid</option>
                    <option value="partial">Partial</option>
                    <option value="unpaid">Unpaid</option>
                </x-ui.select>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-50 text-[10px] uppercase text-gray-400">
                    <tr>
                        <th class="px-5 py-3">Invoice #</th>
                        <th class="px-5 py-3">Patient</th>
                        <th class="px-5 py-3 text-right">Total</th>
                        <th class="px-5 py-3 text-right">Paid</th>
                        <th class="px-5 py-3 text-right">Balance</th>
                        <th class="px-5 py-3">Status</th>
                        <th class="px-5 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($invoices as $invoice)
                        <tr class="border-t border-gray-50 hover:bg-gray-50/50 transition-colors">
                            <td class="px-5 py-3">
                                <div class="flex items-center gap-2">
                                    <div class="w-8 h-8 rounded bg-slate-100 text-slate-600 flex items-center justify-center text-[10px] font-bold">
                                        INV
                                    </div>
                                    <span class="text-xs text-gray-900 font-semibold">{{ $invoice->invoice_number }}</span>
                                </div>
                            </td>
                            <td class="px-5 py-3">
                                <div class="flex items-center gap-2">
                                    <div class="w-8 h-8 rounded-full bg-slate-100 text-slate-600 flex items-center justify-center text-xs font-bold">
                                        {{ strtoupper(substr($invoice->patient->fullName(), 0, 1)) }}
                                    </div>
                                    <span class="text-xs text-gray-700">{{ $invoice->patient->fullName() }}</span>
                                </div>
                            </td>
                            <td class="px-5 py-3 text-right text-xs font-semibold text-gray-900">TSh {{ number_format($invoice->total, 2) }}</td>
                            <td class="px-5 py-3 text-right text-xs text-gray-600">TSh {{ number_format($invoice->paid, 2) }}</td>
                            <td class="px-5 py-3 text-right text-xs text-gray-600">TSh {{ number_format($invoice->total - $invoice->paid, 2) }}</td>
                            <td class="px-5 py-3">
                                <span class="inline-flex items-center gap-1.5 text-[10px] font-medium capitalize text-gray-600">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $invoice->status === 'paid' ? 'bg-emerald-500' : ($invoice->status === 'partial' ? 'bg-amber-500' : 'bg-red-500') }}"></span>
                                    {{ $invoice->status }}
                                </span>
                            </td>
                            <td class="px-5 py-3 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <button type="button" onclick="viewReceipt({{ $invoice->id }})" class="flex items-center gap-1 px-2.5 py-1.5 bg-slate-800 text-white text-[10px] font-medium rounded hover:bg-slate-700 transition-colors">
                                        Receipt
                                    </button>
                                    <a href="{{ route('invoices.pdf', $invoice) }}" class="flex items-center gap-1 px-2.5 py-1.5 border border-gray-200 text-gray-700 text-[10px] font-medium rounded hover:bg-gray-100 transition-colors">
                                        PDF
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="px-5 py-8 text-center text-gray-400 text-xs">
                            <div class="flex flex-col items-center gap-2">
                                <i class="hgi-stroke hgi-file-02 text-4xl text-gray-300"></i>
                                <p>No invoices found</p>
                            </div>
                        </td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-5 py-3 border-t border-gray-100">
            {{ $invoices->links() }}
        </div>
    </div>
</div>

<script>
function viewReceipt(invoiceId) {
    const modal = document.getElementById('receiptSlideOver');
    const panel = document.getElementById('receiptPanel');
    const content = document.getElementById('receiptContent');
    
    content.innerHTML = '<div class="flex items-center justify-center py-10"><div class="animate-spin rounded-full h-8 w-8 border-b-2 border-slate-700"></div></div>';
    
    modal.classList.remove('hidden');
    setTimeout(() => {
        panel.classList.remove('translate-x-full');
    }, 10);
    document.body.style.overflow = 'hidden';
    
    fetch('/invoices/' + invoiceId)
        .then(r => r.text())
        .then(html => {
            content.innerHTML = html;
        })
        .catch(err => {
            content.innerHTML = '<div class="text-center text-red-600">Failed to load receipt</div>';
        });
}

function closeReceipt() {
    const modal = document.getElementById('receiptSlideOver');
    const panel = document.getElementById('receiptPanel');
    panel.classList.add('translate-x-full');
    setTimeout(() => {
        modal.classList.add('hidden');
        document.body.style.overflow = '';
    }, 300);
}
</script>
<div id="receiptSlideOver" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm transition-opacity" onclick="closeReceipt()"></div>
    <div class="absolute inset-y-0 right-0 w-full max-w-lg bg-white shadow-2xl transform transition-transform translate-x-full duration-300 ease-in-out flex flex-col" id="receiptPanel">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between bg-slate-800">
            <h3 class="text-sm font-semibold text-white">Receipt</h3>
            <button onclick="closeReceipt()" class="p-1.5 rounded-lg hover:bg-white/20 text-white">
                <i class="hgi-stroke hgi-cancel-01 text-base"></i>
            </button>
        </div>
        <div class="flex-1 overflow-y-auto p-6" id="receiptContent">
            <!-- Receipt content will be loaded here -->
        </div>
    </div>
</div>

@push('scripts')
<script>
function viewReceipt(invoiceId) {
    const modal = document.getElementById('receiptSlideOver');
    const panel = document.getElementById('receiptPanel');
    const content = document.getElementById('receiptContent');
    
    content.innerHTML = '<div class="flex items-center justify-center py-10"><div class="animate-spin rounded-full h-8 w-8 border-b-2 border-slate-700"></div></div>';
    
    modal.classList.remove('hidden');
    setTimeout(() => {
        panel.classList.remove('translate-x-full');
    }, 10);
    document.body.style.overflow = 'hidden';
    
    fetch('/invoices/' + invoiceId)
        .then(r => r.text())
        .then(html => {
            content.innerHTML = html;
        })
        .catch(err => {
            content.innerHTML = '<div class="text-center text-red-600">Failed to load receipt</div>';
        });
}

function closeReceipt() {
    const modal = document.getElementById('receiptSlideOver');
    const panel = document.getElementById('receiptPanel');
    panel.classList.add('translate-x-full');
    setTimeout(() => {
        modal.classList.add('hidden');
        document.body.style.overflow = '';
    }, 300);
}
</script>
@endpush
@endsection
