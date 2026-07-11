@extends('layouts.dashboard')

@section('title', 'Inventory & Products - ' . config('app.name', 'Laravel'))
@section('page_title', 'Inventory & Products')

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-lg font-bold text-gray-900">Pharmacy Inventory</h2>
            <p class="text-sm text-gray-500">Manage medicines, stock levels and product catalog</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('categories.index') }}" class="inline-flex items-center gap-2 bg-white border border-gray-200 hover:bg-gray-50 text-gray-700 text-sm font-medium px-4 py-2 rounded-lg shadow-sm transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                Categories
            </a>
            <button type="button" onclick="openAddProductPanel()" class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium px-4 py-2 rounded-lg shadow-sm hover:shadow transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Add Product
            </button>
        </div>
    </div>

    {{-- KPI Cards --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-gradient-to-br from-emerald-500 to-emerald-700 rounded-xl p-5 text-white shadow-md hover:shadow-lg transition-shadow">
            <div class="flex items-center justify-between mb-2">
                <div class="w-10 h-10 rounded-full bg-white/20 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                </div>
            </div>
            <div class="text-3xl font-bold">{{ $totalProducts }}</div>
            <div class="text-xs text-emerald-100 uppercase tracking-wide mt-1">Total Products</div>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 p-5 shadow-sm hover:shadow-md transition-shadow">
            <div class="w-10 h-10 rounded-full bg-red-100 text-red-600 flex items-center justify-center mb-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            </div>
            <div class="text-2xl font-bold text-gray-900">{{ $lowStock }}</div>
            <div class="text-xs text-gray-500 uppercase tracking-wide mt-1">Low Stock</div>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 p-5 shadow-sm hover:shadow-md transition-shadow">
            <div class="w-10 h-10 rounded-full bg-amber-100 text-amber-600 flex items-center justify-center mb-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
            </div>
            <div class="text-2xl font-bold text-gray-900">{{ $outOfStock }}</div>
            <div class="text-xs text-gray-500 uppercase tracking-wide mt-1">Out of Stock</div>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 p-5 shadow-sm hover:shadow-md transition-shadow">
            <div class="w-10 h-10 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center mb-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div class="text-2xl font-bold text-gray-900">TSh {{ number_format($totalValue, 0) }}</div>
            <div class="text-xs text-gray-500 uppercase tracking-wide mt-1">Inventory Value</div>
        </div>
    </div>

    {{-- Products Table --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                    <tr>
                        <th class="px-6 py-3">Product</th>
                        <th class="px-6 py-3">SKU</th>
                        <th class="px-6 py-3">Category</th>
                        <th class="px-6 py-3">Stock</th>
                        <th class="px-6 py-3">Selling Price</th>
                        <th class="px-6 py-3">Status</th>
                        <th class="px-6 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($products as $product)
                        <tr class="group hover:bg-emerald-50/40 transition-colors">
                            <td class="px-6 py-3.5">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center text-xs font-bold">
                                        {{ strtoupper(substr($product->name, 0, 1)) }}
                                    </div>
                                    <span class="font-medium text-gray-900">{{ $product->name }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-3.5 text-gray-700">{{ $product->sku ?? '-' }}</td>
                            <td class="px-6 py-3.5 text-gray-700">{{ $product->category ?? '-' }}</td>
                            <td class="px-6 py-3.5 text-gray-700">
                                <span class="font-medium {{ $product->isLowStock() ? 'text-red-600' : 'text-gray-900' }}">{{ $product->quantity }}</span>
                                <span class="text-gray-500">{{ $product->unit }}</span>
                                @if ($product->isLowStock())
                                    <span class="ml-1 px-1.5 py-0.5 rounded text-[10px] font-medium bg-red-100 text-red-700">Low</span>
                                @endif
                            </td>
                            <td class="px-6 py-3.5 text-gray-700 font-medium">TSh {{ number_format($product->selling_price, 2) }}</td>
                            <td class="px-6 py-3.5">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium {{ $product->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-700' }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $product->is_active ? 'bg-emerald-500' : 'bg-gray-400' }}"></span>
                                    {{ $product->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td class="px-6 py-3.5">
                                <div class="flex items-center justify-end gap-1">
                                    <button type="button" onclick="openEditProductPanel('{{ route('products.edit', $product) }}')" class="action-icon group/icon relative p-2 text-blue-600 hover:bg-blue-100 rounded-lg transition-colors" title="Edit">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.43-9.525l-9.17 9.17a2 2 0 00-.586 1.414V17a1 1 0 001 1h2.828a2 2 0 001.414-.586l9.17-9.17a2 2 0 000-2.828l-1.414-1.414a2 2 0 00-2.828 0z"/></svg>
                                        <span class="absolute -top-8 left-1/2 -translate-x-1/2 px-2 py-1 bg-gray-900 text-white text-[10px] rounded opacity-0 group-hover/icon:opacity-100 transition-opacity whitespace-nowrap pointer-events-none">Edit</span>
                                    </button>
                                    <form method="POST" action="{{ route('products.destroy', $product) }}" data-ajax data-confirm="Delete this product?" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="action-icon group/icon relative p-2 text-red-600 hover:bg-red-100 rounded-lg transition-colors" title="Delete">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            <span class="absolute -top-8 left-1/2 -translate-x-1/2 px-2 py-1 bg-gray-900 text-white text-[10px] rounded opacity-0 group-hover/icon:opacity-100 transition-opacity whitespace-nowrap pointer-events-none">Delete</span>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="px-6 py-10 text-center text-gray-400">
                            <div class="flex flex-col items-center gap-2">
                                <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                                <p>No products found</p>
                            </div>
                        </td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-100">
            {{ $products->links() }}
        </div>
    </div>
</div>

{{-- Slide-over Panel --}}
<div id="productSlideOver" class="fixed inset-0 z-50 hidden" aria-hidden="true">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm transition-opacity opacity-0" id="productBackdrop" onclick="closeProductSlideOver()"></div>
    <div class="absolute inset-y-0 right-0 w-full max-w-md transform translate-x-full transition-transform duration-300 ease-out" id="productPanel">
        <div class="h-full bg-white shadow-2xl flex flex-col">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between bg-gray-50/50">
                <div>
                    <h3 class="text-lg font-bold text-gray-900" id="productSlideTitle">Product</h3>
                    <p class="text-xs text-gray-500" id="productSlideSubtitle">Manage product details</p>
                </div>
                <button onclick="closeProductSlideOver()" class="text-gray-400 hover:text-gray-600 p-1 rounded-lg hover:bg-gray-100 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="flex-1 overflow-y-auto p-6" id="productSlideContent"></div>
        </div>
    </div>
</div>

<template id="productFormTemplate">
    <form id="productForm" method="POST" action="" class="space-y-4">
        @csrf
        <input type="hidden" name="_method" id="product_method" value="POST">
        <div class="grid grid-cols-2 gap-4">
            <div class="col-span-2">
                <label class="block text-xs font-medium text-gray-700 mb-1">Product Name <span class="text-red-500">*</span></label>
                <input type="text" name="name" id="prod_name" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" required>
            </div>
            <div class="col-span-2 sm:col-span-1">
                <label class="block text-xs font-medium text-gray-700 mb-1">SKU</label>
                <div class="relative">
                    <input type="text" name="sku" id="prod_sku" class="w-full border border-gray-200 rounded-lg pl-8 pr-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 bg-gray-50/50" placeholder="Auto-generated">
                    <button type="button" onclick="regenerateProductSku()" class="absolute left-2 top-1/2 -translate-y-1/2 text-emerald-600 hover:text-emerald-700" title="Regenerate SKU">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    </button>
                </div>
                <p class="text-[10px] text-gray-400 mt-1">SKU generated automatically from product name</p>
            </div>
            <div class="col-span-2 sm:col-span-1">
                <div class="flex items-center justify-between mb-1">
                    <label class="block text-xs font-medium text-gray-700">Category</label>
                    <a href="{{ route('categories.index') }}" target="_blank" class="text-[10px] text-emerald-600 hover:text-emerald-700 font-medium inline-flex items-center gap-0.5">
                        Manage
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                    </a>
                </div>
                <select name="category" id="prod_category" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 bg-white">
                    <option value="">Select category</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category }}">{{ $category }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-span-2 sm:col-span-1">
                <label class="block text-xs font-medium text-gray-700 mb-1">Cost Price <span class="text-red-500">*</span></label>
                <input type="number" step="0.01" min="0" name="cost_price" id="prod_cost_price" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" required>
            </div>
            <div class="col-span-2 sm:col-span-1">
                <label class="block text-xs font-medium text-gray-700 mb-1">Selling Price <span class="text-red-500">*</span></label>
                <input type="number" step="0.01" min="0" name="selling_price" id="prod_selling_price" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" required>
            </div>
            <div class="col-span-2 sm:col-span-1">
                <label class="block text-xs font-medium text-gray-700 mb-1">Quantity <span class="text-red-500">*</span></label>
                <input type="number" min="0" name="quantity" id="prod_quantity" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" required>
            </div>
            <div class="col-span-2 sm:col-span-1">
                <label class="block text-xs font-medium text-gray-700 mb-1">Reorder Level <span class="text-red-500">*</span></label>
                <input type="number" min="0" name="reorder_level" id="prod_reorder_level" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" required>
            </div>
            <div class="col-span-2 sm:col-span-1">
                <label class="block text-xs font-medium text-gray-700 mb-1">Unit</label>
                <input type="text" name="unit" id="prod_unit" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" placeholder="e.g. tablets, bottles">
            </div>
            <div class="col-span-2 sm:col-span-1 flex items-center gap-2 pt-6">
                <input type="checkbox" name="is_active" id="prod_is_active" value="1" checked class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500">
                <label for="prod_is_active" class="text-sm text-gray-700">Active</label>
            </div>
            <div class="col-span-2">
                <label class="block text-xs font-medium text-gray-700 mb-1">Description</label>
                <textarea name="description" id="prod_description" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" rows="3"></textarea>
            </div>
        </div>
        <div class="flex justify-end gap-2 pt-4 border-t border-gray-100 mt-4">
            <button type="button" onclick="closeProductSlideOver()" class="px-4 py-2 text-sm text-gray-600 hover:bg-gray-100 rounded-lg">Cancel</button>
            <button type="submit" class="px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700" id="prod_submit_btn">Save Product</button>
        </div>
    </form>
</template>

@push('scripts')
<script>
    const prodSlide = document.getElementById('productSlideOver');
    const prodBackdrop = document.getElementById('productBackdrop');
    const prodPanel = document.getElementById('productPanel');
    const prodContent = document.getElementById('productSlideContent');

    function openProductSlideOver(title, subtitle, html) {
        prodSlide.classList.remove('hidden');
        document.getElementById('productSlideTitle').textContent = title;
        document.getElementById('productSlideSubtitle').textContent = subtitle;
        prodContent.innerHTML = html;
        setTimeout(() => {
            prodBackdrop.classList.remove('opacity-0');
            prodPanel.classList.remove('translate-x-full');
        }, 10);
        document.body.style.overflow = 'hidden';
    }

    function closeProductSlideOver() {
        prodBackdrop.classList.add('opacity-0');
        prodPanel.classList.add('translate-x-full');
        setTimeout(() => {
            prodSlide.classList.add('hidden');
            prodContent.innerHTML = '';
            document.body.style.overflow = '';
        }, 300);
    }

    function generateProductSku(name) {
        const clean = name.trim().replace(/[^a-zA-Z0-9\s]/g, '').split(/\s+/).filter(Boolean);
        let prefix = '';
        if (clean.length === 1) {
            prefix = clean[0].substring(0, 3).toUpperCase();
        } else if (clean.length >= 2) {
            prefix = clean.slice(0, 2).map(w => w.charAt(0).toUpperCase()).join('') + clean[clean.length - 1].charAt(0).toUpperCase();
        }
        if (prefix.length < 2) prefix = 'PRD';
        const timestamp = new Date().getTime().toString().slice(-4);
        const random = Math.floor(100 + Math.random() * 900);
        return `${prefix}-${timestamp}-${random}`;
    }

    function regenerateProductSku() {
        const name = document.getElementById('prod_name').value || 'Product';
        document.getElementById('prod_sku').value = generateProductSku(name);
    }

    function attachProductForm(action, successMessage) {
        const form = document.getElementById('productForm');
        form.action = action;
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(form);
            fetch(action, {
                method: 'POST',
                body: formData,
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
            })
            .then(r => r.json().catch(() => ({})))
            .then(data => {
                Swal.fire({ icon: 'success', title: 'Success', text: data.message || successMessage, timer: 2000, showConfirmButton: false });
                closeProductSlideOver();
                setTimeout(() => location.reload(), 1200);
            })
            .catch(err => {
                Swal.fire({ icon: 'error', title: 'Error', text: 'Failed to save product.' });
            });
        });
    }

    function resetProductForm() {
        document.getElementById('product_method').value = 'POST';
        document.getElementById('prod_name').value = '';
        document.getElementById('prod_sku').value = '';
        document.getElementById('prod_category').value = '';
        document.getElementById('prod_cost_price').value = '';
        document.getElementById('prod_selling_price').value = '';
        document.getElementById('prod_quantity').value = '';
        document.getElementById('prod_reorder_level').value = '';
        document.getElementById('prod_unit').value = '';
        document.getElementById('prod_is_active').checked = true;
        document.getElementById('prod_description').value = '';
    }

    function openAddProductPanel() {
        const html = document.getElementById('productFormTemplate').innerHTML;
        openProductSlideOver('Add Product', 'Enter new product details', html);
        resetProductForm();
        regenerateProductSku();
        document.getElementById('prod_name').addEventListener('blur', regenerateProductSku);
        document.getElementById('prod_submit_btn').textContent = 'Save Product';
        attachProductForm('{{ route("products.store") }}', 'Product added successfully.');
    }

    async function openEditProductPanel(url) {
        const html = document.getElementById('productFormTemplate').innerHTML;
        openProductSlideOver('Edit Product', 'Update product details', html);
        try {
            const res = await fetch(url, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } });
            const data = await res.json();
            const p = data.product;
            document.getElementById('product_method').value = 'PUT';
            document.getElementById('prod_name').value = p.name;
            document.getElementById('prod_sku').value = p.sku || '';
            document.getElementById('prod_category').value = p.category || '';
            document.getElementById('prod_cost_price').value = p.cost_price;
            document.getElementById('prod_selling_price').value = p.selling_price;
            document.getElementById('prod_quantity').value = p.quantity;
            document.getElementById('prod_reorder_level').value = p.reorder_level;
            document.getElementById('prod_unit').value = p.unit || '';
            document.getElementById('prod_is_active').checked = p.is_active;
            document.getElementById('prod_description').value = p.description || '';
            document.getElementById('prod_submit_btn').textContent = 'Update Product';
            attachProductForm(url.replace('/edit', ''), 'Product updated successfully.');
        } catch (err) {
            prodContent.innerHTML = '<div class="text-center text-red-600 py-8">Failed to load product details.</div>';
        }
    }
</script>
@endpush
@endsection
