@extends('layouts.dashboard')

@section('title', 'Departments - ' . config('app.name', 'Laravel'))
@section('page_title', 'Human Resources - Departments')

@section('content')
<div class="space-y-5">
    <script>
    const deptSlide = document.getElementById('departmentSlideOver');
    const deptBackdrop = document.getElementById('departmentBackdrop');
    const deptPanel = document.getElementById('departmentPanel');
    const deptContent = document.getElementById('departmentSlideContent');

    function openDepartmentSlideOver(title, subtitle, html) {
        deptSlide.classList.remove('hidden');
        document.getElementById('departmentSlideTitle').textContent = title;
        document.getElementById('departmentSlideSubtitle').textContent = subtitle;
        deptContent.innerHTML = html;
        setTimeout(() => {
            deptBackdrop.classList.remove('opacity-0');
            deptPanel.classList.remove('translate-x-full');
        }, 10);
        document.body.style.overflow = 'hidden';
    }

    function closeDepartmentSlideOver() {
        deptBackdrop.classList.add('opacity-0');
        deptPanel.classList.add('translate-x-full');
        setTimeout(() => {
            deptSlide.classList.add('hidden');
            deptContent.innerHTML = '';
            document.body.style.overflow = '';
        }, 300);
    }

    function attachDepartmentForm(action, successMessage) {
        const form = document.getElementById('departmentForm');
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
                Swal.fire({ icon: 'success', title: 'Success', text: data.message || successMessage, timer: 1500, showConfirmButton: false });
                closeDepartmentSlideOver();
                setTimeout(() => location.reload(), 1000);
            })
            .catch(err => {
                Swal.fire({ icon: 'error', title: 'Error', text: 'Failed to save department.' });
            });
        });
    }

    function resetDepartmentForm() {
        document.getElementById('dept_method').value = 'POST';
        document.getElementById('dept_name').value = '';
        document.getElementById('dept_code').value = '';
        document.getElementById('dept_is_active').value = '1';
        document.getElementById('dept_description').value = '';
    }

    async function openAddDepartmentPanel() {
        const html = document.getElementById('departmentFormTemplate').innerHTML;
        openDepartmentSlideOver('New Department', 'Add a new department', html);
        resetDepartmentForm();
        document.getElementById('dept_submit_btn').textContent = 'Save Department';
        attachDepartmentForm('{{ route("departments.store") }}', 'Department created successfully.');
    }

    async function openEditDepartmentPanel(url) {
        const html = document.getElementById('departmentFormTemplate').innerHTML;
        openDepartmentSlideOver('Edit Department', 'Update department details', html);
        try {
            const res = await fetch(url, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } });
            const data = await res.json();
            const d = data.department;
            document.getElementById('dept_method').value = 'PUT';
            document.getElementById('dept_name').value = d.name;
            document.getElementById('dept_code').value = d.code || '';
            document.getElementById('dept_is_active').value = d.is_active ? '1' : '0';
            document.getElementById('dept_description').value = d.description || '';
            document.getElementById('dept_submit_btn').textContent = 'Update Department';
            attachDepartmentForm(url.replace('/edit', ''), 'Department updated successfully.');
        } catch (err) {
            deptContent.innerHTML = '<div class="text-center text-red-600 py-8">Failed to load department details.</div>';
        }
    }
</script>
    <div class="bg-slate-800 rounded-md p-6 text-white flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <p class="text-[10px] uppercase tracking-wider text-slate-400 mb-1">Human Resources</p>
            <h1 class="text-xl font-bold">Departments</h1>
            <p class="text-xs text-slate-400 mt-1">Manage hospital departments and units.</p>
        </div>
        <button type="button" onclick="openAddDepartmentPanel()" class="inline-flex items-center gap-2 bg-white text-slate-800 text-sm font-medium px-4 py-2 rounded hover:bg-slate-100 transition-colors self-start">
            <i class="hgi-stroke hgi-add-01 text-base"></i>
            Add Department
        </button>
    </div>

    <script>
    const deptSlide = document.getElementById('departmentSlideOver');
    const deptBackdrop = document.getElementById('departmentBackdrop');
    const deptPanel = document.getElementById('departmentPanel');
    const deptContent = document.getElementById('departmentSlideContent');

    function openDepartmentSlideOver(title, subtitle, html) {
        deptSlide.classList.remove('hidden');
        document.getElementById('departmentSlideTitle').textContent = title;
        document.getElementById('departmentSlideSubtitle').textContent = subtitle;
        deptContent.innerHTML = html;
        setTimeout(() => {
            deptBackdrop.classList.remove('opacity-0');
            deptPanel.classList.remove('translate-x-full');
        }, 10);
        document.body.style.overflow = 'hidden';
    }

    function closeDepartmentSlideOver() {
        deptBackdrop.classList.add('opacity-0');
        deptPanel.classList.add('translate-x-full');
        setTimeout(() => {
            deptSlide.classList.add('hidden');
            deptContent.innerHTML = '';
            document.body.style.overflow = '';
        }, 300);
    }

    function attachDepartmentForm(action, successMessage) {
        const form = document.getElementById('departmentForm');
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
                Swal.fire({ icon: 'success', title: 'Success', text: data.message || successMessage, timer: 1500, showConfirmButton: false });
                closeDepartmentSlideOver();
                setTimeout(() => location.reload(), 1000);
            })
            .catch(err => {
                Swal.fire({ icon: 'error', title: 'Error', text: 'Failed to save department.' });
            });
        });
    }

    function resetDepartmentForm() {
        document.getElementById('dept_method').value = 'POST';
        document.getElementById('dept_name').value = '';
        document.getElementById('dept_code').value = '';
        document.getElementById('dept_is_active').value = '1';
        document.getElementById('dept_description').value = '';
    }

    async function openAddDepartmentPanel() {
        const html = document.getElementById('departmentFormTemplate').innerHTML;
        openDepartmentSlideOver('New Department', 'Add a new department', html);
        resetDepartmentForm();
        document.getElementById('dept_submit_btn').textContent = 'Save Department';
        attachDepartmentForm('{{ route("departments.store") }}', 'Department created successfully.');
    }

    async function openEditDepartmentPanel(url) {
        const html = document.getElementById('departmentFormTemplate').innerHTML;
        openDepartmentSlideOver('Edit Department', 'Update department details', html);
        try {
            const res = await fetch(url, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } });
            const data = await res.json();
            const d = data.department;
            document.getElementById('dept_method').value = 'PUT';
            document.getElementById('dept_name').value = d.name;
            document.getElementById('dept_code').value = d.code || '';
            document.getElementById('dept_is_active').value = d.is_active ? '1' : '0';
            document.getElementById('dept_description').value = d.description || '';
            document.getElementById('dept_submit_btn').textContent = 'Update Department';
            attachDepartmentForm(url.replace('/edit', ''), 'Department updated successfully.');
        } catch (err) {
            deptContent.innerHTML = '<div class="text-center text-red-600 py-8">Failed to load department details.</div>';
        }
    }
</script>
    <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
        @php
            $statConfig = [
                ['key' => 'total', 'label' => 'Total Departments', 'icon' => 'hgi-building-03', 'border' => 'border-gray-200', 'value' => 'text-gray-900'],
                ['key' => 'active', 'label' => 'Active', 'icon' => 'hgi-checkmark-circle-02', 'border' => 'border-emerald-200', 'value' => 'text-emerald-600'],
                ['key' => 'inactive', 'label' => 'Inactive', 'icon' => 'hgi-cancel-circle', 'border' => 'border-gray-200', 'value' => 'text-gray-500'],
            ];
        @endphp
        @foreach ($statConfig as $cfg)
            <div class="bg-white rounded-md border {{ $cfg['border'] }} p-4">
                <div class="flex items-center justify-between">
                    <span class="text-[10px] font-medium text-gray-500 uppercase tracking-wide">{{ $cfg['label'] }}</span>
                    <i class="hgi-stroke {{ $cfg['icon'] }} text-base text-gray-400"></i>
                </div>
                <p class="text-2xl font-bold {{ $cfg['value'] }} mt-1">{{ $stats[$cfg['key']] }}</p>
            </div>
        @endforeach
    </div>

    <script>
    const deptSlide = document.getElementById('departmentSlideOver');
    const deptBackdrop = document.getElementById('departmentBackdrop');
    const deptPanel = document.getElementById('departmentPanel');
    const deptContent = document.getElementById('departmentSlideContent');

    function openDepartmentSlideOver(title, subtitle, html) {
        deptSlide.classList.remove('hidden');
        document.getElementById('departmentSlideTitle').textContent = title;
        document.getElementById('departmentSlideSubtitle').textContent = subtitle;
        deptContent.innerHTML = html;
        setTimeout(() => {
            deptBackdrop.classList.remove('opacity-0');
            deptPanel.classList.remove('translate-x-full');
        }, 10);
        document.body.style.overflow = 'hidden';
    }

    function closeDepartmentSlideOver() {
        deptBackdrop.classList.add('opacity-0');
        deptPanel.classList.add('translate-x-full');
        setTimeout(() => {
            deptSlide.classList.add('hidden');
            deptContent.innerHTML = '';
            document.body.style.overflow = '';
        }, 300);
    }

    function attachDepartmentForm(action, successMessage) {
        const form = document.getElementById('departmentForm');
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
                Swal.fire({ icon: 'success', title: 'Success', text: data.message || successMessage, timer: 1500, showConfirmButton: false });
                closeDepartmentSlideOver();
                setTimeout(() => location.reload(), 1000);
            })
            .catch(err => {
                Swal.fire({ icon: 'error', title: 'Error', text: 'Failed to save department.' });
            });
        });
    }

    function resetDepartmentForm() {
        document.getElementById('dept_method').value = 'POST';
        document.getElementById('dept_name').value = '';
        document.getElementById('dept_code').value = '';
        document.getElementById('dept_is_active').value = '1';
        document.getElementById('dept_description').value = '';
    }

    async function openAddDepartmentPanel() {
        const html = document.getElementById('departmentFormTemplate').innerHTML;
        openDepartmentSlideOver('New Department', 'Add a new department', html);
        resetDepartmentForm();
        document.getElementById('dept_submit_btn').textContent = 'Save Department';
        attachDepartmentForm('{{ route("departments.store") }}', 'Department created successfully.');
    }

    async function openEditDepartmentPanel(url) {
        const html = document.getElementById('departmentFormTemplate').innerHTML;
        openDepartmentSlideOver('Edit Department', 'Update department details', html);
        try {
            const res = await fetch(url, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } });
            const data = await res.json();
            const d = data.department;
            document.getElementById('dept_method').value = 'PUT';
            document.getElementById('dept_name').value = d.name;
            document.getElementById('dept_code').value = d.code || '';
            document.getElementById('dept_is_active').value = d.is_active ? '1' : '0';
            document.getElementById('dept_description').value = d.description || '';
            document.getElementById('dept_submit_btn').textContent = 'Update Department';
            attachDepartmentForm(url.replace('/edit', ''), 'Department updated successfully.');
        } catch (err) {
            deptContent.innerHTML = '<div class="text-center text-red-600 py-8">Failed to load department details.</div>';
        }
    }
</script>
    <div class="bg-white rounded-md border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 text-[10px] uppercase text-gray-400 border-b border-gray-100">
                        <th class="px-5 py-3 font-medium text-left">Department</th>
                        <th class="px-5 py-3 font-medium text-left">Code</th>
                        <th class="px-5 py-3 font-medium text-left">Description</th>
                        <th class="px-5 py-3 font-medium text-left">Status</th>
                        <th class="px-5 py-3 font-medium text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse ($departments as $department)
                        <tr class="hover:bg-gray-50/50">
                            <td class="px-5 py-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded {{ $department->is_active ? 'bg-slate-100 text-slate-600' : 'bg-gray-100 text-gray-500' }} flex items-center justify-center text-[10px] font-bold">
                                        {{ strtoupper(substr($department->name, 0, 2)) }}
                                    </div>
                                    <span class="text-xs font-medium text-gray-900">{{ $department->name }}</span>
                                </div>
                            </td>
                            <td class="px-5 py-3 text-gray-600 font-mono text-xs">{{ $department->code ?? '-' }}</td>
                            <td class="px-5 py-3 text-gray-600 text-xs">{{ Str::limit($department->description, 50) }}</td>
                            <td class="px-5 py-3">
                                <span class="inline-flex items-center gap-1.5 text-[10px] font-medium text-gray-600">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $department->is_active ? 'bg-emerald-500' : 'bg-gray-400' }}"></span>
                                    {{ $department->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td class="px-5 py-3">
                                <div class="flex items-center justify-end gap-1">
                                    <button type="button" onclick="openEditDepartmentPanel('{{ route('departments.edit', $department) }}')" class="action-icon group/icon relative p-1.5 text-slate-600 hover:bg-slate-100 rounded transition-colors" title="Edit">
                                        <i class="hgi-stroke hgi-pencil-edit-01 text-base"></i>
                                        <span class="absolute -top-8 left-1/2 -translate-x-1/2 px-2 py-1 bg-gray-900 text-white text-[10px] rounded opacity-0 group-hover/icon:opacity-100 transition-opacity whitespace-nowrap pointer-events-none">Edit</span>
                                    </button>
                                    <form method="POST" action="{{ route('departments.destroy', $department) }}" data-ajax class="inline" id="del-dept-{{ $department->id }}">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                    <x-ui.hold-confirm form="#del-dept-{{ $department->id }}" label="Hold to delete" confirmed-label="Deleted" icon="hgi-cancel-circle" class="min-w-0 h-8 px-2.5" title="Delete" undo="Department deleted" />
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-5 py-8 text-center text-gray-400">
                            <div class="flex flex-col items-center gap-2">
                                <i class="hgi-stroke hgi-building-03 text-4xl text-gray-300"></i>
                                <p class="text-xs">No departments found</p>
                            </div>
                        </td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-5 py-3 border-t border-gray-100">
            {{ $departments->links() }}
        </div>
    </div>
</div>

<script>
    const deptSlide = document.getElementById('departmentSlideOver');
    const deptBackdrop = document.getElementById('departmentBackdrop');
    const deptPanel = document.getElementById('departmentPanel');
    const deptContent = document.getElementById('departmentSlideContent');

    function openDepartmentSlideOver(title, subtitle, html) {
        deptSlide.classList.remove('hidden');
        document.getElementById('departmentSlideTitle').textContent = title;
        document.getElementById('departmentSlideSubtitle').textContent = subtitle;
        deptContent.innerHTML = html;
        setTimeout(() => {
            deptBackdrop.classList.remove('opacity-0');
            deptPanel.classList.remove('translate-x-full');
        }, 10);
        document.body.style.overflow = 'hidden';
    }

    function closeDepartmentSlideOver() {
        deptBackdrop.classList.add('opacity-0');
        deptPanel.classList.add('translate-x-full');
        setTimeout(() => {
            deptSlide.classList.add('hidden');
            deptContent.innerHTML = '';
            document.body.style.overflow = '';
        }, 300);
    }

    function attachDepartmentForm(action, successMessage) {
        const form = document.getElementById('departmentForm');
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
                Swal.fire({ icon: 'success', title: 'Success', text: data.message || successMessage, timer: 1500, showConfirmButton: false });
                closeDepartmentSlideOver();
                setTimeout(() => location.reload(), 1000);
            })
            .catch(err => {
                Swal.fire({ icon: 'error', title: 'Error', text: 'Failed to save department.' });
            });
        });
    }

    function resetDepartmentForm() {
        document.getElementById('dept_method').value = 'POST';
        document.getElementById('dept_name').value = '';
        document.getElementById('dept_code').value = '';
        document.getElementById('dept_is_active').value = '1';
        document.getElementById('dept_description').value = '';
    }

    async function openAddDepartmentPanel() {
        const html = document.getElementById('departmentFormTemplate').innerHTML;
        openDepartmentSlideOver('New Department', 'Add a new department', html);
        resetDepartmentForm();
        document.getElementById('dept_submit_btn').textContent = 'Save Department';
        attachDepartmentForm('{{ route("departments.store") }}', 'Department created successfully.');
    }

    async function openEditDepartmentPanel(url) {
        const html = document.getElementById('departmentFormTemplate').innerHTML;
        openDepartmentSlideOver('Edit Department', 'Update department details', html);
        try {
            const res = await fetch(url, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } });
            const data = await res.json();
            const d = data.department;
            document.getElementById('dept_method').value = 'PUT';
            document.getElementById('dept_name').value = d.name;
            document.getElementById('dept_code').value = d.code || '';
            document.getElementById('dept_is_active').value = d.is_active ? '1' : '0';
            document.getElementById('dept_description').value = d.description || '';
            document.getElementById('dept_submit_btn').textContent = 'Update Department';
            attachDepartmentForm(url.replace('/edit', ''), 'Department updated successfully.');
        } catch (err) {
            deptContent.innerHTML = '<div class="text-center text-red-600 py-8">Failed to load department details.</div>';
        }
    }
</script>
<div id="departmentSlideOver" class="fixed inset-0 z-50 hidden" aria-hidden="true">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm transition-opacity opacity-0" id="departmentBackdrop" onclick="closeDepartmentSlideOver()"></div>
    <div class="absolute inset-y-0 right-0 w-full max-w-lg transform translate-x-full transition-transform duration-300 ease-out" id="departmentPanel">
        <div class="h-full bg-white shadow-2xl flex flex-col">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between bg-gray-50/50">
                <div>
                    <h3 class="text-lg font-bold text-gray-900" id="departmentSlideTitle">Department</h3>
                    <p class="text-xs text-gray-500" id="departmentSlideSubtitle">Manage department details</p>
                </div>
                <button onclick="closeDepartmentSlideOver()" class="text-gray-400 hover:text-gray-600 p-1 rounded-lg hover:bg-gray-100 transition-colors">
                    <i class="hgi-stroke hgi-cancel-01 text-2xl"></i>
                </button>
            </div>
            <div class="flex-1 overflow-y-auto p-6" id="departmentSlideContent"></div>
        </div>
    </div>
</div>

<script>
    const deptSlide = document.getElementById('departmentSlideOver');
    const deptBackdrop = document.getElementById('departmentBackdrop');
    const deptPanel = document.getElementById('departmentPanel');
    const deptContent = document.getElementById('departmentSlideContent');

    function openDepartmentSlideOver(title, subtitle, html) {
        deptSlide.classList.remove('hidden');
        document.getElementById('departmentSlideTitle').textContent = title;
        document.getElementById('departmentSlideSubtitle').textContent = subtitle;
        deptContent.innerHTML = html;
        setTimeout(() => {
            deptBackdrop.classList.remove('opacity-0');
            deptPanel.classList.remove('translate-x-full');
        }, 10);
        document.body.style.overflow = 'hidden';
    }

    function closeDepartmentSlideOver() {
        deptBackdrop.classList.add('opacity-0');
        deptPanel.classList.add('translate-x-full');
        setTimeout(() => {
            deptSlide.classList.add('hidden');
            deptContent.innerHTML = '';
            document.body.style.overflow = '';
        }, 300);
    }

    function attachDepartmentForm(action, successMessage) {
        const form = document.getElementById('departmentForm');
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
                Swal.fire({ icon: 'success', title: 'Success', text: data.message || successMessage, timer: 1500, showConfirmButton: false });
                closeDepartmentSlideOver();
                setTimeout(() => location.reload(), 1000);
            })
            .catch(err => {
                Swal.fire({ icon: 'error', title: 'Error', text: 'Failed to save department.' });
            });
        });
    }

    function resetDepartmentForm() {
        document.getElementById('dept_method').value = 'POST';
        document.getElementById('dept_name').value = '';
        document.getElementById('dept_code').value = '';
        document.getElementById('dept_is_active').value = '1';
        document.getElementById('dept_description').value = '';
    }

    async function openAddDepartmentPanel() {
        const html = document.getElementById('departmentFormTemplate').innerHTML;
        openDepartmentSlideOver('New Department', 'Add a new department', html);
        resetDepartmentForm();
        document.getElementById('dept_submit_btn').textContent = 'Save Department';
        attachDepartmentForm('{{ route("departments.store") }}', 'Department created successfully.');
    }

    async function openEditDepartmentPanel(url) {
        const html = document.getElementById('departmentFormTemplate').innerHTML;
        openDepartmentSlideOver('Edit Department', 'Update department details', html);
        try {
            const res = await fetch(url, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } });
            const data = await res.json();
            const d = data.department;
            document.getElementById('dept_method').value = 'PUT';
            document.getElementById('dept_name').value = d.name;
            document.getElementById('dept_code').value = d.code || '';
            document.getElementById('dept_is_active').value = d.is_active ? '1' : '0';
            document.getElementById('dept_description').value = d.description || '';
            document.getElementById('dept_submit_btn').textContent = 'Update Department';
            attachDepartmentForm(url.replace('/edit', ''), 'Department updated successfully.');
        } catch (err) {
            deptContent.innerHTML = '<div class="text-center text-red-600 py-8">Failed to load department details.</div>';
        }
    }
</script>

@push('scripts')
<script>
    const deptSlide = document.getElementById('departmentSlideOver');
    const deptBackdrop = document.getElementById('departmentBackdrop');
    const deptPanel = document.getElementById('departmentPanel');
    const deptContent = document.getElementById('departmentSlideContent');

    function openDepartmentSlideOver(title, subtitle, html) {
        deptSlide.classList.remove('hidden');
        document.getElementById('departmentSlideTitle').textContent = title;
        document.getElementById('departmentSlideSubtitle').textContent = subtitle;
        deptContent.innerHTML = html;
        setTimeout(() => {
            deptBackdrop.classList.remove('opacity-0');
            deptPanel.classList.remove('translate-x-full');
        }, 10);
        document.body.style.overflow = 'hidden';
    }

    function closeDepartmentSlideOver() {
        deptBackdrop.classList.add('opacity-0');
        deptPanel.classList.add('translate-x-full');
        setTimeout(() => {
            deptSlide.classList.add('hidden');
            deptContent.innerHTML = '';
            document.body.style.overflow = '';
        }, 300);
    }

    function attachDepartmentForm(action, successMessage) {
        const form = document.getElementById('departmentForm');
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
                Swal.fire({ icon: 'success', title: 'Success', text: data.message || successMessage, timer: 1500, showConfirmButton: false });
                closeDepartmentSlideOver();
                setTimeout(() => location.reload(), 1000);
            })
            .catch(err => {
                Swal.fire({ icon: 'error', title: 'Error', text: 'Failed to save department.' });
            });
        });
    }

    function resetDepartmentForm() {
        document.getElementById('dept_method').value = 'POST';
        document.getElementById('dept_name').value = '';
        document.getElementById('dept_code').value = '';
        document.getElementById('dept_is_active').value = '1';
        document.getElementById('dept_description').value = '';
    }

    async function openAddDepartmentPanel() {
        const html = document.getElementById('departmentFormTemplate').innerHTML;
        openDepartmentSlideOver('New Department', 'Add a new department', html);
        resetDepartmentForm();
        document.getElementById('dept_submit_btn').textContent = 'Save Department';
        attachDepartmentForm('{{ route("departments.store") }}', 'Department created successfully.');
    }

    async function openEditDepartmentPanel(url) {
        const html = document.getElementById('departmentFormTemplate').innerHTML;
        openDepartmentSlideOver('Edit Department', 'Update department details', html);
        try {
            const res = await fetch(url, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } });
            const data = await res.json();
            const d = data.department;
            document.getElementById('dept_method').value = 'PUT';
            document.getElementById('dept_name').value = d.name;
            document.getElementById('dept_code').value = d.code || '';
            document.getElementById('dept_is_active').value = d.is_active ? '1' : '0';
            document.getElementById('dept_description').value = d.description || '';
            document.getElementById('dept_submit_btn').textContent = 'Update Department';
            attachDepartmentForm(url.replace('/edit', ''), 'Department updated successfully.');
        } catch (err) {
            deptContent.innerHTML = '<div class="text-center text-red-600 py-8">Failed to load department details.</div>';
        }
    }
</script>
@endpush
@endsection
