<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoices Report - {{ config('app.name') }}</title>
    <style>
        @page { margin: 40px 42px 70px 42px; }
        body { font-family: Helvetica, Arial, sans-serif; font-size: 11.5px; color: #1f2937; margin: 0; }
        .clinic { font-size: 18px; font-weight: bold; color: #1e293b; }
        .clinic-sub { font-size: 9px; color: #6b7280; margin-top: 3px; }
        .doc-title { font-size: 20px; font-weight: bold; color: #94a3b8; letter-spacing: 2px; text-align: right; }
        .doc-meta { font-size: 9.5px; color: #4b5563; text-align: right; margin-top: 4px; line-height: 1.5; }
        .divider { border-bottom: 2px solid #1e293b; margin: 14px 0 16px; }
        .section-label { font-size: 8.5px; font-weight: bold; text-transform: uppercase; letter-spacing: 1px; color: #94a3b8; margin-bottom: 5px; }
        .summary { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .summary td { background: #f8fafc; border: 1px solid #e5e7eb; padding: 8px 12px; }
        .summary .k { font-size: 8.5px; font-weight: bold; text-transform: uppercase; letter-spacing: .5px; color: #64748b; }
        .summary .v { font-size: 13px; font-weight: bold; color: #0f172a; margin-top: 2px; }
        .invoice { margin-bottom: 18px; border: 1px solid #e5e7eb; padding: 14px 16px; page-break-inside: avoid; }
        .inv-head { width: 100%; margin-bottom: 10px; }
        .inv-no { font-size: 12.5px; font-weight: bold; color: #1e293b; }
        .inv-sub { font-size: 9.5px; color: #6b7280; margin-top: 2px; }
        .status { display: inline-block; font-size: 8.5px; font-weight: bold; text-transform: uppercase; letter-spacing: .5px; padding: 2px 8px; border-radius: 8px; }
        .status-paid { background: #d1fae5; color: #065f46; }
        .status-partial { background: #fef3c7; color: #92400e; }
        .status-unpaid { background: #fee2e2; color: #991b1b; }
        .patient { background: #f8fafc; padding: 7px 10px; font-size: 10.5px; color: #374151; margin-bottom: 10px; }
        table.items { width: 100%; border-collapse: collapse; }
        table.items thead th { background: #1e293b; color: #ffffff; font-size: 8.5px; font-weight: bold; text-transform: uppercase; letter-spacing: .5px; padding: 6px 9px; text-align: left; }
        table.items th.num, table.items td.num { text-align: right; }
        table.items th.ctr, table.items td.ctr { text-align: center; }
        table.items td { padding: 6px 9px; border-bottom: 1px solid #e5e7eb; font-size: 10.5px; vertical-align: top; }
        table.items tr { page-break-inside: avoid; }
        .totals td { padding: 2px 0; font-size: 10.5px; }
        .totals .lbl { color: #6b7280; text-align: right; padding-right: 12px; }
        .totals .val { text-align: right; font-weight: bold; color: #111827; }
        .totals tr.grand td { border-top: 1.5px solid #1e293b; padding-top: 5px; }
        .grand-box { margin-top: 8px; border: 2px solid #1e293b; padding: 12px 16px; page-break-inside: avoid; }
        .end { text-align: center; margin-top: 24px; color: #9ca3af; font-size: 9px; }
        .footer { position: fixed; bottom: -48px; left: 0; right: 0; border-top: 1px solid #e5e7eb; padding-top: 6px; font-size: 8.5px; color: #9ca3af; }
    </style>
</head>
<body>
    @php
        $grandTotal = $invoices->sum('total');
        $grandPaid = $invoices->sum('paid');
        $grandBalance = $grandTotal - $grandPaid;
    @endphp

    <div class="footer">
        <table width="100%"><tr>
            <td>{{ config('app.name') }} &middot; Mlimani City, Dar es Salaam &middot; +255 678 233 736</td>
            <td style="text-align:right">Generated {{ now()->format('d M Y, H:i') }}</td>
        </tr></table>
    </div>

    <table width="100%"><tr>
        <td width="55%">
            <div class="clinic">{{ config('app.name') }}</div>
            <div class="clinic-sub">Reproductive Health &amp; Family Planning Clinic</div>
        </td>
        <td>
            <div class="doc-title">INVOICES REPORT</div>
            <div class="doc-meta">{{ $invoices->count() }} invoices &middot; Generated {{ now()->format('d M Y, g:i A') }}</div>
        </td>
    </tr></table>

    <div class="divider"></div>

    <table class="summary">
        <tr>
            <td width="25%"><div class="k">Invoices</div><div class="v">{{ $invoices->count() }}</div></td>
            <td width="25%"><div class="k">Total Billed</div><div class="v">TSh {{ number_format($grandTotal, 2) }}</div></td>
            <td width="25%"><div class="k">Total Paid</div><div class="v" style="color:#047857">TSh {{ number_format($grandPaid, 2) }}</div></td>
            <td width="25%"><div class="k">Outstanding</div><div class="v" style="color:{{ $grandBalance > 0 ? '#b45309' : '#047857' }}">TSh {{ number_format($grandBalance, 2) }}</div></td>
        </tr>
    </table>

    @foreach($invoices as $invoice)
        <div class="invoice">
            <table class="inv-head"><tr>
                <td>
                    <div class="inv-no">Invoice #{{ $invoice->invoice_number }}</div>
                    <div class="inv-sub">{{ $invoice->created_at->format('d M Y') }}</div>
                </td>
                <td style="text-align:right; vertical-align:top;">
                    <span class="status status-{{ $invoice->status }}">{{ ucfirst($invoice->status) }}</span>
                </td>
            </tr></table>

            <div class="patient">
                <strong>{{ $invoice->patient->fullName() }}</strong>
                &nbsp;&middot;&nbsp; MRN: {{ $invoice->patient->mrn ?? '-' }}
                &nbsp;&middot;&nbsp; {{ $invoice->patient->phone ?? '-' }}
            </div>

            <table class="items">
                <thead>
                    <tr>
                        <th>Service / Item</th>
                        <th class="ctr" width="55">Qty</th>
                        <th class="num" width="105">Price</th>
                        <th class="num" width="105">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @if($invoice->items && $invoice->items->count() > 0)
                        @foreach($invoice->items as $item)
                            <tr>
                                <td>{{ $item->description }}</td>
                                <td class="ctr">{{ $item->quantity }}</td>
                                <td class="num">TSh {{ number_format($item->price, 2) }}</td>
                                <td class="num">TSh {{ number_format($item->quantity * $item->price, 2) }}</td>
                            </tr>
                        @endforeach
                    @else
                        <tr><td colspan="4" style="text-align:center; color:#9ca3af;">No items</td></tr>
                    @endif
                </tbody>
            </table>

            <table width="100%" class="totals" style="margin-top:8px;">
                <tr>
                    <td width="65%"></td>
                    <td>
                        <table width="100%">
                            <tr><td class="lbl">Total</td><td class="val">TSh {{ number_format($invoice->total, 2) }}</td></tr>
                            <tr><td class="lbl">Paid</td><td class="val" style="color:#047857">TSh {{ number_format($invoice->paid, 2) }}</td></tr>
                            <tr class="grand"><td class="lbl"><strong>Balance</strong></td><td class="val" style="color:{{ ($invoice->total - $invoice->paid) > 0 ? '#b45309' : '#047857' }}">TSh {{ number_format($invoice->total - $invoice->paid, 2) }}</td></tr>
                        </table>
                    </td>
                </tr>
            </table>
        </div>
    @endforeach

    <div class="grand-box">
        <table width="100%"><tr>
            <td>
                <div class="section-label">Report Totals</div>
                <div style="font-size:12px; font-weight:bold; color:#0f172a;">
                    TSh {{ number_format($grandTotal, 2) }} billed
                    &nbsp;&middot;&nbsp; TSh {{ number_format($grandPaid, 2) }} collected
                    &nbsp;&middot;&nbsp; TSh {{ number_format($grandBalance, 2) }} outstanding
                </div>
            </td>
        </tr></table>
    </div>

    <div class="end">End of report &mdash; {{ $invoices->count() }} invoices</div>

    <script type="text/php">
        if (isset($pdf)) {
            $font = $fontMetrics->getFont("Helvetica", "normal");
            $pdf->page_text(505, 818, "Page {PAGE_NUM} of {PAGE_COUNT}", $font, 8, array(0.42, 0.45, 0.5));
        }
    </script>
</body>
</html>
