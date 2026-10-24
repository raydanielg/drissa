@extends('layouts.dashboard')

@section('title', 'Patients - ' . config('app.name', 'Laravel'))
@section('page_title', 'Patients')

@section('content')
<div class="space-y-5">
    {{-- Header --}}
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

    {{-- Table --}}
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
</div>

{{-- Slide-over --}}
<div id="patientSlideOver" class="fixed inset-0 z-50 hidden" aria-hidden="true">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm transition-opacity opacity-0" id="slideOverBackdrop" onclick="closePatientSlideOver()"></div>
    <div class="absolute inset-y-0 right-0 w-full max-w-md transform translate-x-full transition-transform duration-300 ease-out" id="slideOverPanel">
        <div class="h-full bg-white shadow-2xl flex flex-col">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between bg-gray-50/50">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-md bg-slate-800 flex items-center justify-center text-white">
                        <i class="hgi-stroke hgi-user-add-01 text-base"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-gray-900" id="slideOverTitle">Register New Patient</h3>
                        <p class="text-[10px] text-gray-500" id="slideOverSubtitle">Fill patient details below</p>
                    </div>
                </div>
                <button onclick="closePatientSlideOver()" class="text-gray-400 hover:text-gray-600 p-1.5 rounded-md hover:bg-gray-100 transition-colors">
                    <i class="hgi-stroke hgi-cancel-01 text-xl"></i>
                </button>
            </div>
            <div class="flex-1 overflow-y-auto p-6" id="slideOverContent"></div>
        </div>
    </div>
</div>

{{-- Register form template --}}
<div id="registerPatientTemplate" class="hidden">
    <form id="registerPatientForm" method="POST" action="{{ route('reception.patients.store') }}" class="space-y-4">
        @csrf
        <div>
            <p class="text-[10px] font-semibold uppercase tracking-wider text-gray-400 mb-2">Personal details</p>
            <div class="grid grid-cols-2 gap-3">
                <x-ui.input name="first_name" placeholder="First name *" required class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-slate-300" />
                <x-ui.input name="last_name" placeholder="Last name *" required class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-slate-300" />
                <x-ui.input type="date" name="date_of_birth" required class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-slate-300" />
                <x-ui.select name="gender" required>
                    <option value="">Gender *</option>
                    <option value="male">Male</option>
                    <option value="female">Female</option>
                    <option value="other">Other</option>
                </x-ui.select>
            </div>
        </div>
        <div>
            <p class="text-[10px] font-semibold uppercase tracking-wider text-gray-400 mb-2">Contact & ID</p>
            <div class="grid grid-cols-2 gap-3">
                <x-ui.input name="phone" placeholder="Phone" class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-slate-300" />
                <x-ui.input name="national_id" placeholder="National ID" class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-slate-300" />
            </div>
        </div>
        <div>
            <p class="text-[10px] font-semibold uppercase tracking-wider text-gray-400 mb-2">Medical</p>
            <div class="grid grid-cols-2 gap-3">
                <x-ui.select name="blood_group">
                    <option value="">Blood group</option>
                    @foreach(['A+','A-','B+','B-','AB+','AB-','O+','O-'] as $bg)
                        <option value="{{ $bg }}">{{ $bg }}</option>
                    @endforeach
                </x-ui.select>
                <x-ui.input name="address" placeholder="Address" class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-slate-300" />
            </div>
            <div class="mt-3">
                <x-ui.textarea name="allergies" placeholder="Allergies" class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-slate-300" rows="2"></x-ui.textarea>
            </div>
        </div>
        <div class="flex justify-end gap-2 pt-2 border-t border-gray-100">
            <button type="button" onclick="closePatientSlideOver()" class="px-4 py-2 text-sm text-gray-600 hover:bg-gray-100 rounded-md">Cancel</button>
            <button type="submit" class="px-4 py-2 bg-slate-800 text-white text-sm font-medium rounded-md hover:bg-slate-700">Register Patient</button>
        </div>
    </form>
</div>

{{-- Edit form template --}}
<div id="editPatientTemplate" class="hidden">
    <form id="editPatientForm" method="POST" class="space-y-4">
        @csrf
        @method('PUT')
        <div>
            <p class="text-[10px] font-semibold uppercase tracking-wider text-gray-400 mb-2">Personal details</p>
            <div class="grid grid-cols-2 gap-3">
                <x-ui.input id="edit_first_name" name="first_name" placeholder="First name *" required class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-slate-300" />
                <x-ui.input id="edit_last_name" name="last_name" placeholder="Last name *" required class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-slate-300" />
                <x-ui.input id="edit_date_of_birth" type="date" name="date_of_birth" required class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-slate-300" />
                <x-ui.input id="edit_gender" name="gender" placeholder="Gender *" required class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-slate-300" />
            </div>
        </div>
        <div>
            <p class="text-[10px] font-semibold uppercase tracking-wider text-gray-400 mb-2">Contact & ID</p>
            <div class="grid grid-cols-2 gap-3">
                <x-ui.input id="edit_phone" name="phone" placeholder="Phone" class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-slate-300" />
                <x-ui.input id="edit_national_id" name="national_id" placeholder="National ID" class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-slate-300" />
            </div>
        </div>
        <div>
            <p class="text-[10px] font-semibold uppercase tracking-wider text-gray-400 mb-2">Medical</p>
            <div class="grid grid-cols-2 gap-3">
                <x-ui.input id="edit_blood_group" name="blood_group" placeholder="Blood group" class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-slate-300" />
                <x-ui.input id="edit_address" name="address" placeholder="Address" class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-slate-300" />
            </div>
            <div class="mt-3">
                <x-ui.textarea id="edit_allergies" name="allergies" placeholder="Allergies" class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-slate-300" rows="2"></x-ui.textarea>
            </div>
        </div>
        <div class="flex justify-end gap-2 pt-2 border-t border-gray-100">
            <button type="button" onclick="closePatientSlideOver()" class="px-4 py-2 text-sm text-gray-600 hover:bg-gray-100 rounded-md">Cancel</button>
            <button type="submit" class="px-4 py-2 bg-slate-800 text-white text-sm font-medium rounded-md hover:bg-slate-700">Save Changes</button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    function soEl(id) { return document.getElementById(id); }

    function openPatientSlideOver(title, subtitle, html) {
        var slideOver = soEl('patientSlideOver');
        if (!slideOver) return;
        soEl('slideOverTitle').textContent = title;
        soEl('slideOverSubtitle').textContent = subtitle;
        soEl('slideOverContent').innerHTML = html;
        slideOver.classList.remove('hidden');
        setTimeout(function () {
            soEl('slideOverBackdrop').classList.remove('opacity-0');
            soEl('slideOverPanel').classList.remove('translate-x-full');
        }, 10);
        document.body.style.overflow = 'hidden';
    }

    function closePatientSlideOver() {
        soEl('slideOverBackdrop').classList.add('opacity-0');
        soEl('slideOverPanel').classList.add('translate-x-full');
        setTimeout(function () {
            soEl('patientSlideOver').classList.add('hidden');
            soEl('slideOverContent').innerHTML = '';
            document.body.style.overflow = '';
        }, 300);
    }

    function openRegisterPatientPanel() {
        var tpl = soEl('registerPatientTemplate');
        if (!tpl) return;
        openPatientSlideOver('Register New Patient', 'Enter patient details to register', tpl.innerHTML);
        attachFormHandler('registerPatientForm', '{{ route("reception.patients.store") }}', 'Patient registered successfully.');
    }

    async function openEditPatientPanel(url) {
        var tpl = soEl('editPatientTemplate');
        if (!tpl) return;
        openPatientSlideOver('Edit Patient', 'Update patient information', tpl.innerHTML);
        try {
            var res = await fetch(url, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } });
            var data = await res.json();
            var patient = data.patient;
            soEl('editPatientForm').action = url.replace('/edit', '');
            soEl('edit_first_name').value = patient.first_name;
            soEl('edit_last_name').value = patient.last_name;
            soEl('edit_date_of_birth').value = patient.date_of_birth ? patient.date_of_birth.split(' ')[0] : '';
            soEl('edit_gender').value = patient.gender;
            soEl('edit_phone').value = patient.phone || '';
            soEl('edit_national_id').value = patient.national_id || '';
            soEl('edit_blood_group').value = patient.blood_group || '';
            soEl('edit_address').value = patient.address || '';
            soEl('edit_allergies').value = patient.allergies || '';
            attachFormHandler('editPatientForm', soEl('editPatientForm').action, 'Patient updated successfully.', true);
        } catch (err) {
            soEl('slideOverContent').innerHTML = '<div class="text-center text-red-600 py-8 text-sm">Failed to load patient details.</div>';
        }
    }

    function attachFormHandler(formId, action, successMessage, reload) {
        var form = document.getElementById(formId);
        if (!form) return;
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            var formData = new FormData(form);
            fetch(action, {
                method: 'POST',
                body: formData,
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
            })
            .then(function (r) { return r.json().catch(function () { return {}; }); })
            .then(function (data) {
                if (data && data.success === false) {
                    Swal.fire({ icon: 'error', title: 'Error', text: data.message || 'Could not save patient.' });
                    return;
                }
                if (window.toast) window.toast(data.message || successMessage, 'success');
                else Swal.fire({ icon: 'success', title: 'Success', text: data.message || successMessage, timer: 2000, showConfirmButton: false });
                form.reset();
                closePatientSlideOver();
                setTimeout(function () { location.reload(); }, reload === false ? 800 : 1200);
            })
            .catch(function () {
                Swal.fire({ icon: 'error', title: 'Error', text: 'Something went wrong. Please try again.' });
            });
        });
    }
</script>
@endpush
