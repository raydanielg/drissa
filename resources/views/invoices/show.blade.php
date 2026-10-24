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
                <div class="mt-1 text-slate-800" style="width:110px;margin:2px auto -10px;">
                    <x-ui.signature :delay="2.4" :duration="1.4" :stroke-width="7" view-box="0 0 1000 320"
                        path="M833.9 65 Q789.8 158.1 767.2 195.5 Q744.5 232.9 725.5 264.2 L706.5 295.4 M572.9 270.8 Q560.7 236.5 552.1 226.1 Q543.5 215.7 531.9 205.3 Q520.2 194.9 513.5 190 Q506.7 185.1 504.9 183.9 Q503.1 182.6 501.9 183.9 Q500.6 185.1 497.6 186.9 Q494.5 188.7 492.7 190.6 Q490.8 192.4 485.9 193.7 Q481 194.9 475.5 195.5 Q470 196.1 458.4 194.3 Q446.7 192.4 435.7 184.5 Q424.6 176.5 419.1 170.4 Q413.6 164.2 411.8 161.1 Q409.9 158.1 409.9 155.1 Q409.9 152 413.6 148.9 Q417.3 145.8 433.2 144.6 Q449.1 143.4 522.7 151.4 Q596.2 159.3 651.4 168.5 Q706.5 177.7 728 181.4 Q749.4 185.1 755.5 186.3 Q761.6 187.5 758.6 187.5 Q755.5 187.5 723.7 180.8 Q691.8 174 642.2 159.3 Q592.5 144.6 547.8 132.4 Q503.1 120.1 445.5 106.6 Q387.9 93.1 381.2 92.5 Q374.4 91.9 373.8 93.8 Q373.2 95.6 373.2 99.3 Q373.2 102.9 372.5 110.9 Q371.9 118.9 368.9 128.7 Q365.8 138.5 364.6 139.7 Q363.4 140.9 359.7 139.1 Q356 137.3 353.6 133 Q351.1 128.7 352.4 126.9 Q353.6 125 357.3 128.1 Q360.9 131.1 364.6 133.6 Q368.3 136 370.1 138.5 Q371.9 140.9 373.2 142.8 Q374.4 144.6 375 146.5 Q375.6 148.3 376.9 149.5 Q378.1 150.7 381.2 151.4 Q384.2 152 400.2 152 Q416.1 152 432.6 150.2 Q449.1 148.3 467.5 145.9 Q485.9 143.4 514.7 144 Q543.5 144.6 572.9 146.5 Q602.3 148.3 620.1 151.4 Q637.9 154.4 655.1 160.6 Q672.2 166.7 685.7 173.5 Q699.1 180.2 714.5 190 Q729.8 199.8 738.3 207.2 Q746.9 214.5 751.2 221.3 Q755.5 228 758.6 231.7 Q761.6 235.3 762.3 239 Q762.9 242.7 764.1 246.4 Q765.3 250 765.3 257.4 Q765.3 264.7 765.3 271.5 Q765.3 278.2 764.1 286.8 L762.9 295.4 M397.7 318.6 Q396.4 315 395.2 310.7 Q394 306.4 395.2 278.2 Q396.4 250 397 224.3 Q397.7 198.5 401.4 173.4 Q405 148.3 405 134.2 Q405 120.1 403.2 117.7 Q401.3 115.2 400.1 113.4 Q398.9 111.5 400.8 116.4 Q402.6 121.3 413.6 135.4 Q424.6 149.5 462 176.5 Q499.4 203.4 516 207.1 Q532.5 210.8 545.4 207.1 Q558.2 203.4 561.9 194.9 Q565.6 186.3 558.9 175.9 Q552.1 165.4 539.9 155 Q527.6 144.6 501.9 135.4 Q476.1 126.2 441.2 121.3 Q406.3 116.4 398.9 117.7 Q391.5 118.9 386 123.2 Q380.5 127.5 379.3 136.1 Q378.1 144.6 379.3 154.4 Q380.5 164.2 386.7 172.8 Q392.8 181.4 397.7 184.5 Q402.6 187.5 400.8 181.4 Q398.9 175.3 390.3 166.1 Q381.7 156.9 368.9 147.7 Q356 138.5 333.4 128.1 Q310.7 117.7 291 113.4 Q271.4 109.1 268.4 109.1 Q265.3 109.1 265.3 106 Q265.3 102.9 277 96.2 Q288.6 89.5 308.2 84.6 Q327.8 79.7 349.9 80.3 Q371.9 80.9 417.3 88.9 Q462.6 96.8 492 103.6 Q521.4 110.3 556.3 120.1 Q591.3 129.9 642.8 144.6 Q694.2 159.3 761.6 175.3 Q829 191.2 892.2 202.2 L955.3 213.2 M781.3 311.3 Q628.1 239 573.6 214.5 Q519 190 487.8 169.8 Q456.5 149.5 440.6 134.8 Q424.6 120.1 420.4 114.6 Q416.1 109.1 414.9 106 Q413.6 102.9 412.4 101.7 Q411.2 100.5 410 98.7 Q408.7 96.8 413.6 96.8 Q418.5 96.8 425.3 103 Q432 109.1 439.4 116.5 Q446.7 123.8 462 134.2 Q477.3 144.6 504.3 147.1 Q531.3 149.5 573 138.5 Q614.6 127.5 635.5 115.2 Q656.3 102.9 659.3 95.6 Q662.4 88.2 656.8 76 Q651.3 63.7 642.8 50.9 Q634.2 38 622 31.3 Q609.7 24.5 591.9 23.9 Q574.1 23.3 556.4 26.4 Q538.6 29.4 534.3 35.6 Q530 41.7 528.8 50.3 Q527.6 58.8 532.5 69.9 Q537.4 80.9 544.8 90.1 Q552.1 99.3 561.9 106.7 Q571.7 114 591.3 116.5 Q610.9 118.9 639.7 115.2 Q668.5 111.5 689.4 106.6 Q710.2 101.7 716.3 99.3 Q722.4 96.8 726.1 101.7 Q729.8 106.6 743.9 122 Q758 137.3 782.5 162.4 Q807 187.5 838.9 219.4 L870.7 251.2 M945.5 319.9 Q943 317.4 942.4 315.5 Q941.8 313.7 940 311.9 Q938.1 310.1 937.5 307.7 Q936.9 305.2 935.7 303.4 Q934.4 301.5 927.7 300.3 Q921 299 899.6 297.8 Q878.1 296.6 859.7 294.2 Q841.3 291.7 833.4 289.3 Q825.4 286.8 816.2 285 L807 283.1 M802.1 290.5 Q809.4 303.9 810.7 307.6 Q811.9 311.3 811.9 313.2 Q811.9 315 813.8 315 L815.6 315 M829 311.3 Q825.4 295.4 824.2 291.7 Q822.9 288 821.7 285.6 Q820.5 283.1 818.1 273.9 Q815.6 264.7 813.8 254.3 Q811.9 243.9 809.5 232.3 Q807 220.6 805.8 203.5 Q804.5 186.3 807 161.2 Q809.4 136 810.7 121.9 Q811.9 107.8 813.8 102.3 Q815.6 96.8 817.4 95.6 Q819.2 94.4 821.7 96.9 Q824.1 99.3 830.9 109.1 Q837.6 118.9 838.9 123.2 Q840.1 127.5 841.3 128.7 Q842.5 129.9 842.5 132.4 Q842.5 134.8 851.7 143.4 Q860.9 152 871.3 163 Q881.7 174 890.3 182.6 Q898.9 191.2 903.2 195.5 Q907.5 199.8 908.1 112.8 L908.7 25.7 M907.5 1.2 Q907.5 51.5 907.5 83.4 Q907.5 115.2 907.5 147.7 Q907.5 180.2 908.7 215.7 L909.9 251.2" />
                </div>
                <p class="text-[8px] uppercase tracking-widest text-gray-400 mt-1">Authorized signature</p>
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

