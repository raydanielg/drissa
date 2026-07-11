<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice {{ $invoice->invoice_number }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 13px; color: #333; }
        .header { border-bottom: 2px solid #024938; padding-bottom: 10px; margin-bottom: 20px; }
        .header h1 { color: #024938; margin: 0; }
        .info { margin-bottom: 20px; }
        .info table { width: 100%; }
        .info td { padding: 5px 0; }
        table.items { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        table.items th, table.items td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        table.items th { background: #f3f4f6; }
        .totals { text-align: right; }
        .totals p { margin: 4px 0; }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ config('app.name') }}</h1>
        <p>Invoice #: {{ $invoice->invoice_number }}</p>
    </div>

    <div class="info">
        <table>
            <tr>
                <td><strong>Patient:</strong> {{ $invoice->patient->fullName() }}</td>
                <td style="text-align:right"><strong>Date:</strong> {{ $invoice->created_at->format('d M Y') }}</td>
            </tr>
            <tr>
                <td><strong>Phone:</strong> {{ $invoice->patient->phone ?? '-' }}</td>
                <td style="text-align:right"><strong>Status:</strong> {{ ucfirst($invoice->status) }}</td>
            </tr>
        </table>
    </div>

    <table class="items">
        <thead>
            <tr>
                <th>#</th>
                <th>Description</th>
                <th>Qty</th>
                <th>Unit Price</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($invoice->items as $i => $item)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $item->description }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td>TSh {{ number_format($item->unit_price, 2) }}</td>
                    <td>TSh {{ number_format($item->total, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="totals">
        <p><strong>Total:</strong> TSh {{ number_format($invoice->total, 2) }}</p>
        <p><strong>Paid:</strong> TSh {{ number_format($invoice->paid, 2) }}</p>
        <p><strong>Balance:</strong> TSh {{ number_format($invoice->total - $invoice->paid, 2) }}</p>
    </div>
</body>
</html>
