@extends('layouts.dashboard')

@section('title', 'Reception - ' . config('app.name', 'Laravel'))
@section('page_title', 'Reception Dashboard')

@section('content')
<div class="space-y-6">
    @if (session('status'))
        <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm">{{ session('status') }}</div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Register Patient --}}
        <div class="bg-white rounded-xl border border-gray-100 p-6 shadow-sm">
            <h2 class="text-sm font-semibold text-gray-900 mb-4">Register Patient</h2>
            <form method="POST" action="{{ route('reception.patients.store') }}" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                @csrf
                <input type="text" name="first_name" placeholder="First name" class="border rounded-lg px-3 py-2 text-sm" required>
                <input type="text" name="last_name" placeholder="Last name" class="border rounded-lg px-3 py-2 text-sm" required>
                <input type="date" name="date_of_birth" class="border rounded-lg px-3 py-2 text-sm" required>
                <select name="gender" class="border rounded-lg px-3 py-2 text-sm" required>
                    <option value="">Gender</option>
                    <option value="male">Male</option>
                    <option value="female">Female</option>
                    <option value="other">Other</option>
                </select>
                <input type="text" name="phone" placeholder="Phone" class="border rounded-lg px-3 py-2 text-sm">
                <input type="text" name="national_id" placeholder="National ID" class="border rounded-lg px-3 py-2 text-sm">
                <input type="text" name="blood_group" placeholder="Blood group" class="border rounded-lg px-3 py-2 text-sm">
                <textarea name="address" placeholder="Address" class="border rounded-lg px-3 py-2 text-sm sm:col-span-2" rows="2"></textarea>
                <textarea name="allergies" placeholder="Allergies" class="border rounded-lg px-3 py-2 text-sm sm:col-span-2" rows="2"></textarea>
                <button type="submit" class="sm:col-span-2 bg-emerald-600 text-white text-sm font-medium py-2 rounded-lg hover:bg-emerald-700">Register Patient</button>
            </form>
        </div>

        {{-- Create Visit --}}
        <div class="bg-white rounded-xl border border-gray-100 p-6 shadow-sm">
            <h2 class="text-sm font-semibold text-gray-900 mb-4">Open Visit</h2>
            <form method="POST" action="{{ route('reception.visits.store') }}" class="space-y-4">
                @csrf
                <select name="patient_id" class="w-full border rounded-lg px-3 py-2 text-sm" required>
                    <option value="">Select patient</option>
                    @foreach (\App\Models\Patient::orderBy('first_name')->get() as $patient)
                        <option value="{{ $patient->id }}">{{ $patient->fullName() }} ({{ $patient->mrn }})</option>
                    @endforeach
                </select>
                <select name="type" class="w-full border rounded-lg px-3 py-2 text-sm" required>
                    <option value="outpatient">Outpatient</option>
                    <option value="emergency">Emergency</option>
                    <option value="followup">Follow-up</option>
                </select>
                <textarea name="chief_complaint" placeholder="Chief complaint" class="w-full border rounded-lg px-3 py-2 text-sm" rows="3"></textarea>
                <button type="submit" class="w-full bg-emerald-600 text-white text-sm font-medium py-2 rounded-lg hover:bg-emerald-700">Open Visit</button>
            </form>
        </div>
    </div>

    {{-- Today's Visits --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <h2 class="text-sm font-semibold text-gray-900">Today's Visits</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                    <tr>
                        <th class="px-6 py-3">Visit #</th>
                        <th class="px-6 py-3">Patient</th>
                        <th class="px-6 py-3">Status</th>
                        <th class="px-6 py-3">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($todayVisits as $visit)
                        <tr class="border-t border-gray-100">
                            <td class="px-6 py-3">{{ $visit->visit_number }}</td>
                            <td class="px-6 py-3">{{ $visit->patient->fullName() }}</td>
                            <td class="px-6 py-3 capitalize">{{ str_replace('_', ' ', $visit->status) }}</td>
                            <td class="px-6 py-3">
                                @if ($visit->status === \App\Enums\VisitStatus::Registered->value)
                                    <form method="POST" action="{{ route('reception.visits.assign', $visit) }}" class="flex gap-2">
                                        @csrf
                                        <select name="doctor_id" class="border rounded-lg text-xs px-2 py-1" required>
                                            <option value="">Doctor</option>
                                            @foreach ($doctors as $doctor)
                                                <option value="{{ $doctor->id }}">{{ $doctor->name }}</option>
                                            @endforeach
                                        </select>
                                        <button type="submit" class="bg-gold-400 text-gray-900 text-xs font-medium px-3 py-1 rounded-lg">Assign</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="px-6 py-6 text-center text-gray-400">No visits today</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Waiting for Payment --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <h2 class="text-sm font-semibold text-gray-900">Waiting for Payment</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                    <tr>
                        <th class="px-6 py-3">Visit #</th>
                        <th class="px-6 py-3">Patient</th>
                        <th class="px-6 py-3">Total</th>
                        <th class="px-6 py-3">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($waitingForPayment as $visit)
                        <tr class="border-t border-gray-100">
                            <td class="px-6 py-3">{{ $visit->visit_number }}</td>
                            <td class="px-6 py-3">{{ $visit->patient->fullName() }}</td>
                            <td class="px-6 py-3">{{ number_format($visit->invoice?->total ?? 0) }}</td>
                            <td class="px-6 py-3">
                                <form method="POST" action="{{ route('reception.visits.pay', $visit) }}" class="flex gap-2">
                                    @csrf
                                    <input type="number" name="amount" step="0.01" value="{{ $visit->invoice?->total ?? 0 }}" class="border rounded-lg text-xs px-2 py-1 w-24" required>
                                    <select name="method" class="border rounded-lg text-xs px-2 py-1" required>
                                        <option value="cash">Cash</option>
                                        <option value="card">Card</option>
                                        <option value="mobile_money">Mobile Money</option>
                                        <option value="insurance">Insurance</option>
                                    </select>
                                    <button type="submit" class="bg-emerald-600 text-white text-xs font-medium px-3 py-1 rounded-lg">Pay</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="px-6 py-6 text-center text-gray-400">No pending payments</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
