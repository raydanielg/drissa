@extends('layouts.dashboard')

@section('title', 'Patients - ' . config('app.name', 'Laravel'))
@section('page_title', 'Patients')

@section('content')
<div class="space-y-5">
    <script>
    const slideOver = document.getElementById('patientSlideOver');
    const slideOverBackdrop = document.getElementById('slideOverBackdrop');
    const slideOverPanel = document.getElementById('slideOverPanel');
    const slideOverContent = document.getElementById('slideOverContent');
    const slideOverTitle = document.getElementById('slideOverTitle');
    const slideOverSubtitle = document.getElementById('slideOverSubtitle');

    function openPatientSlideOver(title, subtitle, html) {
        slideOver.classList.remove('hidden');
        slideOverTitle.textContent = title;
        slideOverSubtitle.textContent = subtitle;
        slideOverContent.innerHTML = html;
        setTimeout(() => {
            slideOverBackdrop.classList.remove('opacity-0');
            slideOverPanel.classList.remove('translate-x-full');
        }, 10);
        document.body.style.overflow = 'hidden';
    }

    function closePatientSlideOver() {
        slideOverBackdrop.classList.add('opacity-0');
        slideOverPanel.classList.add('translate-x-full');
        setTimeout(() => {
            slideOver.classList.add('hidden');
            slideOverContent.innerHTML = '';
            document.body.style.overflow = '';
        }, 300);
    }

    function openRegisterPatientPanel() {
        const html = document.getElementById('registerPatientTemplate').innerHTML;
        openPatientSlideOver('Register New Patient', 'Enter patient details to register', html);
        attachFormHandler('registerPatientForm', '{{ route("reception.patients.store") }}', 'Patient registered successfully.');
    }

    async function openEditPatientPanel(url) {
        const html = document.getElementById('editPatientTemplate').innerHTML;
        openPatientSlideOver('Edit Patient', 'Update patient information', html);
        try {
            const res = await fetch(url, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } });
            const data = await res.json();
            const patient = data.patient;
            document.getElementById('editPatientForm').action = url.replace('/edit', '');
            document.getElementById('edit_first_name').value = patient.first_name;
            document.getElementById('edit_last_name').value = patient.last_name;
            document.getElementById('edit_date_of_birth').value = patient.date_of_birth ? patient.date_of_birth.split(' ')[0] : '';
            document.getElementById('edit_gender').value = patient.gender;
            document.getElementById('edit_phone').value = patient.phone || '';
            document.getElementById('edit_national_id').value = patient.national_id || '';
            document.getElementById('edit_blood_group').value = patient.blood_group || '';
            document.getElementById('edit_address').value = patient.address || '';
            document.getElementById('edit_allergies').value = patient.allergies || '';
            attachFormHandler('editPatientForm', document.getElementById('editPatientForm').action, 'Patient updated successfully.', true);
        } catch (err) {
            slideOverContent.innerHTML = '<div class="text-center text-red-600 py-8">Failed to load patient details.</div>';
        }
    }

    function attachFormHandler(formId, action, successMessage, reload = true) {
        const form = document.getElementById(formId) || document.querySelector('form[action="' + action + '"]');
        if (!form) return;
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
                form.reset();
                closePatientSlideOver();
                setTimeout(() => location.reload(), reload ? 1200 : 800);
            })
            .catch(err => {
                Swal.fire({ icon: 'error', title: 'Error', text: 'Something went wrong. Please try again.' });
            });
        });
    }
</script>
    <div class="bg-slate-800 rounded-md p-6 text-white">
        <div class="flex flex-wrap items-end justify-between gap-3">
            <div>
                <p class="text-[10px] uppercase tracking-wider text-slate-400 mb-1">Registry</p>
                <h1 class="text-xl font-bold">Patient Registry</h1>
                <p class="text-xs text-slate-400 mt-1">Manage and search all registered patients.</p>
            </div>
            <button onclick="openRegisterPatientPanel()" class="inline-flex items-center gap-1.5 bg-white hover:bg-gray-100 text-slate-800 text-xs font-semibold px-3 py-1.5 rounded transition-colors">
                <i class="hgi-stroke hgi-user-add-01 text-sm"></i>
                Register Patient
            </button>
        </div>
    </div>

<div class="bg-white rounded-md border border-gray-200 overflow-hidden">
    <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between gap-3">
        <h2 class="text-sm font-bold text-gray-900">All Patients</h2>
        <form method="GET" action="{{ route('patients.index') }}" class="flex gap-2">
            <x-ui.input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name, MRN, phone..." class="border border-gray-200 rounded px-3 py-1.5 text-sm min-w-[250px] focus:ring-2 focus:ring-slate-300 focus:outline-none" />
            <button type="submit" class="bg-slate-800 text-white text-xs font-medium px-3 py-1.5 rounded hover:bg-slate-700">Search</button>
        </form>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left">
            <thead class="bg-gray-50 text-[10px] uppercase text-gray-400">
                <tr>
                    <th class="px-5 py-3">MRN</th>
                    <th class="px-5 py-3">Name</th>
                    <th class="px-5 py-3">Gender</th>
                    <th class="px-5 py-3">Phone</th>
                    <th class="px-5 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($patients as $patient)
                    <tr class="border-t border-gray-50 hover:bg-gray-50/50">
                        <td class="px-5 py-3 text-xs font-semibold text-gray-900">{{ $patient->mrn }}</td>
                        <td class="px-5 py-3 text-sm font-medium text-gray-900">{{ $patient->fullName() }}</td>
                        <td class="px-5 py-3 text-xs text-gray-500 capitalize">{{ $patient->gender }}</td>
                        <td class="px-5 py-3 text-xs text-gray-500">{{ $patient->phone ?? '-' }}</td>
                        <td class="px-5 py-3">
                            <div class="flex items-center justify-end gap-1">
                                <a href="{{ route('patients.show', $patient) }}" class="action-icon group/icon relative p-1.5 text-slate-600 hover:bg-gray-100 rounded transition-colors" title="View">
                                    <i class="hgi-stroke hgi-view text-base"></i>
                                    <span class="absolute -top-8 left-1/2 -translate-x-1/2 px-2 py-1 bg-gray-900 text-white text-[10px] rounded opacity-0 group-hover/icon:opacity-100 transition-opacity whitespace-nowrap pointer-events-none">View</span>
                                </a>
                                <button type="button" onclick="openEditPatientPanel('{{ route('patients.edit', $patient) }}')" class="action-icon group/icon relative p-1.5 text-gray-500 hover:bg-gray-100 rounded transition-colors" title="Edit">
                                    <i class="hgi-stroke hgi-pencil-edit-01 text-base"></i>
                                    <span class="absolute -top-8 left-1/2 -translate-x-1/2 px-2 py-1 bg-gray-900 text-white text-[10px] rounded opacity-0 group-hover/icon:opacity-100 transition-opacity whitespace-nowrap pointer-events-none">Edit</span>
                                </button>
                                <form method="POST" action="{{ route('patients.destroy', $patient) }}" data-ajax data-confirm="Delete this patient record?" class="inline">
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
                    <tr><td colspan="5" class="px-5 py-8 text-center text-gray-400 text-xs">No patients found</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-5 py-3 border-t border-gray-100">{{ $patients->links() }}</div>
</div>

<script>
    const slideOver = document.getElementById('patientSlideOver');
    const slideOverBackdrop = document.getElementById('slideOverBackdrop');
    const slideOverPanel = document.getElementById('slideOverPanel');
    const slideOverContent = document.getElementById('slideOverContent');
    const slideOverTitle = document.getElementById('slideOverTitle');
    const slideOverSubtitle = document.getElementById('slideOverSubtitle');

    function openPatientSlideOver(title, subtitle, html) {
        slideOver.classList.remove('hidden');
        slideOverTitle.textContent = title;
        slideOverSubtitle.textContent = subtitle;
        slideOverContent.innerHTML = html;
        setTimeout(() => {
            slideOverBackdrop.classList.remove('opacity-0');
            slideOverPanel.classList.remove('translate-x-full');
        }, 10);
        document.body.style.overflow = 'hidden';
    }

    function closePatientSlideOver() {
        slideOverBackdrop.classList.add('opacity-0');
        slideOverPanel.classList.add('translate-x-full');
        setTimeout(() => {
            slideOver.classList.add('hidden');
            slideOverContent.innerHTML = '';
            document.body.style.overflow = '';
        }, 300);
    }

    function openRegisterPatientPanel() {
        const html = document.getElementById('registerPatientTemplate').innerHTML;
        openPatientSlideOver('Register New Patient', 'Enter patient details to register', html);
        attachFormHandler('registerPatientForm', '{{ route("reception.patients.store") }}', 'Patient registered successfully.');
    }

    async function openEditPatientPanel(url) {
        const html = document.getElementById('editPatientTemplate').innerHTML;
        openPatientSlideOver('Edit Patient', 'Update patient information', html);
        try {
            const res = await fetch(url, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } });
            const data = await res.json();
            const patient = data.patient;
            document.getElementById('editPatientForm').action = url.replace('/edit', '');
            document.getElementById('edit_first_name').value = patient.first_name;
            document.getElementById('edit_last_name').value = patient.last_name;
            document.getElementById('edit_date_of_birth').value = patient.date_of_birth ? patient.date_of_birth.split(' ')[0] : '';
            document.getElementById('edit_gender').value = patient.gender;
            document.getElementById('edit_phone').value = patient.phone || '';
            document.getElementById('edit_national_id').value = patient.national_id || '';
            document.getElementById('edit_blood_group').value = patient.blood_group || '';
            document.getElementById('edit_address').value = patient.address || '';
            document.getElementById('edit_allergies').value = patient.allergies || '';
            attachFormHandler('editPatientForm', document.getElementById('editPatientForm').action, 'Patient updated successfully.', true);
        } catch (err) {
            slideOverContent.innerHTML = '<div class="text-center text-red-600 py-8">Failed to load patient details.</div>';
        }
    }

    function attachFormHandler(formId, action, successMessage, reload = true) {
        const form = document.getElementById(formId) || document.querySelector('form[action="' + action + '"]');
        if (!form) return;
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
                form.reset();
                closePatientSlideOver();
                setTimeout(() => location.reload(), reload ? 1200 : 800);
            })
            .catch(err => {
                Swal.fire({ icon: 'error', title: 'Error', text: 'Something went wrong. Please try again.' });
            });
        });
    }
</script>
<div id="patientSlideOver" class="fixed inset-0 z-50 hidden" aria-hidden="true">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm transition-opacity opacity-0" id="slideOverBackdrop" onclick="closePatientSlideOver()"></div>
    <div class="absolute inset-y-0 right-0 w-full max-w-md transform translate-x-full transition-transform duration-300 ease-out" id="slideOverPanel">
        <div class="h-full bg-white shadow-2xl flex flex-col">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between bg-gray-50/50">
                <div>
                    <h3 class="text-lg font-bold text-gray-900" id="slideOverTitle">Register New Patient</h3>
                    <p class="text-xs text-gray-500" id="slideOverSubtitle">Fill patient details below</p>
                </div>
                <button onclick="closePatientSlideOver()" class="text-gray-400 hover:text-gray-600 p-1 rounded-lg hover:bg-gray-100 transition-colors">
                    <i class="hgi-stroke hgi-cancel-01 text-2xl"></i>
                </button>
            </div>
            <div class="flex-1 overflow-y-auto p-6" id="slideOverContent">
                <script>
    const slideOver = document.getElementById('patientSlideOver');
    const slideOverBackdrop = document.getElementById('slideOverBackdrop');
    const slideOverPanel = document.getElementById('slideOverPanel');
    const slideOverContent = document.getElementById('slideOverContent');
    const slideOverTitle = document.getElementById('slideOverTitle');
    const slideOverSubtitle = document.getElementById('slideOverSubtitle');

    function openPatientSlideOver(title, subtitle, html) {
        slideOver.classList.remove('hidden');
        slideOverTitle.textContent = title;
        slideOverSubtitle.textContent = subtitle;
        slideOverContent.innerHTML = html;
        setTimeout(() => {
            slideOverBackdrop.classList.remove('opacity-0');
            slideOverPanel.classList.remove('translate-x-full');
        }, 10);
        document.body.style.overflow = 'hidden';
    }

    function closePatientSlideOver() {
        slideOverBackdrop.classList.add('opacity-0');
        slideOverPanel.classList.add('translate-x-full');
        setTimeout(() => {
            slideOver.classList.add('hidden');
            slideOverContent.innerHTML = '';
            document.body.style.overflow = '';
        }, 300);
    }

    function openRegisterPatientPanel() {
        const html = document.getElementById('registerPatientTemplate').innerHTML;
        openPatientSlideOver('Register New Patient', 'Enter patient details to register', html);
        attachFormHandler('registerPatientForm', '{{ route("reception.patients.store") }}', 'Patient registered successfully.');
    }

    async function openEditPatientPanel(url) {
        const html = document.getElementById('editPatientTemplate').innerHTML;
        openPatientSlideOver('Edit Patient', 'Update patient information', html);
        try {
            const res = await fetch(url, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } });
            const data = await res.json();
            const patient = data.patient;
            document.getElementById('editPatientForm').action = url.replace('/edit', '');
            document.getElementById('edit_first_name').value = patient.first_name;
            document.getElementById('edit_last_name').value = patient.last_name;
            document.getElementById('edit_date_of_birth').value = patient.date_of_birth ? patient.date_of_birth.split(' ')[0] : '';
            document.getElementById('edit_gender').value = patient.gender;
            document.getElementById('edit_phone').value = patient.phone || '';
            document.getElementById('edit_national_id').value = patient.national_id || '';
            document.getElementById('edit_blood_group').value = patient.blood_group || '';
            document.getElementById('edit_address').value = patient.address || '';
            document.getElementById('edit_allergies').value = patient.allergies || '';
            attachFormHandler('editPatientForm', document.getElementById('editPatientForm').action, 'Patient updated successfully.', true);
        } catch (err) {
            slideOverContent.innerHTML = '<div class="text-center text-red-600 py-8">Failed to load patient details.</div>';
        }
    }

    function attachFormHandler(formId, action, successMessage, reload = true) {
        const form = document.getElementById(formId) || document.querySelector('form[action="' + action + '"]');
        if (!form) return;
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
                form.reset();
                closePatientSlideOver();
                setTimeout(() => location.reload(), reload ? 1200 : 800);
            })
            .catch(err => {
                Swal.fire({ icon: 'error', title: 'Error', text: 'Something went wrong. Please try again.' });
            });
        });
    }
</script>
            </div>
        </div>
    </div>
</div>
</div>

<script>
    const slideOver = document.getElementById('patientSlideOver');
    const slideOverBackdrop = document.getElementById('slideOverBackdrop');
    const slideOverPanel = document.getElementById('slideOverPanel');
    const slideOverContent = document.getElementById('slideOverContent');
    const slideOverTitle = document.getElementById('slideOverTitle');
    const slideOverSubtitle = document.getElementById('slideOverSubtitle');

    function openPatientSlideOver(title, subtitle, html) {
        slideOver.classList.remove('hidden');
        slideOverTitle.textContent = title;
        slideOverSubtitle.textContent = subtitle;
        slideOverContent.innerHTML = html;
        setTimeout(() => {
            slideOverBackdrop.classList.remove('opacity-0');
            slideOverPanel.classList.remove('translate-x-full');
        }, 10);
        document.body.style.overflow = 'hidden';
    }

    function closePatientSlideOver() {
        slideOverBackdrop.classList.add('opacity-0');
        slideOverPanel.classList.add('translate-x-full');
        setTimeout(() => {
            slideOver.classList.add('hidden');
            slideOverContent.innerHTML = '';
            document.body.style.overflow = '';
        }, 300);
    }

    function openRegisterPatientPanel() {
        const html = document.getElementById('registerPatientTemplate').innerHTML;
        openPatientSlideOver('Register New Patient', 'Enter patient details to register', html);
        attachFormHandler('registerPatientForm', '{{ route("reception.patients.store") }}', 'Patient registered successfully.');
    }

    async function openEditPatientPanel(url) {
        const html = document.getElementById('editPatientTemplate').innerHTML;
        openPatientSlideOver('Edit Patient', 'Update patient information', html);
        try {
            const res = await fetch(url, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } });
            const data = await res.json();
            const patient = data.patient;
            document.getElementById('editPatientForm').action = url.replace('/edit', '');
            document.getElementById('edit_first_name').value = patient.first_name;
            document.getElementById('edit_last_name').value = patient.last_name;
            document.getElementById('edit_date_of_birth').value = patient.date_of_birth ? patient.date_of_birth.split(' ')[0] : '';
            document.getElementById('edit_gender').value = patient.gender;
            document.getElementById('edit_phone').value = patient.phone || '';
            document.getElementById('edit_national_id').value = patient.national_id || '';
            document.getElementById('edit_blood_group').value = patient.blood_group || '';
            document.getElementById('edit_address').value = patient.address || '';
            document.getElementById('edit_allergies').value = patient.allergies || '';
            attachFormHandler('editPatientForm', document.getElementById('editPatientForm').action, 'Patient updated successfully.', true);
        } catch (err) {
            slideOverContent.innerHTML = '<div class="text-center text-red-600 py-8">Failed to load patient details.</div>';
        }
    }

    function attachFormHandler(formId, action, successMessage, reload = true) {
        const form = document.getElementById(formId) || document.querySelector('form[action="' + action + '"]');
        if (!form) return;
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
                form.reset();
                closePatientSlideOver();
                setTimeout(() => location.reload(), reload ? 1200 : 800);
            })
            .catch(err => {
                Swal.fire({ icon: 'error', title: 'Error', text: 'Something went wrong. Please try again.' });
            });
        });
    }
</script>
<script>
    const slideOver = document.getElementById('patientSlideOver');
    const slideOverBackdrop = document.getElementById('slideOverBackdrop');
    const slideOverPanel = document.getElementById('slideOverPanel');
    const slideOverContent = document.getElementById('slideOverContent');
    const slideOverTitle = document.getElementById('slideOverTitle');
    const slideOverSubtitle = document.getElementById('slideOverSubtitle');

    function openPatientSlideOver(title, subtitle, html) {
        slideOver.classList.remove('hidden');
        slideOverTitle.textContent = title;
        slideOverSubtitle.textContent = subtitle;
        slideOverContent.innerHTML = html;
        setTimeout(() => {
            slideOverBackdrop.classList.remove('opacity-0');
            slideOverPanel.classList.remove('translate-x-full');
        }, 10);
        document.body.style.overflow = 'hidden';
    }

    function closePatientSlideOver() {
        slideOverBackdrop.classList.add('opacity-0');
        slideOverPanel.classList.add('translate-x-full');
        setTimeout(() => {
            slideOver.classList.add('hidden');
            slideOverContent.innerHTML = '';
            document.body.style.overflow = '';
        }, 300);
    }

    function openRegisterPatientPanel() {
        const html = document.getElementById('registerPatientTemplate').innerHTML;
        openPatientSlideOver('Register New Patient', 'Enter patient details to register', html);
        attachFormHandler('registerPatientForm', '{{ route("reception.patients.store") }}', 'Patient registered successfully.');
    }

    async function openEditPatientPanel(url) {
        const html = document.getElementById('editPatientTemplate').innerHTML;
        openPatientSlideOver('Edit Patient', 'Update patient information', html);
        try {
            const res = await fetch(url, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } });
            const data = await res.json();
            const patient = data.patient;
            document.getElementById('editPatientForm').action = url.replace('/edit', '');
            document.getElementById('edit_first_name').value = patient.first_name;
            document.getElementById('edit_last_name').value = patient.last_name;
            document.getElementById('edit_date_of_birth').value = patient.date_of_birth ? patient.date_of_birth.split(' ')[0] : '';
            document.getElementById('edit_gender').value = patient.gender;
            document.getElementById('edit_phone').value = patient.phone || '';
            document.getElementById('edit_national_id').value = patient.national_id || '';
            document.getElementById('edit_blood_group').value = patient.blood_group || '';
            document.getElementById('edit_address').value = patient.address || '';
            document.getElementById('edit_allergies').value = patient.allergies || '';
            attachFormHandler('editPatientForm', document.getElementById('editPatientForm').action, 'Patient updated successfully.', true);
        } catch (err) {
            slideOverContent.innerHTML = '<div class="text-center text-red-600 py-8">Failed to load patient details.</div>';
        }
    }

    function attachFormHandler(formId, action, successMessage, reload = true) {
        const form = document.getElementById(formId) || document.querySelector('form[action="' + action + '"]');
        if (!form) return;
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
                form.reset();
                closePatientSlideOver();
                setTimeout(() => location.reload(), reload ? 1200 : 800);
            })
            .catch(err => {
                Swal.fire({ icon: 'error', title: 'Error', text: 'Something went wrong. Please try again.' });
            });
        });
    }
</script>

<script>
    const slideOver = document.getElementById('patientSlideOver');
    const slideOverBackdrop = document.getElementById('slideOverBackdrop');
    const slideOverPanel = document.getElementById('slideOverPanel');
    const slideOverContent = document.getElementById('slideOverContent');
    const slideOverTitle = document.getElementById('slideOverTitle');
    const slideOverSubtitle = document.getElementById('slideOverSubtitle');

    function openPatientSlideOver(title, subtitle, html) {
        slideOver.classList.remove('hidden');
        slideOverTitle.textContent = title;
        slideOverSubtitle.textContent = subtitle;
        slideOverContent.innerHTML = html;
        setTimeout(() => {
            slideOverBackdrop.classList.remove('opacity-0');
            slideOverPanel.classList.remove('translate-x-full');
        }, 10);
        document.body.style.overflow = 'hidden';
    }

    function closePatientSlideOver() {
        slideOverBackdrop.classList.add('opacity-0');
        slideOverPanel.classList.add('translate-x-full');
        setTimeout(() => {
            slideOver.classList.add('hidden');
            slideOverContent.innerHTML = '';
            document.body.style.overflow = '';
        }, 300);
    }

    function openRegisterPatientPanel() {
        const html = document.getElementById('registerPatientTemplate').innerHTML;
        openPatientSlideOver('Register New Patient', 'Enter patient details to register', html);
        attachFormHandler('registerPatientForm', '{{ route("reception.patients.store") }}', 'Patient registered successfully.');
    }

    async function openEditPatientPanel(url) {
        const html = document.getElementById('editPatientTemplate').innerHTML;
        openPatientSlideOver('Edit Patient', 'Update patient information', html);
        try {
            const res = await fetch(url, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } });
            const data = await res.json();
            const patient = data.patient;
            document.getElementById('editPatientForm').action = url.replace('/edit', '');
            document.getElementById('edit_first_name').value = patient.first_name;
            document.getElementById('edit_last_name').value = patient.last_name;
            document.getElementById('edit_date_of_birth').value = patient.date_of_birth ? patient.date_of_birth.split(' ')[0] : '';
            document.getElementById('edit_gender').value = patient.gender;
            document.getElementById('edit_phone').value = patient.phone || '';
            document.getElementById('edit_national_id').value = patient.national_id || '';
            document.getElementById('edit_blood_group').value = patient.blood_group || '';
            document.getElementById('edit_address').value = patient.address || '';
            document.getElementById('edit_allergies').value = patient.allergies || '';
            attachFormHandler('editPatientForm', document.getElementById('editPatientForm').action, 'Patient updated successfully.', true);
        } catch (err) {
            slideOverContent.innerHTML = '<div class="text-center text-red-600 py-8">Failed to load patient details.</div>';
        }
    }

    function attachFormHandler(formId, action, successMessage, reload = true) {
        const form = document.getElementById(formId) || document.querySelector('form[action="' + action + '"]');
        if (!form) return;
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
                form.reset();
                closePatientSlideOver();
                setTimeout(() => location.reload(), reload ? 1200 : 800);
            })
            .catch(err => {
                Swal.fire({ icon: 'error', title: 'Error', text: 'Something went wrong. Please try again.' });
            });
        });
    }
</script>
<script>
    const slideOver = document.getElementById('patientSlideOver');
    const slideOverBackdrop = document.getElementById('slideOverBackdrop');
    const slideOverPanel = document.getElementById('slideOverPanel');
    const slideOverContent = document.getElementById('slideOverContent');
    const slideOverTitle = document.getElementById('slideOverTitle');
    const slideOverSubtitle = document.getElementById('slideOverSubtitle');

    function openPatientSlideOver(title, subtitle, html) {
        slideOver.classList.remove('hidden');
        slideOverTitle.textContent = title;
        slideOverSubtitle.textContent = subtitle;
        slideOverContent.innerHTML = html;
        setTimeout(() => {
            slideOverBackdrop.classList.remove('opacity-0');
            slideOverPanel.classList.remove('translate-x-full');
        }, 10);
        document.body.style.overflow = 'hidden';
    }

    function closePatientSlideOver() {
        slideOverBackdrop.classList.add('opacity-0');
        slideOverPanel.classList.add('translate-x-full');
        setTimeout(() => {
            slideOver.classList.add('hidden');
            slideOverContent.innerHTML = '';
            document.body.style.overflow = '';
        }, 300);
    }

    function openRegisterPatientPanel() {
        const html = document.getElementById('registerPatientTemplate').innerHTML;
        openPatientSlideOver('Register New Patient', 'Enter patient details to register', html);
        attachFormHandler('registerPatientForm', '{{ route("reception.patients.store") }}', 'Patient registered successfully.');
    }

    async function openEditPatientPanel(url) {
        const html = document.getElementById('editPatientTemplate').innerHTML;
        openPatientSlideOver('Edit Patient', 'Update patient information', html);
        try {
            const res = await fetch(url, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } });
            const data = await res.json();
            const patient = data.patient;
            document.getElementById('editPatientForm').action = url.replace('/edit', '');
            document.getElementById('edit_first_name').value = patient.first_name;
            document.getElementById('edit_last_name').value = patient.last_name;
            document.getElementById('edit_date_of_birth').value = patient.date_of_birth ? patient.date_of_birth.split(' ')[0] : '';
            document.getElementById('edit_gender').value = patient.gender;
            document.getElementById('edit_phone').value = patient.phone || '';
            document.getElementById('edit_national_id').value = patient.national_id || '';
            document.getElementById('edit_blood_group').value = patient.blood_group || '';
            document.getElementById('edit_address').value = patient.address || '';
            document.getElementById('edit_allergies').value = patient.allergies || '';
            attachFormHandler('editPatientForm', document.getElementById('editPatientForm').action, 'Patient updated successfully.', true);
        } catch (err) {
            slideOverContent.innerHTML = '<div class="text-center text-red-600 py-8">Failed to load patient details.</div>';
        }
    }

    function attachFormHandler(formId, action, successMessage, reload = true) {
        const form = document.getElementById(formId) || document.querySelector('form[action="' + action + '"]');
        if (!form) return;
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
                form.reset();
                closePatientSlideOver();
                setTimeout(() => location.reload(), reload ? 1200 : 800);
            })
            .catch(err => {
                Swal.fire({ icon: 'error', title: 'Error', text: 'Something went wrong. Please try again.' });
            });
        });
    }
</script>

@push('scripts')
<script>
    const slideOver = document.getElementById('patientSlideOver');
    const slideOverBackdrop = document.getElementById('slideOverBackdrop');
    const slideOverPanel = document.getElementById('slideOverPanel');
    const slideOverContent = document.getElementById('slideOverContent');
    const slideOverTitle = document.getElementById('slideOverTitle');
    const slideOverSubtitle = document.getElementById('slideOverSubtitle');

    function openPatientSlideOver(title, subtitle, html) {
        slideOver.classList.remove('hidden');
        slideOverTitle.textContent = title;
        slideOverSubtitle.textContent = subtitle;
        slideOverContent.innerHTML = html;
        setTimeout(() => {
            slideOverBackdrop.classList.remove('opacity-0');
            slideOverPanel.classList.remove('translate-x-full');
        }, 10);
        document.body.style.overflow = 'hidden';
    }

    function closePatientSlideOver() {
        slideOverBackdrop.classList.add('opacity-0');
        slideOverPanel.classList.add('translate-x-full');
        setTimeout(() => {
            slideOver.classList.add('hidden');
            slideOverContent.innerHTML = '';
            document.body.style.overflow = '';
        }, 300);
    }

    function openRegisterPatientPanel() {
        const html = document.getElementById('registerPatientTemplate').innerHTML;
        openPatientSlideOver('Register New Patient', 'Enter patient details to register', html);
        attachFormHandler('registerPatientForm', '{{ route("reception.patients.store") }}', 'Patient registered successfully.');
    }

    async function openEditPatientPanel(url) {
        const html = document.getElementById('editPatientTemplate').innerHTML;
        openPatientSlideOver('Edit Patient', 'Update patient information', html);
        try {
            const res = await fetch(url, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } });
            const data = await res.json();
            const patient = data.patient;
            document.getElementById('editPatientForm').action = url.replace('/edit', '');
            document.getElementById('edit_first_name').value = patient.first_name;
            document.getElementById('edit_last_name').value = patient.last_name;
            document.getElementById('edit_date_of_birth').value = patient.date_of_birth ? patient.date_of_birth.split(' ')[0] : '';
            document.getElementById('edit_gender').value = patient.gender;
            document.getElementById('edit_phone').value = patient.phone || '';
            document.getElementById('edit_national_id').value = patient.national_id || '';
            document.getElementById('edit_blood_group').value = patient.blood_group || '';
            document.getElementById('edit_address').value = patient.address || '';
            document.getElementById('edit_allergies').value = patient.allergies || '';
            attachFormHandler('editPatientForm', document.getElementById('editPatientForm').action, 'Patient updated successfully.', true);
        } catch (err) {
            slideOverContent.innerHTML = '<div class="text-center text-red-600 py-8">Failed to load patient details.</div>';
        }
    }

    function attachFormHandler(formId, action, successMessage, reload = true) {
        const form = document.getElementById(formId) || document.querySelector('form[action="' + action + '"]');
        if (!form) return;
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
                form.reset();
                closePatientSlideOver();
                setTimeout(() => location.reload(), reload ? 1200 : 800);
            })
            .catch(err => {
                Swal.fire({ icon: 'error', title: 'Error', text: 'Something went wrong. Please try again.' });
            });
        });
    }
</script>
@endpush
@endsection
