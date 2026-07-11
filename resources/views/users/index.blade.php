@extends('layouts.dashboard')

@section('title', 'Medical Staff - ' . config('app.name', 'Laravel'))
@section('page_title', 'Medical Staff / User Accounts')

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-lg font-bold text-gray-900">Medical Staff</h2>
            <p class="text-sm text-gray-500">Manage hospital staff and user accounts</p>
        </div>
        <div class="flex items-center gap-2">
            <div id="bulkActions" class="hidden flex items-center gap-2">
                <button type="button" onclick="bulkActivate()" class="inline-flex items-center gap-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-medium px-3 py-1.5 rounded-lg shadow-sm transition-all">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Activate
                </button>
                <button type="button" onclick="bulkDeactivate()" class="inline-flex items-center gap-1.5 bg-amber-500 hover:bg-amber-600 text-white text-xs font-medium px-3 py-1.5 rounded-lg shadow-sm transition-all">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                    Deactivate
                </button>
                <button type="button" onclick="bulkDelete()" class="inline-flex items-center gap-1.5 bg-red-600 hover:bg-red-700 text-white text-xs font-medium px-3 py-1.5 rounded-lg shadow-sm transition-all">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Delete
                </button>
            </div>
            <button type="button" onclick="openAddUserPanel()" class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium px-4 py-2 rounded-lg shadow-sm hover:shadow transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Add Staff
            </button>
        </div>
    </div>

    {{-- KPI Cards --}}
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
        @php
            $statConfig = [
                ['key' => 'total', 'label' => 'Total Staff', 'icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z', 'from' => 'blue-500', 'to' => 'blue-700', 'border' => 'blue-400', 'text' => 'blue-100', 'sub' => 'blue-200'],
                ['key' => 'active', 'label' => 'Active', 'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z', 'from' => 'emerald-500', 'to' => 'emerald-700', 'border' => 'emerald-400', 'text' => 'emerald-100', 'sub' => 'emerald-200'],
                ['key' => 'inactive', 'label' => 'Inactive', 'icon' => 'M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z', 'from' => 'red-500', 'to' => 'red-700', 'border' => 'red-400', 'text' => 'red-100', 'sub' => 'red-200'],
                ['key' => 'doctors', 'label' => 'Doctors', 'icon' => 'M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z', 'from' => 'purple-500', 'to' => 'purple-700', 'border' => 'purple-400', 'text' => 'purple-100', 'sub' => 'purple-200'],
                ['key' => 'reception', 'label' => 'Reception', 'icon' => 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4', 'from' => 'cyan-500', 'to' => 'cyan-700', 'border' => 'cyan-400', 'text' => 'cyan-100', 'sub' => 'cyan-200'],
                ['key' => 'admin', 'label' => 'Admin', 'icon' => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z', 'from' => 'amber-500', 'to' => 'amber-700', 'border' => 'amber-400', 'text' => 'amber-100', 'sub' => 'amber-200'],
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

    {{-- Users Table --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                    <tr>
                        <th class="px-6 py-3 w-10">
                            <input type="checkbox" id="selectAll" class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500" onchange="toggleAllCheckboxes()">
                        </th>
                        <th class="px-6 py-3">Staff</th>
                        <th class="px-6 py-3">Email</th>
                        <th class="px-6 py-3">Phone</th>
                        <th class="px-6 py-3">Role</th>
                        <th class="px-6 py-3">Status</th>
                        <th class="px-6 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($users as $user)
                        <tr class="group hover:bg-emerald-50/40 transition-colors">
                            <td class="px-6 py-3.5">
                                <input type="checkbox" class="user-checkbox rounded border-gray-300 text-emerald-600 focus:ring-emerald-500" value="{{ $user->id }}" onchange="updateBulkActions()">
                            </td>
                            <td class="px-6 py-3.5">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-emerald-500 to-emerald-700 flex items-center justify-center text-white text-sm font-bold shadow-sm">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="font-medium text-gray-900">{{ $user->name }}</div>
                                        <div class="text-xs text-gray-500">{{ $user->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-3.5 text-gray-700 text-xs">{{ $user->email }}</td>
                            <td class="px-6 py-3.5 text-gray-700 text-xs">{{ $user->phone ?? '-' }}</td>
                            <td class="px-6 py-3.5">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium {{ $user->hasRole('doctor') ? 'bg-purple-100 text-purple-700' : ($user->hasRole('reception') ? 'bg-cyan-100 text-cyan-700' : 'bg-amber-100 text-amber-700') }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $user->hasRole('doctor') ? 'bg-purple-500' : ($user->hasRole('reception') ? 'bg-cyan-500' : 'bg-amber-500') }}"></span>
                                    {{ ucfirst($user->roles->first()?->name ?? 'none') }}
                                </span>
                            </td>
                            <td class="px-6 py-3.5">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium {{ $user->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700' }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $user->is_active ? 'bg-emerald-500' : 'bg-red-500' }}"></span>
                                    {{ $user->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td class="px-6 py-3.5">
                                <div class="flex items-center justify-end gap-1">
                                    <button type="button" onclick="openEditUserPanel('{{ route('users.edit', $user) }}')" class="action-icon group/icon relative p-2 text-emerald-600 hover:bg-emerald-100 rounded-lg transition-colors" title="Edit">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.43-9.525l-9.17 9.17a2 2 0 00-.586 1.414V17a1 1 0 001 1h2.828a2 2 0 001.414-.586l9.17-9.17a2 2 0 000-2.828l-1.414-1.414a2 2 0 00-2.828 0z"/></svg>
                                        <span class="absolute -top-8 left-1/2 -translate-x-1/2 px-2 py-1 bg-gray-900 text-white text-[10px] rounded opacity-0 group-hover/icon:opacity-100 transition-opacity whitespace-nowrap pointer-events-none">Edit</span>
                                    </button>
                                    @if ($user->id !== auth()->id())
                                        <form method="POST" action="{{ route('users.destroy', $user) }}" data-ajax data-confirm="Delete {{ $user->name }}?" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="action-icon group/icon relative p-2 text-red-600 hover:bg-red-100 rounded-lg transition-colors" title="Delete">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                <span class="absolute -top-8 left-1/2 -translate-x-1/2 px-2 py-1 bg-gray-900 text-white text-[10px] rounded opacity-0 group-hover/icon:opacity-100 transition-opacity whitespace-nowrap pointer-events-none">Delete</span>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-6 py-10 text-center text-gray-400">
                            <div class="flex flex-col items-center gap-2">
                                <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                <p>No staff found</p>
                            </div>
                        </td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-100">
            {{ $users->links() }}
        </div>
    </div>
</div>

{{-- Slide-over Panel --}}
<div id="userSlideOver" class="fixed inset-0 z-50 hidden" aria-hidden="true">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm transition-opacity opacity-0" id="userBackdrop" onclick="closeUserSlideOver()"></div>
    <div class="absolute inset-y-0 right-0 w-full max-w-lg transform translate-x-full transition-transform duration-300 ease-out" id="userPanel">
        <div class="h-full bg-white shadow-2xl flex flex-col">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between bg-gray-50/50">
                <div>
                    <h3 class="text-lg font-bold text-gray-900" id="userSlideTitle">Staff</h3>
                    <p class="text-xs text-gray-500" id="userSlideSubtitle">Manage staff details</p>
                </div>
                <button onclick="closeUserSlideOver()" class="text-gray-400 hover:text-gray-600 p-1 rounded-lg hover:bg-gray-100 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="flex-1 overflow-y-auto p-6" id="userSlideContent"></div>
        </div>
    </div>
</div>

<template id="userFormTemplate">
    <form id="userForm" method="POST" action="" class="space-y-4">
        @csrf
        <input type="hidden" name="_method" id="user_method" value="POST">
        <div class="grid grid-cols-2 gap-4">
            <div class="col-span-2">
                <label class="block text-xs font-medium text-gray-700 mb-1">Full Name <span class="text-red-500">*</span></label>
                <input type="text" name="name" id="user_name" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" required>
            </div>
            <div class="col-span-2">
                <label class="block text-xs font-medium text-gray-700 mb-1">Email <span class="text-red-500">*</span></label>
                <input type="email" name="email" id="user_email" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" required>
            </div>
            <div class="col-span-2 sm:col-span-1">
                <label class="block text-xs font-medium text-gray-700 mb-1">Phone</label>
                <input type="text" name="phone" id="user_phone" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
            </div>
            <div class="col-span-2 sm:col-span-1">
                <label class="block text-xs font-medium text-gray-700 mb-1">Role <span class="text-red-500">*</span></label>
                <select name="role" id="user_role" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 bg-white" required>
                    <option value="">Select role...</option>
                </select>
            </div>
            <div class="col-span-2">
                <label class="block text-xs font-medium text-gray-700 mb-1">Password <span class="text-red-500" id="password_required">*</span></label>
                <input type="password" name="password" id="user_password" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" required minlength="6">
                <p class="text-[10px] text-gray-400 mt-1">Minimum 6 characters</p>
            </div>
            <div class="col-span-2">
                <label class="block text-xs font-medium text-gray-700 mb-1">Status <span class="text-red-500">*</span></label>
                <select name="is_active" id="user_is_active" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 bg-white" required>
                    <option value="1">Active</option>
                    <option value="0">Inactive</option>
                </select>
            </div>
        </div>
        <div class="flex justify-end gap-2 pt-4 border-t border-gray-100 mt-4">
            <button type="button" onclick="closeUserSlideOver()" class="px-4 py-2 text-sm text-gray-600 hover:bg-gray-100 rounded-lg">Cancel</button>
            <button type="submit" class="px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700" id="user_submit_btn">Save Staff</button>
        </div>
    </form>
</template>

@push('scripts')
<script>
    const userSlide = document.getElementById('userSlideOver');
    const userBackdrop = document.getElementById('userBackdrop');
    const userPanel = document.getElementById('userPanel');
    const userContent = document.getElementById('userSlideContent');

    function openUserSlideOver(title, subtitle, html) {
        userSlide.classList.remove('hidden');
        document.getElementById('userSlideTitle').textContent = title;
        document.getElementById('userSlideSubtitle').textContent = subtitle;
        userContent.innerHTML = html;
        setTimeout(() => {
            userBackdrop.classList.remove('opacity-0');
            userPanel.classList.remove('translate-x-full');
        }, 10);
        document.body.style.overflow = 'hidden';
    }

    function closeUserSlideOver() {
        userBackdrop.classList.add('opacity-0');
        userPanel.classList.add('translate-x-full');
        setTimeout(() => {
            userSlide.classList.add('hidden');
            userContent.innerHTML = '';
            document.body.style.overflow = '';
        }, 300);
    }

    function populateRoleSelect(roles) {
        const select = document.getElementById('user_role');
        select.innerHTML = '<option value="">Select role...</option>' +
            roles.map(r => `<option value="${r}">${ucfirst(r)}</option>`).join('');
    }

    function attachUserForm(action, successMessage) {
        const form = document.getElementById('userForm');
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
                closeUserSlideOver();
                setTimeout(() => location.reload(), 1000);
            })
            .catch(err => {
                Swal.fire({ icon: 'error', title: 'Error', text: 'Failed to save staff.' });
            });
        });
    }

    function resetUserForm() {
        document.getElementById('user_method').value = 'POST';
        document.getElementById('user_name').value = '';
        document.getElementById('user_email').value = '';
        document.getElementById('user_phone').value = '';
        document.getElementById('user_role').value = '';
        document.getElementById('user_password').value = '';
        document.getElementById('user_password').required = true;
        document.getElementById('password_required').style.display = 'inline';
        document.getElementById('user_is_active').value = '1';
    }

    async function openAddUserPanel() {
        const html = document.getElementById('userFormTemplate').innerHTML;
        openUserSlideOver('New Staff', 'Add a new staff member', html);
        try {
            const res = await fetch('{{ route("users.index") }}', { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } });
            const data = await res.json();
            populateRoleSelect(data.roles);
            resetUserForm();
            document.getElementById('user_submit_btn').textContent = 'Save Staff';
            attachUserForm('{{ route("users.store") }}', 'Staff created successfully.');
        } catch (err) {
            userContent.innerHTML = '<div class="text-center text-red-600 py-8">Failed to load form data.</div>';
        }
    }

    async function openEditUserPanel(url) {
        const html = document.getElementById('userFormTemplate').innerHTML;
        openUserSlideOver('Edit Staff', 'Update staff details', html);
        try {
            const res = await fetch(url, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } });
            const data = await res.json();
            populateRoleSelect(data.roles);
            const u = data.user;
            document.getElementById('user_method').value = 'PUT';
            document.getElementById('user_name').value = u.name;
            document.getElementById('user_email').value = u.email;
            document.getElementById('user_phone').value = u.phone || '';
            document.getElementById('user_role').value = u.roles[0]?.name || '';
            document.getElementById('user_password').value = '';
            document.getElementById('user_password').required = false;
            document.getElementById('password_required').style.display = 'none';
            document.getElementById('user_is_active').value = u.is_active ? '1' : '0';
            document.getElementById('user_submit_btn').textContent = 'Update Staff';
            attachUserForm(url.replace('/edit', ''), 'Staff updated successfully.');
        } catch (err) {
            userContent.innerHTML = '<div class="text-center text-red-600 py-8">Failed to load staff details.</div>';
        }
    }

    function toggleAllCheckboxes() {
        const selectAll = document.getElementById('selectAll');
        const checkboxes = document.querySelectorAll('.user-checkbox');
        checkboxes.forEach(cb => cb.checked = selectAll.checked);
        updateBulkActions();
    }

    function updateBulkActions() {
        const checkboxes = document.querySelectorAll('.user-checkbox:checked');
        const bulkActions = document.getElementById('bulkActions');
        if (checkboxes.length > 0) {
            bulkActions.classList.remove('hidden');
        } else {
            bulkActions.classList.add('hidden');
        }
    }

    function getSelectedIds() {
        const checkboxes = document.querySelectorAll('.user-checkbox:checked');
        return Array.from(checkboxes).map(cb => cb.value);
    }

    async function bulkActivate() {
        const ids = getSelectedIds();
        if (ids.length === 0) return;

        Swal.fire({
            title: 'Activate Selected?',
            text: `Activate ${ids.length} selected staff members?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#10b981',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, Activate',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch('{{ route("users.bulk-activate") }}', {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ ids: ids })
                })
                .then(r => r.json().catch(() => ({})))
                .then(data => {
                    Swal.fire({ icon: 'success', title: 'Success', text: data.message, timer: 1500, showConfirmButton: false });
                    setTimeout(() => location.reload(), 1000);
                })
                .catch(err => {
                    Swal.fire({ icon: 'error', title: 'Error', text: 'Failed to activate staff.' });
                });
            }
        });
    }

    async function bulkDeactivate() {
        const ids = getSelectedIds();
        if (ids.length === 0) return;

        Swal.fire({
            title: 'Deactivate Selected?',
            text: `Deactivate ${ids.length} selected staff members?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#f59e0b',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, Deactivate',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch('{{ route("users.bulk-deactivate") }}', {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ ids: ids })
                })
                .then(r => r.json().catch(() => ({})))
                .then(data => {
                    Swal.fire({ icon: 'success', title: 'Success', text: data.message, timer: 1500, showConfirmButton: false });
                    setTimeout(() => location.reload(), 1000);
                })
                .catch(err => {
                    Swal.fire({ icon: 'error', title: 'Error', text: 'Failed to deactivate staff.' });
                });
            }
        });
    }

    async function bulkDelete() {
        const ids = getSelectedIds();
        if (ids.length === 0) return;

        Swal.fire({
            title: 'Delete Selected?',
            text: `Delete ${ids.length} selected staff members? This action cannot be undone.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, Delete',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch('{{ route("users.bulk-delete") }}', {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ ids: ids })
                })
                .then(r => r.json().catch(() => ({})))
                .then(data => {
                    Swal.fire({ icon: 'success', title: 'Success', text: data.message, timer: 1500, showConfirmButton: false });
                    setTimeout(() => location.reload(), 1000);
                })
                .catch(err => {
                    Swal.fire({ icon: 'error', title: 'Error', text: 'Failed to delete staff.' });
                });
            }
        });
    }
</script>
@endpush
@endsection
