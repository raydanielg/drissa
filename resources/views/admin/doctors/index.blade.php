@extends('layouts.dashboard')

@section('title', 'Doctors - ' . config('app.name', 'Laravel'))
@section('page_title', 'Doctors Management')

@section('content')
<div class="space-y-5">
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
    <div class="bg-slate-800 rounded-md p-6 text-white">
        <div class="flex flex-wrap items-end justify-between gap-3">
            <div>
                <p class="text-[10px] uppercase tracking-wider text-slate-400 mb-1">Admin</p>
                <h1 class="text-xl font-bold">Doctors Management</h1>
                <p class="text-xs text-slate-400 mt-1">View and manage all doctors in the hospital</p>
            </div>
            <div class="flex items-center gap-2">
                <form method="GET" class="flex items-center gap-2">
                    <x-ui.input type="text" name="search" value="{{ request('search') }}" placeholder="Search doctor..." class="border border-slate-600 bg-slate-700 text-white placeholder-slate-400 rounded px-3 py-1.5 text-sm min-w-[200px] focus:ring-2 focus:ring-slate-400" />
                    <x-ui.select name="department_id" class="border border-slate-600 bg-slate-700 text-white rounded px-3 py-1.5 text-sm focus:ring-2 focus:ring-slate-400">
                        <option value="">All Departments</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                        @endforeach
                    </x-ui.select>
                    <button type="submit" class="bg-white/10 hover:bg-white/20 border border-slate-600 text-white text-xs font-medium px-3 py-1.5 rounded transition-colors">Filter</button>
                </form>
                <button type="button" onclick="openAddDoctorPanel()" class="inline-flex items-center gap-1.5 bg-white hover:bg-gray-100 text-slate-800 text-xs font-semibold px-3 py-1.5 rounded transition-colors">
                    <i class="hgi-stroke hgi-add-01 text-sm"></i>
                    Add Doctor
                </button>
            </div>
        </div>
    </div>

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
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
        @php
            $statConfig = [
                ['key' => 'total', 'label' => 'Total Doctors', 'icon' => 'hgi-user-multiple-02'],
                ['key' => 'active', 'label' => 'Active', 'icon' => 'hgi-checkmark-circle-02', 'border' => 'border-emerald-200', 'value' => 'text-emerald-600'],
                ['key' => 'inactive', 'label' => 'Inactive', 'icon' => 'hgi-cancel-circle', 'border' => 'border-red-200', 'value' => 'text-red-600'],
                ['key' => 'departments', 'label' => 'Departments', 'icon' => 'hgi-building-03'],
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
    <div class="bg-white rounded-md border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-50 text-[10px] uppercase text-gray-400">
                    <tr>
                        <th class="px-5 py-3">Doctor</th>
                        <th class="px-5 py-3">Department</th>
                        <th class="px-5 py-3">Phone</th>
                        <th class="px-5 py-3">Status</th>
                        <th class="px-5 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse ($doctors as $doctor)
                        <tr class="hover:bg-gray-50/50">
                            <td class="px-5 py-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-slate-200 flex items-center justify-center text-slate-600 text-xs font-bold">
                                        {{ strtoupper(substr($doctor->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="text-xs font-medium text-gray-900">{{ $doctor->name }}</div>
                                        <div class="text-[10px] text-gray-500">{{ $doctor->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-3 text-xs text-gray-600">{{ $doctor->department?->name ?? 'Not assigned' }}</td>
                            <td class="px-5 py-3 text-xs text-gray-600">{{ $doctor->phone ?? '-' }}</td>
                            <td class="px-5 py-3">
                                <span class="inline-flex items-center gap-1.5 text-[10px] font-medium text-gray-600">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $doctor->is_active ? 'bg-emerald-500' : 'bg-red-500' }}"></span>
                                    {{ $doctor->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td class="px-5 py-3">
                                <div class="flex items-center justify-end gap-1">
                                    <a href="{{ route('admin.doctors.show', $doctor) }}" class="p-1.5 text-slate-600 hover:bg-slate-100 rounded transition-colors" title="View">
                                        <i class="hgi-stroke hgi-view text-base"></i>
                                    </a>
                                    <button type="button" onclick="openEditDoctorPanel('{{ route('admin.doctors.update', $doctor) }}', {{ $doctor->id }})" class="p-1.5 text-slate-600 hover:bg-slate-100 rounded transition-colors" title="Edit">
                                        <i class="hgi-stroke hgi-pencil-edit-01 text-base"></i>
                                    </button>
                                    <form method="POST" action="{{ route('admin.doctors.toggle', $doctor) }}" data-ajax class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="p-1.5 {{ $doctor->is_active ? 'text-amber-600 hover:bg-amber-50' : 'text-emerald-600 hover:bg-emerald-50' }} rounded transition-colors" title="{{ $doctor->is_active ? 'Deactivate' : 'Activate' }}">
                                            <i class="hgi-stroke {{ $doctor->is_active ? 'hgi-unavailable' : 'hgi-tick-02' }} text-base"></i>
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.doctors.delete', $doctor) }}" data-ajax data-confirm="Delete this doctor? This action cannot be undone." class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 text-red-600 hover:bg-red-50 rounded transition-colors" title="Delete">
                                            <i class="hgi-stroke hgi-cancel-circle text-base"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-5 py-8 text-center text-gray-400 text-xs">
                            <div class="flex flex-col items-center gap-2">
                                <i class="hgi-stroke hgi-user-multiple-02 text-4xl text-gray-300"></i>
                                <p>No doctors found</p>
                            </div>
                        </td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-5 py-3 border-t border-gray-100">{{ $doctors->links() }}</div>
    </div>
</div>

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
                    <i class="hgi-stroke hgi-cancel-01 text-2xl"></i>
                </button>
            </div>
            <div class="flex-1 overflow-y-auto p-6" id="doctorSlideContent"></div>
        </div>
    </div>
</div>

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
