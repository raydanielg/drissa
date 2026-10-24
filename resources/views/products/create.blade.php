@extends('layouts.dashboard')

@section('title', 'Add Product - ' . config('app.name', 'Laravel'))
@section('page_title', 'Add Product')

@section('content')
<div class="max-w-2xl mx-auto space-y-5">
    <div class="bg-slate-800 rounded-md p-6 text-white">
        <p class="text-[10px] uppercase tracking-wider text-slate-400 mb-1">Inventory</p>
        <h1 class="text-xl font-bold">Add Product</h1>
        <p class="text-xs text-slate-400 mt-1">Add a new product to the pharmacy catalog.</p>
    </div>

    <div class="bg-white rounded-md border border-gray-200 p-5">
    <form method="POST" action="{{ route('products.store') }}" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        @csrf
        <div class="sm:col-span-2">
            <label class="block text-xs font-medium text-gray-700 mb-1">Name</label>
            <x-ui.input type="text" name="name" placeholder="e.g. Paracetamol 500mg" class="w-full border border-gray-200 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-slate-300 outline-none" required />
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-700 mb-1">SKU</label>
            <x-ui.input type="text" name="sku" placeholder="e.g. MED-001" class="w-full border border-gray-200 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-slate-300 outline-none" />
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-700 mb-1">Category</label>
            <x-ui.input type="text" name="category" placeholder="e.g. Analgesics" class="w-full border border-gray-200 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-slate-300 outline-none" />
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-700 mb-1">Unit</label>
            <x-ui.input type="text" name="unit" placeholder="e.g. tablets, bottles" class="w-full border border-gray-200 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-slate-300 outline-none" />
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-700 mb-1">Quantity</label>
            <x-ui.input type="number" name="quantity" placeholder="0" class="w-full border border-gray-200 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-slate-300 outline-none" required min="0" />
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-700 mb-1">Reorder Level</label>
            <x-ui.input type="number" name="reorder_level" class="w-full border border-gray-200 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-slate-300 outline-none" required min="0" value="10" />
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-700 mb-1">Cost Price</label>
            <x-ui.input type="number" step="0.01" name="cost_price" placeholder="0.00" class="w-full border border-gray-200 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-slate-300 outline-none" required min="0" />
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-700 mb-1">Selling Price</label>
            <x-ui.input type="number" step="0.01" name="selling_price" placeholder="0.00" class="w-full border border-gray-200 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-slate-300 outline-none" required min="0" />
        </div>
        <div class="sm:col-span-2">
            <label class="block text-xs font-medium text-gray-700 mb-1">Description</label>
            <x-ui.textarea name="description" placeholder="Product description or usage notes..." class="w-full border border-gray-200 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-slate-300 outline-none" rows="3"></x-ui.textarea>
        </div>
        <div class="sm:col-span-2 flex items-center gap-2">
            <input type="checkbox" name="is_active" value="1" checked class="rounded border-gray-300 text-slate-600 focus:ring-slate-300">
            <label class="text-sm text-gray-700">Active</label>
        </div>
        <div class="sm:col-span-2 pt-4">
            <button type="submit" class="bg-slate-800 text-white text-sm font-medium px-5 py-2 rounded hover:bg-slate-700">Save Product</button>
        </div>
    </form>
    </div>
</div>
@endsection
