@extends('layouts.dashboard')

@section('title', 'Edit Product - ' . config('app.name', 'Laravel'))
@section('page_title', 'Edit Product')

@section('content')
<div class="max-w-2xl mx-auto space-y-5">
    <div class="bg-slate-800 rounded-md p-6 text-white">
        <p class="text-[10px] uppercase tracking-wider text-slate-400 mb-1">Inventory</p>
        <h1 class="text-xl font-bold">Edit Product</h1>
        <p class="text-xs text-slate-400 mt-1">Update details for {{ $product->name }}.</p>
    </div>

    <div class="bg-white rounded-md border border-gray-200 p-5">
    <form method="POST" action="{{ route('products.update', $product) }}" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        @csrf
        @method('PUT')
        <div class="sm:col-span-2">
            <label class="block text-xs font-medium text-gray-700 mb-1">Name</label>
            <x-ui.input type="text" name="name" value="{{ $product->name }}" class="w-full border border-gray-200 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-slate-300 outline-none" required />
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-700 mb-1">SKU</label>
            <x-ui.input type="text" name="sku" value="{{ $product->sku }}" class="w-full border border-gray-200 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-slate-300 outline-none" />
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-700 mb-1">Category</label>
            <x-ui.input type="text" name="category" value="{{ $product->category }}" class="w-full border border-gray-200 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-slate-300 outline-none" />
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-700 mb-1">Unit</label>
            <x-ui.input type="text" name="unit" value="{{ $product->unit }}" class="w-full border border-gray-200 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-slate-300 outline-none" />
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-700 mb-1">Quantity</label>
            <x-ui.input type="number" name="quantity" value="{{ $product->quantity }}" class="w-full border border-gray-200 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-slate-300 outline-none" required min="0" />
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-700 mb-1">Reorder Level</label>
            <x-ui.input type="number" name="reorder_level" value="{{ $product->reorder_level }}" class="w-full border border-gray-200 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-slate-300 outline-none" required min="0" />
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-700 mb-1">Cost Price</label>
            <x-ui.input type="number" step="0.01" name="cost_price" value="{{ $product->cost_price }}" class="w-full border border-gray-200 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-slate-300 outline-none" required min="0" />
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-700 mb-1">Selling Price</label>
            <x-ui.input type="number" step="0.01" name="selling_price" value="{{ $product->selling_price }}" class="w-full border border-gray-200 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-slate-300 outline-none" required min="0" />
        </div>
        <div class="sm:col-span-2">
            <label class="block text-xs font-medium text-gray-700 mb-1">Description</label>
            <x-ui.textarea name="description" class="w-full border border-gray-200 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-slate-300 outline-none" rows="3">{{ $product->description }}</x-ui.textarea>
        </div>
        <div class="sm:col-span-2 flex items-center gap-2">
            <input type="checkbox" name="is_active" value="1" {{ $product->is_active ? 'checked' : '' }} class="rounded border-gray-300 text-slate-600 focus:ring-slate-300">
            <label class="text-sm text-gray-700">Active</label>
        </div>
        <div class="sm:col-span-2 pt-4">
            <button type="submit" class="bg-slate-800 text-white text-sm font-medium px-5 py-2 rounded hover:bg-slate-700">Update Product</button>
        </div>
    </form>
    </div>
</div>
@endsection
