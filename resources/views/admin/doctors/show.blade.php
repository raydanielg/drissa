@extends('layouts.dashboard')

@section('title', 'Doctor Profile - ' . config('app.name', 'Laravel'))
@section('page_title', 'Doctor Profile: ' . $doctor->name)

@section('content')
<div class="max-w-6xl mx-auto space-y-5">
    <script>
    function resetDoctorPassword(url) {
        Swal.fire({
            title: 'Reset Password?',
            text: 'A new random password will be generated and sent to the doctor\'s email.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#0f172a',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Yes, Reset Password',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(url, {
                    method: 'PUT',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                })
                .then(r => r.json().catch(() => ({})))
                .then(data => {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: data.message || 'Password reset successfully.',
                        timer: 2000,
                        showConfirmButton: false
                    });
                })
                .catch(err => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Failed to reset password.'
                    });
                });
            }
        });
    }
</script>
    <div class="bg-slate-800 rounded-md p-6 text-white">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-16 h-16 rounded-full border-2 border-slate-600 bg-slate-700 flex items-center justify-center text-white text-xl font-bold">
                    {{ strtoupper(substr($doctor->name, 0, 1)) }}
                </div>
                <div>
                    <p class="text-[10px] uppercase tracking-wider text-slate-400 mb-1">Doctor Profile</p>
                    <h1 class="text-xl font-bold">{{ $doctor->name }}</h1>
                    <p class="text-xs text-slate-400 mt-1">{{ $doctor->email }} — {{ $doctor->phone ?? 'No phone' }}</p>
                    <div class="flex items-center gap-2 mt-1">
                        <span class="inline-flex items-center gap-1.5 text-[10px] font-medium text-slate-300">
                            <span class="w-1.5 h-1.5 rounded-full {{ $doctor->is_active ? 'bg-emerald-400' : 'bg-red-400' }}"></span>
                            {{ $doctor->is_active ? 'Active' : 'Inactive' }}
                        </span>
                        <span class="text-[10px] text-slate-400">Department: {{ $doctor->department?->name ?? 'Not assigned' }}</span>
                    </div>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <form method="POST" action="{{ route('admin.doctors.toggle', $doctor) }}" data-ajax>
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="{{ $doctor->is_active ? 'bg-red-500/20 text-red-300 hover:bg-red-500/30' : 'bg-emerald-500/20 text-emerald-300 hover:bg-emerald-500/30' }} border border-slate-600 text-xs font-medium px-3 py-1.5 rounded transition-colors">
                        {{ $doctor->is_active ? 'Deactivate' : 'Activate' }}
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
    function resetDoctorPassword(url) {
        Swal.fire({
            title: 'Reset Password?',
            text: 'A new random password will be generated and sent to the doctor\'s email.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#0f172a',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Yes, Reset Password',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(url, {
                    method: 'PUT',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                })
                .then(r => r.json().catch(() => ({})))
                .then(data => {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: data.message || 'Password reset successfully.',
                        timer: 2000,
                        showConfirmButton: false
                    });
                })
                .catch(err => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Failed to reset password.'
                    });
                });
            }
        });
    }
</script>
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
        @php
            $statConfig = [
                ['key' => 'total_visits', 'label' => 'Total Visits', 'icon' => 'hgi-clipboard'],
                ['key' => 'active_visits', 'label' => 'Active Queue', 'icon' => 'hgi-clock-01', 'border' => 'border-amber-200'],
                ['key' => 'completed_visits', 'label' => 'Completed', 'icon' => 'hgi-checkmark-circle-02', 'border' => 'border-emerald-200', 'value' => 'text-emerald-600'],
                ['key' => 'appointments_today', 'label' => 'Appointments Today', 'icon' => 'hgi-calendar-03'],
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

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
        <script>
    function resetDoctorPassword(url) {
        Swal.fire({
            title: 'Reset Password?',
            text: 'A new random password will be generated and sent to the doctor\'s email.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#0f172a',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Yes, Reset Password',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(url, {
                    method: 'PUT',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                })
                .then(r => r.json().catch(() => ({})))
                .then(data => {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: data.message || 'Password reset successfully.',
                        timer: 2000,
                        showConfirmButton: false
                    });
                })
                .catch(err => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Failed to reset password.'
                    });
                });
            }
        });
    }
</script>
        <div class="space-y-5">
            <div class="bg-white rounded-md border border-gray-200 p-5">
                <h3 class="text-sm font-bold text-gray-900 mb-4">Update Doctor</h3>
                <form method="POST" action="{{ route('admin.doctors.update', $doctor) }}" class="space-y-4" data-ajax>
                    @csrf
                    @method('PUT')
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Department</label>
                        <x-ui.select name="department_id" class="w-full border border-gray-200 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-slate-300 bg-white">
                            <option value="">Select department...</option>
                            @foreach($departments as $dept)
                                <option value="{{ $dept->id }}" {{ $doctor->department_id == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                            @endforeach
                        </x-ui.select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Phone</label>
                        <x-ui.input type="text" name="phone" value="{{ $doctor->phone }}" class="w-full border border-gray-200 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-slate-300" />
                    </div>
                    <div class="flex items-center gap-2">
                        <input type="checkbox" name="is_active" value="1" {{ $doctor->is_active ? 'checked' : '' }} class="rounded border-gray-300">
                        <label class="text-sm text-gray-700">Active</label>
                    </div>
                    <button type="submit" class="w-full bg-slate-800 text-white text-sm font-medium px-4 py-2 rounded hover:bg-slate-700">Update Profile</button>
                </form>
            </div>

            <div class="bg-white rounded-md border border-gray-200 p-5">
                <h3 class="text-sm font-bold text-gray-900 mb-4">Reset Password</h3>
                <p class="text-xs text-gray-500 mb-4">Click to generate a random password and send it to the doctor's email.</p>
                <button type="button" onclick="resetDoctorPassword('{{ route('admin.doctors.reset-password', $doctor) }}')" class="w-full bg-amber-500 text-white text-sm font-medium px-4 py-2 rounded hover:bg-amber-600 flex items-center justify-center gap-2">
                    <i class="hgi-stroke hgi-analytics-02 text-base"></i>
                    Generate & Send Password
                </button>
            </div>
        </div>

        <script>
    function resetDoctorPassword(url) {
        Swal.fire({
            title: 'Reset Password?',
            text: 'A new random password will be generated and sent to the doctor\'s email.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#0f172a',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Yes, Reset Password',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(url, {
                    method: 'PUT',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                })
                .then(r => r.json().catch(() => ({})))
                .then(data => {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: data.message || 'Password reset successfully.',
                        timer: 2000,
                        showConfirmButton: false
                    });
                })
                .catch(err => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Failed to reset password.'
                    });
                });
            }
        });
    }
</script>
        <div class="lg:col-span-2 space-y-5">
            <script>
    function resetDoctorPassword(url) {
        Swal.fire({
            title: 'Reset Password?',
            text: 'A new random password will be generated and sent to the doctor\'s email.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#0f172a',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Yes, Reset Password',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(url, {
                    method: 'PUT',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                })
                .then(r => r.json().catch(() => ({})))
                .then(data => {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: data.message || 'Password reset successfully.',
                        timer: 2000,
                        showConfirmButton: false
                    });
                })
                .catch(err => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Failed to reset password.'
                    });
                });
            }
        });
    }
</script>
            <div class="bg-white rounded-md border border-gray-200 overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100">
                    <h3 class="text-sm font-bold text-gray-900">Current Queue</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-gray-50 text-[10px] uppercase text-gray-400">
                            <tr>
                                <th class="px-5 py-3">Visit #</th>
                                <th class="px-5 py-3">Patient</th>
                                <th class="px-5 py-3">Status</th>
                                <th class="px-5 py-3">Registered</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse ($activeVisits as $visit)
                                <tr class="hover:bg-gray-50/50">
                                    <td class="px-5 py-3 text-xs text-gray-600">{{ $visit->visit_number }}</td>
                                    <td class="px-5 py-3 text-xs text-gray-600">{{ $visit->patient?->fullName() ?? '-' }}</td>
                                    <td class="px-5 py-3 text-xs text-gray-600 capitalize">{{ str_replace('_', ' ', $visit->status) }}</td>
                                    <td class="px-5 py-3 text-xs text-gray-500">{{ $visit->registered_at?->format('M d, Y H:i') }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="px-5 py-8 text-center text-gray-400 text-xs">No active patients in queue</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <script>
    function resetDoctorPassword(url) {
        Swal.fire({
            title: 'Reset Password?',
            text: 'A new random password will be generated and sent to the doctor\'s email.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#0f172a',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Yes, Reset Password',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(url, {
                    method: 'PUT',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                })
                .then(r => r.json().catch(() => ({})))
                .then(data => {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: data.message || 'Password reset successfully.',
                        timer: 2000,
                        showConfirmButton: false
                    });
                })
                .catch(err => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Failed to reset password.'
                    });
                });
            }
        });
    }
</script>
            <div class="bg-white rounded-md border border-gray-200 overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100">
                    <h3 class="text-sm font-bold text-gray-900">Upcoming Appointments</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-gray-50 text-[10px] uppercase text-gray-400">
                            <tr>
                                <th class="px-5 py-3">Patient</th>
                                <th class="px-5 py-3">Date</th>
                                <th class="px-5 py-3">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse ($upcomingAppointments as $appt)
                                <tr class="hover:bg-gray-50/50">
                                    <td class="px-5 py-3 text-xs text-gray-600">{{ $appt->patient?->fullName() ?? '-' }}</td>
                                    <td class="px-5 py-3 text-xs text-gray-600">{{ $appt->scheduled_at?->format('M d, Y H:i') }}</td>
                                    <td class="px-5 py-3 text-xs text-gray-600 capitalize">{{ str_replace('_', ' ', $appt->status) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="px-5 py-8 text-center text-gray-400 text-xs">No upcoming appointments</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <script>
    function resetDoctorPassword(url) {
        Swal.fire({
            title: 'Reset Password?',
            text: 'A new random password will be generated and sent to the doctor\'s email.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#0f172a',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Yes, Reset Password',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(url, {
                    method: 'PUT',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                })
                .then(r => r.json().catch(() => ({})))
                .then(data => {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: data.message || 'Password reset successfully.',
                        timer: 2000,
                        showConfirmButton: false
                    });
                })
                .catch(err => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Failed to reset password.'
                    });
                });
            }
        });
    }
</script>
            <div class="bg-white rounded-md border border-gray-200 overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100">
                    <h3 class="text-sm font-bold text-gray-900">Recent Completed Visits</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-gray-50 text-[10px] uppercase text-gray-400">
                            <tr>
                                <th class="px-5 py-3">Visit #</th>
                                <th class="px-5 py-3">Patient</th>
                                <th class="px-5 py-3">Status</th>
                                <th class="px-5 py-3">Completed</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse ($completedVisits as $visit)
                                <tr class="hover:bg-gray-50/50">
                                    <td class="px-5 py-3 text-xs text-gray-600">{{ $visit->visit_number }}</td>
                                    <td class="px-5 py-3 text-xs text-gray-600">{{ $visit->patient?->fullName() ?? '-' }}</td>
                                    <td class="px-5 py-3 text-xs text-gray-600 capitalize">{{ str_replace('_', ' ', $visit->status) }}</td>
                                    <td class="px-5 py-3 text-xs text-gray-500">{{ $visit->completed_at?->format('M d, Y H:i') ?? $visit->updated_at->format('M d, Y H:i') }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="px-5 py-8 text-center text-gray-400 text-xs">No completed visits</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <script>
    function resetDoctorPassword(url) {
        Swal.fire({
            title: 'Reset Password?',
            text: 'A new random password will be generated and sent to the doctor\'s email.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#0f172a',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Yes, Reset Password',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(url, {
                    method: 'PUT',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                })
                .then(r => r.json().catch(() => ({})))
                .then(data => {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: data.message || 'Password reset successfully.',
                        timer: 2000,
                        showConfirmButton: false
                    });
                })
                .catch(err => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Failed to reset password.'
                    });
                });
            }
        });
    }
</script>
            <div class="bg-white rounded-md border border-gray-200 p-5">
                <h3 class="text-sm font-bold text-gray-900 mb-4">Assign Appointment</h3>
                <form method="POST" action="{{ route('appointments.store') }}" class="space-y-4" data-ajax>
                    @csrf
                    <input type="hidden" name="doctor_id" value="{{ $doctor->id }}">
                    <input type="hidden" name="status" value="scheduled">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Patient</label>
                        <x-ui.select name="patient_id" class="w-full border border-gray-200 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-slate-300 bg-white" required>
                            <option value="">Select patient...</option>
                            @php
                                $patients = \App\Models\Patient::latest()->limit(50)->get();
                            @endphp
                            @foreach($patients as $patient)
                                <option value="{{ $patient->id }}">{{ $patient->fullName() }} ({{ $patient->mrn }})</option>
                            @endforeach
                        </x-ui.select>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Date</label>
                            <x-ui.input type="text" data-fp-date name="appointment_date" class="w-full border border-gray-200 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-slate-300" required />
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Time</label>
                            <x-ui.input type="text" data-fp-time name="start_time" class="w-full border border-gray-200 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-slate-300" required />
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Type</label>
                        <x-ui.select name="type" class="w-full border border-gray-200 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-slate-300 bg-white" required>
                            <option value="general">General Consultation</option>
                            <option value="followup">Follow-up</option>
                            <option value="emergency">Emergency</option>
                        </x-ui.select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Notes</label>
                        <x-ui.textarea name="notes" rows="2" class="w-full border border-gray-200 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-slate-300"></x-ui.textarea>
                    </div>
                    <button type="submit" class="w-full bg-slate-800 text-white text-sm font-medium px-4 py-2 rounded hover:bg-slate-700">Assign Appointment</button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function resetDoctorPassword(url) {
        Swal.fire({
            title: 'Reset Password?',
            text: 'A new random password will be generated and sent to the doctor\'s email.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#0f172a',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Yes, Reset Password',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(url, {
                    method: 'PUT',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                })
                .then(r => r.json().catch(() => ({})))
                .then(data => {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: data.message || 'Password reset successfully.',
                        timer: 2000,
                        showConfirmButton: false
                    });
                })
                .catch(err => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Failed to reset password.'
                    });
                });
            }
        });
    }
</script>
@endpush
@endsection
