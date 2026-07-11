@extends('layouts.dashboard')

@section('title', 'Edit Patient - ' . config('app.name', 'Laravel'))
@section('page_title', 'Edit Patient')

@section('content')
<div class="max-w-4xl mx-auto bg-white rounded-xl border border-gray-100 p-6 shadow-sm">
    <div class="flex items-center gap-4 mb-6">
        <div class="w-14 h-14 rounded-full bg-gradient-to-br from-emerald-500 to-emerald-700 flex items-center justify-center text-white text-lg font-bold">
            {{ strtoupper(substr($patient->first_name, 0, 1)) }}
        </div>
        <div>
            <h2 class="text-xl font-bold text-gray-900">{{ $patient->fullName() }}</h2>
            <p class="text-sm text-gray-500">MRN: {{ $patient->mrn }}</p>
        </div>
    </div>

    <form method="POST" action="{{ route('patients.update', $patient) }}" class="grid grid-cols-1 md:grid-cols-2 gap-4">
        @csrf
        @method('PUT')
        <div>
            <label class="block text-xs font-medium text-gray-700 mb-1">First Name</label>
            <input type="text" name="first_name" value="{{ old('first_name', $patient->first_name) }}" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" required>
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-700 mb-1">Last Name</label>
            <input type="text" name="last_name" value="{{ old('last_name', $patient->last_name) }}" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" required>
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-700 mb-1">Date of Birth</label>
            <input type="date" name="date_of_birth" value="{{ old('date_of_birth', $patient->date_of_birth?->format('Y-m-d')) }}" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" required>
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-700 mb-1">Gender</label>
            <select name="gender" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" required>
                <option value="male" {{ old('gender', $patient->gender) === 'male' ? 'selected' : '' }}>Male</option>
                <option value="female" {{ old('gender', $patient->gender) === 'female' ? 'selected' : '' }}>Female</option>
                <option value="other" {{ old('gender', $patient->gender) === 'other' ? 'selected' : '' }}>Other</option>
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-700 mb-1">Phone</label>
            <input type="text" name="phone" value="{{ old('phone', $patient->phone) }}" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-700 mb-1">National ID</label>
            <input type="text" name="national_id" value="{{ old('national_id', $patient->national_id) }}" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-700 mb-1">Blood Group</label>
            <input type="text" name="blood_group" value="{{ old('blood_group', $patient->blood_group) }}" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
        </div>
        <div class="md:col-span-2">
            <label class="block text-xs font-medium text-gray-700 mb-1">Address</label>
            <textarea name="address" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" rows="2">{{ old('address', $patient->address) }}</textarea>
        </div>
        <div class="md:col-span-2">
            <label class="block text-xs font-medium text-gray-700 mb-1">Allergies</label>
            <textarea name="allergies" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500" rows="2">{{ old('allergies', $patient->allergies) }}</textarea>
        </div>
        <div class="md:col-span-2 flex justify-end gap-2 pt-2">
            <a href="{{ route('patients.show', $patient) }}" class="px-4 py-2 text-sm text-gray-600 hover:bg-gray-100 rounded-lg">Cancel</a>
            <button type="submit" class="px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700">Update Patient</button>
        </div>
    </form>
</div>
@endsection
