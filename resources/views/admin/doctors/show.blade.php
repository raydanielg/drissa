@extends('layouts.dashboard')

@section('title', 'Doctor Profile - ' . config('app.name', 'Laravel'))
@section('page_title', 'Doctor Profile: ' . $doctor->name)

@section('content')
<div class="max-w-6xl mx-auto space-y-6">
    {{-- Header Card --}}
    <div class="bg-white rounded-xl border border-gray-100 p-6 shadow-sm">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-16 h-16 rounded-full bg-gradient-to-br from-emerald-500 to-emerald-700 flex items-center justify-center text-white text-xl font-bold">
                    {{ strtoupper(substr($doctor->name, 0, 1)) }}
                </div>
                <div>
                    <h2 class="text-xl font-bold text-gray-900">{{ $doctor->name }}</h2>
                    <p class="text-sm text-gray-500">{{ $doctor->email }} | {{ $doctor->phone ?? 'No phone' }}</p>
                    <div class="flex items-center gap-2 mt-1">
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-medium {{ $doctor->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700' }}">{{ $doctor->is_active ? 'Active' : 'Inactive' }}</span>
                        <span class="text-xs text-gray-500">Department: {{ $doctor->department?->name ?? 'Not assigned' }}</span>
                    </div>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <form method="POST" action="{{ route('admin.doctors.toggle', $doctor) }}" data-ajax>
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="{{ $doctor->is_active ? 'bg-red-100 text-red-700 hover:bg-red-200' : 'bg-emerald-100 text-emerald-700 hover:bg-emerald-200' }} text-xs font-medium px-3 py-1.5 rounded-lg">
                        {{ $doctor->is_active ? 'Deactivate' : 'Activate' }}
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- KPI Cards --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        @php
            $statConfig = [
                ['key' => 'total_visits', 'label' => 'Total Visits', 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2', 'from' => 'emerald-500', 'to' => 'emerald-700', 'border' => 'emerald-400', 'text' => 'emerald-100', 'sub' => 'emerald-200'],
                ['key' => 'active_visits', 'label' => 'Active Queue', 'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z', 'from' => 'blue-500', 'to' => 'blue-700', 'border' => 'blue-400', 'text' => 'blue-100', 'sub' => 'blue-200'],
                ['key' => 'completed_visits', 'label' => 'Completed', 'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z', 'from' => 'purple-500', 'to' => 'purple-700', 'border' => 'purple-400', 'text' => 'purple-100', 'sub' => 'purple-200'],
                ['key' => 'appointments_today', 'label' => 'Appointments Today', 'icon' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z', 'from' => 'amber-500', 'to' => 'amber-700', 'border' => 'amber-400', 'text' => 'amber-100', 'sub' => 'amber-200'],
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

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Left: Edit Profile & Password --}}
        <div class="space-y-6">
            <div class="bg-white rounded-xl border border-gray-100 p-6 shadow-sm">
                <h3 class="text-sm font-semibold text-gray-900 mb-4">Update Doctor</h3>
                <form method="POST" action="{{ route('admin.doctors.update', $doctor) }}" class="space-y-4" data-ajax>
                    @csrf
                    @method('PUT')
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Department</label>
                        <select name="department_id" class="w-full border rounded-lg px-3 py-2 text-sm">
                            <option value="">Select department...</option>
                            @foreach($departments as $dept)
                                <option value="{{ $dept->id }}" {{ $doctor->department_id == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Phone</label>
                        <input type="text" name="phone" value="{{ $doctor->phone }}" class="w-full border rounded-lg px-3 py-2 text-sm">
                    </div>
                    <div class="flex items-center gap-2">
                        <input type="checkbox" name="is_active" value="1" {{ $doctor->is_active ? 'checked' : '' }} class="rounded border-gray-300">
                        <label class="text-sm text-gray-700">Active</label>
                    </div>
                    <button type="submit" class="w-full bg-emerald-600 text-white text-sm font-medium px-4 py-2 rounded-lg hover:bg-emerald-700">Update Profile</button>
                </form>
            </div>

            <div class="bg-white rounded-xl border border-gray-100 p-6 shadow-sm">
                <h3 class="text-sm font-semibold text-gray-900 mb-4">Reset Password</h3>
                <p class="text-xs text-gray-500 mb-4">Click to generate a random password and send it to the doctor's email.</p>
                <button type="button" onclick="resetDoctorPassword('{{ route('admin.doctors.reset-password', $doctor) }}')" class="w-full bg-amber-500 text-white text-sm font-medium px-4 py-2 rounded-lg hover:bg-amber-600 flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2h4a2 2 0 012 2v9a2 2 0 01-2 2h-4a2 2 0 01-2-2V9a2 2 0 012-2zm-6 0a2 2 0 00-2 2v9a2 2 0 002 2h4a2 2 0 002-2V9a2 2 0 00-2-2h-4a2 2 0 00-2 2z"/></svg>
                    Generate & Send Password
                </button>
            </div>
        </div>

        {{-- Right: Queue & Patients --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Active Queue --}}
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h3 class="text-sm font-semibold text-gray-900">Current Queue</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                            <tr>
                                <th class="px-6 py-3">Visit #</th>
                                <th class="px-6 py-3">Patient</th>
                                <th class="px-6 py-3">Status</th>
                                <th class="px-6 py-3">Registered</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($activeVisits as $visit)
                                <tr class="border-t border-gray-100 hover:bg-gray-50/50">
                                    <td class="px-6 py-3">{{ $visit->visit_number }}</td>
                                    <td class="px-6 py-3">{{ $visit->patient?->fullName() ?? '-' }}</td>
                                    <td class="px-6 py-3"><span class="capitalize">{{ str_replace('_', ' ', $visit->status) }}</span></td>
                                    <td class="px-6 py-3 text-xs text-gray-500">{{ $visit->registered_at?->format('M d, Y H:i') }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="px-6 py-4 text-center text-gray-400 text-sm">No active patients in queue</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Upcoming Appointments --}}
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h3 class="text-sm font-semibold text-gray-900">Upcoming Appointments</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                            <tr>
                                <th class="px-6 py-3">Patient</th>
                                <th class="px-6 py-3">Date</th>
                                <th class="px-6 py-3">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($upcomingAppointments as $appt)
                                <tr class="border-t border-gray-100 hover:bg-gray-50/50">
                                    <td class="px-6 py-3">{{ $appt->patient?->fullName() ?? '-' }}</td>
                                    <td class="px-6 py-3">{{ $appt->scheduled_at?->format('M d, Y H:i') }}</td>
                                    <td class="px-6 py-3"><span class="capitalize">{{ str_replace('_', ' ', $appt->status) }}</span></td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="px-6 py-4 text-center text-gray-400 text-sm">No upcoming appointments</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Recent Completed Visits --}}
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h3 class="text-sm font-semibold text-gray-900">Recent Completed Visits</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                            <tr>
                                <th class="px-6 py-3">Visit #</th>
                                <th class="px-6 py-3">Patient</th>
                                <th class="px-6 py-3">Status</th>
                                <th class="px-6 py-3">Completed</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($completedVisits as $visit)
                                <tr class="border-t border-gray-100 hover:bg-gray-50/50">
                                    <td class="px-6 py-3">{{ $visit->visit_number }}</td>
                                    <td class="px-6 py-3">{{ $visit->patient?->fullName() ?? '-' }}</td>
                                    <td class="px-6 py-3"><span class="capitalize">{{ str_replace('_', ' ', $visit->status) }}</span></td>
                                    <td class="px-6 py-3 text-xs text-gray-500">{{ $visit->completed_at?->format('M d, Y H:i') ?? $visit->updated_at->format('M d, Y H:i') }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="px-6 py-4 text-center text-gray-400 text-sm">No completed visits</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Assign Appointment --}}
            <div class="bg-white rounded-xl border border-gray-100 p-6 shadow-sm">
                <h3 class="text-sm font-semibold text-gray-900 mb-4">Assign Appointment</h3>
                <form method="POST" action="{{ route('appointments.store') }}" class="space-y-4" data-ajax>
                    @csrf
                    <input type="hidden" name="doctor_id" value="{{ $doctor->id }}">
                    <input type="hidden" name="status" value="scheduled">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Patient</label>
                        <select name="patient_id" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 bg-white" required>
                            <option value="">Select patient...</option>
                            @php
                                $patients = \App\Models\Patient::latest()->limit(50)->get();
                            @endphp
                            @foreach($patients as $patient)
                                <option value="{{ $patient->id }}">{{ $patient->fullName() }} ({{ $patient->mrn }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Date</label>
                            <input type="date" name="appointment_date" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" required>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Time</label>
                            <input type="time" name="start_time" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" required>
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Type</label>
                        <select name="type" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 bg-white" required>
                            <option value="general">General Consultation</option>
                            <option value="followup">Follow-up</option>
                            <option value="emergency">Emergency</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Notes</label>
                        <textarea name="notes" rows="2" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"></textarea>
                    </div>
                    <button type="submit" class="w-full bg-emerald-600 text-white text-sm font-medium px-4 py-2 rounded-lg hover:bg-emerald-700">Assign Appointment</button>
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
            confirmButtonColor: '#f59e0b',
            cancelButtonColor: '#d33',
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
