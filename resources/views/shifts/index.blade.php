@extends('layouts.dashboard')

@section('title', 'Shifts - ' . config('app.name', 'Laravel'))
@section('page_title', 'HR - Shifts')

@section('content')
<div class="space-y-6">
    @if (session('status'))
        <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm">{{ session('status') }}</div>
    @endif

    {{-- KPI Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl p-4 shadow-lg hover:shadow-xl transition-all hover:-translate-y-1 relative overflow-hidden">
            <div class="absolute top-0 right-0 w-16 h-16 bg-white/10 rounded-full -mr-8 -mt-8"></div>
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-2">
                    <svg class="w-6 h-6 text-blue-100" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div class="text-2xl font-bold text-white">{{ $stats['total'] ?? 0 }}</div>
                <div class="text-xs text-blue-100 font-medium">Total Shifts</div>
            </div>
        </div>
        <div class="bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-xl p-4 shadow-lg hover:shadow-xl transition-all hover:-translate-y-1 relative overflow-hidden">
            <div class="absolute top-0 right-0 w-16 h-16 bg-white/10 rounded-full -mr-8 -mt-8"></div>
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-2">
                    <svg class="w-6 h-6 text-emerald-100" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div class="text-2xl font-bold text-white">{{ $stats['active'] ?? 0 }}</div>
                <div class="text-xs text-emerald-100 font-medium">Active Shifts</div>
            </div>
        </div>
        <div class="bg-gradient-to-br from-gray-500 to-gray-600 rounded-xl p-4 shadow-lg hover:shadow-xl transition-all hover:-translate-y-1 relative overflow-hidden">
            <div class="absolute top-0 right-0 w-16 h-16 bg-white/10 rounded-full -mr-8 -mt-8"></div>
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-2">
                    <svg class="w-6 h-6 text-gray-100" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div class="text-2xl font-bold text-white">{{ $stats['inactive'] ?? 0 }}</div>
                <div class="text-xs text-gray-100 font-medium">Inactive Shifts</div>
            </div>
        </div>
    </div>

    {{-- Shifts Table --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <h2 class="text-sm font-semibold text-gray-900">Shifts Management</h2>
            <button type="button" onclick="openShiftForm()" class="flex items-center gap-1.5 px-3 py-1.5 bg-emerald-600 text-white text-xs font-medium rounded-lg hover:bg-emerald-700 transition-colors shadow-sm">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Add Shift
            </button>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                    <tr>
                        <th class="px-6 py-3">Name</th>
                        <th class="px-6 py-3">Start Time</th>
                        <th class="px-6 py-3">End Time</th>
                        <th class="px-6 py-3">Duration</th>
                        <th class="px-6 py-3">Status</th>
                        <th class="px-6 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($shifts as $shift)
                        <tr class="border-t border-gray-100 hover:bg-gray-50/50 transition-colors">
                            <td class="px-6 py-3">
                                <div class="flex items-center gap-2">
                                    <div class="w-8 h-8 rounded-full bg-gradient-to-br from-blue-500 to-blue-700 flex items-center justify-center text-white text-xs font-bold shadow-sm">
                                        {{ strtoupper(substr($shift->name, 0, 1)) }}
                                    </div>
                                    <span class="text-xs text-gray-900 font-medium">{{ $shift->name }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-3 text-gray-600 text-xs">{{ $shift->start_time }}</td>
                            <td class="px-6 py-3 text-gray-600 text-xs">{{ $shift->end_time }}</td>
                            <td class="px-6 py-3 text-gray-600 text-xs">{{ $shift->duration() }} min</td>
                            <td class="px-6 py-3">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-medium {{ $shift->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-700' }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $shift->is_active ? 'bg-emerald-500' : 'bg-gray-400' }}"></span>
                                    {{ $shift->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td class="px-6 py-3 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <button type="button" onclick="editShift({{ $shift->id }}, '{{ $shift->name }}', '{{ $shift->start_time }}', '{{ $shift->end_time }}', {{ $shift->is_active ? 'true' : 'false' }})" class="flex items-center gap-1.5 px-2 py-1.5 bg-blue-600 text-white text-xs font-medium rounded-lg hover:bg-blue-700 transition-colors shadow-sm">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        Edit
                                    </button>
                                    <button type="button" onclick="deleteShift({{ $shift->id }})" class="flex items-center gap-1.5 px-2 py-1.5 bg-red-600 text-white text-xs font-medium rounded-lg hover:bg-red-700 transition-colors shadow-sm">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        Delete
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-6 py-10 text-center text-gray-400">
                            <div class="flex flex-col items-center gap-2">
                                <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <p>No shifts found</p>
                            </div>
                        </td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-100">
            {{ $shifts->links() }}
        </div>
    </div>
</div>

{{-- Shift Form Slide-over --}}
<div id="shiftSlideOver" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm transition-opacity" onclick="closeShiftForm()"></div>
    <div class="absolute inset-y-0 right-0 w-full max-w-md bg-white shadow-2xl transform transition-transform translate-x-full duration-300 ease-in-out flex flex-col" id="shiftPanel">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between bg-gradient-to-r from-emerald-500 to-emerald-600">
            <h3 class="text-sm font-semibold text-white" id="shiftFormTitle">Add Shift</h3>
            <button onclick="closeShiftForm()" class="p-1.5 rounded-lg hover:bg-white/20 text-white">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
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
                        <input type="text" name="name" id="shiftName" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" placeholder="e.g., Morning Shift" required>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Start Time</label>
                        <input type="time" name="start_time" id="shiftStartTime" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" required>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">End Time</label>
                        <input type="time" name="end_time" id="shiftEndTime" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" required>
                    </div>
                    <div>
                        <label class="flex items-center gap-2">
                            <input type="checkbox" name="is_active" id="shiftIsActive" class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500">
                            <span class="text-xs text-gray-700">Active</span>
                        </label>
                    </div>
                </div>
                <div class="flex justify-end gap-2 mt-6">
                    <button type="button" onclick="closeShiftForm()" class="px-4 py-2 text-sm text-gray-600 hover:bg-gray-100 rounded-lg">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700">Save Shift</button>
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
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#d33',
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
