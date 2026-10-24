@extends('layouts.dashboard')

@section('title', 'Clinic Rooms - ' . config('app.name', 'Laravel'))
@section('page_title', 'Clinic Management - Rooms')

@section('content')
<div class="space-y-5">
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
    <div class="bg-slate-800 rounded-md p-6 text-white flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <p class="text-[10px] uppercase tracking-wider text-slate-400 mb-1">Clinic Management</p>
            <h1 class="text-xl font-bold">Clinic Rooms</h1>
            <p class="text-xs text-slate-400 mt-1">Manage consultation rooms, wards and facilities.</p>
        </div>
        <button type="button" onclick="openAddRoomPanel()" class="inline-flex items-center gap-2 bg-white text-slate-800 text-sm font-medium px-4 py-2 rounded hover:bg-slate-100 transition-colors self-start">
            <i class="hgi-stroke hgi-add-01 text-base"></i>
            Add Room
        </button>
    </div>

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
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        @php
            $statConfig = [
                ['key' => 'total', 'label' => 'Total Rooms', 'icon' => 'hgi-building-03', 'border' => 'border-gray-200', 'value' => 'text-gray-900'],
                ['key' => 'available', 'label' => 'Available', 'icon' => 'hgi-checkmark-circle-02', 'border' => 'border-emerald-200', 'value' => 'text-emerald-600'],
                ['key' => 'occupied', 'label' => 'Occupied', 'icon' => 'hgi-user-multiple-02', 'border' => 'border-amber-200', 'value' => 'text-amber-600'],
                ['key' => 'maintenance', 'label' => 'Maintenance', 'icon' => 'hgi-settings-02', 'border' => 'border-red-200', 'value' => 'text-red-600'],
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
    <div class="bg-white rounded-md border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 text-[10px] uppercase text-gray-400 border-b border-gray-100">
                        <th class="px-5 py-3 font-medium text-left">Room</th>
                        <th class="px-5 py-3 font-medium text-left">Code</th>
                        <th class="px-5 py-3 font-medium text-left">Department</th>
                        <th class="px-5 py-3 font-medium text-left">Type</th>
                        <th class="px-5 py-3 font-medium text-left">Capacity</th>
                        <th class="px-5 py-3 font-medium text-left">Status</th>
                        <th class="px-5 py-3 font-medium text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse ($rooms as $room)
                        <tr class="hover:bg-gray-50/50">
                            <td class="px-5 py-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded {{ $room->status === 'available' ? 'bg-slate-100 text-slate-600' : 'bg-gray-100 text-gray-500' }} flex items-center justify-center text-[10px] font-bold">
                                        {{ strtoupper(substr($room->name, 0, 2)) }}
                                    </div>
                                    <span class="text-xs font-medium text-gray-900">{{ $room->name }}</span>
                                </div>
                            </td>
                            <td class="px-5 py-3 text-gray-600 font-mono text-xs">{{ $room->code ?? '-' }}</td>
                            <td class="px-5 py-3 text-xs text-gray-600">{{ $room->department?->name ?? '-' }}</td>
                            <td class="px-5 py-3 text-xs text-gray-600 capitalize">{{ $room->type }}</td>
                            <td class="px-5 py-3 text-xs text-gray-600">
                                <div class="flex items-center gap-1.5">
                                    <i class="hgi-stroke hgi-user-multiple-02 text-sm text-gray-400"></i>
                                    {{ $room->capacity }}
                                </div>
                            </td>
                            <td class="px-5 py-3">
                                @php
                                    $dotConfig = [
                                        'available' => 'bg-emerald-500',
                                        'occupied' => 'bg-amber-500',
                                        'maintenance' => 'bg-red-500',
                                    ];
                                    $dot = $dotConfig[$room->status] ?? 'bg-slate-400';
                                @endphp
                                <span class="inline-flex items-center gap-1.5 text-[10px] font-medium text-gray-600 capitalize">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $dot }}"></span>
                                    {{ $room->status }}
                                </span>
                            </td>
                            <td class="px-5 py-3">
                                <div class="flex items-center justify-end gap-1">
                                    <button type="button" onclick="openEditRoomPanel('{{ route('clinic-rooms.edit', $room) }}')" class="action-icon group/icon relative p-1.5 text-slate-600 hover:bg-slate-100 rounded transition-colors" title="Edit">
                                        <i class="hgi-stroke hgi-pencil-edit-01 text-base"></i>
                                        <span class="absolute -top-8 left-1/2 -translate-x-1/2 px-2 py-1 bg-gray-900 text-white text-[10px] rounded opacity-0 group-hover/icon:opacity-100 transition-opacity whitespace-nowrap pointer-events-none">Edit</span>
                                    </button>
                                    <form method="POST" action="{{ route('clinic-rooms.destroy', $room) }}" data-ajax data-confirm="Delete this room?" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="action-icon group/icon relative p-1.5 text-red-600 hover:bg-red-50 rounded transition-colors" title="Delete">
                                            <i class="hgi-stroke hgi-cancel-circle text-base"></i>
                                            <span class="absolute -top-8 left-1/2 -translate-x-1/2 px-2 py-1 bg-gray-900 text-white text-[10px] rounded opacity-0 group-hover/icon:opacity-100 transition-opacity whitespace-nowrap pointer-events-none">Delete</span>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="px-5 py-8 text-center text-gray-400">
                            <div class="flex flex-col items-center gap-2">
                                <i class="hgi-stroke hgi-building-03 text-4xl text-gray-300"></i>
                                <p class="text-xs">No rooms found</p>
                            </div>
                        </td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-5 py-3 border-t border-gray-100">
            {{ $rooms->links() }}
        </div>
    </div>
</div>

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
                    <i class="hgi-stroke hgi-cancel-01 text-2xl"></i>
                </button>
            </div>
            <div class="flex-1 overflow-y-auto p-6" id="roomSlideContent"></div>
        </div>
    </div>
</div>

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
