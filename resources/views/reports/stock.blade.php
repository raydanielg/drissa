@extends('layouts.dashboard')

@section('title', 'Stock Report - ' . config('app.name', 'Laravel'))
@section('page_title', 'Stock Report')

@section('content')
<div class="space-y-5">
{{-- Header --}}
<div class="bg-slate-800 rounded-md p-6 text-white">
    <p class="text-[10px] uppercase tracking-wider text-slate-400 mb-1">Reports</p>
    <h1 class="text-xl font-bold">Stock Report</h1>
    <p class="text-xs text-slate-400 mt-1">Inventory levels for products and medications.</p>
</div>

<div class="bg-white rounded-md border border-gray-200 overflow-hidden">
    <div class="px-5 py-4 border-b border-gray-100">
        <h3 class="text-sm font-bold text-gray-900">Products Inventory</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left">
            <thead class="bg-gray-50 text-[10px] uppercase text-gray-400">
                <tr>
                    <th class="px-5 py-3">Name</th>
                    <th class="px-5 py-3">SKU</th>
                    <th class="px-5 py-3">Stock</th>
                    <th class="px-5 py-3">Reorder Level</th>
                    <th class="px-5 py-3">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse ($products as $product)
                    <tr class="hover:bg-gray-50/50">
                        <td class="px-5 py-3 text-xs font-medium text-gray-900">{{ $product->name }}</td>
                        <td class="px-5 py-3 text-xs text-gray-600">{{ $product->sku ?? '-' }}</td>
                        <td class="px-5 py-3 text-xs text-gray-600">{{ $product->quantity }} {{ $product->unit }}</td>
                        <td class="px-5 py-3 text-xs text-gray-600">{{ $product->reorder_level }}</td>
                        <td class="px-5 py-3">
                            @if ($product->isLowStock())
                                <span class="inline-flex items-center gap-1.5 text-[10px] font-medium text-gray-600"><span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>Low Stock</span>
                            @else
                                <span class="inline-flex items-center gap-1.5 text-[10px] font-medium text-gray-600"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>OK</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-5 py-8 text-center text-gray-400 text-xs">No products</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-5 py-3 border-t border-gray-100">
        {{ $products->links() }}
    </div>
</div>

<div class="bg-white rounded-md border border-gray-200 overflow-hidden">
    <div class="px-5 py-4 border-b border-gray-100">
        <h3 class="text-sm font-bold text-gray-900">Medications Inventory</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left">
            <thead class="bg-gray-50 text-[10px] uppercase text-gray-400">
                <tr>
                    <th class="px-5 py-3">Name</th>
                    <th class="px-5 py-3">Stock</th>
                    <th class="px-5 py-3">Reorder Level</th>
                    <th class="px-5 py-3 text-right">Price</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse ($medications as $medication)
                    <tr class="hover:bg-gray-50/50">
                        <td class="px-5 py-3 text-xs font-medium text-gray-900">{{ $medication->name }}</td>
                        <td class="px-5 py-3 text-xs text-gray-600">{{ $medication->stock_quantity }}</td>
                        <td class="px-5 py-3 text-xs text-gray-600">{{ $medication->reorder_level }}</td>
                        <td class="px-5 py-3 text-xs font-semibold text-gray-900 text-right">TSh {{ number_format($medication->unit_price, 2) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="px-5 py-8 text-center text-gray-400 text-xs">No medications</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-5 py-3 border-t border-gray-100">
        {{ $medications->links() }}
    </div>
</div>
</div>
@endsection
