@extends('layouts.dashboard')

@section('title', 'Invoices - ' . config('app.name', 'Laravel'))
@section('page_title', 'Financial Management - Invoices')

@section('content')
<div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-100">
        <h2 class="text-sm font-semibold text-gray-900">Invoices</h2>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left">
            <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                <tr>
                    <th class="px-6 py-3">Invoice #</th>
                    <th class="px-6 py-3">Patient</th>
                    <th class="px-6 py-3">Total</th>
                    <th class="px-6 py-3">Paid</th>
                    <th class="px-6 py-3">Balance</th>
                    <th class="px-6 py-3">Status</th>
                    <th class="px-6 py-3">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($invoices as $invoice)
                    <tr class="border-t border-gray-100 hover:bg-gray-50/50">
                        <td class="px-6 py-3 font-medium">{{ $invoice->invoice_number }}</td>
                        <td class="px-6 py-3">{{ $invoice->patient->fullName() }}</td>
                        <td class="px-6 py-3">TSh {{ number_format($invoice->total, 2) }}</td>
                        <td class="px-6 py-3">TSh {{ number_format($invoice->paid, 2) }}</td>
                        <td class="px-6 py-3">TSh {{ number_format($invoice->total - $invoice->paid, 2) }}</td>
                        <td class="px-6 py-3">
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-medium capitalize
                                @if($invoice->status === 'paid') bg-emerald-100 text-emerald-700
                                @elseif($invoice->status === 'partial') bg-gold-100 text-gold-700
                                @else bg-red-100 text-red-700 @endif">
                                {{ $invoice->status }}
                            </span>
                        </td>
                        <td class="px-6 py-3 flex items-center gap-2">
                            <a href="{{ route('invoices.show', $invoice) }}" class="text-emerald-600 hover:text-emerald-700 text-xs font-medium">View</a>
                            <a href="{{ route('invoices.pdf', $invoice) }}" class="text-sky-600 hover:text-sky-700 text-xs font-medium">PDF</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="px-6 py-6 text-center text-gray-400">No invoices found</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-6 py-4 border-t border-gray-100">
        {{ $invoices->links() }}
    </div>
</div>
@endsection
