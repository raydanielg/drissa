@extends('layouts.dashboard')

@section('title', 'Edit Product - ' . config('app.name', 'Laravel'))
@section('page_title', 'Edit Product')

@section('content')
<div class="max-w-2xl mx-auto bg-white rounded-xl border border-gray-100 p-6 shadow-sm">
    <form method="POST" action="{{ route('products.update', $product) }}" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        @csrf
        @method('PUT')
        <div class="sm:col-span-2">
            <label class="block text-xs font-medium text-gray-700 mb-1">Name</label>
            <input type="text" name="name" value="{{ $product->name }}" class="w-full border rounded-lg px-3 py-2 text-sm" required>
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-700 mb-1">SKU</label>
            <input type="text" name="sku" value="{{ $product->sku }}" class="w-full border rounded-lg px-3 py-2 text-sm">
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-700 mb-1">Category</label>
            <input type="text" name="category" value="{{ $product->category }}" class="w-full border rounded-lg px-3 py-2 text-sm">
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-700 mb-1">Unit</label>
            <input type="text" name="unit" value="{{ $product->unit }}" class="w-full border rounded-lg px-3 py-2 text-sm">
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-700 mb-1">Quantity</label>
            <input type="number" name="quantity" value="{{ $product->quantity }}" class="w-full border rounded-lg px-3 py-2 text-sm" required min="0">
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-700 mb-1">Reorder Level</label>
            <input type="number" name="reorder_level" value="{{ $product->reorder_level }}" class="w-full border rounded-lg px-3 py-2 text-sm" required min="0">
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-700 mb-1">Cost Price</label>
            <input type="number" step="0.01" name="cost_price" value="{{ $product->cost_price }}" class="w-full border rounded-lg px-3 py-2 text-sm" required min="0">
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-700 mb-1">Selling Price</label>
            <input type="number" step="0.01" name="selling_price" value="{{ $product->selling_price }}" class="w-full border rounded-lg px-3 py-2 text-sm" required min="0">
        </div>
        <div class="sm:col-span-2">
            <label class="block text-xs font-medium text-gray-700 mb-1">Description</label>
            <textarea name="description" class="w-full border rounded-lg px-3 py-2 text-sm" rows="3">{{ $product->description }}</textarea>
        </div>
        <div class="sm:col-span-2 flex items-center gap-2">
            <input type="checkbox" name="is_active" value="1" {{ $product->is_active ? 'checked' : '' }} class="rounded border-gray-300">
            <label class="text-sm text-gray-700">Active</label>
        </div>
        <div class="sm:col-span-2 pt-4">
            <button type="submit" class="bg-emerald-600 text-white text-sm font-medium px-5 py-2 rounded-lg hover:bg-emerald-700">Update Product</button>
        </div>
    </form>
</div>
@endsection
