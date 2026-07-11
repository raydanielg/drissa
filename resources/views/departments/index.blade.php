@extends('layouts.dashboard')

@section('title', 'Departments - ' . config('app.name', 'Laravel'))
@section('page_title', 'Human Resources - Departments')

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-lg font-bold text-gray-900">Departments</h2>
            <p class="text-sm text-gray-500">Manage hospital departments and units</p>
        </div>
        <button type="button" onclick="openAddDepartmentPanel()" class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium px-4 py-2 rounded-lg shadow-sm hover:shadow transition-all">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Add Department
        </button>
    </div>

    {{-- KPI Cards --}}
    <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
        @php
            $statConfig = [
                ['key' => 'total', 'label' => 'Total Departments', 'icon' => 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4', 'from' => 'blue-500', 'to' => 'blue-700', 'border' => 'blue-400', 'text' => 'blue-100', 'sub' => 'blue-200'],
                ['key' => 'active', 'label' => 'Active', 'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z', 'from' => 'emerald-500', 'to' => 'emerald-700', 'border' => 'emerald-400', 'text' => 'emerald-100', 'sub' => 'emerald-200'],
                ['key' => 'inactive', 'label' => 'Inactive', 'icon' => 'M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z', 'from' => 'red-500', 'to' => 'red-700', 'border' => 'red-400', 'text' => 'red-100', 'sub' => 'red-200'],
            ];
        @endphp
        @foreach ($statConfig as $cfg)
            <div class="card-sm block bg-gradient-to-br from-{{ $cfg['from'] }} to-{{ $cfg['to'] }} rounded-xl border border-{{ $cfg['border'] }} p-4 text-white relative overflow-hidden shadow-md hover:shadow-lg transition-shadow">
                <div class="absolute top-0 right-0 w-16 h-16 bg-white/10 rounded-full -mr-8 -mt-8"></div>
                <div class="relative z-10">
                    <div class="flex items-start justify-between mb-2">
                        <span class="text-[10px] font-medium {{ $cfg['text'] }}">{{ $cfg['label'] }}</span>
                        <svg class="w-4 h-4 {{ $cfg['sub'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $cfg['icon'] }}"/></svg>
                    </div>
                    <div class="text-2xl font-bold">{{ $stats[$cfg['key']] }}</div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Departments Table --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                    <tr>
                        <th class="px-6 py-3">Department</th>
                        <th class="px-6 py-3">Code</th>
                        <th class="px-6 py-3">Description</th>
                        <th class="px-6 py-3">Status</th>
                        <th class="px-6 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($departments as $department)
                        <tr class="group hover:bg-emerald-50/40 transition-colors">
                            <td class="px-6 py-3.5">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-lg {{ $department->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-700' }} flex items-center justify-center text-sm font-bold">
                                        {{ strtoupper(substr($department->name, 0, 2)) }}
                                    </div>
                                    <span class="font-medium text-gray-900">{{ $department->name }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-3.5 text-gray-700 font-mono text-xs">{{ $department->code ?? '-' }}</td>
                            <td class="px-6 py-3.5 text-gray-700 text-xs">{{ Str::limit($department->description, 50) }}</td>
                            <td class="px-6 py-3.5">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium {{ $department->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-700' }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $department->is_active ? 'bg-emerald-500' : 'bg-gray-500' }}"></span>
                                    {{ $department->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td class="px-6 py-3.5">
                                <div class="flex items-center justify-end gap-1">
                                    <button type="button" onclick="openEditDepartmentPanel('{{ route('departments.edit', $department) }}')" class="action-icon group/icon relative p-2 text-emerald-600 hover:bg-emerald-100 rounded-lg transition-colors" title="Edit">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.43-9.525l-9.17 9.17a2 2 0 00-.586 1.414V17a1 1 0 001 1h2.828a2 2 0 001.414-.586l9.17-9.17a2 2 0 000-2.828l-1.414-1.414a2 2 0 00-2.828 0z"/></svg>
                                        <span class="absolute -top-8 left-1/2 -translate-x-1/2 px-2 py-1 bg-gray-900 text-white text-[10px] rounded opacity-0 group-hover/icon:opacity-100 transition-opacity whitespace-nowrap pointer-events-none">Edit</span>
                                    </button>
                                    <form method="POST" action="{{ route('departments.destroy', $department) }}" data-ajax data-confirm="Delete this department?" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="action-icon group/icon relative p-2 text-red-600 hover:bg-red-100 rounded-lg transition-colors" title="Delete">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            <span class="absolute -top-8 left-1/2 -translate-x-1/2 px-2 py-1 bg-gray-900 text-white text-[10px] rounded opacity-0 group-hover/icon:opacity-100 transition-opacity whitespace-nowrap pointer-events-none">Delete</span>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-6 py-10 text-center text-gray-400">
                            <div class="flex flex-col items-center gap-2">
                                <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                <p>No departments found</p>
                            </div>
                        </td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-100">
            {{ $departments->links() }}
        </div>
    </div>
</div>

{{-- Slide-over Panel --}}
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
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="flex-1 overflow-y-auto p-6" id="departmentSlideContent"></div>
        </div>
    </div>
</div>

<template id="departmentFormTemplate">
    <form id="departmentForm" method="POST" action="" class="space-y-4">
        @csrf
        <input type="hidden" name="_method" id="dept_method" value="POST">
        <div class="grid grid-cols-2 gap-4">
            <div class="col-span-2">
                <label class="block text-xs font-medium text-gray-700 mb-1">Department Name <span class="text-red-500">*</span></label>
                <input type="text" name="name" id="dept_name" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" required>
            </div>
            <div class="col-span-2 sm:col-span-1">
                <label class="block text-xs font-medium text-gray-700 mb-1">Code</label>
                <input type="text" name="code" id="dept_code" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
            </div>
            <div class="col-span-2 sm:col-span-1">
                <label class="block text-xs font-medium text-gray-700 mb-1">Status <span class="text-red-500">*</span></label>
                <select name="is_active" id="dept_is_active" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 bg-white" required>
                    <option value="1">Active</option>
                    <option value="0">Inactive</option>
                </select>
            </div>
            <div class="col-span-2">
                <label class="block text-xs font-medium text-gray-700 mb-1">Description</label>
                <textarea name="description" id="dept_description" rows="3" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"></textarea>
            </div>
        </div>
        <div class="flex justify-end gap-2 pt-4 border-t border-gray-100 mt-4">
            <button type="button" onclick="closeDepartmentSlideOver()" class="px-4 py-2 text-sm text-gray-600 hover:bg-gray-100 rounded-lg">Cancel</button>
            <button type="submit" class="px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700" id="dept_submit_btn">Save Department</button>
        </div>
    </form>
</template>

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
