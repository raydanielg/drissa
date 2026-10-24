@extends('layouts.dashboard')

@section('title', 'Invoice ' . $invoice->invoice_number . ' - ' . config('app.name', 'Laravel'))
@section('page_title', 'Invoice ' . $invoice->invoice_number)

@section('content')
<div class="max-w-3xl mx-auto space-y-5">
    {{-- Header --}}
    <div class="bg-slate-800 rounded-md p-6 text-white">
        <div class="flex flex-wrap items-end justify-between gap-3">
            <div>
                <p class="text-[10px] uppercase tracking-wider text-slate-400 mb-1">Billing</p>
                <h1 class="text-xl font-bold">Invoice #{{ $invoice->invoice_number }}</h1>
                <p class="text-xs text-slate-400 mt-1">{{ config('app.name') }}</p>
            </div>
            <a href="{{ route('invoices.pdf', $invoice) }}" class="inline-flex items-center gap-1.5 bg-white hover:bg-gray-100 text-slate-800 text-xs font-semibold px-3 py-1.5 rounded transition-colors">
                <i class="hgi-stroke hgi-upload-01 text-sm"></i>
                Download PDF
            </a>
        </div>
    </div>

    <div class="bg-white rounded-md border border-gray-200 p-5">

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
        <thead class="bg-gray-50 text-[10px] uppercase text-gray-400">
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
</div>
@endsection
