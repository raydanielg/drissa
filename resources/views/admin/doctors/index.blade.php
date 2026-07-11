@extends('layouts.dashboard')

@section('title', 'Doctors - ' . config('app.name', 'Laravel'))
@section('page_title', 'Doctors Management')

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-lg font-bold text-gray-900">Doctors Management</h2>
            <p class="text-sm text-gray-500">View and manage all doctors in the hospital</p>
        </div>
        <div class="flex items-center gap-2">
            <form method="GET" class="flex items-center gap-2">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search doctor..." class="border border-gray-200 rounded-lg px-3 py-1.5 text-sm min-w-[200px]">
                <select name="department_id" class="border border-gray-200 rounded-lg px-3 py-1.5 text-sm">
                    <option value="">All Departments</option>
                    @foreach($departments as $dept)
                        <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                    @endforeach
                </select>
                <button type="submit" class="bg-gray-100 text-gray-700 text-xs font-medium px-3 py-1.5 rounded-lg hover:bg-gray-200">Filter</button>
            </form>
            <button type="button" onclick="openAddDoctorPanel()" class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium px-4 py-2 rounded-lg shadow-sm hover:shadow transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Add Doctor
            </button>
        </div>
    </div>

    {{-- KPI Cards --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        @php
            $statConfig = [
                ['key' => 'total', 'label' => 'Total Doctors', 'icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z', 'from' => 'blue-500', 'to' => 'blue-700', 'border' => 'blue-400', 'text' => 'blue-100', 'sub' => 'blue-200'],
                ['key' => 'active', 'label' => 'Active', 'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z', 'from' => 'emerald-500', 'to' => 'emerald-700', 'border' => 'emerald-400', 'text' => 'emerald-100', 'sub' => 'emerald-200'],
                ['key' => 'inactive', 'label' => 'Inactive', 'icon' => 'M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z', 'from' => 'red-500', 'to' => 'red-700', 'border' => 'red-400', 'text' => 'red-100', 'sub' => 'red-200'],
                ['key' => 'departments', 'label' => 'Departments', 'icon' => 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4', 'from' => 'purple-500', 'to' => 'purple-700', 'border' => 'purple-400', 'text' => 'purple-100', 'sub' => 'purple-200'],
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

    {{-- Doctors Table --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                    <tr>
                        <th class="px-6 py-3">Doctor</th>
                        <th class="px-6 py-3">Department</th>
                        <th class="px-6 py-3">Phone</th>
                        <th class="px-6 py-3">Status</th>
                        <th class="px-6 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($doctors as $doctor)
                        <tr class="group hover:bg-emerald-50/40 transition-colors">
                            <td class="px-6 py-3.5">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-emerald-500 to-emerald-700 flex items-center justify-center text-white text-sm font-bold shadow-sm">
                                        {{ strtoupper(substr($doctor->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="font-medium text-gray-900">{{ $doctor->name }}</div>
                                        <div class="text-xs text-gray-500">{{ $doctor->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-3.5 text-gray-700">{{ $doctor->department?->name ?? 'Not assigned' }}</td>
                            <td class="px-6 py-3.5 text-gray-700">{{ $doctor->phone ?? '-' }}</td>
                            <td class="px-6 py-3.5">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium {{ $doctor->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700' }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $doctor->is_active ? 'bg-emerald-500' : 'bg-red-500' }}"></span>
                                    {{ $doctor->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td class="px-6 py-3.5">
                                <div class="flex items-center justify-end gap-1">
                                    <a href="{{ route('admin.doctors.show', $doctor) }}" class="action-icon group/icon relative p-2 text-blue-600 hover:bg-blue-100 rounded-lg transition-colors" title="View Profile">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        <span class="absolute -top-8 left-1/2 -translate-x-1/2 px-2 py-1 bg-gray-900 text-white text-[10px] rounded opacity-0 group-hover/icon:opacity-100 transition-opacity whitespace-nowrap pointer-events-none">View</span>
                                    </a>
                                    <button type="button" onclick="openEditDoctorPanel('{{ route('admin.doctors.update', $doctor) }}', {{ $doctor->id }})" class="action-icon group/icon relative p-2 text-emerald-600 hover:bg-emerald-100 rounded-lg transition-colors" title="Edit">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.43-9.525l-9.17 9.17a2 2 0 00-.586 1.414V17a1 1 0 001 1h2.828a2 2 0 001.414-.586l9.17-9.17a2 2 0 000-2.828l-1.414-1.414a2 2 0 00-2.828 0z"/></svg>
                                        <span class="absolute -top-8 left-1/2 -translate-x-1/2 px-2 py-1 bg-gray-900 text-white text-[10px] rounded opacity-0 group-hover/icon:opacity-100 transition-opacity whitespace-nowrap pointer-events-none">Edit</span>
                                    </button>
                                    <form method="POST" action="{{ route('admin.doctors.toggle', $doctor) }}" data-ajax class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="action-icon group/icon relative p-2 {{ $doctor->is_active ? 'text-amber-600 hover:bg-amber-100' : 'text-emerald-600 hover:bg-emerald-100' }} rounded-lg transition-colors" title="{{ $doctor->is_active ? 'Deactivate' : 'Activate' }}">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $doctor->is_active ? 'M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636' : 'M5 13l4 4L19 7' }}"/></svg>
                                            <span class="absolute -top-8 left-1/2 -translate-x-1/2 px-2 py-1 bg-gray-900 text-white text-[10px] rounded opacity-0 group-hover/icon:opacity-100 transition-opacity whitespace-nowrap pointer-events-none">{{ $doctor->is_active ? 'Deactivate' : 'Activate' }}</span>
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.doctors.delete', $doctor) }}" data-ajax data-confirm="Delete this doctor? This action cannot be undone." class="inline">
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
                                <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                <p>No doctors found</p>
                            </div>
                        </td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-100">{{ $doctors->links() }}</div>
    </div>
</div>

{{-- Slide-over Panel --}}
<div id="doctorSlideOver" class="fixed inset-0 z-50 hidden" aria-hidden="true">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm transition-opacity opacity-0" id="doctorBackdrop" onclick="closeDoctorSlideOver()"></div>
    <div class="absolute inset-y-0 right-0 w-full max-w-lg transform translate-x-full transition-transform duration-300 ease-out" id="doctorPanel">
        <div class="h-full bg-white shadow-2xl flex flex-col">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between bg-gray-50/50">
                <div>
                    <h3 class="text-lg font-bold text-gray-900" id="doctorSlideTitle">Add Doctor</h3>
                    <p class="text-xs text-gray-500" id="doctorSlideSubtitle">Register a new doctor</p>
                </div>
                <button onclick="closeDoctorSlideOver()" class="text-gray-400 hover:text-gray-600 p-1 rounded-lg hover:bg-gray-100 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="flex-1 overflow-y-auto p-6" id="doctorSlideContent"></div>
        </div>
    </div>
</div>

<template id="doctorFormTemplate">
    <form id="doctorForm" method="POST" action="{{ route('admin.doctors.store') }}" class="space-y-4">
        @csrf
        <div class="grid grid-cols-2 gap-4">
            <div class="col-span-2">
                <label class="block text-xs font-medium text-gray-700 mb-1">Full Name <span class="text-red-500">*</span></label>
                <input type="text" name="name" id="doctor_name" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" required>
            </div>
            <div class="col-span-2">
                <label class="block text-xs font-medium text-gray-700 mb-1">Email <span class="text-red-500">*</span></label>
                <input type="email" name="email" id="doctor_email" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" required>
            </div>
            <div class="col-span-2 sm:col-span-1">
                <label class="block text-xs font-medium text-gray-700 mb-1">Phone</label>
                <input type="text" name="phone" id="doctor_phone" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
            </div>
            <div class="col-span-2 sm:col-span-1">
                <label class="block text-xs font-medium text-gray-700 mb-1">Department</label>
                <select name="department_id" id="doctor_department_id" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 bg-white">
                    <option value="">Select department</option>
                </select>
            </div>
            <div class="col-span-2">
                <label class="block text-xs font-medium text-gray-700 mb-1">Password <span class="text-red-500">*</span></label>
                <input type="password" name="password" id="doctor_password" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" required minlength="8">
                <p class="text-[10px] text-gray-400 mt-1">Minimum 8 characters</p>
            </div>
        </div>
        <div class="flex justify-end gap-2 pt-4 border-t border-gray-100 mt-4">
            <button type="button" onclick="closeDoctorSlideOver()" class="px-4 py-2 text-sm text-gray-600 hover:bg-gray-100 rounded-lg">Cancel</button>
            <button type="submit" class="px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700">Add Doctor</button>
        </div>
    </form>
</template>

@push('scripts')
<script>
    const doctorSlide = document.getElementById('doctorSlideOver');
    const doctorBackdrop = document.getElementById('doctorBackdrop');
    const doctorPanel = document.getElementById('doctorPanel');
    const doctorContent = document.getElementById('doctorSlideContent');

    function openDoctorSlideOver(title, subtitle, html) {
        doctorSlide.classList.remove('hidden');
        document.getElementById('doctorSlideTitle').textContent = title;
        document.getElementById('doctorSlideSubtitle').textContent = subtitle;
        doctorContent.innerHTML = html;
        setTimeout(() => {
            doctorBackdrop.classList.remove('opacity-0');
            doctorPanel.classList.remove('translate-x-full');
        }, 10);
        document.body.style.overflow = 'hidden';
    }

    function closeDoctorSlideOver() {
        doctorBackdrop.classList.add('opacity-0');
        doctorPanel.classList.add('translate-x-full');
        setTimeout(() => {
            doctorSlide.classList.add('hidden');
            doctorContent.innerHTML = '';
            document.body.style.overflow = '';
        }, 300);
    }

    function populateDepartmentSelect(departments) {
        const select = document.getElementById('doctor_department_id');
        select.innerHTML = '<option value="">Select department</option>' +
            departments.map(d => `<option value="${d.id}">${d.name}</option>`).join('');
    }

    function attachDoctorForm(action, successMessage) {
        const form = document.getElementById('doctorForm');
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
                closeDoctorSlideOver();
                setTimeout(() => location.reload(), 1000);
            })
            .catch(err => {
                Swal.fire({ icon: 'error', title: 'Error', text: 'Failed to save doctor.' });
            });
        });
    }

    function resetDoctorForm() {
        document.getElementById('doctor_name').value = '';
        document.getElementById('doctor_email').value = '';
        document.getElementById('doctor_phone').value = '';
        document.getElementById('doctor_department_id').value = '';
        document.getElementById('doctor_password').value = '';
    }

    async function openAddDoctorPanel() {
        const html = document.getElementById('doctorFormTemplate').innerHTML;
        openDoctorSlideOver('Add Doctor', 'Register a new doctor', html);
        try {
            const res = await fetch('{{ route("admin.doctors.index") }}', { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } });
            const data = await res.json();
            populateDepartmentSelect(data.departments);
            resetDoctorForm();
            attachDoctorForm('{{ route("admin.doctors.store") }}', 'Doctor added successfully.');
        } catch (err) {
            doctorContent.innerHTML = '<div class="text-center text-red-600 py-8">Failed to load form data.</div>';
        }
    }

    async function openEditDoctorPanel(url, doctorId) {
        const html = document.getElementById('doctorFormTemplate').innerHTML;
        openDoctorSlideOver('Edit Doctor', 'Update doctor details', html);
        try {
            const res = await fetch(url, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } });
            const data = await res.json();
            populateDepartmentSelect(data.departments);
            const d = data.doctor;
            document.getElementById('doctor_name').value = d.name;
            document.getElementById('doctor_email').value = d.email;
            document.getElementById('doctor_phone').value = d.phone || '';
            document.getElementById('doctor_department_id').value = d.department_id || '';
            document.getElementById('doctor_password').value = '';
            document.getElementById('doctor_password').removeAttribute('required');
            document.getElementById('doctor_password').removeAttribute('minlength');
            attachDoctorForm(url, 'Doctor updated successfully.');
        } catch (err) {
            doctorContent.innerHTML = '<div class="text-center text-red-600 py-8">Failed to load doctor details.</div>';
        }
    }
</script>
@endpush
@endsection
