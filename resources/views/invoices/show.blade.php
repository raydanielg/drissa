@extends('layouts.dashboard')

@section('title', 'Invoice ' . $invoice->invoice_number . ' - ' . config('app.name', 'Laravel'))
@section('page_title', 'Invoice ' . $invoice->invoice_number)

@section('content')
<div class="max-w-3xl mx-auto bg-white rounded-xl border border-gray-100 p-8 shadow-sm">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-lg font-bold text-gray-900">{{ config('app.name') }}</h2>
            <p class="text-sm text-gray-500">Invoice #: {{ $invoice->invoice_number }}</p>
        </div>
        <a href="{{ route('invoices.pdf', $invoice) }}" class="bg-emerald-600 text-white text-xs font-medium px-3 py-1.5 rounded-lg hover:bg-emerald-700">Download PDF</a>
    </div>

    <div class="grid grid-cols-2 gap-6 mb-6">
        <div>
            <p class="text-xs text-gray-500 uppercase">Patient</p>
            <p class="text-sm font-medium text-gray-900">{{ $invoice->patient->fullName() }}</p>
            <p class="text-sm text-gray-500">{{ $invoice->patient->phone ?? '-' }}</p>
        </div>
        <div class="text-right">
            <p class="text-xs text-gray-500 uppercase">Date</p>
            <p class="text-sm font-medium text-gray-900">{{ $invoice->created_at->format('d M Y') }}</p>
            <p class="text-sm text-gray-500">Status: <span class="capitalize font-medium">{{ $invoice->status }}</span></p>
        </div>
    </div>

    <table class="w-full text-sm text-left mb-6">
        <thead class="bg-gray-50 text-xs uppercase text-gray-500">
            <tr>
                <th class="px-4 py-2">Item</th>
                <th class="px-4 py-2 text-right">Qty</th>
                <th class="px-4 py-2 text-right">Price</th>
                <th class="px-4 py-2 text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($invoice->items as $item)
                <tr class="border-t border-gray-100">
                    <td class="px-4 py-2">{{ $item->description }}</td>
                    <td class="px-4 py-2 text-right">{{ $item->quantity }}</td>
                    <td class="px-4 py-2 text-right">TSh {{ number_format($item->unit_price, 2) }}</td>
                    <td class="px-4 py-2 text-right">TSh {{ number_format($item->total, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="border-t border-gray-100 pt-4 flex justify-between">
        <div>
            <p class="text-xs text-gray-500">Paid: TSh {{ number_format($invoice->paid, 2) }}</p>
            <p class="text-xs text-gray-500">Balance: TSh {{ number_format($invoice->total - $invoice->paid, 2) }}</p>
        </div>
        <p class="text-lg font-bold text-gray-900">Total: TSh {{ number_format($invoice->total, 2) }}</p>
    </div>
</div>
@endsection
