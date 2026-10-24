@extends('layouts.dashboard')

@section('title', 'Medical Staff - ' . config('app.name', 'Laravel'))
@section('page_title', 'Medical Staff / User Accounts')

@section('content')
<div class="space-y-5">
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

    function ucfirst(str) {
        return str.charAt(0).toUpperCase() + str.slice(1);
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
            const btn = document.getElementById('user_submit_btn');
            const originalText = btn.textContent;
            btn.disabled = true;
            btn.textContent = 'Saving...';
            btn.classList.add('opacity-75', 'cursor-not-allowed');
            const formData = new FormData(form);
            fetch(action, {
                method: 'POST',
                body: formData,
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
            })
            .then(r => r.json().then(data => ({ ok: r.ok, data })))
            .then(({ ok, data }) => {
                if (ok) {
                    Swal.fire({ icon: 'success', title: 'Success', text: data.message || successMessage, timer: 1500, showConfirmButton: false });
                    closeUserSlideOver();
                    setTimeout(() => location.reload(), 1000);
                } else {
                    let errorMsg = data.message || 'Failed to save staff.';
                    if (data.errors) {
                        errorMsg = Object.values(data.errors).flat().join('\n');
                    }
                    Swal.fire({ icon: 'error', title: 'Validation Error', text: errorMsg });
                }
                btn.disabled = false;
                btn.textContent = originalText;
                btn.classList.remove('opacity-75', 'cursor-not-allowed');
            })
            .catch(() => {
                Swal.fire({ icon: 'error', title: 'Error', text: 'Failed to save staff. Please try again.' });
                btn.disabled = false;
                btn.textContent = originalText;
                btn.classList.remove('opacity-75', 'cursor-not-allowed');
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
            confirmButtonColor: '#0f172a',
            cancelButtonColor: '#6b7280',
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
            confirmButtonColor: '#0f172a',
            cancelButtonColor: '#6b7280',
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
            confirmButtonColor: '#0f172a',
            cancelButtonColor: '#6b7280',
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
    <div class="bg-slate-800 rounded-md p-6 text-white">
        <div class="flex flex-wrap items-end justify-between gap-3">
            <div>
                <p class="text-[10px] uppercase tracking-wider text-slate-400 mb-1">Admin</p>
                <h1 class="text-xl font-bold">Medical Staff</h1>
                <p class="text-xs text-slate-400 mt-1">Manage hospital staff and user accounts</p>
            </div>
            <div class="flex items-center gap-2">
                <div id="bulkActions" class="hidden flex items-center gap-2">
                    <button type="button" onclick="bulkActivate()" class="inline-flex items-center gap-1.5 bg-white/10 hover:bg-white/20 border border-slate-600 text-white text-xs font-medium px-3 py-1.5 rounded transition-colors">
                        <i class="hgi-stroke hgi-tick-02 text-sm"></i>
                        Activate
                    </button>
                    <button type="button" onclick="bulkDeactivate()" class="inline-flex items-center gap-1.5 bg-white/10 hover:bg-white/20 border border-slate-600 text-white text-xs font-medium px-3 py-1.5 rounded transition-colors">
                        <i class="hgi-stroke hgi-unavailable text-sm"></i>
                        Deactivate
                    </button>
                    <button type="button" onclick="bulkDelete()" class="inline-flex items-center gap-1.5 bg-red-600 hover:bg-red-700 text-white text-xs font-medium px-3 py-1.5 rounded transition-colors">
                        <i class="hgi-stroke hgi-cancel-circle text-sm"></i>
                        Delete
                    </button>
                </div>
                <button type="button" onclick="openAddUserPanel()" class="inline-flex items-center gap-1.5 bg-white hover:bg-gray-100 text-slate-800 text-xs font-semibold px-3 py-1.5 rounded transition-colors">
                    <i class="hgi-stroke hgi-add-01 text-sm"></i>
                    Add Staff
                </button>
            </div>
        </div>
    </div>

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

    function ucfirst(str) {
        return str.charAt(0).toUpperCase() + str.slice(1);
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
            const btn = document.getElementById('user_submit_btn');
            const originalText = btn.textContent;
            btn.disabled = true;
            btn.textContent = 'Saving...';
            btn.classList.add('opacity-75', 'cursor-not-allowed');
            const formData = new FormData(form);
            fetch(action, {
                method: 'POST',
                body: formData,
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
            })
            .then(r => r.json().then(data => ({ ok: r.ok, data })))
            .then(({ ok, data }) => {
                if (ok) {
                    Swal.fire({ icon: 'success', title: 'Success', text: data.message || successMessage, timer: 1500, showConfirmButton: false });
                    closeUserSlideOver();
                    setTimeout(() => location.reload(), 1000);
                } else {
                    let errorMsg = data.message || 'Failed to save staff.';
                    if (data.errors) {
                        errorMsg = Object.values(data.errors).flat().join('\n');
                    }
                    Swal.fire({ icon: 'error', title: 'Validation Error', text: errorMsg });
                }
                btn.disabled = false;
                btn.textContent = originalText;
                btn.classList.remove('opacity-75', 'cursor-not-allowed');
            })
            .catch(() => {
                Swal.fire({ icon: 'error', title: 'Error', text: 'Failed to save staff. Please try again.' });
                btn.disabled = false;
                btn.textContent = originalText;
                btn.classList.remove('opacity-75', 'cursor-not-allowed');
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
            confirmButtonColor: '#0f172a',
            cancelButtonColor: '#6b7280',
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
            confirmButtonColor: '#0f172a',
            cancelButtonColor: '#6b7280',
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
            confirmButtonColor: '#0f172a',
            cancelButtonColor: '#6b7280',
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
    <div class="grid grid-cols-2 md:grid-cols-5 gap-3">
        @php
            $statConfig = [
                ['key' => 'total', 'label' => 'Total Staff', 'icon' => 'hgi-user-multiple-02'],
                ['key' => 'active', 'label' => 'Active', 'icon' => 'hgi-checkmark-circle-02', 'border' => 'border-emerald-200', 'value' => 'text-emerald-600'],
                ['key' => 'inactive', 'label' => 'Inactive', 'icon' => 'hgi-cancel-circle', 'border' => 'border-red-200', 'value' => 'text-red-600'],
                ['key' => 'doctors', 'label' => 'Doctors', 'icon' => 'hgi-user-circle'],
                ['key' => 'reception', 'label' => 'Reception', 'icon' => 'hgi-building-03'],
                ['key' => 'lab', 'label' => 'Lab', 'icon' => 'hgi-test-tube-01'],
                ['key' => 'pharmacy', 'label' => 'Pharmacy', 'icon' => 'hgi-test-tube-01'],
                ['key' => 'ultrasound', 'label' => 'Ultrasound', 'icon' => 'hgi-analytics-02'],
                ['key' => 'manager', 'label' => 'Managers', 'icon' => 'hgi-file-02'],
                ['key' => 'admin', 'label' => 'Admin', 'icon' => 'hgi-checkmark-badge-01'],
            ];
        @endphp
        @foreach ($statConfig as $cfg)
            <div class="bg-white rounded-md border {{ $cfg['border'] ?? 'border-gray-200' }} p-4">
                <div class="flex items-start justify-between">
                    <p class="text-[10px] font-medium text-gray-500 uppercase tracking-wide">{{ $cfg['label'] }}</p>
                    <i class="hgi-stroke {{ $cfg['icon'] }} text-base text-gray-400"></i>
                </div>
                <p class="text-2xl font-bold {{ $cfg['value'] ?? 'text-gray-900' }} mt-1">{{ $stats[$cfg['key']] }}</p>
            </div>
        @endforeach
    </div>

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

    function ucfirst(str) {
        return str.charAt(0).toUpperCase() + str.slice(1);
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
            const btn = document.getElementById('user_submit_btn');
            const originalText = btn.textContent;
            btn.disabled = true;
            btn.textContent = 'Saving...';
            btn.classList.add('opacity-75', 'cursor-not-allowed');
            const formData = new FormData(form);
            fetch(action, {
                method: 'POST',
                body: formData,
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
            })
            .then(r => r.json().then(data => ({ ok: r.ok, data })))
            .then(({ ok, data }) => {
                if (ok) {
                    Swal.fire({ icon: 'success', title: 'Success', text: data.message || successMessage, timer: 1500, showConfirmButton: false });
                    closeUserSlideOver();
                    setTimeout(() => location.reload(), 1000);
                } else {
                    let errorMsg = data.message || 'Failed to save staff.';
                    if (data.errors) {
                        errorMsg = Object.values(data.errors).flat().join('\n');
                    }
                    Swal.fire({ icon: 'error', title: 'Validation Error', text: errorMsg });
                }
                btn.disabled = false;
                btn.textContent = originalText;
                btn.classList.remove('opacity-75', 'cursor-not-allowed');
            })
            .catch(() => {
                Swal.fire({ icon: 'error', title: 'Error', text: 'Failed to save staff. Please try again.' });
                btn.disabled = false;
                btn.textContent = originalText;
                btn.classList.remove('opacity-75', 'cursor-not-allowed');
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
            confirmButtonColor: '#0f172a',
            cancelButtonColor: '#6b7280',
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
            confirmButtonColor: '#0f172a',
            cancelButtonColor: '#6b7280',
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
            confirmButtonColor: '#0f172a',
            cancelButtonColor: '#6b7280',
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
    <div class="bg-white rounded-md border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-50 text-[10px] uppercase text-gray-400">
                    <tr>
                        <th class="px-5 py-3 w-10">
                            <input type="checkbox" id="selectAll" class="rounded border-gray-300 text-slate-600 focus:ring-slate-300" onchange="toggleAllCheckboxes()">
                        </th>
                        <th class="px-5 py-3">Staff</th>
                        <th class="px-5 py-3">Email</th>
                        <th class="px-5 py-3">Phone</th>
                        <th class="px-5 py-3">Role</th>
                        <th class="px-5 py-3">Status</th>
                        <th class="px-5 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse ($users as $user)
                        <tr class="hover:bg-gray-50/50">
                            <td class="px-5 py-3">
                                <input type="checkbox" class="user-checkbox rounded border-gray-300 text-slate-600 focus:ring-slate-300" value="{{ $user->id }}" onchange="updateBulkActions()">
                            </td>
                            <td class="px-5 py-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-slate-200 flex items-center justify-center text-slate-600 text-xs font-bold">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="text-xs font-medium text-gray-900">{{ $user->name }}</div>
                                        <div class="text-[10px] text-gray-500">{{ $user->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-3 text-xs text-gray-600">{{ $user->email }}</td>
                            <td class="px-5 py-3 text-xs text-gray-600">{{ $user->phone ?? '-' }}</td>
                            <td class="px-5 py-3">
                                @php
                                    $roleName = $user->roles->first()?->name ?? 'none';
                                    $roleDots = [
                                        'admin' => 'bg-amber-500',
                                        'doctor' => 'bg-slate-500',
                                        'reception' => 'bg-slate-400',
                                        'lab' => 'bg-slate-500',
                                        'pharmacy' => 'bg-slate-500',
                                        'nurse' => 'bg-slate-500',
                                        'ultrasound' => 'bg-slate-500',
                                        'manager' => 'bg-slate-600',
                                    ];
                                    $rd = $roleDots[$roleName] ?? 'bg-gray-400';
                                @endphp
                                <span class="inline-flex items-center gap-1.5 text-[10px] font-medium text-gray-600">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $rd }}"></span>
                                    {{ ucfirst($roleName) }}
                                </span>
                            </td>
                            <td class="px-5 py-3">
                                <span class="inline-flex items-center gap-1.5 text-[10px] font-medium text-gray-600">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $user->is_active ? 'bg-emerald-500' : 'bg-red-500' }}"></span>
                                    {{ $user->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td class="px-5 py-3">
                                <div class="flex items-center justify-end gap-1">
                                    <button type="button" onclick="openEditUserPanel('{{ route('users.edit', $user) }}')" class="p-1.5 text-slate-600 hover:bg-slate-100 rounded transition-colors" title="Edit">
                                        <i class="hgi-stroke hgi-pencil-edit-01 text-base"></i>
                                    </button>
                                    @if ($user->id !== auth()->id())
                                        <form method="POST" action="{{ route('users.destroy', $user) }}" data-ajax data-confirm="Delete {{ $user->name }}?" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1.5 text-red-600 hover:bg-red-50 rounded transition-colors" title="Delete">
                                                <i class="hgi-stroke hgi-cancel-circle text-base"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="px-5 py-8 text-center text-gray-400 text-xs">
                            <div class="flex flex-col items-center gap-2">
                                <i class="hgi-stroke hgi-user-multiple-02 text-4xl text-gray-300"></i>
                                <p>No staff found</p>
                            </div>
                        </td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-5 py-3 border-t border-gray-100">
            {{ $users->links() }}
        </div>
    </div>
</div>

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

    function ucfirst(str) {
        return str.charAt(0).toUpperCase() + str.slice(1);
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
            const btn = document.getElementById('user_submit_btn');
            const originalText = btn.textContent;
            btn.disabled = true;
            btn.textContent = 'Saving...';
            btn.classList.add('opacity-75', 'cursor-not-allowed');
            const formData = new FormData(form);
            fetch(action, {
                method: 'POST',
                body: formData,
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
            })
            .then(r => r.json().then(data => ({ ok: r.ok, data })))
            .then(({ ok, data }) => {
                if (ok) {
                    Swal.fire({ icon: 'success', title: 'Success', text: data.message || successMessage, timer: 1500, showConfirmButton: false });
                    closeUserSlideOver();
                    setTimeout(() => location.reload(), 1000);
                } else {
                    let errorMsg = data.message || 'Failed to save staff.';
                    if (data.errors) {
                        errorMsg = Object.values(data.errors).flat().join('\n');
                    }
                    Swal.fire({ icon: 'error', title: 'Validation Error', text: errorMsg });
                }
                btn.disabled = false;
                btn.textContent = originalText;
                btn.classList.remove('opacity-75', 'cursor-not-allowed');
            })
            .catch(() => {
                Swal.fire({ icon: 'error', title: 'Error', text: 'Failed to save staff. Please try again.' });
                btn.disabled = false;
                btn.textContent = originalText;
                btn.classList.remove('opacity-75', 'cursor-not-allowed');
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
            confirmButtonColor: '#0f172a',
            cancelButtonColor: '#6b7280',
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
            confirmButtonColor: '#0f172a',
            cancelButtonColor: '#6b7280',
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
            confirmButtonColor: '#0f172a',
            cancelButtonColor: '#6b7280',
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
<div id="userSlideOver" class="fixed inset-0 z-50 hidden" aria-hidden="true">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm transition-opacity opacity-0" id="userBackdrop" onclick="closeUserSlideOver()"></div>
    <div class="absolute inset-y-0 right-0 w-full max-w-lg transform translate-x-full transition-transform duration-300 ease-out" id="userPanel">
        <div class="h-full bg-white shadow-2xl flex flex-col">
            <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between bg-gray-50">
                <div>
                    <h3 class="text-sm font-bold text-gray-900" id="userSlideTitle">Staff</h3>
                    <p class="text-xs text-gray-500" id="userSlideSubtitle">Manage staff details</p>
                </div>
                <button onclick="closeUserSlideOver()" class="text-gray-400 hover:text-gray-600 p-1 rounded hover:bg-gray-100 transition-colors">
                    <i class="hgi-stroke hgi-cancel-01 text-xl"></i>
                </button>
            </div>
            <div class="flex-1 overflow-y-auto p-6" id="userSlideContent"></div>
        </div>
    </div>
</div>

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

    function ucfirst(str) {
        return str.charAt(0).toUpperCase() + str.slice(1);
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
            const btn = document.getElementById('user_submit_btn');
            const originalText = btn.textContent;
            btn.disabled = true;
            btn.textContent = 'Saving...';
            btn.classList.add('opacity-75', 'cursor-not-allowed');
            const formData = new FormData(form);
            fetch(action, {
                method: 'POST',
                body: formData,
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
            })
            .then(r => r.json().then(data => ({ ok: r.ok, data })))
            .then(({ ok, data }) => {
                if (ok) {
                    Swal.fire({ icon: 'success', title: 'Success', text: data.message || successMessage, timer: 1500, showConfirmButton: false });
                    closeUserSlideOver();
                    setTimeout(() => location.reload(), 1000);
                } else {
                    let errorMsg = data.message || 'Failed to save staff.';
                    if (data.errors) {
                        errorMsg = Object.values(data.errors).flat().join('\n');
                    }
                    Swal.fire({ icon: 'error', title: 'Validation Error', text: errorMsg });
                }
                btn.disabled = false;
                btn.textContent = originalText;
                btn.classList.remove('opacity-75', 'cursor-not-allowed');
            })
            .catch(() => {
                Swal.fire({ icon: 'error', title: 'Error', text: 'Failed to save staff. Please try again.' });
                btn.disabled = false;
                btn.textContent = originalText;
                btn.classList.remove('opacity-75', 'cursor-not-allowed');
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
            confirmButtonColor: '#0f172a',
            cancelButtonColor: '#6b7280',
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
            confirmButtonColor: '#0f172a',
            cancelButtonColor: '#6b7280',
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
            confirmButtonColor: '#0f172a',
            cancelButtonColor: '#6b7280',
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

    function ucfirst(str) {
        return str.charAt(0).toUpperCase() + str.slice(1);
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
            const btn = document.getElementById('user_submit_btn');
            const originalText = btn.textContent;
            btn.disabled = true;
            btn.textContent = 'Saving...';
            btn.classList.add('opacity-75', 'cursor-not-allowed');
            const formData = new FormData(form);
            fetch(action, {
                method: 'POST',
                body: formData,
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
            })
            .then(r => r.json().then(data => ({ ok: r.ok, data })))
            .then(({ ok, data }) => {
                if (ok) {
                    Swal.fire({ icon: 'success', title: 'Success', text: data.message || successMessage, timer: 1500, showConfirmButton: false });
                    closeUserSlideOver();
                    setTimeout(() => location.reload(), 1000);
                } else {
                    let errorMsg = data.message || 'Failed to save staff.';
                    if (data.errors) {
                        errorMsg = Object.values(data.errors).flat().join('\n');
                    }
                    Swal.fire({ icon: 'error', title: 'Validation Error', text: errorMsg });
                }
                btn.disabled = false;
                btn.textContent = originalText;
                btn.classList.remove('opacity-75', 'cursor-not-allowed');
            })
            .catch(() => {
                Swal.fire({ icon: 'error', title: 'Error', text: 'Failed to save staff. Please try again.' });
                btn.disabled = false;
                btn.textContent = originalText;
                btn.classList.remove('opacity-75', 'cursor-not-allowed');
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
            confirmButtonColor: '#0f172a',
            cancelButtonColor: '#6b7280',
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
            confirmButtonColor: '#0f172a',
            cancelButtonColor: '#6b7280',
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
            confirmButtonColor: '#0f172a',
            cancelButtonColor: '#6b7280',
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
