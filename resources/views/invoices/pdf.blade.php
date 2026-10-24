<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice {{ $invoice->invoice_number }}</title>
    <style>
        @page { margin: 40px 42px 70px 42px; }
        body { font-family: Helvetica, Arial, sans-serif; font-size: 12px; color: #1f2937; margin: 0; }
        .clinic { font-size: 18px; font-weight: bold; color: #1e293b; }
        .clinic-sub { font-size: 9px; color: #6b7280; margin-top: 3px; }
        .doc-title { font-size: 22px; font-weight: bold; color: #94a3b8; letter-spacing: 3px; text-align: right; }
        .meta { font-size: 10px; color: #4b5563; text-align: right; line-height: 1.5; }
        .status { display: inline-block; font-size: 9px; font-weight: bold; text-transform: uppercase; letter-spacing: .5px; padding: 2px 8px; border-radius: 8px; }
        .status-paid { background: #d1fae5; color: #065f46; }
        .status-partial { background: #fef3c7; color: #92400e; }
        .status-unpaid { background: #fee2e2; color: #991b1b; }
        .divider { border-bottom: 2px solid #1e293b; margin: 14px 0 18px; }
        .section-label { font-size: 8.5px; font-weight: bold; text-transform: uppercase; letter-spacing: 1px; color: #94a3b8; margin-bottom: 5px; }
        .info-val { font-size: 11.5px; color: #111827; line-height: 1.5; }
        table.items { width: 100%; border-collapse: collapse; margin-top: 6px; }
        table.items thead th { background: #1e293b; color: #ffffff; font-size: 9px; font-weight: bold; text-transform: uppercase; letter-spacing: .5px; padding: 7px 10px; text-align: left; }
        table.items th.num, table.items td.num { text-align: right; }
        table.items th.ctr, table.items td.ctr { text-align: center; }
        table.items td { padding: 7px 10px; border-bottom: 1px solid #e5e7eb; font-size: 11px; vertical-align: top; }
        table.items tr { page-break-inside: avoid; }
        .totals { margin-top: 14px; }
        .totals td { padding: 3px 0; font-size: 11px; }
        .totals .lbl { color: #6b7280; text-align: right; padding-right: 14px; }
        .totals .val { text-align: right; font-weight: bold; color: #111827; }
        .totals tr.grand td { border-top: 2px solid #1e293b; padding-top: 6px; font-size: 12.5px; }
        .bal-ok { color: #047857; }
        .bal-due { color: #b45309; }
        .note { margin-top: 34px; font-size: 9.5px; color: #6b7280; }
        .footer { position: fixed; bottom: -48px; left: 0; right: 0; border-top: 1px solid #e5e7eb; padding-top: 6px; font-size: 8.5px; color: #9ca3af; }
    </style>
</head>
<body>
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
        <td class="doc-title">INVOICE</td>
    </tr></table>

    <div class="divider"></div>

    <table width="100%">
        <tr>
            <td width="55%" valign="top">
                <div class="section-label">Billed To</div>
                <div class="info-val"><strong>{{ $invoice->patient->fullName() }}</strong></div>
                <div class="info-val" style="color:#6b7280">MRN: {{ $invoice->patient->mrn ?? '-' }}</div>
                <div class="info-val" style="color:#6b7280">{{ $invoice->patient->phone ?? '-' }}</div>
            </td>
            <td width="45%" valign="top" class="meta">
                <div><strong>Invoice #:</strong> {{ $invoice->invoice_number }}</div>
                <div><strong>Date:</strong> {{ $invoice->created_at->format('d M Y') }}</div>
                <div style="margin-top:5px"><span class="status status-{{ $invoice->status }}">{{ ucfirst($invoice->status) }}</span></div>
            </td>
        </tr>
    </table>

    <table class="items">
        <thead>
            <tr>
                <th class="ctr" width="32">#</th>
                <th>Description</th>
                <th class="ctr" width="55">Qty</th>
                <th class="num" width="110">Unit Price</th>
                <th class="num" width="110">Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($invoice->items as $i => $item)
                <tr>
                    <td class="ctr">{{ $i + 1 }}</td>
                    <td>{{ $item->description }}</td>
                    <td class="ctr">{{ $item->quantity }}</td>
                    <td class="num">TSh {{ number_format($item->unit_price, 2) }}</td>
                    <td class="num">TSh {{ number_format($item->total, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table width="100%" class="totals">
        <tr>
            <td width="60%"></td>
            <td>
                <table width="100%">
                    <tr><td class="lbl">Subtotal</td><td class="val">TSh {{ number_format($invoice->total, 2) }}</td></tr>
                    <tr><td class="lbl">Amount Paid</td><td class="val bal-ok">TSh {{ number_format($invoice->paid, 2) }}</td></tr>
                    <tr class="grand"><td class="lbl"><strong>Balance Due</strong></td><td class="val {{ ($invoice->total - $invoice->paid) > 0 ? 'bal-due' : 'bal-ok' }}">TSh {{ number_format($invoice->total - $invoice->paid, 2) }}</td></tr>
                </table>
            </td>
        </tr>
    </table>

    <div class="note">Thank you for choosing {{ config('app.name') }}. Please present this invoice for any billing inquiries.</div>

    <script type="text/php">
        if (isset($pdf)) {
            $font = $fontMetrics->getFont("Helvetica", "normal");
            $pdf->page_text(505, 818, "Page {PAGE_NUM} of {PAGE_COUNT}", $font, 8, array(0.42, 0.45, 0.5));
        }
    </script>
</body>
</html>
