@extends('layouts.dashboard')

@section('title', 'Lab Test Types - ' . config('app.name', 'Laravel'))
@section('page_title', 'Lab Test Types')

@section('content')
<div class="space-y-5">
    <script>
    const labTestSlide = document.getElementById('labTestSlideOver');
    const labTestBackdrop = document.getElementById('labTestBackdrop');
    const labTestPanel = document.getElementById('labTestPanel');
    const labTestSlideContent = document.getElementById('labTestSlideContent');

    function openLabTestSlideOver(html) {
        labTestSlide.classList.remove('hidden');
        labTestSlideContent.innerHTML = html;
        setTimeout(() => {
            labTestBackdrop.classList.remove('opacity-0');
            labTestPanel.classList.remove('translate-x-full');
        }, 10);
        document.body.style.overflow = 'hidden';
    }

    function closeLabTestSlideOver() {
        labTestBackdrop.classList.add('opacity-0');
        labTestPanel.classList.add('translate-x-full');
        setTimeout(() => {
            labTestSlide.classList.add('hidden');
            labTestSlideContent.innerHTML = '';
            document.body.style.overflow = '';
        }, 300);
    }

    function attachLabTestForm(action, successMessage) {
        const form = document.getElementById('labTestForm');
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
                closeLabTestSlideOver();
                setTimeout(() => location.reload(), 1200);
            })
            .catch(err => {
                Swal.fire({ icon: 'error', title: 'Error', text: 'Failed to save test type.' });
            });
        });
    }

    function resetLabTestForm() {
        document.getElementById('lt_method').value = 'POST';
        document.getElementById('lt_name').value = '';
        document.getElementById('lt_code').value = '';
        document.getElementById('lt_unit').value = '';
        document.getElementById('lt_reference_range').value = '';
        document.getElementById('lt_reference_range_male').value = '';
        document.getElementById('lt_reference_range_female').value = '';
        document.getElementById('lt_reference_range_pregnant').value = '';
        document.getElementById('lt_reference_range_safe').value = '';
        document.getElementById('lt_price').value = '';
        document.getElementById('lt_is_active').checked = true;
        document.getElementById('lt_description').value = '';
    }

    function openAddLabTestPanel() {
        const html = document.getElementById('labTestFormTemplate').innerHTML;
        openLabTestSlideOver(html);
        resetLabTestForm();
        document.getElementById('lt_submit_btn').textContent = 'Save Test Type';
        attachLabTestForm('{{ route("lab-tests.store") }}', 'Test type created successfully.');
    }

    async function openEditLabTestPanel(url) {
        const html = document.getElementById('labTestFormTemplate').innerHTML;
        openLabTestSlideOver(html);
        try {
            const res = await fetch(url, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } });
            const data = await res.json();
            const test = data.test;
            document.getElementById('lt_method').value = 'PUT';
            document.getElementById('lt_name').value = test.name;
            document.getElementById('lt_code').value = test.code || '';
            document.getElementById('lt_unit').value = test.unit || '';
            document.getElementById('lt_reference_range').value = test.reference_range || '';
            document.getElementById('lt_reference_range_male').value = test.reference_range_male || '';
            document.getElementById('lt_reference_range_female').value = test.reference_range_female || '';
            document.getElementById('lt_reference_range_pregnant').value = test.reference_range_pregnant || '';
            document.getElementById('lt_reference_range_safe').value = test.reference_range_safe || '';
            document.getElementById('lt_price').value = test.price;
            document.getElementById('lt_is_active').checked = test.is_active;
            document.getElementById('lt_description').value = test.description || '';
            document.getElementById('lt_submit_btn').textContent = 'Update Test Type';
            attachLabTestForm(url.replace('/edit', ''), 'Test type updated successfully.');
        } catch (err) {
            labTestSlideContent.innerHTML = '<div class="text-center text-red-600 py-8">Failed to load test type details.</div>';
        }
    }

    // --- Import Modal ---
    function openImportModal() {
        document.getElementById('importModal').classList.remove('hidden');
    }

    function closeImportModal() {
        document.getElementById('importModal').classList.add('hidden');
    }

    function handleFileSelect(input) {
        const file = input.files[0];
        if (!file) return;
        document.getElementById('fileName').textContent = file.name;
        document.getElementById('fileSize').textContent = (file.size / 1024).toFixed(1) + ' KB';
        document.getElementById('dropZoneContent').classList.add('hidden');
        document.getElementById('filePreview').classList.remove('hidden');
        document.getElementById('importBtn').disabled = false;
    }

    function submitImport() {
        const fileInput = document.getElementById('importFile');
        const file = fileInput.files[0];
        if (!file) return;

        const formData = new FormData();
        formData.append('file', file);

        const btn = document.getElementById('importBtn');
        btn.disabled = true;
        btn.innerHTML = '<svg class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke-width="3" class="opacity-25"/><path d="M4 12a8 8 0 018-8" stroke-width="3" class="opacity-75"/></svg> Importing...';

        fetch('{{ route("lab-tests.import") }}', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
            body: formData,
        })
        .then(r => r.json())
        .then(data => {
            closeImportModal();
            if (data.success) {
                Swal.fire({
                    toast: true, position: 'top-end', icon: 'success',
                    title: data.message, showConfirmButton: false, timer: 4000,
                });
                setTimeout(() => location.reload(), 1500);
            } else {
                Swal.fire({ toast: true, position: 'top-end', icon: 'error', title: data.message || 'Import failed', showConfirmButton: false, timer: 4000 });
            }
        })
        .catch(err => {
            Swal.fire({ toast: true, position: 'top-end', icon: 'error', title: 'Network error: ' + err.message, showConfirmButton: false, timer: 4000 });
        })
        .finally(() => {
            btn.disabled = false;
            btn.innerHTML = '<i class="hgi-stroke hgi-upload-01 text-base"></i> Import Now';
        });
    }

    // Close import modal on backdrop click
    document.getElementById('importModal').addEventListener('click', function(e) {
        if (e.target === this) closeImportModal();
    });
</script>
    <div class="bg-slate-800 rounded-md p-6 text-white flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <p class="text-[10px] uppercase tracking-wider text-slate-400 mb-1">Laboratory</p>
            <h1 class="text-xl font-bold">Lab Test Types</h1>
            <p class="text-xs text-slate-400 mt-1">Manage laboratory tests, prices and reference ranges.</p>
        </div>
        <div class="flex items-center gap-2">
            @if($tests->total() === 0)
            <button type="button" onclick="openImportModal()" class="inline-flex items-center gap-2 bg-white/10 hover:bg-white/20 text-white text-sm font-medium px-4 py-2 rounded transition-colors">
                <i class="hgi-stroke hgi-upload-01 text-base"></i>
                Import Sample Data
            </button>
            @endif
            <button type="button" onclick="openAddLabTestPanel()" class="inline-flex items-center gap-2 bg-white text-slate-800 text-sm font-medium px-4 py-2 rounded hover:bg-slate-100 transition-colors">
                <i class="hgi-stroke hgi-add-01 text-base"></i>
                Add Test Type
            </button>
        </div>
    </div>

    <script>
    const labTestSlide = document.getElementById('labTestSlideOver');
    const labTestBackdrop = document.getElementById('labTestBackdrop');
    const labTestPanel = document.getElementById('labTestPanel');
    const labTestSlideContent = document.getElementById('labTestSlideContent');

    function openLabTestSlideOver(html) {
        labTestSlide.classList.remove('hidden');
        labTestSlideContent.innerHTML = html;
        setTimeout(() => {
            labTestBackdrop.classList.remove('opacity-0');
            labTestPanel.classList.remove('translate-x-full');
        }, 10);
        document.body.style.overflow = 'hidden';
    }

    function closeLabTestSlideOver() {
        labTestBackdrop.classList.add('opacity-0');
        labTestPanel.classList.add('translate-x-full');
        setTimeout(() => {
            labTestSlide.classList.add('hidden');
            labTestSlideContent.innerHTML = '';
            document.body.style.overflow = '';
        }, 300);
    }

    function attachLabTestForm(action, successMessage) {
        const form = document.getElementById('labTestForm');
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
                closeLabTestSlideOver();
                setTimeout(() => location.reload(), 1200);
            })
            .catch(err => {
                Swal.fire({ icon: 'error', title: 'Error', text: 'Failed to save test type.' });
            });
        });
    }

    function resetLabTestForm() {
        document.getElementById('lt_method').value = 'POST';
        document.getElementById('lt_name').value = '';
        document.getElementById('lt_code').value = '';
        document.getElementById('lt_unit').value = '';
        document.getElementById('lt_reference_range').value = '';
        document.getElementById('lt_reference_range_male').value = '';
        document.getElementById('lt_reference_range_female').value = '';
        document.getElementById('lt_reference_range_pregnant').value = '';
        document.getElementById('lt_reference_range_safe').value = '';
        document.getElementById('lt_price').value = '';
        document.getElementById('lt_is_active').checked = true;
        document.getElementById('lt_description').value = '';
    }

    function openAddLabTestPanel() {
        const html = document.getElementById('labTestFormTemplate').innerHTML;
        openLabTestSlideOver(html);
        resetLabTestForm();
        document.getElementById('lt_submit_btn').textContent = 'Save Test Type';
        attachLabTestForm('{{ route("lab-tests.store") }}', 'Test type created successfully.');
    }

    async function openEditLabTestPanel(url) {
        const html = document.getElementById('labTestFormTemplate').innerHTML;
        openLabTestSlideOver(html);
        try {
            const res = await fetch(url, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } });
            const data = await res.json();
            const test = data.test;
            document.getElementById('lt_method').value = 'PUT';
            document.getElementById('lt_name').value = test.name;
            document.getElementById('lt_code').value = test.code || '';
            document.getElementById('lt_unit').value = test.unit || '';
            document.getElementById('lt_reference_range').value = test.reference_range || '';
            document.getElementById('lt_reference_range_male').value = test.reference_range_male || '';
            document.getElementById('lt_reference_range_female').value = test.reference_range_female || '';
            document.getElementById('lt_reference_range_pregnant').value = test.reference_range_pregnant || '';
            document.getElementById('lt_reference_range_safe').value = test.reference_range_safe || '';
            document.getElementById('lt_price').value = test.price;
            document.getElementById('lt_is_active').checked = test.is_active;
            document.getElementById('lt_description').value = test.description || '';
            document.getElementById('lt_submit_btn').textContent = 'Update Test Type';
            attachLabTestForm(url.replace('/edit', ''), 'Test type updated successfully.');
        } catch (err) {
            labTestSlideContent.innerHTML = '<div class="text-center text-red-600 py-8">Failed to load test type details.</div>';
        }
    }

    // --- Import Modal ---
    function openImportModal() {
        document.getElementById('importModal').classList.remove('hidden');
    }

    function closeImportModal() {
        document.getElementById('importModal').classList.add('hidden');
    }

    function handleFileSelect(input) {
        const file = input.files[0];
        if (!file) return;
        document.getElementById('fileName').textContent = file.name;
        document.getElementById('fileSize').textContent = (file.size / 1024).toFixed(1) + ' KB';
        document.getElementById('dropZoneContent').classList.add('hidden');
        document.getElementById('filePreview').classList.remove('hidden');
        document.getElementById('importBtn').disabled = false;
    }

    function submitImport() {
        const fileInput = document.getElementById('importFile');
        const file = fileInput.files[0];
        if (!file) return;

        const formData = new FormData();
        formData.append('file', file);

        const btn = document.getElementById('importBtn');
        btn.disabled = true;
        btn.innerHTML = '<svg class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke-width="3" class="opacity-25"/><path d="M4 12a8 8 0 018-8" stroke-width="3" class="opacity-75"/></svg> Importing...';

        fetch('{{ route("lab-tests.import") }}', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
            body: formData,
        })
        .then(r => r.json())
        .then(data => {
            closeImportModal();
            if (data.success) {
                Swal.fire({
                    toast: true, position: 'top-end', icon: 'success',
                    title: data.message, showConfirmButton: false, timer: 4000,
                });
                setTimeout(() => location.reload(), 1500);
            } else {
                Swal.fire({ toast: true, position: 'top-end', icon: 'error', title: data.message || 'Import failed', showConfirmButton: false, timer: 4000 });
            }
        })
        .catch(err => {
            Swal.fire({ toast: true, position: 'top-end', icon: 'error', title: 'Network error: ' + err.message, showConfirmButton: false, timer: 4000 });
        })
        .finally(() => {
            btn.disabled = false;
            btn.innerHTML = '<i class="hgi-stroke hgi-upload-01 text-base"></i> Import Now';
        });
    }

    // Close import modal on backdrop click
    document.getElementById('importModal').addEventListener('click', function(e) {
        if (e.target === this) closeImportModal();
    });
</script>
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
        <div class="bg-white rounded-md border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-medium text-gray-500 uppercase tracking-wide">Total Types</span>
                <i class="hgi-stroke hgi-test-tube-01 text-base text-gray-400"></i>
            </div>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ $tests->total() }}</p>
        </div>
        <div class="bg-white rounded-md border border-emerald-200 p-4">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-medium text-gray-500 uppercase tracking-wide">Active</span>
                <i class="hgi-stroke hgi-checkmark-circle-02 text-base text-gray-400"></i>
            </div>
            <p class="text-2xl font-bold text-emerald-600 mt-1">{{ $tests->where('is_active', true)->count() }}</p>
        </div>
        <div class="bg-white rounded-md border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-medium text-gray-500 uppercase tracking-wide">Inactive</span>
                <i class="hgi-stroke hgi-cancel-circle text-base text-gray-400"></i>
            </div>
            <p class="text-2xl font-bold text-gray-500 mt-1">{{ $tests->where('is_active', false)->count() }}</p>
        </div>
        <div class="bg-white rounded-md border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-medium text-gray-500 uppercase tracking-wide">Total Price</span>
                <i class="hgi-stroke hgi-money-03 text-base text-gray-400"></i>
            </div>
            <p class="text-lg font-bold text-gray-900 mt-1">TSh {{ number_format($tests->sum('price'), 0) }}</p>
        </div>
    </div>

    <script>
    const labTestSlide = document.getElementById('labTestSlideOver');
    const labTestBackdrop = document.getElementById('labTestBackdrop');
    const labTestPanel = document.getElementById('labTestPanel');
    const labTestSlideContent = document.getElementById('labTestSlideContent');

    function openLabTestSlideOver(html) {
        labTestSlide.classList.remove('hidden');
        labTestSlideContent.innerHTML = html;
        setTimeout(() => {
            labTestBackdrop.classList.remove('opacity-0');
            labTestPanel.classList.remove('translate-x-full');
        }, 10);
        document.body.style.overflow = 'hidden';
    }

    function closeLabTestSlideOver() {
        labTestBackdrop.classList.add('opacity-0');
        labTestPanel.classList.add('translate-x-full');
        setTimeout(() => {
            labTestSlide.classList.add('hidden');
            labTestSlideContent.innerHTML = '';
            document.body.style.overflow = '';
        }, 300);
    }

    function attachLabTestForm(action, successMessage) {
        const form = document.getElementById('labTestForm');
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
                closeLabTestSlideOver();
                setTimeout(() => location.reload(), 1200);
            })
            .catch(err => {
                Swal.fire({ icon: 'error', title: 'Error', text: 'Failed to save test type.' });
            });
        });
    }

    function resetLabTestForm() {
        document.getElementById('lt_method').value = 'POST';
        document.getElementById('lt_name').value = '';
        document.getElementById('lt_code').value = '';
        document.getElementById('lt_unit').value = '';
        document.getElementById('lt_reference_range').value = '';
        document.getElementById('lt_reference_range_male').value = '';
        document.getElementById('lt_reference_range_female').value = '';
        document.getElementById('lt_reference_range_pregnant').value = '';
        document.getElementById('lt_reference_range_safe').value = '';
        document.getElementById('lt_price').value = '';
        document.getElementById('lt_is_active').checked = true;
        document.getElementById('lt_description').value = '';
    }

    function openAddLabTestPanel() {
        const html = document.getElementById('labTestFormTemplate').innerHTML;
        openLabTestSlideOver(html);
        resetLabTestForm();
        document.getElementById('lt_submit_btn').textContent = 'Save Test Type';
        attachLabTestForm('{{ route("lab-tests.store") }}', 'Test type created successfully.');
    }

    async function openEditLabTestPanel(url) {
        const html = document.getElementById('labTestFormTemplate').innerHTML;
        openLabTestSlideOver(html);
        try {
            const res = await fetch(url, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } });
            const data = await res.json();
            const test = data.test;
            document.getElementById('lt_method').value = 'PUT';
            document.getElementById('lt_name').value = test.name;
            document.getElementById('lt_code').value = test.code || '';
            document.getElementById('lt_unit').value = test.unit || '';
            document.getElementById('lt_reference_range').value = test.reference_range || '';
            document.getElementById('lt_reference_range_male').value = test.reference_range_male || '';
            document.getElementById('lt_reference_range_female').value = test.reference_range_female || '';
            document.getElementById('lt_reference_range_pregnant').value = test.reference_range_pregnant || '';
            document.getElementById('lt_reference_range_safe').value = test.reference_range_safe || '';
            document.getElementById('lt_price').value = test.price;
            document.getElementById('lt_is_active').checked = test.is_active;
            document.getElementById('lt_description').value = test.description || '';
            document.getElementById('lt_submit_btn').textContent = 'Update Test Type';
            attachLabTestForm(url.replace('/edit', ''), 'Test type updated successfully.');
        } catch (err) {
            labTestSlideContent.innerHTML = '<div class="text-center text-red-600 py-8">Failed to load test type details.</div>';
        }
    }

    // --- Import Modal ---
    function openImportModal() {
        document.getElementById('importModal').classList.remove('hidden');
    }

    function closeImportModal() {
        document.getElementById('importModal').classList.add('hidden');
    }

    function handleFileSelect(input) {
        const file = input.files[0];
        if (!file) return;
        document.getElementById('fileName').textContent = file.name;
        document.getElementById('fileSize').textContent = (file.size / 1024).toFixed(1) + ' KB';
        document.getElementById('dropZoneContent').classList.add('hidden');
        document.getElementById('filePreview').classList.remove('hidden');
        document.getElementById('importBtn').disabled = false;
    }

    function submitImport() {
        const fileInput = document.getElementById('importFile');
        const file = fileInput.files[0];
        if (!file) return;

        const formData = new FormData();
        formData.append('file', file);

        const btn = document.getElementById('importBtn');
        btn.disabled = true;
        btn.innerHTML = '<svg class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke-width="3" class="opacity-25"/><path d="M4 12a8 8 0 018-8" stroke-width="3" class="opacity-75"/></svg> Importing...';

        fetch('{{ route("lab-tests.import") }}', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
            body: formData,
        })
        .then(r => r.json())
        .then(data => {
            closeImportModal();
            if (data.success) {
                Swal.fire({
                    toast: true, position: 'top-end', icon: 'success',
                    title: data.message, showConfirmButton: false, timer: 4000,
                });
                setTimeout(() => location.reload(), 1500);
            } else {
                Swal.fire({ toast: true, position: 'top-end', icon: 'error', title: data.message || 'Import failed', showConfirmButton: false, timer: 4000 });
            }
        })
        .catch(err => {
            Swal.fire({ toast: true, position: 'top-end', icon: 'error', title: 'Network error: ' + err.message, showConfirmButton: false, timer: 4000 });
        })
        .finally(() => {
            btn.disabled = false;
            btn.innerHTML = '<i class="hgi-stroke hgi-upload-01 text-base"></i> Import Now';
        });
    }

    // Close import modal on backdrop click
    document.getElementById('importModal').addEventListener('click', function(e) {
        if (e.target === this) closeImportModal();
    });
</script>
    <div class="bg-white rounded-md border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 text-[10px] uppercase text-gray-400 border-b border-gray-100">
                        <th class="px-5 py-3 font-medium text-left">Name</th>
                        <th class="px-5 py-3 font-medium text-left">Code</th>
                        <th class="px-5 py-3 font-medium text-left">Unit</th>
                        <th class="px-5 py-3 font-medium text-left">Reference Ranges</th>
                        <th class="px-5 py-3 font-medium text-left">Price</th>
                        <th class="px-5 py-3 font-medium text-left">Status</th>
                        <th class="px-5 py-3 font-medium text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse ($tests as $test)
                        <tr class="hover:bg-gray-50/50">
                            <td class="px-5 py-3 text-xs font-medium text-gray-900">{{ $test->name }}</td>
                            <td class="px-5 py-3 text-xs text-gray-600">{{ $test->code ?? '-' }}</td>
                            <td class="px-5 py-3 text-xs text-gray-600">{{ $test->unit ?? '-' }}</td>
                            <td class="px-5 py-3">
                                <div class="space-y-0.5">
                                    @if($test->reference_range)
                                        <div class="text-xs text-gray-600"><span class="text-gray-400">Gen:</span> {{ $test->reference_range }}</div>
                                    @endif
                                    @if($test->reference_range_male)
                                        <div class="text-xs text-gray-600"><span class="text-gray-400">M:</span> {{ $test->reference_range_male }}</div>
                                    @endif
                                    @if($test->reference_range_female)
                                        <div class="text-xs text-gray-600"><span class="text-gray-400">F:</span> {{ $test->reference_range_female }}</div>
                                    @endif
                                    @if($test->reference_range_pregnant)
                                        <div class="text-xs text-gray-600"><span class="text-gray-400">Preg:</span> {{ $test->reference_range_pregnant }}</div>
                                    @endif
                                    @if($test->reference_range_safe)
                                        <div class="text-xs text-gray-600"><span class="text-emerald-500">Safe:</span> {{ $test->reference_range_safe }}</div>
                                    @endif
                                    @if(!$test->reference_range && !$test->reference_range_male && !$test->reference_range_female && !$test->reference_range_pregnant && !$test->reference_range_safe)
                                        <span class="text-gray-400 text-xs">-</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-5 py-3 text-xs font-semibold text-gray-900">TSh {{ number_format($test->price, 2) }}</td>
                            <td class="px-5 py-3">
                                <span class="inline-flex items-center gap-1.5 text-[10px] font-medium text-gray-600">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $test->is_active ? 'bg-emerald-500' : 'bg-gray-400' }}"></span>
                                    {{ $test->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td class="px-5 py-3">
                                <div class="flex items-center justify-end gap-1">
                                    <button type="button" onclick="openEditLabTestPanel('{{ route('lab-tests.edit', $test) }}')" class="action-icon group/icon relative p-1.5 text-slate-600 hover:bg-slate-100 rounded transition-colors" title="Edit">
                                        <i class="hgi-stroke hgi-pencil-edit-01 text-base"></i>
                                        <span class="absolute -top-8 left-1/2 -translate-x-1/2 px-2 py-1 bg-gray-900 text-white text-[10px] rounded opacity-0 group-hover/icon:opacity-100 transition-opacity whitespace-nowrap pointer-events-none">Edit</span>
                                    </button>
                                    <form method="POST" action="{{ route('lab-tests.destroy', $test) }}" data-ajax data-confirm="Delete this test type?" class="inline">
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
                                <i class="hgi-stroke hgi-test-tube-01 text-4xl text-gray-300"></i>
                                <p class="text-xs">No test types found</p>
                            </div>
                        </td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-5 py-3 border-t border-gray-100">
            {{ $tests->links() }}
        </div>
    </div>
</div>

<script>
    const labTestSlide = document.getElementById('labTestSlideOver');
    const labTestBackdrop = document.getElementById('labTestBackdrop');
    const labTestPanel = document.getElementById('labTestPanel');
    const labTestSlideContent = document.getElementById('labTestSlideContent');

    function openLabTestSlideOver(html) {
        labTestSlide.classList.remove('hidden');
        labTestSlideContent.innerHTML = html;
        setTimeout(() => {
            labTestBackdrop.classList.remove('opacity-0');
            labTestPanel.classList.remove('translate-x-full');
        }, 10);
        document.body.style.overflow = 'hidden';
    }

    function closeLabTestSlideOver() {
        labTestBackdrop.classList.add('opacity-0');
        labTestPanel.classList.add('translate-x-full');
        setTimeout(() => {
            labTestSlide.classList.add('hidden');
            labTestSlideContent.innerHTML = '';
            document.body.style.overflow = '';
        }, 300);
    }

    function attachLabTestForm(action, successMessage) {
        const form = document.getElementById('labTestForm');
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
                closeLabTestSlideOver();
                setTimeout(() => location.reload(), 1200);
            })
            .catch(err => {
                Swal.fire({ icon: 'error', title: 'Error', text: 'Failed to save test type.' });
            });
        });
    }

    function resetLabTestForm() {
        document.getElementById('lt_method').value = 'POST';
        document.getElementById('lt_name').value = '';
        document.getElementById('lt_code').value = '';
        document.getElementById('lt_unit').value = '';
        document.getElementById('lt_reference_range').value = '';
        document.getElementById('lt_reference_range_male').value = '';
        document.getElementById('lt_reference_range_female').value = '';
        document.getElementById('lt_reference_range_pregnant').value = '';
        document.getElementById('lt_reference_range_safe').value = '';
        document.getElementById('lt_price').value = '';
        document.getElementById('lt_is_active').checked = true;
        document.getElementById('lt_description').value = '';
    }

    function openAddLabTestPanel() {
        const html = document.getElementById('labTestFormTemplate').innerHTML;
        openLabTestSlideOver(html);
        resetLabTestForm();
        document.getElementById('lt_submit_btn').textContent = 'Save Test Type';
        attachLabTestForm('{{ route("lab-tests.store") }}', 'Test type created successfully.');
    }

    async function openEditLabTestPanel(url) {
        const html = document.getElementById('labTestFormTemplate').innerHTML;
        openLabTestSlideOver(html);
        try {
            const res = await fetch(url, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } });
            const data = await res.json();
            const test = data.test;
            document.getElementById('lt_method').value = 'PUT';
            document.getElementById('lt_name').value = test.name;
            document.getElementById('lt_code').value = test.code || '';
            document.getElementById('lt_unit').value = test.unit || '';
            document.getElementById('lt_reference_range').value = test.reference_range || '';
            document.getElementById('lt_reference_range_male').value = test.reference_range_male || '';
            document.getElementById('lt_reference_range_female').value = test.reference_range_female || '';
            document.getElementById('lt_reference_range_pregnant').value = test.reference_range_pregnant || '';
            document.getElementById('lt_reference_range_safe').value = test.reference_range_safe || '';
            document.getElementById('lt_price').value = test.price;
            document.getElementById('lt_is_active').checked = test.is_active;
            document.getElementById('lt_description').value = test.description || '';
            document.getElementById('lt_submit_btn').textContent = 'Update Test Type';
            attachLabTestForm(url.replace('/edit', ''), 'Test type updated successfully.');
        } catch (err) {
            labTestSlideContent.innerHTML = '<div class="text-center text-red-600 py-8">Failed to load test type details.</div>';
        }
    }

    // --- Import Modal ---
    function openImportModal() {
        document.getElementById('importModal').classList.remove('hidden');
    }

    function closeImportModal() {
        document.getElementById('importModal').classList.add('hidden');
    }

    function handleFileSelect(input) {
        const file = input.files[0];
        if (!file) return;
        document.getElementById('fileName').textContent = file.name;
        document.getElementById('fileSize').textContent = (file.size / 1024).toFixed(1) + ' KB';
        document.getElementById('dropZoneContent').classList.add('hidden');
        document.getElementById('filePreview').classList.remove('hidden');
        document.getElementById('importBtn').disabled = false;
    }

    function submitImport() {
        const fileInput = document.getElementById('importFile');
        const file = fileInput.files[0];
        if (!file) return;

        const formData = new FormData();
        formData.append('file', file);

        const btn = document.getElementById('importBtn');
        btn.disabled = true;
        btn.innerHTML = '<svg class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke-width="3" class="opacity-25"/><path d="M4 12a8 8 0 018-8" stroke-width="3" class="opacity-75"/></svg> Importing...';

        fetch('{{ route("lab-tests.import") }}', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
            body: formData,
        })
        .then(r => r.json())
        .then(data => {
            closeImportModal();
            if (data.success) {
                Swal.fire({
                    toast: true, position: 'top-end', icon: 'success',
                    title: data.message, showConfirmButton: false, timer: 4000,
                });
                setTimeout(() => location.reload(), 1500);
            } else {
                Swal.fire({ toast: true, position: 'top-end', icon: 'error', title: data.message || 'Import failed', showConfirmButton: false, timer: 4000 });
            }
        })
        .catch(err => {
            Swal.fire({ toast: true, position: 'top-end', icon: 'error', title: 'Network error: ' + err.message, showConfirmButton: false, timer: 4000 });
        })
        .finally(() => {
            btn.disabled = false;
            btn.innerHTML = '<i class="hgi-stroke hgi-upload-01 text-base"></i> Import Now';
        });
    }

    // Close import modal on backdrop click
    document.getElementById('importModal').addEventListener('click', function(e) {
        if (e.target === this) closeImportModal();
    });
</script>
<div id="labTestSlideOver" class="fixed inset-0 z-50 hidden" aria-hidden="true">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm transition-opacity opacity-0" id="labTestBackdrop" onclick="closeLabTestSlideOver()"></div>
    <div class="absolute inset-y-0 right-0 w-full max-w-md transform translate-x-full transition-transform duration-300 ease-out" id="labTestPanel">
        <div class="h-full bg-white shadow-2xl flex flex-col">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between bg-gray-50/50">
                <div>
                    <h3 class="text-lg font-bold text-gray-900" id="labTestSlideTitle">Edit Test Type</h3>
                    <p class="text-xs text-gray-500" id="labTestSlideSubtitle">Update test type details</p>
                </div>
                <button onclick="closeLabTestSlideOver()" class="text-gray-400 hover:text-gray-600 p-1 rounded-lg hover:bg-gray-100 transition-colors">
                    <i class="hgi-stroke hgi-cancel-01 text-2xl"></i>
                </button>
            </div>
            <div class="flex-1 overflow-y-auto p-6" id="labTestSlideContent"></div>
        </div>
    </div>
</div>

<script>
    const labTestSlide = document.getElementById('labTestSlideOver');
    const labTestBackdrop = document.getElementById('labTestBackdrop');
    const labTestPanel = document.getElementById('labTestPanel');
    const labTestSlideContent = document.getElementById('labTestSlideContent');

    function openLabTestSlideOver(html) {
        labTestSlide.classList.remove('hidden');
        labTestSlideContent.innerHTML = html;
        setTimeout(() => {
            labTestBackdrop.classList.remove('opacity-0');
            labTestPanel.classList.remove('translate-x-full');
        }, 10);
        document.body.style.overflow = 'hidden';
    }

    function closeLabTestSlideOver() {
        labTestBackdrop.classList.add('opacity-0');
        labTestPanel.classList.add('translate-x-full');
        setTimeout(() => {
            labTestSlide.classList.add('hidden');
            labTestSlideContent.innerHTML = '';
            document.body.style.overflow = '';
        }, 300);
    }

    function attachLabTestForm(action, successMessage) {
        const form = document.getElementById('labTestForm');
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
                closeLabTestSlideOver();
                setTimeout(() => location.reload(), 1200);
            })
            .catch(err => {
                Swal.fire({ icon: 'error', title: 'Error', text: 'Failed to save test type.' });
            });
        });
    }

    function resetLabTestForm() {
        document.getElementById('lt_method').value = 'POST';
        document.getElementById('lt_name').value = '';
        document.getElementById('lt_code').value = '';
        document.getElementById('lt_unit').value = '';
        document.getElementById('lt_reference_range').value = '';
        document.getElementById('lt_reference_range_male').value = '';
        document.getElementById('lt_reference_range_female').value = '';
        document.getElementById('lt_reference_range_pregnant').value = '';
        document.getElementById('lt_reference_range_safe').value = '';
        document.getElementById('lt_price').value = '';
        document.getElementById('lt_is_active').checked = true;
        document.getElementById('lt_description').value = '';
    }

    function openAddLabTestPanel() {
        const html = document.getElementById('labTestFormTemplate').innerHTML;
        openLabTestSlideOver(html);
        resetLabTestForm();
        document.getElementById('lt_submit_btn').textContent = 'Save Test Type';
        attachLabTestForm('{{ route("lab-tests.store") }}', 'Test type created successfully.');
    }

    async function openEditLabTestPanel(url) {
        const html = document.getElementById('labTestFormTemplate').innerHTML;
        openLabTestSlideOver(html);
        try {
            const res = await fetch(url, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } });
            const data = await res.json();
            const test = data.test;
            document.getElementById('lt_method').value = 'PUT';
            document.getElementById('lt_name').value = test.name;
            document.getElementById('lt_code').value = test.code || '';
            document.getElementById('lt_unit').value = test.unit || '';
            document.getElementById('lt_reference_range').value = test.reference_range || '';
            document.getElementById('lt_reference_range_male').value = test.reference_range_male || '';
            document.getElementById('lt_reference_range_female').value = test.reference_range_female || '';
            document.getElementById('lt_reference_range_pregnant').value = test.reference_range_pregnant || '';
            document.getElementById('lt_reference_range_safe').value = test.reference_range_safe || '';
            document.getElementById('lt_price').value = test.price;
            document.getElementById('lt_is_active').checked = test.is_active;
            document.getElementById('lt_description').value = test.description || '';
            document.getElementById('lt_submit_btn').textContent = 'Update Test Type';
            attachLabTestForm(url.replace('/edit', ''), 'Test type updated successfully.');
        } catch (err) {
            labTestSlideContent.innerHTML = '<div class="text-center text-red-600 py-8">Failed to load test type details.</div>';
        }
    }

    // --- Import Modal ---
    function openImportModal() {
        document.getElementById('importModal').classList.remove('hidden');
    }

    function closeImportModal() {
        document.getElementById('importModal').classList.add('hidden');
    }

    function handleFileSelect(input) {
        const file = input.files[0];
        if (!file) return;
        document.getElementById('fileName').textContent = file.name;
        document.getElementById('fileSize').textContent = (file.size / 1024).toFixed(1) + ' KB';
        document.getElementById('dropZoneContent').classList.add('hidden');
        document.getElementById('filePreview').classList.remove('hidden');
        document.getElementById('importBtn').disabled = false;
    }

    function submitImport() {
        const fileInput = document.getElementById('importFile');
        const file = fileInput.files[0];
        if (!file) return;

        const formData = new FormData();
        formData.append('file', file);

        const btn = document.getElementById('importBtn');
        btn.disabled = true;
        btn.innerHTML = '<svg class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke-width="3" class="opacity-25"/><path d="M4 12a8 8 0 018-8" stroke-width="3" class="opacity-75"/></svg> Importing...';

        fetch('{{ route("lab-tests.import") }}', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
            body: formData,
        })
        .then(r => r.json())
        .then(data => {
            closeImportModal();
            if (data.success) {
                Swal.fire({
                    toast: true, position: 'top-end', icon: 'success',
                    title: data.message, showConfirmButton: false, timer: 4000,
                });
                setTimeout(() => location.reload(), 1500);
            } else {
                Swal.fire({ toast: true, position: 'top-end', icon: 'error', title: data.message || 'Import failed', showConfirmButton: false, timer: 4000 });
            }
        })
        .catch(err => {
            Swal.fire({ toast: true, position: 'top-end', icon: 'error', title: 'Network error: ' + err.message, showConfirmButton: false, timer: 4000 });
        })
        .finally(() => {
            btn.disabled = false;
            btn.innerHTML = '<i class="hgi-stroke hgi-upload-01 text-base"></i> Import Now';
        });
    }

    // Close import modal on backdrop click
    document.getElementById('importModal').addEventListener('click', function(e) {
        if (e.target === this) closeImportModal();
    });
</script>

<script>
    const labTestSlide = document.getElementById('labTestSlideOver');
    const labTestBackdrop = document.getElementById('labTestBackdrop');
    const labTestPanel = document.getElementById('labTestPanel');
    const labTestSlideContent = document.getElementById('labTestSlideContent');

    function openLabTestSlideOver(html) {
        labTestSlide.classList.remove('hidden');
        labTestSlideContent.innerHTML = html;
        setTimeout(() => {
            labTestBackdrop.classList.remove('opacity-0');
            labTestPanel.classList.remove('translate-x-full');
        }, 10);
        document.body.style.overflow = 'hidden';
    }

    function closeLabTestSlideOver() {
        labTestBackdrop.classList.add('opacity-0');
        labTestPanel.classList.add('translate-x-full');
        setTimeout(() => {
            labTestSlide.classList.add('hidden');
            labTestSlideContent.innerHTML = '';
            document.body.style.overflow = '';
        }, 300);
    }

    function attachLabTestForm(action, successMessage) {
        const form = document.getElementById('labTestForm');
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
                closeLabTestSlideOver();
                setTimeout(() => location.reload(), 1200);
            })
            .catch(err => {
                Swal.fire({ icon: 'error', title: 'Error', text: 'Failed to save test type.' });
            });
        });
    }

    function resetLabTestForm() {
        document.getElementById('lt_method').value = 'POST';
        document.getElementById('lt_name').value = '';
        document.getElementById('lt_code').value = '';
        document.getElementById('lt_unit').value = '';
        document.getElementById('lt_reference_range').value = '';
        document.getElementById('lt_reference_range_male').value = '';
        document.getElementById('lt_reference_range_female').value = '';
        document.getElementById('lt_reference_range_pregnant').value = '';
        document.getElementById('lt_reference_range_safe').value = '';
        document.getElementById('lt_price').value = '';
        document.getElementById('lt_is_active').checked = true;
        document.getElementById('lt_description').value = '';
    }

    function openAddLabTestPanel() {
        const html = document.getElementById('labTestFormTemplate').innerHTML;
        openLabTestSlideOver(html);
        resetLabTestForm();
        document.getElementById('lt_submit_btn').textContent = 'Save Test Type';
        attachLabTestForm('{{ route("lab-tests.store") }}', 'Test type created successfully.');
    }

    async function openEditLabTestPanel(url) {
        const html = document.getElementById('labTestFormTemplate').innerHTML;
        openLabTestSlideOver(html);
        try {
            const res = await fetch(url, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } });
            const data = await res.json();
            const test = data.test;
            document.getElementById('lt_method').value = 'PUT';
            document.getElementById('lt_name').value = test.name;
            document.getElementById('lt_code').value = test.code || '';
            document.getElementById('lt_unit').value = test.unit || '';
            document.getElementById('lt_reference_range').value = test.reference_range || '';
            document.getElementById('lt_reference_range_male').value = test.reference_range_male || '';
            document.getElementById('lt_reference_range_female').value = test.reference_range_female || '';
            document.getElementById('lt_reference_range_pregnant').value = test.reference_range_pregnant || '';
            document.getElementById('lt_reference_range_safe').value = test.reference_range_safe || '';
            document.getElementById('lt_price').value = test.price;
            document.getElementById('lt_is_active').checked = test.is_active;
            document.getElementById('lt_description').value = test.description || '';
            document.getElementById('lt_submit_btn').textContent = 'Update Test Type';
            attachLabTestForm(url.replace('/edit', ''), 'Test type updated successfully.');
        } catch (err) {
            labTestSlideContent.innerHTML = '<div class="text-center text-red-600 py-8">Failed to load test type details.</div>';
        }
    }

    // --- Import Modal ---
    function openImportModal() {
        document.getElementById('importModal').classList.remove('hidden');
    }

    function closeImportModal() {
        document.getElementById('importModal').classList.add('hidden');
    }

    function handleFileSelect(input) {
        const file = input.files[0];
        if (!file) return;
        document.getElementById('fileName').textContent = file.name;
        document.getElementById('fileSize').textContent = (file.size / 1024).toFixed(1) + ' KB';
        document.getElementById('dropZoneContent').classList.add('hidden');
        document.getElementById('filePreview').classList.remove('hidden');
        document.getElementById('importBtn').disabled = false;
    }

    function submitImport() {
        const fileInput = document.getElementById('importFile');
        const file = fileInput.files[0];
        if (!file) return;

        const formData = new FormData();
        formData.append('file', file);

        const btn = document.getElementById('importBtn');
        btn.disabled = true;
        btn.innerHTML = '<svg class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke-width="3" class="opacity-25"/><path d="M4 12a8 8 0 018-8" stroke-width="3" class="opacity-75"/></svg> Importing...';

        fetch('{{ route("lab-tests.import") }}', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
            body: formData,
        })
        .then(r => r.json())
        .then(data => {
            closeImportModal();
            if (data.success) {
                Swal.fire({
                    toast: true, position: 'top-end', icon: 'success',
                    title: data.message, showConfirmButton: false, timer: 4000,
                });
                setTimeout(() => location.reload(), 1500);
            } else {
                Swal.fire({ toast: true, position: 'top-end', icon: 'error', title: data.message || 'Import failed', showConfirmButton: false, timer: 4000 });
            }
        })
        .catch(err => {
            Swal.fire({ toast: true, position: 'top-end', icon: 'error', title: 'Network error: ' + err.message, showConfirmButton: false, timer: 4000 });
        })
        .finally(() => {
            btn.disabled = false;
            btn.innerHTML = '<i class="hgi-stroke hgi-upload-01 text-base"></i> Import Now';
        });
    }

    // Close import modal on backdrop click
    document.getElementById('importModal').addEventListener('click', function(e) {
        if (e.target === this) closeImportModal();
    });
</script>
<div id="importModal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-md shadow-xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
            <div>
                <h3 class="text-sm font-bold text-gray-900">Import Lab Test Types</h3>
                <p class="text-xs text-gray-500 mt-0.5">Upload a CSV file or use the sample template to get started quickly.</p>
            </div>
            <button type="button" onclick="closeImportModal()" class="text-gray-400 hover:text-gray-600">
                <i class="hgi-stroke hgi-cancel-01 text-xl"></i>
            </button>
        </div>

        <div class="p-5 space-y-5">
            <script>
    const labTestSlide = document.getElementById('labTestSlideOver');
    const labTestBackdrop = document.getElementById('labTestBackdrop');
    const labTestPanel = document.getElementById('labTestPanel');
    const labTestSlideContent = document.getElementById('labTestSlideContent');

    function openLabTestSlideOver(html) {
        labTestSlide.classList.remove('hidden');
        labTestSlideContent.innerHTML = html;
        setTimeout(() => {
            labTestBackdrop.classList.remove('opacity-0');
            labTestPanel.classList.remove('translate-x-full');
        }, 10);
        document.body.style.overflow = 'hidden';
    }

    function closeLabTestSlideOver() {
        labTestBackdrop.classList.add('opacity-0');
        labTestPanel.classList.add('translate-x-full');
        setTimeout(() => {
            labTestSlide.classList.add('hidden');
            labTestSlideContent.innerHTML = '';
            document.body.style.overflow = '';
        }, 300);
    }

    function attachLabTestForm(action, successMessage) {
        const form = document.getElementById('labTestForm');
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
                closeLabTestSlideOver();
                setTimeout(() => location.reload(), 1200);
            })
            .catch(err => {
                Swal.fire({ icon: 'error', title: 'Error', text: 'Failed to save test type.' });
            });
        });
    }

    function resetLabTestForm() {
        document.getElementById('lt_method').value = 'POST';
        document.getElementById('lt_name').value = '';
        document.getElementById('lt_code').value = '';
        document.getElementById('lt_unit').value = '';
        document.getElementById('lt_reference_range').value = '';
        document.getElementById('lt_reference_range_male').value = '';
        document.getElementById('lt_reference_range_female').value = '';
        document.getElementById('lt_reference_range_pregnant').value = '';
        document.getElementById('lt_reference_range_safe').value = '';
        document.getElementById('lt_price').value = '';
        document.getElementById('lt_is_active').checked = true;
        document.getElementById('lt_description').value = '';
    }

    function openAddLabTestPanel() {
        const html = document.getElementById('labTestFormTemplate').innerHTML;
        openLabTestSlideOver(html);
        resetLabTestForm();
        document.getElementById('lt_submit_btn').textContent = 'Save Test Type';
        attachLabTestForm('{{ route("lab-tests.store") }}', 'Test type created successfully.');
    }

    async function openEditLabTestPanel(url) {
        const html = document.getElementById('labTestFormTemplate').innerHTML;
        openLabTestSlideOver(html);
        try {
            const res = await fetch(url, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } });
            const data = await res.json();
            const test = data.test;
            document.getElementById('lt_method').value = 'PUT';
            document.getElementById('lt_name').value = test.name;
            document.getElementById('lt_code').value = test.code || '';
            document.getElementById('lt_unit').value = test.unit || '';
            document.getElementById('lt_reference_range').value = test.reference_range || '';
            document.getElementById('lt_reference_range_male').value = test.reference_range_male || '';
            document.getElementById('lt_reference_range_female').value = test.reference_range_female || '';
            document.getElementById('lt_reference_range_pregnant').value = test.reference_range_pregnant || '';
            document.getElementById('lt_reference_range_safe').value = test.reference_range_safe || '';
            document.getElementById('lt_price').value = test.price;
            document.getElementById('lt_is_active').checked = test.is_active;
            document.getElementById('lt_description').value = test.description || '';
            document.getElementById('lt_submit_btn').textContent = 'Update Test Type';
            attachLabTestForm(url.replace('/edit', ''), 'Test type updated successfully.');
        } catch (err) {
            labTestSlideContent.innerHTML = '<div class="text-center text-red-600 py-8">Failed to load test type details.</div>';
        }
    }

    // --- Import Modal ---
    function openImportModal() {
        document.getElementById('importModal').classList.remove('hidden');
    }

    function closeImportModal() {
        document.getElementById('importModal').classList.add('hidden');
    }

    function handleFileSelect(input) {
        const file = input.files[0];
        if (!file) return;
        document.getElementById('fileName').textContent = file.name;
        document.getElementById('fileSize').textContent = (file.size / 1024).toFixed(1) + ' KB';
        document.getElementById('dropZoneContent').classList.add('hidden');
        document.getElementById('filePreview').classList.remove('hidden');
        document.getElementById('importBtn').disabled = false;
    }

    function submitImport() {
        const fileInput = document.getElementById('importFile');
        const file = fileInput.files[0];
        if (!file) return;

        const formData = new FormData();
        formData.append('file', file);

        const btn = document.getElementById('importBtn');
        btn.disabled = true;
        btn.innerHTML = '<svg class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke-width="3" class="opacity-25"/><path d="M4 12a8 8 0 018-8" stroke-width="3" class="opacity-75"/></svg> Importing...';

        fetch('{{ route("lab-tests.import") }}', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
            body: formData,
        })
        .then(r => r.json())
        .then(data => {
            closeImportModal();
            if (data.success) {
                Swal.fire({
                    toast: true, position: 'top-end', icon: 'success',
                    title: data.message, showConfirmButton: false, timer: 4000,
                });
                setTimeout(() => location.reload(), 1500);
            } else {
                Swal.fire({ toast: true, position: 'top-end', icon: 'error', title: data.message || 'Import failed', showConfirmButton: false, timer: 4000 });
            }
        })
        .catch(err => {
            Swal.fire({ toast: true, position: 'top-end', icon: 'error', title: 'Network error: ' + err.message, showConfirmButton: false, timer: 4000 });
        })
        .finally(() => {
            btn.disabled = false;
            btn.innerHTML = '<i class="hgi-stroke hgi-upload-01 text-base"></i> Import Now';
        });
    }

    // Close import modal on backdrop click
    document.getElementById('importModal').addEventListener('click', function(e) {
        if (e.target === this) closeImportModal();
    });
</script>
            <div class="bg-gray-50 border border-gray-200 rounded-md p-4 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <i class="hgi-stroke hgi-analytics-01 text-xl text-gray-400"></i>
                    <div>
                        <p class="text-xs font-bold text-gray-900">Download Sample Template</p>
                        <p class="text-[10px] text-gray-500 mt-0.5">CSV file with 30 common lab tests pre-filled</p>
                    </div>
                </div>
                <a href="{{ route('lab-tests.sample') }}" class="bg-slate-800 hover:bg-slate-700 text-white text-xs font-medium px-4 py-2 rounded transition-colors flex items-center gap-1.5">
                    <i class="hgi-stroke hgi-upload-01 text-base"></i>
                    Download
                </a>
            </div>

            <script>
    const labTestSlide = document.getElementById('labTestSlideOver');
    const labTestBackdrop = document.getElementById('labTestBackdrop');
    const labTestPanel = document.getElementById('labTestPanel');
    const labTestSlideContent = document.getElementById('labTestSlideContent');

    function openLabTestSlideOver(html) {
        labTestSlide.classList.remove('hidden');
        labTestSlideContent.innerHTML = html;
        setTimeout(() => {
            labTestBackdrop.classList.remove('opacity-0');
            labTestPanel.classList.remove('translate-x-full');
        }, 10);
        document.body.style.overflow = 'hidden';
    }

    function closeLabTestSlideOver() {
        labTestBackdrop.classList.add('opacity-0');
        labTestPanel.classList.add('translate-x-full');
        setTimeout(() => {
            labTestSlide.classList.add('hidden');
            labTestSlideContent.innerHTML = '';
            document.body.style.overflow = '';
        }, 300);
    }

    function attachLabTestForm(action, successMessage) {
        const form = document.getElementById('labTestForm');
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
                closeLabTestSlideOver();
                setTimeout(() => location.reload(), 1200);
            })
            .catch(err => {
                Swal.fire({ icon: 'error', title: 'Error', text: 'Failed to save test type.' });
            });
        });
    }

    function resetLabTestForm() {
        document.getElementById('lt_method').value = 'POST';
        document.getElementById('lt_name').value = '';
        document.getElementById('lt_code').value = '';
        document.getElementById('lt_unit').value = '';
        document.getElementById('lt_reference_range').value = '';
        document.getElementById('lt_reference_range_male').value = '';
        document.getElementById('lt_reference_range_female').value = '';
        document.getElementById('lt_reference_range_pregnant').value = '';
        document.getElementById('lt_reference_range_safe').value = '';
        document.getElementById('lt_price').value = '';
        document.getElementById('lt_is_active').checked = true;
        document.getElementById('lt_description').value = '';
    }

    function openAddLabTestPanel() {
        const html = document.getElementById('labTestFormTemplate').innerHTML;
        openLabTestSlideOver(html);
        resetLabTestForm();
        document.getElementById('lt_submit_btn').textContent = 'Save Test Type';
        attachLabTestForm('{{ route("lab-tests.store") }}', 'Test type created successfully.');
    }

    async function openEditLabTestPanel(url) {
        const html = document.getElementById('labTestFormTemplate').innerHTML;
        openLabTestSlideOver(html);
        try {
            const res = await fetch(url, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } });
            const data = await res.json();
            const test = data.test;
            document.getElementById('lt_method').value = 'PUT';
            document.getElementById('lt_name').value = test.name;
            document.getElementById('lt_code').value = test.code || '';
            document.getElementById('lt_unit').value = test.unit || '';
            document.getElementById('lt_reference_range').value = test.reference_range || '';
            document.getElementById('lt_reference_range_male').value = test.reference_range_male || '';
            document.getElementById('lt_reference_range_female').value = test.reference_range_female || '';
            document.getElementById('lt_reference_range_pregnant').value = test.reference_range_pregnant || '';
            document.getElementById('lt_reference_range_safe').value = test.reference_range_safe || '';
            document.getElementById('lt_price').value = test.price;
            document.getElementById('lt_is_active').checked = test.is_active;
            document.getElementById('lt_description').value = test.description || '';
            document.getElementById('lt_submit_btn').textContent = 'Update Test Type';
            attachLabTestForm(url.replace('/edit', ''), 'Test type updated successfully.');
        } catch (err) {
            labTestSlideContent.innerHTML = '<div class="text-center text-red-600 py-8">Failed to load test type details.</div>';
        }
    }

    // --- Import Modal ---
    function openImportModal() {
        document.getElementById('importModal').classList.remove('hidden');
    }

    function closeImportModal() {
        document.getElementById('importModal').classList.add('hidden');
    }

    function handleFileSelect(input) {
        const file = input.files[0];
        if (!file) return;
        document.getElementById('fileName').textContent = file.name;
        document.getElementById('fileSize').textContent = (file.size / 1024).toFixed(1) + ' KB';
        document.getElementById('dropZoneContent').classList.add('hidden');
        document.getElementById('filePreview').classList.remove('hidden');
        document.getElementById('importBtn').disabled = false;
    }

    function submitImport() {
        const fileInput = document.getElementById('importFile');
        const file = fileInput.files[0];
        if (!file) return;

        const formData = new FormData();
        formData.append('file', file);

        const btn = document.getElementById('importBtn');
        btn.disabled = true;
        btn.innerHTML = '<svg class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke-width="3" class="opacity-25"/><path d="M4 12a8 8 0 018-8" stroke-width="3" class="opacity-75"/></svg> Importing...';

        fetch('{{ route("lab-tests.import") }}', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
            body: formData,
        })
        .then(r => r.json())
        .then(data => {
            closeImportModal();
            if (data.success) {
                Swal.fire({
                    toast: true, position: 'top-end', icon: 'success',
                    title: data.message, showConfirmButton: false, timer: 4000,
                });
                setTimeout(() => location.reload(), 1500);
            } else {
                Swal.fire({ toast: true, position: 'top-end', icon: 'error', title: data.message || 'Import failed', showConfirmButton: false, timer: 4000 });
            }
        })
        .catch(err => {
            Swal.fire({ toast: true, position: 'top-end', icon: 'error', title: 'Network error: ' + err.message, showConfirmButton: false, timer: 4000 });
        })
        .finally(() => {
            btn.disabled = false;
            btn.innerHTML = '<i class="hgi-stroke hgi-upload-01 text-base"></i> Import Now';
        });
    }

    // Close import modal on backdrop click
    document.getElementById('importModal').addEventListener('click', function(e) {
        if (e.target === this) closeImportModal();
    });
</script>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-2">Upload CSV File</label>
                <div id="dropZone" class="border-2 border-dashed border-gray-200 rounded-md p-8 text-center hover:border-slate-400 transition-colors cursor-pointer" onclick="document.getElementById('importFile').click()">
                    <x-ui.input type="file" id="importFile" accept=".csv,.txt" class="hidden" onchange="handleFileSelect(this)" />
                    <div id="dropZoneContent">
                        <i class="hgi-stroke hgi-cloud-upload text-4xl mx-auto text-gray-300 mb-3"></i>
                        <p class="text-sm text-gray-500 font-medium">Click to select or drag CSV file here</p>
                        <p class="text-xs text-gray-400 mt-1">Max 2MB. Supported: .csv, .txt</p>
                    </div>
                    <div id="filePreview" class="hidden">
                        <div class="flex items-center justify-center gap-2">
                            <i class="hgi-stroke hgi-file-02 text-3xl text-slate-600"></i>
                            <div class="text-left">
                                <p class="text-sm font-bold text-gray-900" id="fileName"></p>
                                <p class="text-xs text-gray-500" id="fileSize"></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <script>
    const labTestSlide = document.getElementById('labTestSlideOver');
    const labTestBackdrop = document.getElementById('labTestBackdrop');
    const labTestPanel = document.getElementById('labTestPanel');
    const labTestSlideContent = document.getElementById('labTestSlideContent');

    function openLabTestSlideOver(html) {
        labTestSlide.classList.remove('hidden');
        labTestSlideContent.innerHTML = html;
        setTimeout(() => {
            labTestBackdrop.classList.remove('opacity-0');
            labTestPanel.classList.remove('translate-x-full');
        }, 10);
        document.body.style.overflow = 'hidden';
    }

    function closeLabTestSlideOver() {
        labTestBackdrop.classList.add('opacity-0');
        labTestPanel.classList.add('translate-x-full');
        setTimeout(() => {
            labTestSlide.classList.add('hidden');
            labTestSlideContent.innerHTML = '';
            document.body.style.overflow = '';
        }, 300);
    }

    function attachLabTestForm(action, successMessage) {
        const form = document.getElementById('labTestForm');
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
                closeLabTestSlideOver();
                setTimeout(() => location.reload(), 1200);
            })
            .catch(err => {
                Swal.fire({ icon: 'error', title: 'Error', text: 'Failed to save test type.' });
            });
        });
    }

    function resetLabTestForm() {
        document.getElementById('lt_method').value = 'POST';
        document.getElementById('lt_name').value = '';
        document.getElementById('lt_code').value = '';
        document.getElementById('lt_unit').value = '';
        document.getElementById('lt_reference_range').value = '';
        document.getElementById('lt_reference_range_male').value = '';
        document.getElementById('lt_reference_range_female').value = '';
        document.getElementById('lt_reference_range_pregnant').value = '';
        document.getElementById('lt_reference_range_safe').value = '';
        document.getElementById('lt_price').value = '';
        document.getElementById('lt_is_active').checked = true;
        document.getElementById('lt_description').value = '';
    }

    function openAddLabTestPanel() {
        const html = document.getElementById('labTestFormTemplate').innerHTML;
        openLabTestSlideOver(html);
        resetLabTestForm();
        document.getElementById('lt_submit_btn').textContent = 'Save Test Type';
        attachLabTestForm('{{ route("lab-tests.store") }}', 'Test type created successfully.');
    }

    async function openEditLabTestPanel(url) {
        const html = document.getElementById('labTestFormTemplate').innerHTML;
        openLabTestSlideOver(html);
        try {
            const res = await fetch(url, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } });
            const data = await res.json();
            const test = data.test;
            document.getElementById('lt_method').value = 'PUT';
            document.getElementById('lt_name').value = test.name;
            document.getElementById('lt_code').value = test.code || '';
            document.getElementById('lt_unit').value = test.unit || '';
            document.getElementById('lt_reference_range').value = test.reference_range || '';
            document.getElementById('lt_reference_range_male').value = test.reference_range_male || '';
            document.getElementById('lt_reference_range_female').value = test.reference_range_female || '';
            document.getElementById('lt_reference_range_pregnant').value = test.reference_range_pregnant || '';
            document.getElementById('lt_reference_range_safe').value = test.reference_range_safe || '';
            document.getElementById('lt_price').value = test.price;
            document.getElementById('lt_is_active').checked = test.is_active;
            document.getElementById('lt_description').value = test.description || '';
            document.getElementById('lt_submit_btn').textContent = 'Update Test Type';
            attachLabTestForm(url.replace('/edit', ''), 'Test type updated successfully.');
        } catch (err) {
            labTestSlideContent.innerHTML = '<div class="text-center text-red-600 py-8">Failed to load test type details.</div>';
        }
    }

    // --- Import Modal ---
    function openImportModal() {
        document.getElementById('importModal').classList.remove('hidden');
    }

    function closeImportModal() {
        document.getElementById('importModal').classList.add('hidden');
    }

    function handleFileSelect(input) {
        const file = input.files[0];
        if (!file) return;
        document.getElementById('fileName').textContent = file.name;
        document.getElementById('fileSize').textContent = (file.size / 1024).toFixed(1) + ' KB';
        document.getElementById('dropZoneContent').classList.add('hidden');
        document.getElementById('filePreview').classList.remove('hidden');
        document.getElementById('importBtn').disabled = false;
    }

    function submitImport() {
        const fileInput = document.getElementById('importFile');
        const file = fileInput.files[0];
        if (!file) return;

        const formData = new FormData();
        formData.append('file', file);

        const btn = document.getElementById('importBtn');
        btn.disabled = true;
        btn.innerHTML = '<svg class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke-width="3" class="opacity-25"/><path d="M4 12a8 8 0 018-8" stroke-width="3" class="opacity-75"/></svg> Importing...';

        fetch('{{ route("lab-tests.import") }}', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
            body: formData,
        })
        .then(r => r.json())
        .then(data => {
            closeImportModal();
            if (data.success) {
                Swal.fire({
                    toast: true, position: 'top-end', icon: 'success',
                    title: data.message, showConfirmButton: false, timer: 4000,
                });
                setTimeout(() => location.reload(), 1500);
            } else {
                Swal.fire({ toast: true, position: 'top-end', icon: 'error', title: data.message || 'Import failed', showConfirmButton: false, timer: 4000 });
            }
        })
        .catch(err => {
            Swal.fire({ toast: true, position: 'top-end', icon: 'error', title: 'Network error: ' + err.message, showConfirmButton: false, timer: 4000 });
        })
        .finally(() => {
            btn.disabled = false;
            btn.innerHTML = '<i class="hgi-stroke hgi-upload-01 text-base"></i> Import Now';
        });
    }

    // Close import modal on backdrop click
    document.getElementById('importModal').addEventListener('click', function(e) {
        if (e.target === this) closeImportModal();
    });
</script>
            <div class="bg-gray-50 rounded-md p-4">
                <p class="text-xs font-bold text-gray-700 mb-2 flex items-center gap-1.5">
                    <i class="hgi-stroke hgi-information-circle text-base text-gray-500"></i>
                    CSV Format
                </p>
                <div class="overflow-x-auto">
                    <table class="w-full text-xs">
                        <thead>
                            <tr class="text-left text-gray-500 border-b border-gray-200">
                                <th class="py-1.5 pr-3">name</th>
                                <th class="py-1.5 pr-3">code</th>
                                <th class="py-1.5 pr-3">unit</th>
                                <th class="py-1.5 pr-3">reference_range</th>
                                <th class="py-1.5 pr-3">price</th>
                                <th class="py-1.5 pr-3">is_active</th>
                                <th class="py-1.5">description</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-600">
                            <tr>
                                <td class="py-1.5 pr-3">Complete Blood Count</td>
                                <td class="py-1.5 pr-3">CBC</td>
                                <td class="py-1.5 pr-3">cells/uL</td>
                                <td class="py-1.5 pr-3">4.0-11.0</td>
                                <td class="py-1.5 pr-3">15000</td>
                                <td class="py-1.5 pr-3">1</td>
                                <td class="py-1.5">Full blood count</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="flex justify-end gap-3 pt-2 border-t border-gray-100">
                <button type="button" onclick="closeImportModal()" class="px-4 py-2 border border-gray-200 hover:bg-gray-100 text-gray-700 text-sm font-medium rounded transition-colors">Cancel</button>
                <button type="button" id="importBtn" onclick="submitImport()" disabled class="px-5 py-2 bg-slate-800 hover:bg-slate-700 text-white text-sm font-medium rounded transition-colors flex items-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed">
                    <i class="hgi-stroke hgi-upload-01 text-base"></i>
                    Import Now
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    const labTestSlide = document.getElementById('labTestSlideOver');
    const labTestBackdrop = document.getElementById('labTestBackdrop');
    const labTestPanel = document.getElementById('labTestPanel');
    const labTestSlideContent = document.getElementById('labTestSlideContent');

    function openLabTestSlideOver(html) {
        labTestSlide.classList.remove('hidden');
        labTestSlideContent.innerHTML = html;
        setTimeout(() => {
            labTestBackdrop.classList.remove('opacity-0');
            labTestPanel.classList.remove('translate-x-full');
        }, 10);
        document.body.style.overflow = 'hidden';
    }

    function closeLabTestSlideOver() {
        labTestBackdrop.classList.add('opacity-0');
        labTestPanel.classList.add('translate-x-full');
        setTimeout(() => {
            labTestSlide.classList.add('hidden');
            labTestSlideContent.innerHTML = '';
            document.body.style.overflow = '';
        }, 300);
    }

    function attachLabTestForm(action, successMessage) {
        const form = document.getElementById('labTestForm');
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
                closeLabTestSlideOver();
                setTimeout(() => location.reload(), 1200);
            })
            .catch(err => {
                Swal.fire({ icon: 'error', title: 'Error', text: 'Failed to save test type.' });
            });
        });
    }

    function resetLabTestForm() {
        document.getElementById('lt_method').value = 'POST';
        document.getElementById('lt_name').value = '';
        document.getElementById('lt_code').value = '';
        document.getElementById('lt_unit').value = '';
        document.getElementById('lt_reference_range').value = '';
        document.getElementById('lt_reference_range_male').value = '';
        document.getElementById('lt_reference_range_female').value = '';
        document.getElementById('lt_reference_range_pregnant').value = '';
        document.getElementById('lt_reference_range_safe').value = '';
        document.getElementById('lt_price').value = '';
        document.getElementById('lt_is_active').checked = true;
        document.getElementById('lt_description').value = '';
    }

    function openAddLabTestPanel() {
        const html = document.getElementById('labTestFormTemplate').innerHTML;
        openLabTestSlideOver(html);
        resetLabTestForm();
        document.getElementById('lt_submit_btn').textContent = 'Save Test Type';
        attachLabTestForm('{{ route("lab-tests.store") }}', 'Test type created successfully.');
    }

    async function openEditLabTestPanel(url) {
        const html = document.getElementById('labTestFormTemplate').innerHTML;
        openLabTestSlideOver(html);
        try {
            const res = await fetch(url, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } });
            const data = await res.json();
            const test = data.test;
            document.getElementById('lt_method').value = 'PUT';
            document.getElementById('lt_name').value = test.name;
            document.getElementById('lt_code').value = test.code || '';
            document.getElementById('lt_unit').value = test.unit || '';
            document.getElementById('lt_reference_range').value = test.reference_range || '';
            document.getElementById('lt_reference_range_male').value = test.reference_range_male || '';
            document.getElementById('lt_reference_range_female').value = test.reference_range_female || '';
            document.getElementById('lt_reference_range_pregnant').value = test.reference_range_pregnant || '';
            document.getElementById('lt_reference_range_safe').value = test.reference_range_safe || '';
            document.getElementById('lt_price').value = test.price;
            document.getElementById('lt_is_active').checked = test.is_active;
            document.getElementById('lt_description').value = test.description || '';
            document.getElementById('lt_submit_btn').textContent = 'Update Test Type';
            attachLabTestForm(url.replace('/edit', ''), 'Test type updated successfully.');
        } catch (err) {
            labTestSlideContent.innerHTML = '<div class="text-center text-red-600 py-8">Failed to load test type details.</div>';
        }
    }

    // --- Import Modal ---
    function openImportModal() {
        document.getElementById('importModal').classList.remove('hidden');
    }

    function closeImportModal() {
        document.getElementById('importModal').classList.add('hidden');
    }

    function handleFileSelect(input) {
        const file = input.files[0];
        if (!file) return;
        document.getElementById('fileName').textContent = file.name;
        document.getElementById('fileSize').textContent = (file.size / 1024).toFixed(1) + ' KB';
        document.getElementById('dropZoneContent').classList.add('hidden');
        document.getElementById('filePreview').classList.remove('hidden');
        document.getElementById('importBtn').disabled = false;
    }

    function submitImport() {
        const fileInput = document.getElementById('importFile');
        const file = fileInput.files[0];
        if (!file) return;

        const formData = new FormData();
        formData.append('file', file);

        const btn = document.getElementById('importBtn');
        btn.disabled = true;
        btn.innerHTML = '<svg class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke-width="3" class="opacity-25"/><path d="M4 12a8 8 0 018-8" stroke-width="3" class="opacity-75"/></svg> Importing...';

        fetch('{{ route("lab-tests.import") }}', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
            body: formData,
        })
        .then(r => r.json())
        .then(data => {
            closeImportModal();
            if (data.success) {
                Swal.fire({
                    toast: true, position: 'top-end', icon: 'success',
                    title: data.message, showConfirmButton: false, timer: 4000,
                });
                setTimeout(() => location.reload(), 1500);
            } else {
                Swal.fire({ toast: true, position: 'top-end', icon: 'error', title: data.message || 'Import failed', showConfirmButton: false, timer: 4000 });
            }
        })
        .catch(err => {
            Swal.fire({ toast: true, position: 'top-end', icon: 'error', title: 'Network error: ' + err.message, showConfirmButton: false, timer: 4000 });
        })
        .finally(() => {
            btn.disabled = false;
            btn.innerHTML = '<i class="hgi-stroke hgi-upload-01 text-base"></i> Import Now';
        });
    }

    // Close import modal on backdrop click
    document.getElementById('importModal').addEventListener('click', function(e) {
        if (e.target === this) closeImportModal();
    });
</script>
@endpush
@endsection
