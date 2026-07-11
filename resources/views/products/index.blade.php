@extends('layouts.dashboard')

@section('title', 'Inventory & Products - ' . config('app.name', 'Laravel'))
@section('page_title', 'Inventory & Products')

@section('content')
<div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mb-6">
    <div class="bg-white rounded-xl border border-gray-100 p-4 shadow-sm">
        <p class="text-xs text-gray-500 uppercase">Total Products</p>
        <p class="text-2xl font-bold text-gray-900">{{ $products->total() }}</p>
    </div>
    <div class="bg-white rounded-xl border border-gray-100 p-4 shadow-sm">
        <p class="text-xs text-gray-500 uppercase">Low Stock</p>
        <p class="text-2xl font-bold text-red-600">{{ $lowStock }}</p>
    </div>
    <div class="bg-white rounded-xl border border-gray-100 p-4 shadow-sm">
        <p class="text-xs text-gray-500 uppercase">Out of Stock</p>
        <p class="text-2xl font-bold text-gold-600">{{ $products->first()?->where('quantity', 0)->count() ?? 0 }}</p>
    </div>
</div>

<div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
        <h2 class="text-sm font-semibold text-gray-900">Products</h2>
        <a href="{{ route('products.create') }}" class="bg-emerald-600 text-white text-xs font-medium px-3 py-1.5 rounded-lg hover:bg-emerald-700">+ Add Product</a>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left">
            <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                <tr>
                    <th class="px-6 py-3">Name</th>
                    <th class="px-6 py-3">SKU</th>
                    <th class="px-6 py-3">Category</th>
                    <th class="px-6 py-3">Stock</th>
                    <th class="px-6 py-3">Selling Price</th>
                    <th class="px-6 py-3">Status</th>
                    <th class="px-6 py-3">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($products as $product)
                    <tr class="border-t border-gray-100 hover:bg-gray-50/50">
                        <td class="px-6 py-3 font-medium">{{ $product->name }}</td>
                        <td class="px-6 py-3">{{ $product->sku ?? '-' }}</td>
                        <td class="px-6 py-3">{{ $product->category ?? '-' }}</td>
                        <td class="px-6 py-3">{{ $product->quantity }} {{ $product->unit }}</td>
                        <td class="px-6 py-3">TSh {{ number_format($product->selling_price, 2) }}</td>
                        <td class="px-6 py-3">
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-medium {{ $product->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-700' }}">
                                {{ $product->is_active ? 'Active' : 'Inactive' }}
                            </span>
                            @if ($product->isLowStock())
                                <span class="ml-1 px-2 py-0.5 rounded-full text-[10px] font-medium bg-red-100 text-red-700">Low</span>
                            @endif
                        </td>
                        <td class="px-6 py-3 flex items-center gap-2">
                            <a href="{{ route('products.edit', $product) }}" class="text-emerald-600 hover:text-emerald-700 text-xs font-medium">Edit</a>
                            <form method="POST" action="{{ route('products.destroy', $product) }}" data-ajax data-confirm="Delete this product?" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-700 text-xs font-medium">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="px-6 py-6 text-center text-gray-400">No products found</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-6 py-4 border-t border-gray-100">
        {{ $products->links() }}
    </div>
</div>
@endsection
