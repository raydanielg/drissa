@extends('layouts.dashboard')

@section('title', 'Edit Clinic Room - ' . config('app.name', 'Laravel'))
@section('page_title', 'Edit Clinic Room')

@section('content')
<div class="max-w-2xl mx-auto bg-white rounded-xl border border-gray-100 p-6 shadow-sm">
    <form method="POST" action="{{ route('clinic-rooms.update', $clinicRoom) }}" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        @csrf
        @method('PUT')
        <div class="sm:col-span-2">
            <label class="block text-xs font-medium text-gray-700 mb-1">Room Name</label>
            <input type="text" name="name" value="{{ $clinicRoom->name }}" class="w-full border rounded-lg px-3 py-2 text-sm" required>
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-700 mb-1">Code</label>
            <input type="text" name="code" value="{{ $clinicRoom->code }}" class="w-full border rounded-lg px-3 py-2 text-sm">
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-700 mb-1">Department</label>
            <select name="department_id" class="w-full border rounded-lg px-3 py-2 text-sm">
                <option value="">Select department</option>
                @foreach ($departments as $department)
                    <option value="{{ $department->id }}" {{ $clinicRoom->department_id == $department->id ? 'selected' : '' }}>{{ $department->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-700 mb-1">Type</label>
            <select name="type" class="w-full border rounded-lg px-3 py-2 text-sm">
                @foreach (['consultation', 'procedure', 'ward', 'emergency'] as $type)
                    <option value="{{ $type }}" {{ $clinicRoom->type === $type ? 'selected' : '' }}>{{ ucfirst($type) }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-700 mb-1">Status</label>
            <select name="status" class="w-full border rounded-lg px-3 py-2 text-sm">
                @foreach (['available', 'occupied', 'maintenance'] as $status)
                    <option value="{{ $status }}" {{ $clinicRoom->status === $status ? 'selected' : '' }}>{{ ucfirst($status) }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-700 mb-1">Capacity</label>
            <input type="number" name="capacity" value="{{ $clinicRoom->capacity }}" min="1" class="w-full border rounded-lg px-3 py-2 text-sm">
        </div>
        <div class="sm:col-span-2">
            <label class="block text-xs font-medium text-gray-700 mb-1">Description</label>
            <textarea name="description" class="w-full border rounded-lg px-3 py-2 text-sm" rows="3">{{ $clinicRoom->description }}</textarea>
        </div>
        <div class="sm:col-span-2 pt-4">
            <button type="submit" class="bg-emerald-600 text-white text-sm font-medium px-5 py-2 rounded-lg hover:bg-emerald-700">Update Room</button>
        </div>
    </form>
</div>
@endsection
