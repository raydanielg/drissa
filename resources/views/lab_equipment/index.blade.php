@extends('layouts.dashboard')

@section('title', 'Lab Equipment - ' . config('app.name', 'Laravel'))
@section('page_title', 'Lab Equipment')

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-lg font-bold text-gray-900">Lab Equipment</h2>
            <p class="text-sm text-gray-500">Track lab devices, service dates and operational status</p>
        </div>
        <button type="button" onclick="openAddLabEquipmentPanel()" class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium px-4 py-2 rounded-lg shadow-sm hover:shadow transition-all">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Add Equipment
        </button>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl border border-gray-100 p-5 shadow-sm text-center hover:shadow-md transition-shadow">
            <div class="w-12 h-12 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center mx-auto mb-3">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"/></svg>
            </div>
            <div class="text-2xl font-bold text-gray-900">{{ $stats['total'] }}</div>
            <div class="text-xs text-gray-500 uppercase tracking-wide mt-1">Total Equipment</div>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 p-5 shadow-sm text-center hover:shadow-md transition-shadow">
            <div class="w-12 h-12 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center mx-auto mb-3">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div class="text-2xl font-bold text-gray-900">{{ $stats['active'] }}</div>
            <div class="text-xs text-gray-500 uppercase tracking-wide mt-1">Active</div>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 p-5 shadow-sm text-center hover:shadow-md transition-shadow">
            <div class="w-12 h-12 rounded-full bg-amber-100 text-amber-600 flex items-center justify-center mx-auto mb-3">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            </div>
            <div class="text-2xl font-bold text-gray-900">{{ $stats['maintenance'] }}</div>
            <div class="text-xs text-gray-500 uppercase tracking-wide mt-1">Maintenance</div>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 p-5 shadow-sm text-center hover:shadow-md transition-shadow">
            <div class="w-12 h-12 rounded-full bg-red-100 text-red-600 flex items-center justify-center mx-auto mb-3">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
            </div>
            <div class="text-2xl font-bold text-gray-900">{{ $stats['retired'] }}</div>
            <div class="text-xs text-gray-500 uppercase tracking-wide mt-1">Retired</div>
        </div>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                    <tr>
                        <th class="px-6 py-3">Name</th>
                        <th class="px-6 py-3">Model / S/N</th>
                        <th class="px-6 py-3">Manufacturer</th>
                        <th class="px-6 py-3">Next Service</th>
                        <th class="px-6 py-3">Status</th>
                        <th class="px-6 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($equipment as $item)
                        <tr class="group hover:bg-emerald-50/40 transition-colors">
                            <td class="px-6 py-3.5 font-medium text-gray-900">{{ $item->name }}</td>
                            <td class="px-6 py-3.5 text-gray-700">
                                {{ $item->model ?? '-' }}
                                <div class="text-gray-400 text-xs">{{ $item->serial_number ?? '-' }}</div>
                            </td>
                            <td class="px-6 py-3.5 text-gray-700">{{ $item->manufacturer ?? '-' }}</td>
                            <td class="px-6 py-3.5 text-gray-700">{{ $item->next_service_date?->format('d M Y') ?? '-' }}</td>
                            <td class="px-6 py-3.5">
                                @php
                                    $statusColors = [
                                        'active' => ['bg-emerald-50 text-emerald-700 border-emerald-200', 'bg-emerald-500'],
                                        'maintenance' => ['bg-amber-50 text-amber-700 border-amber-200', 'bg-amber-500'],
                                        'retired' => ['bg-red-50 text-red-700 border-red-200', 'bg-red-500'],
                                    ];
                                    [$selectClass, $dotClass] = $statusColors[$item->status] ?? ['bg-gray-50 text-gray-700 border-gray-200', 'bg-gray-400'];
                                @endphp
                                <div class="relative inline-flex items-center">
                                    <select onchange="updateLabEquipmentStatus(this, '{{ route('lab-equipment.status.update', $item) }}')" class="status-select pl-7 pr-6 py-1 rounded-full text-xs font-medium capitalize border appearance-none cursor-pointer focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 {{ $selectClass }}">
                                        <option value="active" {{ $item->status === 'active' ? 'selected' : '' }}>Active</option>
                                        <option value="maintenance" {{ $item->status === 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                                        <option value="retired" {{ $item->status === 'retired' ? 'selected' : '' }}>Retired</option>
                                    </select>
                                    <span class="absolute left-2.5 w-1.5 h-1.5 rounded-full {{ $dotClass }}"></span>
                                    <svg class="w-3 h-3 absolute right-2 text-current opacity-60 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                </div>
                            </td>
                            <td class="px-6 py-3.5">
                                <div class="flex items-center justify-end gap-1">
                                    <button type="button" onclick="openEditLabEquipmentPanel('{{ route('lab-equipment.edit', $item) }}')" class="action-icon group/icon relative p-2 text-blue-600 hover:bg-blue-100 rounded-lg transition-colors" title="Edit">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.43-9.525l-9.17 9.17a2 2 0 00-.586 1.414V17a1 1 0 001 1h2.828a2 2 0 001.414-.586l9.17-9.17a2 2 0 000-2.828l-1.414-1.414a2 2 0 00-2.828 0z"/></svg>
                                        <span class="absolute -top-8 left-1/2 -translate-x-1/2 px-2 py-1 bg-gray-900 text-white text-[10px] rounded opacity-0 group-hover/icon:opacity-100 transition-opacity whitespace-nowrap pointer-events-none">Edit</span>
                                    </button>
                                    <form method="POST" action="{{ route('lab-equipment.destroy', $item) }}" data-ajax data-confirm="Delete this equipment?" class="inline">
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
                        <tr><td colspan="6" class="px-6 py-10 text-center text-gray-400">
                            <div class="flex flex-col items-center gap-2">
                                <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"/></svg>
                                <p>No lab equipment found</p>
                            </div>
                        </td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-100">
            {{ $equipment->links() }}
        </div>
    </div>
</div>

{{-- Slide-over Panel --}}
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
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="flex-1 overflow-y-auto p-6" id="labEquipSlideContent"></div>
        </div>
    </div>
</div>

<template id="labEquipmentFormTemplate">
    <form id="labEquipmentForm" method="POST" action="" class="space-y-4">
        @csrf
        <input type="hidden" name="_method" id="lab_equip_method" value="POST">
        <div class="grid grid-cols-2 gap-4">
            <div class="col-span-2">
                <label class="block text-xs font-medium text-gray-700 mb-1">Name <span class="text-red-500">*</span></label>
                <input type="text" name="name" id="le_name" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" required>
            </div>
            <div class="col-span-2 sm:col-span-1">
                <label class="block text-xs font-medium text-gray-700 mb-1">Model</label>
                <input type="text" name="model" id="le_model" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
            </div>
            <div class="col-span-2 sm:col-span-1">
                <label class="block text-xs font-medium text-gray-700 mb-1">Serial Number</label>
                <input type="text" name="serial_number" id="le_serial_number" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
            </div>
            <div class="col-span-2">
                <label class="block text-xs font-medium text-gray-700 mb-1">Manufacturer</label>
                <input type="text" name="manufacturer" id="le_manufacturer" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
            </div>
            <div class="col-span-2 sm:col-span-1">
                <label class="block text-xs font-medium text-gray-700 mb-1">Purchase Date</label>
                <input type="date" name="purchase_date" id="le_purchase_date" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
            </div>
            <div class="col-span-2 sm:col-span-1">
                <label class="block text-xs font-medium text-gray-700 mb-1">Status <span class="text-red-500">*</span></label>
                <select name="status" id="le_status" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 bg-white" required>
                    <option value="active">Active</option>
                    <option value="maintenance">Maintenance</option>
                    <option value="retired">Retired</option>
                </select>
            </div>
            <div class="col-span-2 sm:col-span-1">
                <label class="block text-xs font-medium text-gray-700 mb-1">Last Service Date</label>
                <input type="date" name="last_service_date" id="le_last_service_date" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
            </div>
            <div class="col-span-2 sm:col-span-1">
                <label class="block text-xs font-medium text-gray-700 mb-1">Next Service Date</label>
                <input type="date" name="next_service_date" id="le_next_service_date" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
            </div>
            <div class="col-span-2">
                <label class="block text-xs font-medium text-gray-700 mb-1">Notes</label>
                <textarea name="notes" id="le_notes" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" rows="3"></textarea>
            </div>
        </div>
        <div class="flex justify-end gap-2 pt-4 border-t border-gray-100 mt-4">
            <button type="button" onclick="closeLabEquipmentSlideOver()" class="px-4 py-2 text-sm text-gray-600 hover:bg-gray-100 rounded-lg">Cancel</button>
            <button type="submit" class="px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700" id="le_submit_btn">Save Equipment</button>
        </div>
    </form>
</template>

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
