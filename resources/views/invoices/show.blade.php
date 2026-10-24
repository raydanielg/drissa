@extends('layouts.dashboard')

@section('title', 'Invoice ' . $invoice->invoice_number . ' - ' . config('app.name', 'Laravel'))
@section('page_title', 'Invoice ' . $invoice->invoice_number)

@section('content')
@php
    // serrated torn-paper bottom edge (40 teeth)
    $teeth = [];
    for ($i = 0; $i < 80; $i++) {
        $x = 100 - (($i + 1) * 100) / 80;
        $y = $i % 2 === 0 ? '100%' : 'calc(100% - 5px)';
        $teeth[] = "$x% $y";
    }
    $clip = 'polygon(0 0, 100% 0, 100% calc(100% - 5px), ' . implode(', ', $teeth) . ')';
@endphp

<div class="max-w-sm mx-auto space-y-5">
    {{-- Header actions --}}
    <div class="flex items-center justify-between">
        <a href="{{ route('invoices.index') }}" class="inline-flex items-center gap-1.5 text-xs font-medium text-gray-500 hover:text-gray-800 transition-colors">
            <i class="hgi-stroke hgi-arrow-left-01 text-sm"></i> Invoices
        </a>
        <div class="flex items-center gap-2">
            <button type="button" onclick="window.print()" class="inline-flex items-center gap-1.5 bg-slate-800 hover:bg-slate-700 text-white text-xs font-semibold px-3 py-1.5 rounded transition-colors">
                <i class="hgi-stroke hgi-printer text-sm"></i> Print
            </button>
            <a href="{{ route('invoices.pdf', $invoice) }}" class="inline-flex items-center gap-1.5 border border-gray-200 hover:bg-gray-100 text-gray-700 text-xs font-semibold px-3 py-1.5 rounded transition-colors">
                <i class="hgi-stroke hgi-upload-01 text-sm"></i> PDF
            </a>
        </div>
    </div>

    {{-- Printer machine --}}
    <div class="printer-machine relative rounded-2xl border border-gray-300 bg-gradient-to-b from-gray-200 to-gray-300 p-3 pb-9 shadow-lg">
        {{-- screen --}}
        <div class="flex items-center justify-between rounded-xl border border-gray-300 bg-slate-900 px-4 py-3 shadow-inner">
            <div class="flex items-center gap-2.5 min-w-0">
                <img src="{{ asset('logo.png') }}" alt="" class="w-7 h-7 rounded-md object-cover">
                <div class="min-w-0">
                    <p class="text-[11px] font-semibold text-white truncate">{{ config('app.name', 'Uzazi Clinic') }}</p>
                    <p class="text-[10px] text-slate-400">Thermal printer · #{{ $invoice->invoice_number }}</p>
                </div>
            </div>
            <div class="flex items-center gap-2 shrink-0" role="status" aria-live="polite">
                <span id="printerIcon" class="text-slate-400">
                    <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                </span>
                <span id="printerStatus" class="text-[11px] font-medium text-slate-300">Printing receipt…</span>
            </div>
        </div>
        {{-- output slot --}}
        <div class="absolute inset-x-6 bottom-3 h-2 rounded border border-gray-400 bg-slate-800 shadow-inner"></div>
    </div>

    {{-- Paper output --}}
    <div class="receipt-viewport -mt-4 overflow-hidden px-8 pb-2 relative z-10">
        <div class="absolute inset-x-8 -top-0.5 h-2 bg-slate-800/60 blur-[5px] pointer-events-none z-20"></div>
        <article id="receiptPaper" class="receipt-paper relative bg-white px-6 pt-7 pb-9 font-mono text-[11px] leading-relaxed text-gray-800 shadow-md" style="clip-path: {{ $clip }}">
            {{-- header --}}
            <div class="text-center">
                <img src="{{ asset('logo.png') }}" alt="" class="w-10 h-10 rounded-md object-cover mx-auto mb-2">
                <p class="font-bold text-sm tracking-wide uppercase">{{ config('app.name', 'Uzazi Clinic') }}</p>
                <p class="text-[10px] text-gray-500">Better Life, Brighter Future</p>
            </div>

            <div class="my-3 border-t border-dashed border-gray-300"></div>

            <div class="flex justify-between">
                <span class="text-gray-500">Receipt No</span>
                <span class="font-semibold">#{{ $invoice->invoice_number }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-500">Date</span>
                <span>{{ $invoice->created_at->format('d M Y, H:i') }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-500">Patient</span>
                <span class="font-semibold">{{ $invoice->patient->fullName() }}</span>
            </div>
            @if($invoice->patient->phone)
            <div class="flex justify-between">
                <span class="text-gray-500">Phone</span>
                <span>{{ $invoice->patient->phone }}</span>
            </div>
            @endif

            <div class="my-3 border-t border-dashed border-gray-300"></div>

            {{-- items --}}
            <div class="flex justify-between font-semibold uppercase text-[9px] tracking-wider text-gray-400 mb-1">
                <span>Item</span>
                <span>Total</span>
            </div>
            @foreach ($invoice->items as $item)
            <div class="flex justify-between gap-2">
                <span class="truncate">{{ $item->quantity }}&times; {{ $item->description }}</span>
                <span class="shrink-0">{{ number_format($item->total, 0) }}</span>
            </div>
            @endforeach

            <div class="my-3 border-t border-dashed border-gray-300"></div>

            <div class="flex justify-between font-bold text-[13px]">
                <span>TOTAL</span>
                <span>TSh {{ number_format($invoice->total, 0) }}</span>
            </div>
            <div class="flex justify-between text-gray-500">
                <span>Paid</span>
                <span>TSh {{ number_format($invoice->paid, 0) }}</span>
            </div>
            <div class="flex justify-between {{ $invoice->total - $invoice->paid > 0 ? 'text-red-600 font-semibold' : 'text-gray-500' }}">
                <span>Balance</span>
                <span>TSh {{ number_format(max(0, $invoice->total - $invoice->paid), 0) }}</span>
            </div>

            <div class="my-3 border-t border-dashed border-gray-300"></div>

            <div class="text-center">
                <span class="inline-block border text-[9px] font-bold uppercase tracking-widest rounded px-2 py-0.5 {{ $invoice->status === 'paid' ? 'border-emerald-500 text-emerald-600' : 'border-amber-500 text-amber-600' }}">{{ $invoice->status }}</span>
                <p class="text-[10px] text-gray-500 mt-3">Asante kwa kutembelea {{ config('app.name', 'Uzazi Clinic') }}</p>
                <p class="text-[9px] text-gray-400">Powered by {{ config('app.name') }} HIS</p>
            </div>
        </article>
    </div>
</div>
@endsection

@push('styles')
<style>
/* stepped thermal-printer feed */
@keyframes receiptFeed {
    0%   { transform: translateY(calc(-100% + 2px)); }
    7%   { transform: translateY(-91%); }
    10%  { transform: translateY(-91%); }
    18%  { transform: translateY(-81%); }
    21%  { transform: translateY(-81%); }
    28%  { transform: translateY(-70%); }
    31%  { transform: translateY(-70%); }
    39%  { transform: translateY(-58%); }
    42%  { transform: translateY(-58%); }
    49%  { transform: translateY(-45%); }
    52%  { transform: translateY(-45%); }
    60%  { transform: translateY(-32%); }
    63%  { transform: translateY(-32%); }
    70%  { transform: translateY(-20%); }
    73%  { transform: translateY(-20%); }
    81%  { transform: translateY(-10%); }
    84%  { transform: translateY(-10%); }
    91%  { transform: translateY(-3%); }
    94%  { transform: translateY(-3%); }
    100% { transform: translateY(0); }
}
.receipt-paper { animation: receiptFeed 1.9s linear .3s both; }
@media (prefers-reduced-motion: reduce) {
    .receipt-paper { animation: none; }
}
@media print {
    body * { visibility: hidden; }
    #receiptPaper, #receiptPaper * { visibility: visible; }
    #receiptPaper { position: absolute; inset: 0; animation: none; transform: none; box-shadow: none; width: 100%; }
}
</style>
@endpush

@push('scripts')
<script>
setTimeout(function () {
    var icon = document.getElementById('printerIcon');
    var status = document.getElementById('printerStatus');
    if (icon) icon.innerHTML = '<i class="hgi-stroke hgi-checkmark-circle-02 text-base text-emerald-400"></i>';
    if (status) { status.textContent = 'Receipt printed'; status.classList.remove('text-slate-300'); status.classList.add('text-emerald-400'); }
}, 2300);
</script>
@endpush

