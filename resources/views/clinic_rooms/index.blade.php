@extends('layouts.dashboard')

@section('title', 'Clinic Rooms - ' . config('app.name', 'Laravel'))
@section('page_title', 'Clinic Management - Rooms')

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-lg font-bold text-gray-900">Clinic Rooms</h2>
            <p class="text-sm text-gray-500">Manage consultation rooms, wards and facilities</p>
        </div>
        <button type="button" onclick="openAddRoomPanel()" class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium px-4 py-2 rounded-lg shadow-sm hover:shadow transition-all">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Add Room
        </button>
    </div>

    {{-- KPI Cards --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        @php
            $statConfig = [
                ['key' => 'total', 'label' => 'Total Rooms', 'icon' => 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4', 'from' => 'blue-500', 'to' => 'blue-700', 'border' => 'blue-400', 'text' => 'blue-100', 'sub' => 'blue-200'],
                ['key' => 'available', 'label' => 'Available', 'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z', 'from' => 'emerald-500', 'to' => 'emerald-700', 'border' => 'emerald-400', 'text' => 'emerald-100', 'sub' => 'emerald-200'],
                ['key' => 'occupied', 'label' => 'Occupied', 'icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z', 'from' => 'amber-500', 'to' => 'amber-700', 'border' => 'amber-400', 'text' => 'amber-100', 'sub' => 'amber-200'],
                ['key' => 'maintenance', 'label' => 'Maintenance', 'icon' => 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z', 'from' => 'red-500', 'to' => 'red-700', 'border' => 'red-400', 'text' => 'red-100', 'sub' => 'red-200'],
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

    {{-- Rooms Table --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                    <tr>
                        <th class="px-6 py-3">Room</th>
                        <th class="px-6 py-3">Code</th>
                        <th class="px-6 py-3">Department</th>
                        <th class="px-6 py-3">Type</th>
                        <th class="px-6 py-3">Capacity</th>
                        <th class="px-6 py-3">Status</th>
                        <th class="px-6 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($rooms as $room)
                        <tr class="group hover:bg-emerald-50/40 transition-colors">
                            <td class="px-6 py-3.5">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-lg {{ $room->status === 'available' ? 'bg-emerald-100 text-emerald-700' : ($room->status === 'occupied' ? 'bg-gold-100 text-gold-700' : 'bg-red-100 text-red-700') }} flex items-center justify-center text-sm font-bold">
                                        {{ strtoupper(substr($room->name, 0, 2)) }}
                                    </div>
                                    <span class="font-medium text-gray-900">{{ $room->name }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-3.5 text-gray-700 font-mono text-xs">{{ $room->code ?? '-' }}</td>
                            <td class="px-6 py-3.5 text-gray-700">{{ $room->department?->name ?? '-' }}</td>
                            <td class="px-6 py-3.5 capitalize text-gray-700">{{ $room->type }}</td>
                            <td class="px-6 py-3.5 text-gray-700">
                                <div class="flex items-center gap-1.5">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    {{ $room->capacity }}
                                </div>
                            </td>
                            <td class="px-6 py-3.5">
                                @php
                                    $statusConfig = [
                                        'available' => ['bg-emerald-100 text-emerald-700', 'bg-emerald-500'],
                                        'occupied' => ['bg-gold-100 text-gold-700', 'bg-gold-500'],
                                        'maintenance' => ['bg-red-100 text-red-700', 'bg-red-500'],
                                    ];
                                    $config = $statusConfig[$room->status] ?? ['bg-gray-100 text-gray-700', 'bg-gray-500'];
                                @endphp
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium capitalize {{ $config[0] }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $config[1] }}"></span>
                                    {{ $room->status }}
                                </span>
                            </td>
                            <td class="px-6 py-3.5">
                                <div class="flex items-center justify-end gap-1">
                                    <button type="button" onclick="openEditRoomPanel('{{ route('clinic-rooms.edit', $room) }}')" class="action-icon group/icon relative p-2 text-blue-600 hover:bg-blue-100 rounded-lg transition-colors" title="Edit">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.43-9.525l-9.17 9.17a2 2 0 00-.586 1.414V17a1 1 0 001 1h2.828a2 2 0 001.414-.586l9.17-9.17a2 2 0 000-2.828l-1.414-1.414a2 2 0 00-2.828 0z"/></svg>
                                        <span class="absolute -top-8 left-1/2 -translate-x-1/2 px-2 py-1 bg-gray-900 text-white text-[10px] rounded opacity-0 group-hover/icon:opacity-100 transition-opacity whitespace-nowrap pointer-events-none">Edit</span>
                                    </button>
                                    <form method="POST" action="{{ route('clinic-rooms.destroy', $room) }}" data-ajax data-confirm="Delete this room?" class="inline">
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
                        <tr><td colspan="7" class="px-6 py-10 text-center text-gray-400">
                            <div class="flex flex-col items-center gap-2">
                                <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                <p>No rooms found</p>
                            </div>
                        </td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-100">
            {{ $rooms->links() }}
        </div>
    </div>
</div>

{{-- Slide-over Panel --}}
<div id="roomSlideOver" class="fixed inset-0 z-50 hidden" aria-hidden="true">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm transition-opacity opacity-0" id="roomBackdrop" onclick="closeRoomSlideOver()"></div>
    <div class="absolute inset-y-0 right-0 w-full max-w-lg transform translate-x-full transition-transform duration-300 ease-out" id="roomPanel">
        <div class="h-full bg-white shadow-2xl flex flex-col">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between bg-gray-50/50">
                <div>
                    <h3 class="text-lg font-bold text-gray-900" id="roomSlideTitle">Room</h3>
                    <p class="text-xs text-gray-500" id="roomSlideSubtitle">Manage room details</p>
                </div>
                <button onclick="closeRoomSlideOver()" class="text-gray-400 hover:text-gray-600 p-1 rounded-lg hover:bg-gray-100 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="flex-1 overflow-y-auto p-6" id="roomSlideContent"></div>
        </div>
    </div>
</div>

<template id="roomFormTemplate">
    <form id="roomForm" method="POST" action="" class="space-y-4">
        @csrf
        <input type="hidden" name="_method" id="room_method" value="POST">
        <div class="grid grid-cols-2 gap-4">
            <div class="col-span-2">
                <label class="block text-xs font-medium text-gray-700 mb-1">Room Name <span class="text-red-500">*</span></label>
                <input type="text" name="name" id="room_name" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" required>
            </div>
            <div class="col-span-2 sm:col-span-1">
                <label class="block text-xs font-medium text-gray-700 mb-1">Code</label>
                <input type="text" name="code" id="room_code" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
            </div>
            <div class="col-span-2 sm:col-span-1">
                <label class="block text-xs font-medium text-gray-700 mb-1">Department</label>
                <select name="department_id" id="room_department_id" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 bg-white">
                    <option value="">Select department</option>
                </select>
            </div>
            <div class="col-span-2 sm:col-span-1">
                <label class="block text-xs font-medium text-gray-700 mb-1">Type <span class="text-red-500">*</span></label>
                <select name="type" id="room_type" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 bg-white" required>
                    <option value="consultation">Consultation</option>
                    <option value="procedure">Procedure</option>
                    <option value="ward">Ward</option>
                    <option value="emergency">Emergency</option>
                </select>
            </div>
            <div class="col-span-2 sm:col-span-1">
                <label class="block text-xs font-medium text-gray-700 mb-1">Capacity <span class="text-red-500">*</span></label>
                <input type="number" name="capacity" id="room_capacity" min="1" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" required>
            </div>
            <div class="col-span-2 sm:col-span-1">
                <label class="block text-xs font-medium text-gray-700 mb-1">Status <span class="text-red-500">*</span></label>
                <select name="status" id="room_status" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 bg-white" required>
                    <option value="available">Available</option>
                    <option value="occupied">Occupied</option>
                    <option value="maintenance">Maintenance</option>
                </select>
            </div>
            <div class="col-span-2">
                <label class="block text-xs font-medium text-gray-700 mb-1">Description</label>
                <textarea name="description" id="room_description" rows="3" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"></textarea>
            </div>
        </div>
        <div class="flex justify-end gap-2 pt-4 border-t border-gray-100 mt-4">
            <button type="button" onclick="closeRoomSlideOver()" class="px-4 py-2 text-sm text-gray-600 hover:bg-gray-100 rounded-lg">Cancel</button>
            <button type="submit" class="px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700" id="room_submit_btn">Save Room</button>
        </div>
    </form>
</template>

@push('scripts')
<script>
    const roomSlide = document.getElementById('roomSlideOver');
    const roomBackdrop = document.getElementById('roomBackdrop');
    const roomPanel = document.getElementById('roomPanel');
    const roomContent = document.getElementById('roomSlideContent');

    function openRoomSlideOver(title, subtitle, html) {
        roomSlide.classList.remove('hidden');
        document.getElementById('roomSlideTitle').textContent = title;
        document.getElementById('roomSlideSubtitle').textContent = subtitle;
        roomContent.innerHTML = html;
        setTimeout(() => {
            roomBackdrop.classList.remove('opacity-0');
            roomPanel.classList.remove('translate-x-full');
        }, 10);
        document.body.style.overflow = 'hidden';
    }

    function closeRoomSlideOver() {
        roomBackdrop.classList.add('opacity-0');
        roomPanel.classList.add('translate-x-full');
        setTimeout(() => {
            roomSlide.classList.add('hidden');
            roomContent.innerHTML = '';
            document.body.style.overflow = '';
        }, 300);
    }

    function populateDepartmentSelect(departments) {
        const select = document.getElementById('room_department_id');
        select.innerHTML = '<option value="">Select department</option>' +
            departments.map(d => `<option value="${d.id}">${d.name}</option>`).join('');
    }

    function attachRoomForm(action, successMessage) {
        const form = document.getElementById('roomForm');
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
                closeRoomSlideOver();
                setTimeout(() => location.reload(), 1000);
            })
            .catch(err => {
                Swal.fire({ icon: 'error', title: 'Error', text: 'Failed to save room.' });
            });
        });
    }

    function resetRoomForm() {
        document.getElementById('room_method').value = 'POST';
        document.getElementById('room_name').value = '';
        document.getElementById('room_code').value = '';
        document.getElementById('room_department_id').value = '';
        document.getElementById('room_type').value = 'consultation';
        document.getElementById('room_capacity').value = '1';
        document.getElementById('room_status').value = 'available';
        document.getElementById('room_description').value = '';
    }

    async function openAddRoomPanel() {
        const html = document.getElementById('roomFormTemplate').innerHTML;
        openRoomSlideOver('New Room', 'Add a new clinic room', html);
        try {
            const res = await fetch('{{ route("clinic-rooms.create") }}', { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } });
            const data = await res.json();
            populateDepartmentSelect(data.departments);
            resetRoomForm();
            document.getElementById('room_submit_btn').textContent = 'Save Room';
            attachRoomForm('{{ route("clinic-rooms.store") }}', 'Room created successfully.');
        } catch (err) {
            roomContent.innerHTML = '<div class="text-center text-red-600 py-8">Failed to load form data.</div>';
        }
    }

    async function openEditRoomPanel(url) {
        const html = document.getElementById('roomFormTemplate').innerHTML;
        openRoomSlideOver('Edit Room', 'Update room details', html);
        try {
            const res = await fetch(url, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } });
            const data = await res.json();
            populateDepartmentSelect(data.departments);
            const r = data.room;
            document.getElementById('room_method').value = 'PUT';
            document.getElementById('room_name').value = r.name;
            document.getElementById('room_code').value = r.code || '';
            document.getElementById('room_department_id').value = r.department_id || '';
            document.getElementById('room_type').value = r.type;
            document.getElementById('room_capacity').value = r.capacity;
            document.getElementById('room_status').value = r.status;
            document.getElementById('room_description').value = r.description || '';
            document.getElementById('room_submit_btn').textContent = 'Update Room';
            attachRoomForm(url.replace('/edit', ''), 'Room updated successfully.');
        } catch (err) {
            roomContent.innerHTML = '<div class="text-center text-red-600 py-8">Failed to load room details.</div>';
        }
    }
</script>
@endpush
@endsection
