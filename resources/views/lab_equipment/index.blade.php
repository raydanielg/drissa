@extends('layouts.dashboard')

@section('title', 'Lab Equipment - ' . config('app.name', 'Laravel'))
@section('page_title', 'Lab Equipment')

@section('content')
<div class="space-y-5">
    <script>
    const leSlide = document.getElementById('labEquipmentSlideOver');
    const leBackdrop = document.getElementById('labEquipBackdrop');
    const lePanel = document.getElementById('labEquipPanel');
    const leContent = document.getElementById('labEquipSlideContent');
    const leTitle = document.getElementById('labEquipSlideTitle');
    const leSubtitle = document.getElementById('labEquipSlideSubtitle');

    function openLabEquipmentSlideOver(title, subtitle, html) {
        leSlide.classList.remove('hidden');
        leTitle.textContent = title;
        leSubtitle.textContent = subtitle;
        leContent.innerHTML = html;
        setTimeout(() => {
            leBackdrop.classList.remove('opacity-0');
            lePanel.classList.remove('translate-x-full');
        }, 10);
        document.body.style.overflow = 'hidden';
    }

    function closeLabEquipmentSlideOver() {
        leBackdrop.classList.add('opacity-0');
        lePanel.classList.add('translate-x-full');
        setTimeout(() => {
            leSlide.classList.add('hidden');
            leContent.innerHTML = '';
            document.body.style.overflow = '';
        }, 300);
    }

    function attachLabEquipmentForm(action, successMessage) {
        const form = document.getElementById('labEquipmentForm');
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
                closeLabEquipmentSlideOver();
                setTimeout(() => location.reload(), 1200);
            })
            .catch(err => {
                Swal.fire({ icon: 'error', title: 'Error', text: 'Failed to save equipment.' });
            });
        });
    }

    function openAddLabEquipmentPanel() {
        const html = document.getElementById('labEquipmentFormTemplate').innerHTML;
        openLabEquipmentSlideOver('Add Equipment', 'Enter new lab equipment details', html);
        document.getElementById('lab_equip_method').value = 'POST';
        document.getElementById('le_submit_btn').textContent = 'Save Equipment';
        attachLabEquipmentForm('{{ route("lab-equipment.store") }}', 'Equipment added successfully.');
    }

    function updateLabEquipmentStatus(select, url) {
        const status = select.value;
        const originalText = select.options[select.selectedIndex].text;
        const formData = new FormData();
        formData.append('status', status);
        formData.append('_token', '{{ csrf_token() }}');
        formData.append('_method', 'PATCH');

        fetch(url, {
            method: 'POST',
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
        })
        .then(r => r.json().catch(() => ({})))
        .then(data => {
            Swal.fire({ icon: 'success', title: 'Status Updated', text: data.message || 'Equipment status updated.', timer: 1500, showConfirmButton: false });
            setTimeout(() => location.reload(), 1000);
        })
        .catch(err => {
            Swal.fire({ icon: 'error', title: 'Error', text: 'Failed to update status.' });
        });
    }

    async function openEditLabEquipmentPanel(url) {
        const html = document.getElementById('labEquipmentFormTemplate').innerHTML;
        openLabEquipmentSlideOver('Edit Equipment', 'Update equipment details', html);
        document.getElementById('lab_equip_method').value = 'PUT';
        document.getElementById('le_submit_btn').textContent = 'Update Equipment';
        try {
            const res = await fetch(url, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } });
            const data = await res.json();
            const item = data.item;
            const setValue = (id, val) => { const el = document.getElementById(id); if (el) el.value = val || ''; };
            setValue('le_name', item.name);
            setValue('le_model', item.model);
            setValue('le_serial_number', item.serial_number);
            setValue('le_manufacturer', item.manufacturer);
            setValue('le_purchase_date', item.purchase_date ? item.purchase_date.split(' ')[0] : '');
            setValue('le_last_service_date', item.last_service_date ? item.last_service_date.split(' ')[0] : '');
            setValue('le_next_service_date', item.next_service_date ? item.next_service_date.split(' ')[0] : '');
            setValue('le_status', item.status);
            setValue('le_notes', item.notes);
            attachLabEquipmentForm(url.replace('/edit', ''), 'Equipment updated successfully.');
        } catch (err) {
            leContent.innerHTML = '<div class="text-center text-red-600 py-8">Failed to load equipment details.</div>';
        }
    }
</script>
    <div class="bg-slate-800 rounded-md p-6 text-white flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <p class="text-[10px] uppercase tracking-wider text-slate-400 mb-1">Laboratory</p>
            <h1 class="text-xl font-bold">Lab Equipment</h1>
            <p class="text-xs text-slate-400 mt-1">Track lab devices, service dates and operational status.</p>
        </div>
        <button type="button" onclick="openAddLabEquipmentPanel()" class="inline-flex items-center gap-2 bg-white text-slate-800 text-sm font-medium px-4 py-2 rounded hover:bg-slate-100 transition-colors self-start">
            <i class="hgi-stroke hgi-add-01 text-base"></i>
            Add Equipment
        </button>
    </div>

    <script>
    const leSlide = document.getElementById('labEquipmentSlideOver');
    const leBackdrop = document.getElementById('labEquipBackdrop');
    const lePanel = document.getElementById('labEquipPanel');
    const leContent = document.getElementById('labEquipSlideContent');
    const leTitle = document.getElementById('labEquipSlideTitle');
    const leSubtitle = document.getElementById('labEquipSlideSubtitle');

    function openLabEquipmentSlideOver(title, subtitle, html) {
        leSlide.classList.remove('hidden');
        leTitle.textContent = title;
        leSubtitle.textContent = subtitle;
        leContent.innerHTML = html;
        setTimeout(() => {
            leBackdrop.classList.remove('opacity-0');
            lePanel.classList.remove('translate-x-full');
        }, 10);
        document.body.style.overflow = 'hidden';
    }

    function closeLabEquipmentSlideOver() {
        leBackdrop.classList.add('opacity-0');
        lePanel.classList.add('translate-x-full');
        setTimeout(() => {
            leSlide.classList.add('hidden');
            leContent.innerHTML = '';
            document.body.style.overflow = '';
        }, 300);
    }

    function attachLabEquipmentForm(action, successMessage) {
        const form = document.getElementById('labEquipmentForm');
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
                closeLabEquipmentSlideOver();
                setTimeout(() => location.reload(), 1200);
            })
            .catch(err => {
                Swal.fire({ icon: 'error', title: 'Error', text: 'Failed to save equipment.' });
            });
        });
    }

    function openAddLabEquipmentPanel() {
        const html = document.getElementById('labEquipmentFormTemplate').innerHTML;
        openLabEquipmentSlideOver('Add Equipment', 'Enter new lab equipment details', html);
        document.getElementById('lab_equip_method').value = 'POST';
        document.getElementById('le_submit_btn').textContent = 'Save Equipment';
        attachLabEquipmentForm('{{ route("lab-equipment.store") }}', 'Equipment added successfully.');
    }

    function updateLabEquipmentStatus(select, url) {
        const status = select.value;
        const originalText = select.options[select.selectedIndex].text;
        const formData = new FormData();
        formData.append('status', status);
        formData.append('_token', '{{ csrf_token() }}');
        formData.append('_method', 'PATCH');

        fetch(url, {
            method: 'POST',
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
        })
        .then(r => r.json().catch(() => ({})))
        .then(data => {
            Swal.fire({ icon: 'success', title: 'Status Updated', text: data.message || 'Equipment status updated.', timer: 1500, showConfirmButton: false });
            setTimeout(() => location.reload(), 1000);
        })
        .catch(err => {
            Swal.fire({ icon: 'error', title: 'Error', text: 'Failed to update status.' });
        });
    }

    async function openEditLabEquipmentPanel(url) {
        const html = document.getElementById('labEquipmentFormTemplate').innerHTML;
        openLabEquipmentSlideOver('Edit Equipment', 'Update equipment details', html);
        document.getElementById('lab_equip_method').value = 'PUT';
        document.getElementById('le_submit_btn').textContent = 'Update Equipment';
        try {
            const res = await fetch(url, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } });
            const data = await res.json();
            const item = data.item;
            const setValue = (id, val) => { const el = document.getElementById(id); if (el) el.value = val || ''; };
            setValue('le_name', item.name);
            setValue('le_model', item.model);
            setValue('le_serial_number', item.serial_number);
            setValue('le_manufacturer', item.manufacturer);
            setValue('le_purchase_date', item.purchase_date ? item.purchase_date.split(' ')[0] : '');
            setValue('le_last_service_date', item.last_service_date ? item.last_service_date.split(' ')[0] : '');
            setValue('le_next_service_date', item.next_service_date ? item.next_service_date.split(' ')[0] : '');
            setValue('le_status', item.status);
            setValue('le_notes', item.notes);
            attachLabEquipmentForm(url.replace('/edit', ''), 'Equipment updated successfully.');
        } catch (err) {
            leContent.innerHTML = '<div class="text-center text-red-600 py-8">Failed to load equipment details.</div>';
        }
    }
</script>
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
        <div class="bg-white rounded-md border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-medium text-gray-500 uppercase tracking-wide">Total Equipment</span>
                <i class="hgi-stroke hgi-cpu text-base text-gray-400"></i>
            </div>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['total'] }}</p>
        </div>
        <div class="bg-white rounded-md border border-emerald-200 p-4">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-medium text-gray-500 uppercase tracking-wide">Active</span>
                <i class="hgi-stroke hgi-checkmark-circle-02 text-base text-gray-400"></i>
            </div>
            <p class="text-2xl font-bold text-emerald-600 mt-1">{{ $stats['active'] }}</p>
        </div>
        <div class="bg-white rounded-md border border-amber-200 p-4">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-medium text-gray-500 uppercase tracking-wide">Maintenance</span>
                <i class="hgi-stroke hgi-settings-02 text-base text-gray-400"></i>
            </div>
            <p class="text-2xl font-bold text-amber-600 mt-1">{{ $stats['maintenance'] }}</p>
        </div>
        <div class="bg-white rounded-md border border-red-200 p-4">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-medium text-gray-500 uppercase tracking-wide">Retired</span>
                <i class="hgi-stroke hgi-unavailable text-base text-gray-400"></i>
            </div>
            <p class="text-2xl font-bold text-red-600 mt-1">{{ $stats['retired'] }}</p>
        </div>
    </div>

    <script>
    const leSlide = document.getElementById('labEquipmentSlideOver');
    const leBackdrop = document.getElementById('labEquipBackdrop');
    const lePanel = document.getElementById('labEquipPanel');
    const leContent = document.getElementById('labEquipSlideContent');
    const leTitle = document.getElementById('labEquipSlideTitle');
    const leSubtitle = document.getElementById('labEquipSlideSubtitle');

    function openLabEquipmentSlideOver(title, subtitle, html) {
        leSlide.classList.remove('hidden');
        leTitle.textContent = title;
        leSubtitle.textContent = subtitle;
        leContent.innerHTML = html;
        setTimeout(() => {
            leBackdrop.classList.remove('opacity-0');
            lePanel.classList.remove('translate-x-full');
        }, 10);
        document.body.style.overflow = 'hidden';
    }

    function closeLabEquipmentSlideOver() {
        leBackdrop.classList.add('opacity-0');
        lePanel.classList.add('translate-x-full');
        setTimeout(() => {
            leSlide.classList.add('hidden');
            leContent.innerHTML = '';
            document.body.style.overflow = '';
        }, 300);
    }

    function attachLabEquipmentForm(action, successMessage) {
        const form = document.getElementById('labEquipmentForm');
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
                closeLabEquipmentSlideOver();
                setTimeout(() => location.reload(), 1200);
            })
            .catch(err => {
                Swal.fire({ icon: 'error', title: 'Error', text: 'Failed to save equipment.' });
            });
        });
    }

    function openAddLabEquipmentPanel() {
        const html = document.getElementById('labEquipmentFormTemplate').innerHTML;
        openLabEquipmentSlideOver('Add Equipment', 'Enter new lab equipment details', html);
        document.getElementById('lab_equip_method').value = 'POST';
        document.getElementById('le_submit_btn').textContent = 'Save Equipment';
        attachLabEquipmentForm('{{ route("lab-equipment.store") }}', 'Equipment added successfully.');
    }

    function updateLabEquipmentStatus(select, url) {
        const status = select.value;
        const originalText = select.options[select.selectedIndex].text;
        const formData = new FormData();
        formData.append('status', status);
        formData.append('_token', '{{ csrf_token() }}');
        formData.append('_method', 'PATCH');

        fetch(url, {
            method: 'POST',
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
        })
        .then(r => r.json().catch(() => ({})))
        .then(data => {
            Swal.fire({ icon: 'success', title: 'Status Updated', text: data.message || 'Equipment status updated.', timer: 1500, showConfirmButton: false });
            setTimeout(() => location.reload(), 1000);
        })
        .catch(err => {
            Swal.fire({ icon: 'error', title: 'Error', text: 'Failed to update status.' });
        });
    }

    async function openEditLabEquipmentPanel(url) {
        const html = document.getElementById('labEquipmentFormTemplate').innerHTML;
        openLabEquipmentSlideOver('Edit Equipment', 'Update equipment details', html);
        document.getElementById('lab_equip_method').value = 'PUT';
        document.getElementById('le_submit_btn').textContent = 'Update Equipment';
        try {
            const res = await fetch(url, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } });
            const data = await res.json();
            const item = data.item;
            const setValue = (id, val) => { const el = document.getElementById(id); if (el) el.value = val || ''; };
            setValue('le_name', item.name);
            setValue('le_model', item.model);
            setValue('le_serial_number', item.serial_number);
            setValue('le_manufacturer', item.manufacturer);
            setValue('le_purchase_date', item.purchase_date ? item.purchase_date.split(' ')[0] : '');
            setValue('le_last_service_date', item.last_service_date ? item.last_service_date.split(' ')[0] : '');
            setValue('le_next_service_date', item.next_service_date ? item.next_service_date.split(' ')[0] : '');
            setValue('le_status', item.status);
            setValue('le_notes', item.notes);
            attachLabEquipmentForm(url.replace('/edit', ''), 'Equipment updated successfully.');
        } catch (err) {
            leContent.innerHTML = '<div class="text-center text-red-600 py-8">Failed to load equipment details.</div>';
        }
    }
</script>
    <div class="bg-white rounded-md border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 text-[10px] uppercase text-gray-400 border-b border-gray-100">
                        <th class="px-5 py-3 font-medium text-left">Name</th>
                        <th class="px-5 py-3 font-medium text-left">Model / S/N</th>
                        <th class="px-5 py-3 font-medium text-left">Manufacturer</th>
                        <th class="px-5 py-3 font-medium text-left">Next Service</th>
                        <th class="px-5 py-3 font-medium text-left">Status</th>
                        <th class="px-5 py-3 font-medium text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse ($equipment as $item)
                        <tr class="hover:bg-gray-50/50">
                            <td class="px-5 py-3 text-xs font-medium text-gray-900">{{ $item->name }}</td>
                            <td class="px-5 py-3 text-xs text-gray-600">
                                {{ $item->model ?? '-' }}
                                <div class="text-gray-400 text-[10px]">{{ $item->serial_number ?? '-' }}</div>
                            </td>
                            <td class="px-5 py-3 text-xs text-gray-600">{{ $item->manufacturer ?? '-' }}</td>
                            <td class="px-5 py-3 text-xs text-gray-600">{{ $item->next_service_date?->format('d M Y') ?? '-' }}</td>
                            <td class="px-5 py-3">
                                @php
                                    $statusColors = [
                                        'active' => ['bg-white text-emerald-700 border-emerald-200', 'bg-emerald-500'],
                                        'maintenance' => ['bg-white text-amber-700 border-amber-200', 'bg-amber-500'],
                                        'retired' => ['bg-white text-red-700 border-red-200', 'bg-red-500'],
                                    ];
                                    [$selectClass, $dotClass] = $statusColors[$item->status] ?? ['bg-white text-gray-700 border-gray-200', 'bg-gray-400'];
                                @endphp
                                <div class="relative inline-flex items-center">
                                    <select onchange="updateLabEquipmentStatus(this, '{{ route('lab-equipment.status.update', $item) }}')" class="status-select pl-6 pr-6 py-1 rounded text-[10px] font-medium capitalize border appearance-none cursor-pointer focus:ring-2 focus:ring-slate-300 {{ $selectClass }}">
                                        <option value="active" {{ $item->status === 'active' ? 'selected' : '' }}>Active</option>
                                        <option value="maintenance" {{ $item->status === 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                                        <option value="retired" {{ $item->status === 'retired' ? 'selected' : '' }}>Retired</option>
                                    </select>
                                    <span class="absolute left-2 w-1.5 h-1.5 rounded-full {{ $dotClass }}"></span>
                                    <i class="hgi-stroke hgi-arrow-down-01 text-xs absolute right-2 text-current opacity-60 pointer-events-none"></i>
                                </div>
                            </td>
                            <td class="px-5 py-3">
                                <div class="flex items-center justify-end gap-1">
                                    <button type="button" onclick="openEditLabEquipmentPanel('{{ route('lab-equipment.edit', $item) }}')" class="action-icon group/icon relative p-1.5 text-slate-600 hover:bg-slate-100 rounded transition-colors" title="Edit">
                                        <i class="hgi-stroke hgi-pencil-edit-01 text-base"></i>
                                        <span class="absolute -top-8 left-1/2 -translate-x-1/2 px-2 py-1 bg-gray-900 text-white text-[10px] rounded opacity-0 group-hover/icon:opacity-100 transition-opacity whitespace-nowrap pointer-events-none">Edit</span>
                                    </button>
                                    <form method="POST" action="{{ route('lab-equipment.destroy', $item) }}" data-ajax data-confirm="Delete this equipment?" class="inline">
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
                        <tr><td colspan="6" class="px-5 py-8 text-center text-gray-400">
                            <div class="flex flex-col items-center gap-2">
                                <i class="hgi-stroke hgi-cpu text-4xl text-gray-300"></i>
                                <p class="text-xs">No lab equipment found</p>
                            </div>
                        </td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-5 py-3 border-t border-gray-100">
            {{ $equipment->links() }}
        </div>
    </div>
</div>

<script>
    const leSlide = document.getElementById('labEquipmentSlideOver');
    const leBackdrop = document.getElementById('labEquipBackdrop');
    const lePanel = document.getElementById('labEquipPanel');
    const leContent = document.getElementById('labEquipSlideContent');
    const leTitle = document.getElementById('labEquipSlideTitle');
    const leSubtitle = document.getElementById('labEquipSlideSubtitle');

    function openLabEquipmentSlideOver(title, subtitle, html) {
        leSlide.classList.remove('hidden');
        leTitle.textContent = title;
        leSubtitle.textContent = subtitle;
        leContent.innerHTML = html;
        setTimeout(() => {
            leBackdrop.classList.remove('opacity-0');
            lePanel.classList.remove('translate-x-full');
        }, 10);
        document.body.style.overflow = 'hidden';
    }

    function closeLabEquipmentSlideOver() {
        leBackdrop.classList.add('opacity-0');
        lePanel.classList.add('translate-x-full');
        setTimeout(() => {
            leSlide.classList.add('hidden');
            leContent.innerHTML = '';
            document.body.style.overflow = '';
        }, 300);
    }

    function attachLabEquipmentForm(action, successMessage) {
        const form = document.getElementById('labEquipmentForm');
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
                closeLabEquipmentSlideOver();
                setTimeout(() => location.reload(), 1200);
            })
            .catch(err => {
                Swal.fire({ icon: 'error', title: 'Error', text: 'Failed to save equipment.' });
            });
        });
    }

    function openAddLabEquipmentPanel() {
        const html = document.getElementById('labEquipmentFormTemplate').innerHTML;
        openLabEquipmentSlideOver('Add Equipment', 'Enter new lab equipment details', html);
        document.getElementById('lab_equip_method').value = 'POST';
        document.getElementById('le_submit_btn').textContent = 'Save Equipment';
        attachLabEquipmentForm('{{ route("lab-equipment.store") }}', 'Equipment added successfully.');
    }

    function updateLabEquipmentStatus(select, url) {
        const status = select.value;
        const originalText = select.options[select.selectedIndex].text;
        const formData = new FormData();
        formData.append('status', status);
        formData.append('_token', '{{ csrf_token() }}');
        formData.append('_method', 'PATCH');

        fetch(url, {
            method: 'POST',
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
        })
        .then(r => r.json().catch(() => ({})))
        .then(data => {
            Swal.fire({ icon: 'success', title: 'Status Updated', text: data.message || 'Equipment status updated.', timer: 1500, showConfirmButton: false });
            setTimeout(() => location.reload(), 1000);
        })
        .catch(err => {
            Swal.fire({ icon: 'error', title: 'Error', text: 'Failed to update status.' });
        });
    }

    async function openEditLabEquipmentPanel(url) {
        const html = document.getElementById('labEquipmentFormTemplate').innerHTML;
        openLabEquipmentSlideOver('Edit Equipment', 'Update equipment details', html);
        document.getElementById('lab_equip_method').value = 'PUT';
        document.getElementById('le_submit_btn').textContent = 'Update Equipment';
        try {
            const res = await fetch(url, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } });
            const data = await res.json();
            const item = data.item;
            const setValue = (id, val) => { const el = document.getElementById(id); if (el) el.value = val || ''; };
            setValue('le_name', item.name);
            setValue('le_model', item.model);
            setValue('le_serial_number', item.serial_number);
            setValue('le_manufacturer', item.manufacturer);
            setValue('le_purchase_date', item.purchase_date ? item.purchase_date.split(' ')[0] : '');
            setValue('le_last_service_date', item.last_service_date ? item.last_service_date.split(' ')[0] : '');
            setValue('le_next_service_date', item.next_service_date ? item.next_service_date.split(' ')[0] : '');
            setValue('le_status', item.status);
            setValue('le_notes', item.notes);
            attachLabEquipmentForm(url.replace('/edit', ''), 'Equipment updated successfully.');
        } catch (err) {
            leContent.innerHTML = '<div class="text-center text-red-600 py-8">Failed to load equipment details.</div>';
        }
    }
</script>
<div id="labEquipmentSlideOver" class="fixed inset-0 z-50 hidden" aria-hidden="true">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm transition-opacity opacity-0" id="labEquipBackdrop" onclick="closeLabEquipmentSlideOver()"></div>
    <div class="absolute inset-y-0 right-0 w-full max-w-md transform translate-x-full transition-transform duration-300 ease-out" id="labEquipPanel">
        <div class="h-full bg-white shadow-2xl flex flex-col">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between bg-gray-50/50">
                <div>
                    <h3 class="text-lg font-bold text-gray-900" id="labEquipSlideTitle">Equipment</h3>
                    <p class="text-xs text-gray-500" id="labEquipSlideSubtitle">Manage equipment details</p>
                </div>
                <button onclick="closeLabEquipmentSlideOver()" class="text-gray-400 hover:text-gray-600 p-1 rounded-lg hover:bg-gray-100 transition-colors">
                    <i class="hgi-stroke hgi-cancel-01 text-2xl"></i>
                </button>
            </div>
            <div class="flex-1 overflow-y-auto p-6" id="labEquipSlideContent"></div>
        </div>
    </div>
</div>

<script>
    const leSlide = document.getElementById('labEquipmentSlideOver');
    const leBackdrop = document.getElementById('labEquipBackdrop');
    const lePanel = document.getElementById('labEquipPanel');
    const leContent = document.getElementById('labEquipSlideContent');
    const leTitle = document.getElementById('labEquipSlideTitle');
    const leSubtitle = document.getElementById('labEquipSlideSubtitle');

    function openLabEquipmentSlideOver(title, subtitle, html) {
        leSlide.classList.remove('hidden');
        leTitle.textContent = title;
        leSubtitle.textContent = subtitle;
        leContent.innerHTML = html;
        setTimeout(() => {
            leBackdrop.classList.remove('opacity-0');
            lePanel.classList.remove('translate-x-full');
        }, 10);
        document.body.style.overflow = 'hidden';
    }

    function closeLabEquipmentSlideOver() {
        leBackdrop.classList.add('opacity-0');
        lePanel.classList.add('translate-x-full');
        setTimeout(() => {
            leSlide.classList.add('hidden');
            leContent.innerHTML = '';
            document.body.style.overflow = '';
        }, 300);
    }

    function attachLabEquipmentForm(action, successMessage) {
        const form = document.getElementById('labEquipmentForm');
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
                closeLabEquipmentSlideOver();
                setTimeout(() => location.reload(), 1200);
            })
            .catch(err => {
                Swal.fire({ icon: 'error', title: 'Error', text: 'Failed to save equipment.' });
            });
        });
    }

    function openAddLabEquipmentPanel() {
        const html = document.getElementById('labEquipmentFormTemplate').innerHTML;
        openLabEquipmentSlideOver('Add Equipment', 'Enter new lab equipment details', html);
        document.getElementById('lab_equip_method').value = 'POST';
        document.getElementById('le_submit_btn').textContent = 'Save Equipment';
        attachLabEquipmentForm('{{ route("lab-equipment.store") }}', 'Equipment added successfully.');
    }

    function updateLabEquipmentStatus(select, url) {
        const status = select.value;
        const originalText = select.options[select.selectedIndex].text;
        const formData = new FormData();
        formData.append('status', status);
        formData.append('_token', '{{ csrf_token() }}');
        formData.append('_method', 'PATCH');

        fetch(url, {
            method: 'POST',
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
        })
        .then(r => r.json().catch(() => ({})))
        .then(data => {
            Swal.fire({ icon: 'success', title: 'Status Updated', text: data.message || 'Equipment status updated.', timer: 1500, showConfirmButton: false });
            setTimeout(() => location.reload(), 1000);
        })
        .catch(err => {
            Swal.fire({ icon: 'error', title: 'Error', text: 'Failed to update status.' });
        });
    }

    async function openEditLabEquipmentPanel(url) {
        const html = document.getElementById('labEquipmentFormTemplate').innerHTML;
        openLabEquipmentSlideOver('Edit Equipment', 'Update equipment details', html);
        document.getElementById('lab_equip_method').value = 'PUT';
        document.getElementById('le_submit_btn').textContent = 'Update Equipment';
        try {
            const res = await fetch(url, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } });
            const data = await res.json();
            const item = data.item;
            const setValue = (id, val) => { const el = document.getElementById(id); if (el) el.value = val || ''; };
            setValue('le_name', item.name);
            setValue('le_model', item.model);
            setValue('le_serial_number', item.serial_number);
            setValue('le_manufacturer', item.manufacturer);
            setValue('le_purchase_date', item.purchase_date ? item.purchase_date.split(' ')[0] : '');
            setValue('le_last_service_date', item.last_service_date ? item.last_service_date.split(' ')[0] : '');
            setValue('le_next_service_date', item.next_service_date ? item.next_service_date.split(' ')[0] : '');
            setValue('le_status', item.status);
            setValue('le_notes', item.notes);
            attachLabEquipmentForm(url.replace('/edit', ''), 'Equipment updated successfully.');
        } catch (err) {
            leContent.innerHTML = '<div class="text-center text-red-600 py-8">Failed to load equipment details.</div>';
        }
    }
</script>

@push('scripts')
<script>
    const leSlide = document.getElementById('labEquipmentSlideOver');
    const leBackdrop = document.getElementById('labEquipBackdrop');
    const lePanel = document.getElementById('labEquipPanel');
    const leContent = document.getElementById('labEquipSlideContent');
    const leTitle = document.getElementById('labEquipSlideTitle');
    const leSubtitle = document.getElementById('labEquipSlideSubtitle');

    function openLabEquipmentSlideOver(title, subtitle, html) {
        leSlide.classList.remove('hidden');
        leTitle.textContent = title;
        leSubtitle.textContent = subtitle;
        leContent.innerHTML = html;
        setTimeout(() => {
            leBackdrop.classList.remove('opacity-0');
            lePanel.classList.remove('translate-x-full');
        }, 10);
        document.body.style.overflow = 'hidden';
    }

    function closeLabEquipmentSlideOver() {
        leBackdrop.classList.add('opacity-0');
        lePanel.classList.add('translate-x-full');
        setTimeout(() => {
            leSlide.classList.add('hidden');
            leContent.innerHTML = '';
            document.body.style.overflow = '';
        }, 300);
    }

    function attachLabEquipmentForm(action, successMessage) {
        const form = document.getElementById('labEquipmentForm');
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
                closeLabEquipmentSlideOver();
                setTimeout(() => location.reload(), 1200);
            })
            .catch(err => {
                Swal.fire({ icon: 'error', title: 'Error', text: 'Failed to save equipment.' });
            });
        });
    }

    function openAddLabEquipmentPanel() {
        const html = document.getElementById('labEquipmentFormTemplate').innerHTML;
        openLabEquipmentSlideOver('Add Equipment', 'Enter new lab equipment details', html);
        document.getElementById('lab_equip_method').value = 'POST';
        document.getElementById('le_submit_btn').textContent = 'Save Equipment';
        attachLabEquipmentForm('{{ route("lab-equipment.store") }}', 'Equipment added successfully.');
    }

    function updateLabEquipmentStatus(select, url) {
        const status = select.value;
        const originalText = select.options[select.selectedIndex].text;
        const formData = new FormData();
        formData.append('status', status);
        formData.append('_token', '{{ csrf_token() }}');
        formData.append('_method', 'PATCH');

        fetch(url, {
            method: 'POST',
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
        })
        .then(r => r.json().catch(() => ({})))
        .then(data => {
            Swal.fire({ icon: 'success', title: 'Status Updated', text: data.message || 'Equipment status updated.', timer: 1500, showConfirmButton: false });
            setTimeout(() => location.reload(), 1000);
        })
        .catch(err => {
            Swal.fire({ icon: 'error', title: 'Error', text: 'Failed to update status.' });
        });
    }

    async function openEditLabEquipmentPanel(url) {
        const html = document.getElementById('labEquipmentFormTemplate').innerHTML;
        openLabEquipmentSlideOver('Edit Equipment', 'Update equipment details', html);
        document.getElementById('lab_equip_method').value = 'PUT';
        document.getElementById('le_submit_btn').textContent = 'Update Equipment';
        try {
            const res = await fetch(url, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } });
            const data = await res.json();
            const item = data.item;
            const setValue = (id, val) => { const el = document.getElementById(id); if (el) el.value = val || ''; };
            setValue('le_name', item.name);
            setValue('le_model', item.model);
            setValue('le_serial_number', item.serial_number);
            setValue('le_manufacturer', item.manufacturer);
            setValue('le_purchase_date', item.purchase_date ? item.purchase_date.split(' ')[0] : '');
            setValue('le_last_service_date', item.last_service_date ? item.last_service_date.split(' ')[0] : '');
            setValue('le_next_service_date', item.next_service_date ? item.next_service_date.split(' ')[0] : '');
            setValue('le_status', item.status);
            setValue('le_notes', item.notes);
            attachLabEquipmentForm(url.replace('/edit', ''), 'Equipment updated successfully.');
        } catch (err) {
            leContent.innerHTML = '<div class="text-center text-red-600 py-8">Failed to load equipment details.</div>';
        }
    }
</script>
@endpush
@endsection
