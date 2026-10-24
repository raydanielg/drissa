@extends('layouts.dashboard')

@section('title', 'Shifts - ' . config('app.name', 'Laravel'))
@section('page_title', 'HR - Shifts')

@section('content')
<div class="space-y-5">
    @if (session('status'))
        <div class="p-3 rounded-md bg-white border-l-2 border-emerald-500 text-emerald-700 text-xs">{{ session('status') }}</div>
    @endif

    <script>
function openShiftForm() {
    document.getElementById('shiftFormTitle').textContent = 'Add Shift';
    document.getElementById('shiftForm').action = '{{ route('shifts.store') }}';
    document.getElementById('shiftMethod').value = 'POST';
    document.getElementById('shiftId').value = '';
    document.getElementById('shiftName').value = '';
    document.getElementById('shiftStartTime').value = '';
    document.getElementById('shiftEndTime').value = '';
    document.getElementById('shiftIsActive').checked = true;
    
    const modal = document.getElementById('shiftSlideOver');
    const panel = document.getElementById('shiftPanel');
    modal.classList.remove('hidden');
    setTimeout(() => {
        panel.classList.remove('translate-x-full');
    }, 10);
    document.body.style.overflow = 'hidden';
}

function editShift(id, name, startTime, endTime, isActive) {
    document.getElementById('shiftFormTitle').textContent = 'Edit Shift';
    document.getElementById('shiftForm').action = '/shifts/' + id;
    document.getElementById('shiftMethod').value = 'PUT';
    document.getElementById('shiftId').value = id;
    document.getElementById('shiftName').value = name;
    document.getElementById('shiftStartTime').value = startTime;
    document.getElementById('shiftEndTime').value = endTime;
    document.getElementById('shiftIsActive').checked = isActive;
    
    const modal = document.getElementById('shiftSlideOver');
    const panel = document.getElementById('shiftPanel');
    modal.classList.remove('hidden');
    setTimeout(() => {
        panel.classList.remove('translate-x-full');
    }, 10);
    document.body.style.overflow = 'hidden';
}

function closeShiftForm() {
    const modal = document.getElementById('shiftSlideOver');
    const panel = document.getElementById('shiftPanel');
    panel.classList.add('translate-x-full');
    setTimeout(() => {
        modal.classList.add('hidden');
        document.body.style.overflow = '';
    }, 300);
}

function deleteShift(id) {
    Swal.fire({
        title: 'Delete Shift?',
        text: 'This action cannot be undone.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#0f172a',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Yes, Delete',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch('/shifts/' + id, {
                method: 'DELETE',
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
            })
            .then(r => r.json().catch(() => ({})))
            .then(data => {
                Swal.fire({ icon: 'success', title: 'Success', text: data.message || 'Shift deleted successfully.', timer: 1500, showConfirmButton: false });
                setTimeout(() => location.reload(), 1000);
            })
            .catch(err => {
                Swal.fire({ icon: 'error', title: 'Error', text: 'Failed to delete shift.' });
            });
        }
    });
}

document.getElementById('shiftForm')?.addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    fetch(this.action, {
        method: this.querySelector('#shiftMethod').value,
        body: formData,
        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
    })
    .then(r => r.json().catch(() => ({})))
    .then(data => {
        Swal.fire({ icon: 'success', title: 'Success', text: data.message || 'Shift saved successfully.', timer: 1500, showConfirmButton: false });
        closeShiftForm();
        setTimeout(() => location.reload(), 1000);
    })
    .catch(err => {
        Swal.fire({ icon: 'error', title: 'Error', text: 'Failed to save shift.' });
    });
});
</script>
    <div class="bg-slate-800 rounded-md p-6 text-white">
        <p class="text-[10px] uppercase tracking-wider text-slate-400 mb-1">HR</p>
        <h1 class="text-xl font-bold">Shifts</h1>
        <p class="text-xs text-slate-400 mt-1">Staff shift schedules and availability.</p>
    </div>

    <script>
function openShiftForm() {
    document.getElementById('shiftFormTitle').textContent = 'Add Shift';
    document.getElementById('shiftForm').action = '{{ route('shifts.store') }}';
    document.getElementById('shiftMethod').value = 'POST';
    document.getElementById('shiftId').value = '';
    document.getElementById('shiftName').value = '';
    document.getElementById('shiftStartTime').value = '';
    document.getElementById('shiftEndTime').value = '';
    document.getElementById('shiftIsActive').checked = true;
    
    const modal = document.getElementById('shiftSlideOver');
    const panel = document.getElementById('shiftPanel');
    modal.classList.remove('hidden');
    setTimeout(() => {
        panel.classList.remove('translate-x-full');
    }, 10);
    document.body.style.overflow = 'hidden';
}

function editShift(id, name, startTime, endTime, isActive) {
    document.getElementById('shiftFormTitle').textContent = 'Edit Shift';
    document.getElementById('shiftForm').action = '/shifts/' + id;
    document.getElementById('shiftMethod').value = 'PUT';
    document.getElementById('shiftId').value = id;
    document.getElementById('shiftName').value = name;
    document.getElementById('shiftStartTime').value = startTime;
    document.getElementById('shiftEndTime').value = endTime;
    document.getElementById('shiftIsActive').checked = isActive;
    
    const modal = document.getElementById('shiftSlideOver');
    const panel = document.getElementById('shiftPanel');
    modal.classList.remove('hidden');
    setTimeout(() => {
        panel.classList.remove('translate-x-full');
    }, 10);
    document.body.style.overflow = 'hidden';
}

function closeShiftForm() {
    const modal = document.getElementById('shiftSlideOver');
    const panel = document.getElementById('shiftPanel');
    panel.classList.add('translate-x-full');
    setTimeout(() => {
        modal.classList.add('hidden');
        document.body.style.overflow = '';
    }, 300);
}

function deleteShift(id) {
    Swal.fire({
        title: 'Delete Shift?',
        text: 'This action cannot be undone.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#0f172a',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Yes, Delete',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch('/shifts/' + id, {
                method: 'DELETE',
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
            })
            .then(r => r.json().catch(() => ({})))
            .then(data => {
                Swal.fire({ icon: 'success', title: 'Success', text: data.message || 'Shift deleted successfully.', timer: 1500, showConfirmButton: false });
                setTimeout(() => location.reload(), 1000);
            })
            .catch(err => {
                Swal.fire({ icon: 'error', title: 'Error', text: 'Failed to delete shift.' });
            });
        }
    });
}

document.getElementById('shiftForm')?.addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    fetch(this.action, {
        method: this.querySelector('#shiftMethod').value,
        body: formData,
        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
    })
    .then(r => r.json().catch(() => ({})))
    .then(data => {
        Swal.fire({ icon: 'success', title: 'Success', text: data.message || 'Shift saved successfully.', timer: 1500, showConfirmButton: false });
        closeShiftForm();
        setTimeout(() => location.reload(), 1000);
    })
    .catch(err => {
        Swal.fire({ icon: 'error', title: 'Error', text: 'Failed to save shift.' });
    });
});
</script>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
        <div class="bg-white rounded-md border border-gray-200 p-4">
            <div class="flex items-start justify-between">
                <p class="text-[10px] font-medium text-gray-500 uppercase tracking-wide">Total Shifts</p>
                <i class="hgi-stroke hgi-clock-01 text-base text-gray-400"></i>
            </div>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['total'] ?? 0 }}</p>
        </div>
        <div class="bg-white rounded-md border border-emerald-200 p-4">
            <div class="flex items-start justify-between">
                <p class="text-[10px] font-medium text-gray-500 uppercase tracking-wide">Active Shifts</p>
                <i class="hgi-stroke hgi-checkmark-circle-02 text-base text-emerald-600"></i>
            </div>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['active'] ?? 0 }}</p>
        </div>
        <div class="bg-white rounded-md border border-gray-200 p-4">
            <div class="flex items-start justify-between">
                <p class="text-[10px] font-medium text-gray-500 uppercase tracking-wide">Inactive Shifts</p>
                <i class="hgi-stroke hgi-cancel-circle text-base text-gray-400"></i>
            </div>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['inactive'] ?? 0 }}</p>
        </div>
    </div>

    <script>
function openShiftForm() {
    document.getElementById('shiftFormTitle').textContent = 'Add Shift';
    document.getElementById('shiftForm').action = '{{ route('shifts.store') }}';
    document.getElementById('shiftMethod').value = 'POST';
    document.getElementById('shiftId').value = '';
    document.getElementById('shiftName').value = '';
    document.getElementById('shiftStartTime').value = '';
    document.getElementById('shiftEndTime').value = '';
    document.getElementById('shiftIsActive').checked = true;
    
    const modal = document.getElementById('shiftSlideOver');
    const panel = document.getElementById('shiftPanel');
    modal.classList.remove('hidden');
    setTimeout(() => {
        panel.classList.remove('translate-x-full');
    }, 10);
    document.body.style.overflow = 'hidden';
}

function editShift(id, name, startTime, endTime, isActive) {
    document.getElementById('shiftFormTitle').textContent = 'Edit Shift';
    document.getElementById('shiftForm').action = '/shifts/' + id;
    document.getElementById('shiftMethod').value = 'PUT';
    document.getElementById('shiftId').value = id;
    document.getElementById('shiftName').value = name;
    document.getElementById('shiftStartTime').value = startTime;
    document.getElementById('shiftEndTime').value = endTime;
    document.getElementById('shiftIsActive').checked = isActive;
    
    const modal = document.getElementById('shiftSlideOver');
    const panel = document.getElementById('shiftPanel');
    modal.classList.remove('hidden');
    setTimeout(() => {
        panel.classList.remove('translate-x-full');
    }, 10);
    document.body.style.overflow = 'hidden';
}

function closeShiftForm() {
    const modal = document.getElementById('shiftSlideOver');
    const panel = document.getElementById('shiftPanel');
    panel.classList.add('translate-x-full');
    setTimeout(() => {
        modal.classList.add('hidden');
        document.body.style.overflow = '';
    }, 300);
}

function deleteShift(id) {
    Swal.fire({
        title: 'Delete Shift?',
        text: 'This action cannot be undone.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#0f172a',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Yes, Delete',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch('/shifts/' + id, {
                method: 'DELETE',
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
            })
            .then(r => r.json().catch(() => ({})))
            .then(data => {
                Swal.fire({ icon: 'success', title: 'Success', text: data.message || 'Shift deleted successfully.', timer: 1500, showConfirmButton: false });
                setTimeout(() => location.reload(), 1000);
            })
            .catch(err => {
                Swal.fire({ icon: 'error', title: 'Error', text: 'Failed to delete shift.' });
            });
        }
    });
}

document.getElementById('shiftForm')?.addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    fetch(this.action, {
        method: this.querySelector('#shiftMethod').value,
        body: formData,
        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
    })
    .then(r => r.json().catch(() => ({})))
    .then(data => {
        Swal.fire({ icon: 'success', title: 'Success', text: data.message || 'Shift saved successfully.', timer: 1500, showConfirmButton: false });
        closeShiftForm();
        setTimeout(() => location.reload(), 1000);
    })
    .catch(err => {
        Swal.fire({ icon: 'error', title: 'Error', text: 'Failed to save shift.' });
    });
});
</script>
    <div class="bg-white rounded-md border border-gray-200 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
            <h2 class="text-sm font-bold text-gray-900">Shifts Management</h2>
            <button type="button" onclick="openShiftForm()" class="flex items-center gap-1.5 px-3 py-1.5 bg-slate-800 text-white text-xs font-medium rounded hover:bg-slate-700 transition-colors">
                <i class="hgi-stroke hgi-add-01 text-sm"></i>
                Add Shift
            </button>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-50 text-[10px] uppercase text-gray-400">
                    <tr>
                        <th class="px-5 py-3">Name</th>
                        <th class="px-5 py-3">Start Time</th>
                        <th class="px-5 py-3">End Time</th>
                        <th class="px-5 py-3">Duration</th>
                        <th class="px-5 py-3">Status</th>
                        <th class="px-5 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse ($shifts as $shift)
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="px-5 py-3">
                                <div class="flex items-center gap-2">
                                    <div class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center text-slate-600 text-xs font-bold">
                                        {{ strtoupper(substr($shift->name, 0, 1)) }}
                                    </div>
                                    <span class="text-xs text-gray-900 font-medium">{{ $shift->name }}</span>
                                </div>
                            </td>
                            <td class="px-5 py-3 text-gray-600 text-xs">{{ $shift->start_time }}</td>
                            <td class="px-5 py-3 text-gray-600 text-xs">{{ $shift->end_time }}</td>
                            <td class="px-5 py-3 text-gray-600 text-xs">{{ $shift->duration() }} min</td>
                            <td class="px-5 py-3">
                                <span class="inline-flex items-center gap-1.5 text-[10px] font-medium text-gray-600">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $shift->is_active ? 'bg-emerald-500' : 'bg-slate-400' }}"></span>
                                    {{ $shift->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td class="px-5 py-3 text-right">
                                <div class="flex items-center justify-end gap-1">
                                    <button type="button" onclick="editShift({{ $shift->id }}, '{{ $shift->name }}', '{{ $shift->start_time }}', '{{ $shift->end_time }}', {{ $shift->is_active ? 'true' : 'false' }})" class="p-1.5 rounded text-slate-600 hover:bg-slate-100 transition-colors" title="Edit">
                                        <i class="hgi-stroke hgi-pencil-edit-01 text-base"></i>
                                    </button>
                                    <button type="button" onclick="deleteShift({{ $shift->id }})" class="p-1.5 rounded text-red-600 hover:bg-red-50 transition-colors" title="Delete">
                                        <i class="hgi-stroke hgi-delete-02 text-base"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-5 py-10 text-center text-gray-400 text-xs">
                            <div class="flex flex-col items-center gap-2">
                                <i class="hgi-stroke hgi-clock-01 text-4xl text-gray-300"></i>
                                <p>No shifts found</p>
                            </div>
                        </td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-5 py-3 border-t border-gray-100">
            {{ $shifts->links() }}
        </div>
    </div>
</div>

<script>
function openShiftForm() {
    document.getElementById('shiftFormTitle').textContent = 'Add Shift';
    document.getElementById('shiftForm').action = '{{ route('shifts.store') }}';
    document.getElementById('shiftMethod').value = 'POST';
    document.getElementById('shiftId').value = '';
    document.getElementById('shiftName').value = '';
    document.getElementById('shiftStartTime').value = '';
    document.getElementById('shiftEndTime').value = '';
    document.getElementById('shiftIsActive').checked = true;
    
    const modal = document.getElementById('shiftSlideOver');
    const panel = document.getElementById('shiftPanel');
    modal.classList.remove('hidden');
    setTimeout(() => {
        panel.classList.remove('translate-x-full');
    }, 10);
    document.body.style.overflow = 'hidden';
}

function editShift(id, name, startTime, endTime, isActive) {
    document.getElementById('shiftFormTitle').textContent = 'Edit Shift';
    document.getElementById('shiftForm').action = '/shifts/' + id;
    document.getElementById('shiftMethod').value = 'PUT';
    document.getElementById('shiftId').value = id;
    document.getElementById('shiftName').value = name;
    document.getElementById('shiftStartTime').value = startTime;
    document.getElementById('shiftEndTime').value = endTime;
    document.getElementById('shiftIsActive').checked = isActive;
    
    const modal = document.getElementById('shiftSlideOver');
    const panel = document.getElementById('shiftPanel');
    modal.classList.remove('hidden');
    setTimeout(() => {
        panel.classList.remove('translate-x-full');
    }, 10);
    document.body.style.overflow = 'hidden';
}

function closeShiftForm() {
    const modal = document.getElementById('shiftSlideOver');
    const panel = document.getElementById('shiftPanel');
    panel.classList.add('translate-x-full');
    setTimeout(() => {
        modal.classList.add('hidden');
        document.body.style.overflow = '';
    }, 300);
}

function deleteShift(id) {
    Swal.fire({
        title: 'Delete Shift?',
        text: 'This action cannot be undone.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#0f172a',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Yes, Delete',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch('/shifts/' + id, {
                method: 'DELETE',
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
            })
            .then(r => r.json().catch(() => ({})))
            .then(data => {
                Swal.fire({ icon: 'success', title: 'Success', text: data.message || 'Shift deleted successfully.', timer: 1500, showConfirmButton: false });
                setTimeout(() => location.reload(), 1000);
            })
            .catch(err => {
                Swal.fire({ icon: 'error', title: 'Error', text: 'Failed to delete shift.' });
            });
        }
    });
}

document.getElementById('shiftForm')?.addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    fetch(this.action, {
        method: this.querySelector('#shiftMethod').value,
        body: formData,
        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
    })
    .then(r => r.json().catch(() => ({})))
    .then(data => {
        Swal.fire({ icon: 'success', title: 'Success', text: data.message || 'Shift saved successfully.', timer: 1500, showConfirmButton: false });
        closeShiftForm();
        setTimeout(() => location.reload(), 1000);
    })
    .catch(err => {
        Swal.fire({ icon: 'error', title: 'Error', text: 'Failed to save shift.' });
    });
});
</script>
<div id="shiftSlideOver" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm transition-opacity" onclick="closeShiftForm()"></div>
    <div class="absolute inset-y-0 right-0 w-full max-w-md bg-white shadow-2xl transform transition-transform translate-x-full duration-300 ease-in-out flex flex-col" id="shiftPanel">
        <div class="px-6 py-4 border-b border-slate-700 flex items-center justify-between bg-slate-800">
            <h3 class="text-sm font-semibold text-white" id="shiftFormTitle">Add Shift</h3>
            <button onclick="closeShiftForm()" class="p-1.5 rounded hover:bg-slate-700 text-slate-300">
                <i class="hgi-stroke hgi-cancel-01 text-base"></i>
            </button>
        </div>
        <div class="flex-1 overflow-y-auto p-6">
            <form id="shiftForm" method="POST" action="{{ route('shifts.store') }}">
                @csrf
                <input type="hidden" name="_method" value="POST" id="shiftMethod">
                <input type="hidden" name="shift_id" id="shiftId">
                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Shift Name</label>
                        <x-ui.input type="text" name="name" id="shiftName" class="w-full border border-gray-200 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-slate-300" placeholder="e.g., Morning Shift" required />
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Start Time</label>
                        <x-ui.input type="text" data-fp-time name="start_time" id="shiftStartTime" class="w-full border border-gray-200 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-slate-300" required />
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">End Time</label>
                        <x-ui.input type="text" data-fp-time name="end_time" id="shiftEndTime" class="w-full border border-gray-200 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-slate-300" required />
                    </div>
                    <div>
                        <label class="flex items-center gap-2">
                            <input type="checkbox" name="is_active" id="shiftIsActive" class="rounded border-gray-300 text-slate-800 focus:ring-slate-300">
                            <span class="text-xs text-gray-700">Active</span>
                        </label>
                    </div>
                </div>
                <div class="flex justify-end gap-2 mt-6">
                    <button type="button" onclick="closeShiftForm()" class="px-4 py-2 text-sm text-gray-700 border border-gray-200 hover:bg-gray-100 rounded">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-slate-800 text-white text-sm font-medium rounded hover:bg-slate-700">Save Shift</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function openShiftForm() {
    document.getElementById('shiftFormTitle').textContent = 'Add Shift';
    document.getElementById('shiftForm').action = '{{ route('shifts.store') }}';
    document.getElementById('shiftMethod').value = 'POST';
    document.getElementById('shiftId').value = '';
    document.getElementById('shiftName').value = '';
    document.getElementById('shiftStartTime').value = '';
    document.getElementById('shiftEndTime').value = '';
    document.getElementById('shiftIsActive').checked = true;
    
    const modal = document.getElementById('shiftSlideOver');
    const panel = document.getElementById('shiftPanel');
    modal.classList.remove('hidden');
    setTimeout(() => {
        panel.classList.remove('translate-x-full');
    }, 10);
    document.body.style.overflow = 'hidden';
}

function editShift(id, name, startTime, endTime, isActive) {
    document.getElementById('shiftFormTitle').textContent = 'Edit Shift';
    document.getElementById('shiftForm').action = '/shifts/' + id;
    document.getElementById('shiftMethod').value = 'PUT';
    document.getElementById('shiftId').value = id;
    document.getElementById('shiftName').value = name;
    document.getElementById('shiftStartTime').value = startTime;
    document.getElementById('shiftEndTime').value = endTime;
    document.getElementById('shiftIsActive').checked = isActive;
    
    const modal = document.getElementById('shiftSlideOver');
    const panel = document.getElementById('shiftPanel');
    modal.classList.remove('hidden');
    setTimeout(() => {
        panel.classList.remove('translate-x-full');
    }, 10);
    document.body.style.overflow = 'hidden';
}

function closeShiftForm() {
    const modal = document.getElementById('shiftSlideOver');
    const panel = document.getElementById('shiftPanel');
    panel.classList.add('translate-x-full');
    setTimeout(() => {
        modal.classList.add('hidden');
        document.body.style.overflow = '';
    }, 300);
}

function deleteShift(id) {
    Swal.fire({
        title: 'Delete Shift?',
        text: 'This action cannot be undone.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#0f172a',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Yes, Delete',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch('/shifts/' + id, {
                method: 'DELETE',
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
            })
            .then(r => r.json().catch(() => ({})))
            .then(data => {
                Swal.fire({ icon: 'success', title: 'Success', text: data.message || 'Shift deleted successfully.', timer: 1500, showConfirmButton: false });
                setTimeout(() => location.reload(), 1000);
            })
            .catch(err => {
                Swal.fire({ icon: 'error', title: 'Error', text: 'Failed to delete shift.' });
            });
        }
    });
}

document.getElementById('shiftForm')?.addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    fetch(this.action, {
        method: this.querySelector('#shiftMethod').value,
        body: formData,
        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
    })
    .then(r => r.json().catch(() => ({})))
    .then(data => {
        Swal.fire({ icon: 'success', title: 'Success', text: data.message || 'Shift saved successfully.', timer: 1500, showConfirmButton: false });
        closeShiftForm();
        setTimeout(() => location.reload(), 1000);
    })
    .catch(err => {
        Swal.fire({ icon: 'error', title: 'Error', text: 'Failed to save shift.' });
    });
});
</script>
@endpush
@endsection
