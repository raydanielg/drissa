@extends('layouts.dashboard')

@section('title', 'Categories - ' . config('app.name', 'Laravel'))
@section('page_title', 'Product Categories')

@section('content')
<div class="space-y-5">
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
    <div class="bg-slate-800 rounded-md p-6 text-white flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <p class="text-[10px] uppercase tracking-wider text-slate-400 mb-1">Inventory</p>
            <h1 class="text-xl font-bold">Product Categories</h1>
            <p class="text-xs text-slate-400 mt-1">Manage pharmacy product categories.</p>
        </div>
        <button type="button" onclick="openAddCategoryPanel()" class="inline-flex items-center gap-2 bg-white text-slate-800 text-sm font-medium px-4 py-2 rounded hover:bg-slate-100 transition-colors self-start">
            <i class="hgi-stroke hgi-add-01 text-base"></i>
            Add Category
        </button>
    </div>

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
    <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
        <div class="bg-white rounded-md border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-medium text-gray-500 uppercase tracking-wide">Total Categories</span>
                <i class="hgi-stroke hgi-tag-01 text-base text-gray-400"></i>
            </div>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ $total }}</p>
        </div>
        <div class="bg-white rounded-md border border-emerald-200 p-4">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-medium text-gray-500 uppercase tracking-wide">Active</span>
                <i class="hgi-stroke hgi-checkmark-circle-02 text-base text-gray-400"></i>
            </div>
            <p class="text-2xl font-bold text-emerald-600 mt-1">{{ $active }}</p>
        </div>
        <div class="bg-white rounded-md border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-medium text-gray-500 uppercase tracking-wide">Inactive</span>
                <i class="hgi-stroke hgi-cancel-circle text-base text-gray-400"></i>
            </div>
            <p class="text-2xl font-bold text-gray-500 mt-1">{{ $inactive }}</p>
        </div>
    </div>

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
    <div class="bg-white rounded-md border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 text-[10px] uppercase text-gray-400 border-b border-gray-100">
                        <th class="px-5 py-3 font-medium text-left">Category</th>
                        <th class="px-5 py-3 font-medium text-left">Description</th>
                        <th class="px-5 py-3 font-medium text-left">Status</th>
                        <th class="px-5 py-3 font-medium text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse ($categories as $category)
                        <tr class="hover:bg-gray-50/50">
                            <td class="px-5 py-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded bg-slate-100 text-slate-600 flex items-center justify-center text-[10px] font-bold">
                                        {{ strtoupper(substr($category->name, 0, 1)) }}
                                    </div>
                                    <span class="text-xs font-medium text-gray-900">{{ $category->name }}</span>
                                </div>
                            </td>
                            <td class="px-5 py-3 text-xs text-gray-600 max-w-sm truncate">{{ $category->description ?? '-' }}</td>
                            <td class="px-5 py-3">
                                <span class="inline-flex items-center gap-1.5 text-[10px] font-medium text-gray-600">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $category->is_active ? 'bg-emerald-500' : 'bg-gray-400' }}"></span>
                                    {{ $category->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td class="px-5 py-3">
                                <div class="flex items-center justify-end gap-1">
                                    <button type="button" onclick="openEditCategoryPanel('{{ route('categories.edit', $category) }}')" class="action-icon group/icon relative p-1.5 text-slate-600 hover:bg-slate-100 rounded transition-colors" title="Edit">
                                        <i class="hgi-stroke hgi-pencil-edit-01 text-base"></i>
                                        <span class="absolute -top-8 left-1/2 -translate-x-1/2 px-2 py-1 bg-gray-900 text-white text-[10px] rounded opacity-0 group-hover/icon:opacity-100 transition-opacity whitespace-nowrap pointer-events-none">Edit</span>
                                    </button>
                                    <form method="POST" action="{{ route('categories.destroy', $category) }}" data-ajax data-confirm="Delete this category?" class="inline">
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
                        <tr><td colspan="4" class="px-5 py-8 text-center text-gray-400">
                            <div class="flex flex-col items-center gap-2">
                                <i class="hgi-stroke hgi-tag-01 text-4xl text-gray-300"></i>
                                <p class="text-xs">No categories found</p>
                            </div>
                        </td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-5 py-3 border-t border-gray-100">
            {{ $categories->links() }}
        </div>
    </div>
</div>

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
                    <i class="hgi-stroke hgi-cancel-01 text-2xl"></i>
                </button>
            </div>
            <div class="flex-1 overflow-y-auto p-6" id="categorySlideContent"></div>
        </div>
    </div>
</div>

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
