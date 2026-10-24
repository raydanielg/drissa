@extends('layouts.dashboard')

@section('title', 'Edit Patient - ' . config('app.name', 'Laravel'))
@section('page_title', 'Edit Patient')

@section('content')
<div class="max-w-4xl mx-auto space-y-5">
    <div class="bg-slate-800 rounded-md p-6 text-white">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 rounded-full border-2 border-slate-600 bg-slate-700 flex items-center justify-center text-white text-lg font-bold">
                {{ strtoupper(substr($patient->first_name, 0, 1)) }}
            </div>
            <div>
                <p class="text-[10px] uppercase tracking-wider text-slate-400 mb-1">Edit Patient</p>
                <h1 class="text-xl font-bold">{{ $patient->fullName() }}</h1>
                <p class="text-xs text-slate-400 mt-1">MRN: {{ $patient->mrn }}</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-md border border-gray-200 p-5">
    <form method="POST" action="{{ route('patients.update', $patient) }}" class="grid grid-cols-1 md:grid-cols-2 gap-4">
        @csrf
        @method('PUT')
        <div>
            <label class="block text-xs font-medium text-gray-700 mb-1">First Name</label>
            <x-ui.input type="text" name="first_name" value="{{ old('first_name', $patient->first_name) }}" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-slate-300" required />
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-700 mb-1">Last Name</label>
            <x-ui.input type="text" name="last_name" value="{{ old('last_name', $patient->last_name) }}" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-slate-300" required />
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-700 mb-1">Date of Birth</label>
            <x-ui.input type="text" data-fp-date name="date_of_birth" value="{{ old('date_of_birth', $patient->date_of_birth?->format('Y-m-d')) }}" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-slate-300" required />
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-700 mb-1">Gender</label>
            <x-ui.select name="gender" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-slate-300" required>
                <option value="male" {{ old('gender', $patient->gender) === 'male' ? 'selected' : '' }}>Male</option>
                <option value="female" {{ old('gender', $patient->gender) === 'female' ? 'selected' : '' }}>Female</option>
                <option value="other" {{ old('gender', $patient->gender) === 'other' ? 'selected' : '' }}>Other</option>
            </x-ui.select>
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-700 mb-1">Phone</label>
            <x-ui.input type="text" name="phone" value="{{ old('phone', $patient->phone) }}" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-slate-300" />
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-700 mb-1">National ID</label>
            <x-ui.input type="text" name="national_id" value="{{ old('national_id', $patient->national_id) }}" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-slate-300" />
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-700 mb-1">Blood Group</label>
            <x-ui.input type="text" name="blood_group" value="{{ old('blood_group', $patient->blood_group) }}" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-slate-300" />
        </div>
        <div class="md:col-span-2">
            <label class="block text-xs font-medium text-gray-700 mb-1">Address</label>
            <x-ui.textarea name="address" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-slate-300" rows="2">{{ old('address', $patient->address) }}</x-ui.textarea>
        </div>
        <div class="md:col-span-2">
            <label class="block text-xs font-medium text-gray-700 mb-1">Allergies</label>
            <x-ui.textarea name="allergies" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-slate-300" rows="2">{{ old('allergies', $patient->allergies) }}</x-ui.textarea>
        </div>
        <div class="md:col-span-2 flex justify-end gap-2 pt-2">
            <a href="{{ route('patients.show', $patient) }}" class="px-4 py-2 text-sm text-gray-700 border border-gray-200 hover:bg-gray-100 rounded">Cancel</a>
            <button type="submit" class="px-4 py-2 bg-slate-800 text-white text-sm font-medium rounded hover:bg-slate-700">Update Patient</button>
        </div>
    </form>
    </div>
</div>
@endsection
