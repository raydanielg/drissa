<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>All Invoices - {{ config('app.name') }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; color: #333; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #10b981; padding-bottom: 20px; }
        .header h1 { color: #10b981; margin: 0; }
        .header p { color: #666; margin: 5px 0 0; }
        .invoice { margin-bottom: 40px; border: 1px solid #e5e7eb; border-radius: 8px; padding: 20px; page-break-inside: avoid; }
        .invoice-header { display: flex; justify-content: space-between; margin-bottom: 20px; }
        .invoice-number { font-size: 14px; font-weight: bold; color: #10b981; }
        .invoice-date { color: #666; }
        .patient-info { background: #f9fafb; padding: 15px; border-radius: 6px; margin-bottom: 20px; }
        .patient-info h3 { margin: 0 0 10px; color: #374151; }
        .patient-info p { margin: 5px 0; color: #6b7280; }
        .table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .table th { background: #10b981; color: white; padding: 10px; text-align: left; font-weight: 600; }
        .table td { padding: 10px; border-bottom: 1px solid #e5e7eb; }
        .table tr:last-child td { border-bottom: none; }
        .total-section { text-align: right; margin-top: 20px; }
        .total-row { display: flex; justify-content: flex-end; gap: 20px; margin-bottom: 5px; }
        .total-row span { font-weight: 600; }
        .grand-total { font-size: 16px; color: #10b981; font-weight: bold; }
        .status { padding: 4px 12px; border-radius: 12px; font-size: 11px; font-weight: 600; text-transform: uppercase; }
        .status.paid { background: #d1fae5; color: #065f46; }
        .status.partial { background: #fef3c7; color: #92400e; }
        .status.unpaid { background: #fee2e2; color: #991b1b; }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ config('app.name') }}</h1>
        <p>All Invoices Report</p>
        <p>Generated: {{ now()->format('F j, Y - g:i A') }}</p>
    </div>

    @foreach($invoices as $invoice)
        <div class="invoice">
            <div class="invoice-header">
                <div>
                    <div class="invoice-number">Invoice #{{ $invoice->invoice_number }}</div>
                    <div class="invoice-date">Date: {{ $invoice->created_at->format('F j, Y') }}</div>
                </div>
                <div>
                    <span class="status {{ $invoice->status }}">{{ $invoice->status }}</span>
                </div>
            </div>

            <div class="patient-info">
                <h3>Patient Information</h3>
                <p><strong>Name:</strong> {{ $invoice->patient->fullName() }}</p>
                <p><strong>MRN:</strong> {{ $invoice->patient->mrn }}</p>
                <p><strong>Phone:</strong> {{ $invoice->patient->phone }}</p>
            </div>

            <table class="table">
                <thead>
                    <tr>
                        <th>Service</th>
                        <th>Quantity</th>
                        <th>Price</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    @if($invoice->items && $invoice->items->count() > 0)
                        @foreach($invoice->items as $item)
                            <tr>
                                <td>{{ $item->description }}</td>
                                <td>{{ $item->quantity }}</td>
                                <td>TSh {{ number_format($item->price, 2) }}</td>
                                <td>TSh {{ number_format($item->quantity * $item->price, 2) }}</td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="4" style="text-align: center; color: #999;">No items</td>
                        </tr>
                    @endif
                </tbody>
            </table>

            <div class="total-section">
                <div class="total-row">
                    <span>Total:</span>
                    <span>TSh {{ number_format($invoice->total, 2) }}</span>
                </div>
                <div class="total-row">
                    <span>Paid:</span>
                    <span>TSh {{ number_format($invoice->paid, 2) }}</span>
                </div>
                <div class="total-row grand-total">
                    <span>Balance:</span>
                    <span>TSh {{ number_format($invoice->total - $invoice->paid, 2) }}</span>
                </div>
            </div>
        </div>
    @endforeach

    <div style="text-align: center; margin-top: 40px; color: #999; font-size: 10px;">
        <p>End of Report - {{ $invoices->count() }} invoices</p>
    </div>
</body>
</html>
