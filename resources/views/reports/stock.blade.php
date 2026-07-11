@extends('layouts.dashboard')

@section('title', 'Stock Report - ' . config('app.name', 'Laravel'))
@section('page_title', 'Stock Report')

@section('content')
<div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden mb-6">
    <div class="px-6 py-4 border-b border-gray-100">
        <h3 class="text-sm font-semibold text-gray-900">Products Inventory</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left">
            <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                <tr>
                    <th class="px-6 py-3">Name</th>
                    <th class="px-6 py-3">SKU</th>
                    <th class="px-6 py-3">Stock</th>
                    <th class="px-6 py-3">Reorder Level</th>
                    <th class="px-6 py-3">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($products as $product)
                    <tr class="border-t border-gray-100 hover:bg-gray-50/50">
                        <td class="px-6 py-3 font-medium">{{ $product->name }}</td>
                        <td class="px-6 py-3">{{ $product->sku ?? '-' }}</td>
                        <td class="px-6 py-3">{{ $product->quantity }} {{ $product->unit }}</td>
                        <td class="px-6 py-3">{{ $product->reorder_level }}</td>
                        <td class="px-6 py-3">
                            @if ($product->isLowStock())
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-medium bg-red-100 text-red-700">Low Stock</span>
                            @else
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-medium bg-emerald-100 text-emerald-700">OK</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-6 py-6 text-center text-gray-400">No products</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-6 py-4 border-t border-gray-100">
        {{ $products->links() }}
    </div>
</div>

<div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-100">
        <h3 class="text-sm font-semibold text-gray-900">Medications Inventory</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left">
            <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                <tr>
                    <th class="px-6 py-3">Name</th>
                    <th class="px-6 py-3">Stock</th>
                    <th class="px-6 py-3">Reorder Level</th>
                    <th class="px-6 py-3">Price</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($medications as $medication)
                    <tr class="border-t border-gray-100 hover:bg-gray-50/50">
                        <td class="px-6 py-3 font-medium">{{ $medication->name }}</td>
                        <td class="px-6 py-3">{{ $medication->stock_quantity }}</td>
                        <td class="px-6 py-3">{{ $medication->reorder_level }}</td>
                        <td class="px-6 py-3">TSh {{ number_format($medication->unit_price, 2) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="px-6 py-6 text-center text-gray-400">No medications</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-6 py-4 border-t border-gray-100">
        {{ $medications->links() }}
    </div>
</div>
@endsection
