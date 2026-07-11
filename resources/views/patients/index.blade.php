@extends('layouts.dashboard')

@section('title', 'Patients - ' . config('app.name', 'Laravel'))
@section('page_title', 'Patients')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-lg font-bold text-gray-900">Patient Registry</h2>
            <p class="text-sm text-gray-500">Manage and search all registered patients</p>
        </div>
        <button onclick="openRegisterPatientPanel()" class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium px-4 py-2 rounded-lg shadow-sm hover:shadow transition-all">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
            Register Patient
        </button>
    </div>

<div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
        <h2 class="text-sm font-semibold text-gray-900">Patient Registry</h2>
        <form method="GET" action="{{ route('patients.index') }}" class="flex gap-2">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name, MRN, phone..." class="border border-gray-200 rounded-lg px-3 py-1.5 text-sm min-w-[250px]">
            <button type="submit" class="bg-emerald-600 text-white text-xs font-medium px-3 py-1.5 rounded-lg hover:bg-emerald-700">Search</button>
        </form>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left">
            <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                <tr>
                    <th class="px-6 py-3">MRN</th>
                    <th class="px-6 py-3">Name</th>
                    <th class="px-6 py-3">Gender</th>
                    <th class="px-6 py-3">Phone</th>
                    <th class="px-6 py-3">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($patients as $patient)
                    <tr class="border-t border-gray-100 hover:bg-gray-50/50">
                        <td class="px-6 py-3 font-medium">{{ $patient->mrn }}</td>
                        <td class="px-6 py-3">{{ $patient->fullName() }}</td>
                        <td class="px-6 py-3 capitalize">{{ $patient->gender }}</td>
                        <td class="px-6 py-3">{{ $patient->phone ?? '-' }}</td>
                        <td class="px-6 py-3">
                            <div class="flex items-center justify-end gap-1">
                                <a href="{{ route('patients.show', $patient) }}" class="action-icon group/icon relative p-2 text-emerald-600 hover:bg-emerald-100 rounded-lg transition-colors" title="View">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    <span class="absolute -top-8 left-1/2 -translate-x-1/2 px-2 py-1 bg-gray-900 text-white text-[10px] rounded opacity-0 group-hover/icon:opacity-100 transition-opacity whitespace-nowrap pointer-events-none">View</span>
                                </a>
                                <button type="button" onclick="openEditPatientPanel('{{ route('patients.edit', $patient) }}')" class="action-icon group/icon relative p-2 text-blue-600 hover:bg-blue-100 rounded-lg transition-colors" title="Edit">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.43-9.525l-9.17 9.17a2 2 0 00-.586 1.414V17a1 1 0 001 1h2.828a2 2 0 001.414-.586l9.17-9.17a2 2 0 000-2.828l-1.414-1.414a2 2 0 00-2.828 0z"/></svg>
                                    <span class="absolute -top-8 left-1/2 -translate-x-1/2 px-2 py-1 bg-gray-900 text-white text-[10px] rounded opacity-0 group-hover/icon:opacity-100 transition-opacity whitespace-nowrap pointer-events-none">Edit</span>
                                </button>
                                <form method="POST" action="{{ route('patients.destroy', $patient) }}" data-ajax data-confirm="Delete this patient record?" class="inline">
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
                    <tr><td colspan="5" class="px-6 py-6 text-center text-gray-400">No patients found</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-6 py-4 border-t border-gray-100">{{ $patients->links() }}</div>
</div>

{{-- Slide-over Panel --}}
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
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="flex-1 overflow-y-auto p-6" id="slideOverContent">
                {{-- Content loaded here --}}
            </div>
        </div>
    </div>
</div>
</div>

{{-- Register Form Template --}}
<template id="registerPatientTemplate">
    <form id="registerPatientForm" method="POST" action="{{ route('reception.patients.store') }}" class="space-y-4">
        @csrf
        <div class="grid grid-cols-2 gap-4">
            <div class="col-span-2 sm:col-span-1">
                <label class="block text-xs font-medium text-gray-700 mb-1">First Name</label>
                <input type="text" name="first_name" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" required>
            </div>
            <div class="col-span-2 sm:col-span-1">
                <label class="block text-xs font-medium text-gray-700 mb-1">Last Name</label>
                <input type="text" name="last_name" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" required>
            </div>
            <div class="col-span-2 sm:col-span-1">
                <label class="block text-xs font-medium text-gray-700 mb-1">Date of Birth</label>
                <input type="date" name="date_of_birth" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" required>
            </div>
            <div class="col-span-2 sm:col-span-1">
                <label class="block text-xs font-medium text-gray-700 mb-1">Gender</label>
                <select name="gender" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" required>
                    <option value="">Select</option>
                    <option value="male">Male</option>
                    <option value="female">Female</option>
                    <option value="other">Other</option>
                </select>
            </div>
            <div class="col-span-2 sm:col-span-1">
                <label class="block text-xs font-medium text-gray-700 mb-1">Phone</label>
                <input type="text" name="phone" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
            </div>
            <div class="col-span-2 sm:col-span-1">
                <label class="block text-xs font-medium text-gray-700 mb-1">National ID</label>
                <input type="text" name="national_id" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
            </div>
            <div class="col-span-2 sm:col-span-1">
                <label class="block text-xs font-medium text-gray-700 mb-1">Blood Group</label>
                <input type="text" name="blood_group" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
            </div>
            <div class="col-span-2">
                <label class="block text-xs font-medium text-gray-700 mb-1">Address</label>
                <textarea name="address" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" rows="2"></textarea>
            </div>
            <div class="col-span-2">
                <label class="block text-xs font-medium text-gray-700 mb-1">Allergies</label>
                <textarea name="allergies" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" rows="2"></textarea>
            </div>
        </div>
        <div class="flex justify-end gap-2 pt-4 border-t border-gray-100 mt-4">
            <button type="button" onclick="closePatientSlideOver()" class="px-4 py-2 text-sm text-gray-600 hover:bg-gray-100 rounded-lg">Cancel</button>
            <button type="submit" class="px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700">Register Patient</button>
        </div>
    </form>
</template>

{{-- Edit Patient Form Template --}}
<template id="editPatientTemplate">
    <form id="editPatientForm" method="POST" action="" class="space-y-4">
        @csrf
        @method('PUT')
        <div class="grid grid-cols-2 gap-4">
            <div class="col-span-2 sm:col-span-1">
                <label class="block text-xs font-medium text-gray-700 mb-1">First Name</label>
                <input type="text" name="first_name" id="edit_first_name" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" required>
            </div>
            <div class="col-span-2 sm:col-span-1">
                <label class="block text-xs font-medium text-gray-700 mb-1">Last Name</label>
                <input type="text" name="last_name" id="edit_last_name" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" required>
            </div>
            <div class="col-span-2 sm:col-span-1">
                <label class="block text-xs font-medium text-gray-700 mb-1">Date of Birth</label>
                <input type="date" name="date_of_birth" id="edit_date_of_birth" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" required>
            </div>
            <div class="col-span-2 sm:col-span-1">
                <label class="block text-xs font-medium text-gray-700 mb-1">Gender</label>
                <select name="gender" id="edit_gender" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" required>
                    <option value="male">Male</option>
                    <option value="female">Female</option>
                    <option value="other">Other</option>
                </select>
            </div>
            <div class="col-span-2 sm:col-span-1">
                <label class="block text-xs font-medium text-gray-700 mb-1">Phone</label>
                <input type="text" name="phone" id="edit_phone" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
            </div>
            <div class="col-span-2 sm:col-span-1">
                <label class="block text-xs font-medium text-gray-700 mb-1">National ID</label>
                <input type="text" name="national_id" id="edit_national_id" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
            </div>
            <div class="col-span-2 sm:col-span-1">
                <label class="block text-xs font-medium text-gray-700 mb-1">Blood Group</label>
                <input type="text" name="blood_group" id="edit_blood_group" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
            </div>
            <div class="col-span-2">
                <label class="block text-xs font-medium text-gray-700 mb-1">Address</label>
                <textarea name="address" id="edit_address" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" rows="2"></textarea>
            </div>
            <div class="col-span-2">
                <label class="block text-xs font-medium text-gray-700 mb-1">Allergies</label>
                <textarea name="allergies" id="edit_allergies" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" rows="2"></textarea>
            </div>
        </div>
        <div class="flex justify-end gap-2 pt-4 border-t border-gray-100 mt-4">
            <button type="button" onclick="closePatientSlideOver()" class="px-4 py-2 text-sm text-gray-600 hover:bg-gray-100 rounded-lg">Cancel</button>
            <button type="submit" class="px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700">Update Patient</button>
        </div>
    </form>
</template>

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
