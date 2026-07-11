@extends('layouts.dashboard')

@section('title', 'Categories - ' . config('app.name', 'Laravel'))
@section('page_title', 'Product Categories')

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-lg font-bold text-gray-900">Product Categories</h2>
            <p class="text-sm text-gray-500">Manage pharmacy product categories</p>
        </div>
        <button type="button" onclick="openAddCategoryPanel()" class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium px-4 py-2 rounded-lg shadow-sm hover:shadow transition-all">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Add Category
        </button>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
        <div class="bg-gradient-to-br from-emerald-500 to-emerald-700 rounded-xl p-5 text-white shadow-md hover:shadow-lg transition-shadow">
            <div class="w-10 h-10 rounded-full bg-white/20 flex items-center justify-center mb-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
            </div>
            <div class="text-3xl font-bold">{{ $total }}</div>
            <div class="text-xs text-emerald-100 uppercase tracking-wide mt-1">Total Categories</div>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 p-5 shadow-sm hover:shadow-md transition-shadow">
            <div class="w-10 h-10 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center mb-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div class="text-2xl font-bold text-gray-900">{{ $active }}</div>
            <div class="text-xs text-gray-500 uppercase tracking-wide mt-1">Active</div>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 p-5 shadow-sm hover:shadow-md transition-shadow">
            <div class="w-10 h-10 rounded-full bg-gray-100 text-gray-600 flex items-center justify-center mb-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div class="text-2xl font-bold text-gray-900">{{ $inactive }}</div>
            <div class="text-xs text-gray-500 uppercase tracking-wide mt-1">Inactive</div>
        </div>
    </div>

    {{-- Categories Table --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                    <tr>
                        <th class="px-6 py-3">Category</th>
                        <th class="px-6 py-3">Description</th>
                        <th class="px-6 py-3">Status</th>
                        <th class="px-6 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($categories as $category)
                        <tr class="group hover:bg-emerald-50/40 transition-colors">
                            <td class="px-6 py-3.5">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center text-xs font-bold">
                                        {{ strtoupper(substr($category->name, 0, 1)) }}
                                    </div>
                                    <span class="font-medium text-gray-900">{{ $category->name }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-3.5 text-gray-700 max-w-sm truncate">{{ $category->description ?? '-' }}</td>
                            <td class="px-6 py-3.5">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium {{ $category->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-700' }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $category->is_active ? 'bg-emerald-500' : 'bg-gray-400' }}"></span>
                                    {{ $category->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td class="px-6 py-3.5">
                                <div class="flex items-center justify-end gap-1">
                                    <button type="button" onclick="openEditCategoryPanel('{{ route('categories.edit', $category) }}')" class="action-icon group/icon relative p-2 text-blue-600 hover:bg-blue-100 rounded-lg transition-colors" title="Edit">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.43-9.525l-9.17 9.17a2 2 0 00-.586 1.414V17a1 1 0 001 1h2.828a2 2 0 001.414-.586l9.17-9.17a2 2 0 000-2.828l-1.414-1.414a2 2 0 00-2.828 0z"/></svg>
                                        <span class="absolute -top-8 left-1/2 -translate-x-1/2 px-2 py-1 bg-gray-900 text-white text-[10px] rounded opacity-0 group-hover/icon:opacity-100 transition-opacity whitespace-nowrap pointer-events-none">Edit</span>
                                    </button>
                                    <form method="POST" action="{{ route('categories.destroy', $category) }}" data-ajax data-confirm="Delete this category?" class="inline">
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
                        <tr><td colspan="4" class="px-6 py-10 text-center text-gray-400">
                            <div class="flex flex-col items-center gap-2">
                                <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                                <p>No categories found</p>
                            </div>
                        </td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-100">
            {{ $categories->links() }}
        </div>
    </div>
</div>

{{-- Slide-over Panel --}}
<div id="categorySlideOver" class="fixed inset-0 z-50 hidden" aria-hidden="true">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm transition-opacity opacity-0" id="categoryBackdrop" onclick="closeCategorySlideOver()"></div>
    <div class="absolute inset-y-0 right-0 w-full max-w-md transform translate-x-full transition-transform duration-300 ease-out" id="categoryPanel">
        <div class="h-full bg-white shadow-2xl flex flex-col">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between bg-gray-50/50">
                <div>
                    <h3 class="text-lg font-bold text-gray-900" id="categorySlideTitle">Category</h3>
                    <p class="text-xs text-gray-500" id="categorySlideSubtitle">Manage category</p>
                </div>
                <button onclick="closeCategorySlideOver()" class="text-gray-400 hover:text-gray-600 p-1 rounded-lg hover:bg-gray-100 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="flex-1 overflow-y-auto p-6" id="categorySlideContent"></div>
        </div>
    </div>
</div>

<template id="categoryFormTemplate">
    <form id="categoryForm" method="POST" action="" class="space-y-4">
        @csrf
        <input type="hidden" name="_method" id="cat_method" value="POST">
        <div>
            <label class="block text-xs font-medium text-gray-700 mb-1">Category Name <span class="text-red-500">*</span></label>
            <input type="text" name="name" id="cat_name" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" required>
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-700 mb-1">Description</label>
            <textarea name="description" id="cat_description" rows="4" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"></textarea>
        </div>
        <div class="flex items-center gap-2">
            <input type="checkbox" name="is_active" id="cat_is_active" value="1" checked class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500">
            <label for="cat_is_active" class="text-sm text-gray-700">Active</label>
        </div>
        <div class="flex justify-end gap-2 pt-4 border-t border-gray-100 mt-4">
            <button type="button" onclick="closeCategorySlideOver()" class="px-4 py-2 text-sm text-gray-600 hover:bg-gray-100 rounded-lg">Cancel</button>
            <button type="submit" class="px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700" id="cat_submit_btn">Save Category</button>
        </div>
    </form>
</template>

@push('scripts')
<script>
    const catSlide = document.getElementById('categorySlideOver');
    const catBackdrop = document.getElementById('categoryBackdrop');
    const catPanel = document.getElementById('categoryPanel');
    const catContent = document.getElementById('categorySlideContent');

    function openCategorySlideOver(title, subtitle, html) {
        catSlide.classList.remove('hidden');
        document.getElementById('categorySlideTitle').textContent = title;
        document.getElementById('categorySlideSubtitle').textContent = subtitle;
        catContent.innerHTML = html;
        setTimeout(() => {
            catBackdrop.classList.remove('opacity-0');
            catPanel.classList.remove('translate-x-full');
        }, 10);
        document.body.style.overflow = 'hidden';
    }

    function closeCategorySlideOver() {
        catBackdrop.classList.add('opacity-0');
        catPanel.classList.add('translate-x-full');
        setTimeout(() => {
            catSlide.classList.add('hidden');
            catContent.innerHTML = '';
            document.body.style.overflow = '';
        }, 300);
    }

    function attachCategoryForm(action, successMessage) {
        const form = document.getElementById('categoryForm');
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
                closeCategorySlideOver();
                setTimeout(() => location.reload(), 1200);
            })
            .catch(err => {
                Swal.fire({ icon: 'error', title: 'Error', text: 'Failed to save category.' });
            });
        });
    }

    function resetCategoryForm() {
        document.getElementById('cat_method').value = 'POST';
        document.getElementById('cat_name').value = '';
        document.getElementById('cat_description').value = '';
        document.getElementById('cat_is_active').checked = true;
    }

    function openAddCategoryPanel() {
        const html = document.getElementById('categoryFormTemplate').innerHTML;
        openCategorySlideOver('Add Category', 'Enter new category details', html);
        resetCategoryForm();
        document.getElementById('cat_submit_btn').textContent = 'Save Category';
        attachCategoryForm('{{ route("categories.store") }}', 'Category added successfully.');
    }

    async function openEditCategoryPanel(url) {
        const html = document.getElementById('categoryFormTemplate').innerHTML;
        openCategorySlideOver('Edit Category', 'Update category details', html);
        try {
            const res = await fetch(url, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } });
            const data = await res.json();
            const c = data.category;
            document.getElementById('cat_method').value = 'PUT';
            document.getElementById('cat_name').value = c.name;
            document.getElementById('cat_description').value = c.description || '';
            document.getElementById('cat_is_active').checked = c.is_active;
            document.getElementById('cat_submit_btn').textContent = 'Update Category';
            attachCategoryForm(url.replace('/edit', ''), 'Category updated successfully.');
        } catch (err) {
            catContent.innerHTML = '<div class="text-center text-red-600 py-8">Failed to load category details.</div>';
        }
    }
</script>
@endpush
@endsection
