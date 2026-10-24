@extends('layouts.dashboard')

@section('title', 'Inventory & Products - ' . config('app.name', 'Laravel'))
@section('page_title', 'Inventory & Products')

@section('content')
<div class="space-y-5">
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
    <div class="bg-slate-800 rounded-md p-6 text-white flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <p class="text-[10px] uppercase tracking-wider text-slate-400 mb-1">Inventory</p>
            <h1 class="text-xl font-bold">Pharmacy Inventory</h1>
            <p class="text-xs text-slate-400 mt-1">Manage medicines, stock levels and product catalog.</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('categories.index') }}" class="inline-flex items-center gap-2 bg-white/10 hover:bg-white/20 text-white text-sm font-medium px-4 py-2 rounded transition-colors">
                <i class="hgi-stroke hgi-tag-01 text-base"></i>
                Categories
            </a>
            <button type="button" onclick="openAddProductPanel()" class="inline-flex items-center gap-2 bg-white text-slate-800 text-sm font-medium px-4 py-2 rounded hover:bg-slate-100 transition-colors">
                <i class="hgi-stroke hgi-add-01 text-base"></i>
                Add Product
            </button>
        </div>
    </div>

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
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
        <div class="bg-white rounded-md border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-medium text-gray-500 uppercase tracking-wide">Total Products</span>
                <i class="hgi-stroke hgi-package text-base text-gray-400"></i>
            </div>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ $totalProducts }}</p>
        </div>
        <div class="bg-white rounded-md border {{ $lowStock > 0 ? 'border-amber-200' : 'border-gray-200' }} p-4">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-medium text-gray-500 uppercase tracking-wide">Low Stock</span>
                <i class="hgi-stroke hgi-alert-02 text-base text-gray-400"></i>
            </div>
            <p class="text-2xl font-bold {{ $lowStock > 0 ? 'text-amber-600' : 'text-gray-900' }} mt-1">{{ $lowStock }}</p>
        </div>
        <div class="bg-white rounded-md border {{ $outOfStock > 0 ? 'border-red-200' : 'border-gray-200' }} p-4">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-medium text-gray-500 uppercase tracking-wide">Out of Stock</span>
                <i class="hgi-stroke hgi-unavailable text-base text-gray-400"></i>
            </div>
            <p class="text-2xl font-bold {{ $outOfStock > 0 ? 'text-red-600' : 'text-gray-900' }} mt-1">{{ $outOfStock }}</p>
        </div>
        <div class="bg-white rounded-md border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-medium text-gray-500 uppercase tracking-wide">Inventory Value</span>
                <i class="hgi-stroke hgi-money-03 text-base text-gray-400"></i>
            </div>
            <p class="text-lg font-bold text-gray-900 mt-1">TSh {{ number_format($totalValue, 0) }}</p>
        </div>
    </div>

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
    <div class="bg-white rounded-md border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 text-[10px] uppercase text-gray-400 border-b border-gray-100">
                        <th class="px-5 py-3 font-medium text-left">Product</th>
                        <th class="px-5 py-3 font-medium text-left">SKU</th>
                        <th class="px-5 py-3 font-medium text-left">Category</th>
                        <th class="px-5 py-3 font-medium text-left">Stock</th>
                        <th class="px-5 py-3 font-medium text-left">Selling Price</th>
                        <th class="px-5 py-3 font-medium text-left">Status</th>
                        <th class="px-5 py-3 font-medium text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse ($products as $product)
                        <tr class="hover:bg-gray-50/50">
                            <td class="px-5 py-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded bg-slate-100 text-slate-600 flex items-center justify-center text-[10px] font-bold">
                                        {{ strtoupper(substr($product->name, 0, 1)) }}
                                    </div>
                                    <span class="text-xs font-medium text-gray-900">{{ $product->name }}</span>
                                </div>
                            </td>
                            <td class="px-5 py-3 text-xs text-gray-600">{{ $product->sku ?? '-' }}</td>
                            <td class="px-5 py-3 text-xs text-gray-600">{{ $product->category ?? '-' }}</td>
                            <td class="px-5 py-3 text-xs">
                                <span class="font-medium {{ $product->isLowStock() ? 'text-red-600' : 'text-gray-900' }}">{{ $product->quantity }}</span>
                                <span class="text-gray-500">{{ $product->unit }}</span>
                                @if ($product->isLowStock())
                                    <span class="ml-1 inline-flex items-center gap-1 text-[10px] font-medium text-red-600">
                                        <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>Low
                                    </span>
                                @endif
                            </td>
                            <td class="px-5 py-3 text-xs font-semibold text-gray-900">TSh {{ number_format($product->selling_price, 2) }}</td>
                            <td class="px-5 py-3">
                                <span class="inline-flex items-center gap-1.5 text-[10px] font-medium text-gray-600">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $product->is_active ? 'bg-emerald-500' : 'bg-gray-400' }}"></span>
                                    {{ $product->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td class="px-5 py-3">
                                <div class="flex items-center justify-end gap-1">
                                    <button type="button" onclick="openEditProductPanel('{{ route('products.edit', $product) }}')" class="action-icon group/icon relative p-1.5 text-slate-600 hover:bg-slate-100 rounded transition-colors" title="Edit">
                                        <i class="hgi-stroke hgi-pencil-edit-01 text-base"></i>
                                        <span class="absolute -top-8 left-1/2 -translate-x-1/2 px-2 py-1 bg-gray-900 text-white text-[10px] rounded opacity-0 group-hover/icon:opacity-100 transition-opacity whitespace-nowrap pointer-events-none">Edit</span>
                                    </button>
                                    <form method="POST" action="{{ route('products.destroy', $product) }}" data-ajax data-confirm="Delete this product?" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="action-icon group/icon relative p-1.5 text-red-600 hover:bg-red-50 rounded transition-colors" title="Delete">
                                            <i class="hgi-stroke hgi-delete-02 text-base"></i>
                                            <span class="absolute -top-8 left-1/2 -translate-x-1/2 px-2 py-1 bg-gray-900 text-white text-[10px] rounded opacity-0 group-hover/icon:opacity-100 transition-opacity whitespace-nowrap pointer-events-none">Delete</span>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="px-5 py-8 text-center text-gray-400">
                            <div class="flex flex-col items-center gap-2">
                                <i class="hgi-stroke hgi-package text-4xl text-gray-300"></i>
                                <p class="text-xs">No products found</p>
                            </div>
                        </td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-5 py-3 border-t border-gray-100">
            {{ $products->links() }}
        </div>
    </div>
</div>

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
                    <i class="hgi-stroke hgi-cancel-01 text-2xl"></i>
                </button>
            </div>
            <div class="flex-1 overflow-y-auto p-6" id="productSlideContent"></div>
        </div>
    </div>
</div>

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
