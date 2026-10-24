@extends('layouts.dashboard')

@section('title', 'Edit Clinic Room - ' . config('app.name', 'Laravel'))
@section('page_title', 'Edit Clinic Room')

@section('content')
<div class="max-w-2xl mx-auto space-y-5">
    <div class="bg-slate-800 rounded-md p-6 text-white">
        <p class="text-[10px] uppercase tracking-wider text-slate-400 mb-1">Clinic Management</p>
        <h1 class="text-xl font-bold">Edit Clinic Room</h1>
        <p class="text-xs text-slate-400 mt-1">Update details for {{ $clinicRoom->name }}.</p>
    </div>

    <div class="bg-white rounded-md border border-gray-200 p-5">
    <form method="POST" action="{{ route('clinic-rooms.update', $clinicRoom) }}" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        @csrf
        @method('PUT')
        <div class="sm:col-span-2">
            <label class="block text-xs font-medium text-gray-700 mb-1">Room Name</label>
            <x-ui.input type="text" name="name" value="{{ $clinicRoom->name }}" class="w-full border border-gray-200 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-slate-300 outline-none bg-white" required />
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-700 mb-1">Code</label>
            <x-ui.input type="text" name="code" value="{{ $clinicRoom->code }}" class="w-full border border-gray-200 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-slate-300 outline-none bg-white" />
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-700 mb-1">Department</label>
            <x-ui.select name="department_id" class="w-full border border-gray-200 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-slate-300 outline-none bg-white">
                <option value="">Select department</option>
                @foreach ($departments as $department)
                    <option value="{{ $department->id }}" {{ $clinicRoom->department_id == $department->id ? 'selected' : '' }}>{{ $department->name }}</option>
                @endforeach
            </x-ui.select>
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-700 mb-1">Type</label>
            <x-ui.select name="type" class="w-full border border-gray-200 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-slate-300 outline-none bg-white">
                @foreach (['consultation', 'procedure', 'ward', 'emergency'] as $type)
                    <option value="{{ $type }}" {{ $clinicRoom->type === $type ? 'selected' : '' }}>{{ ucfirst($type) }}</option>
                @endforeach
            </x-ui.select>
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-700 mb-1">Status</label>
            <x-ui.select name="status" class="w-full border border-gray-200 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-slate-300 outline-none bg-white">
                @foreach (['available', 'occupied', 'maintenance'] as $status)
                    <option value="{{ $status }}" {{ $clinicRoom->status === $status ? 'selected' : '' }}>{{ ucfirst($status) }}</option>
                @endforeach
            </x-ui.select>
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-700 mb-1">Capacity</label>
            <x-ui.input type="number" name="capacity" value="{{ $clinicRoom->capacity }}" min="1" class="w-full border border-gray-200 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-slate-300 outline-none bg-white" />
        </div>
        <div class="sm:col-span-2">
            <label class="block text-xs font-medium text-gray-700 mb-1">Description</label>
            <x-ui.textarea name="description" class="w-full border border-gray-200 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-slate-300 outline-none bg-white" rows="3">{{ $clinicRoom->description }}</x-ui.textarea>
        </div>
        <div class="sm:col-span-2 pt-4">
            <button type="submit" class="bg-slate-800 text-white text-sm font-medium px-5 py-2 rounded hover:bg-slate-700">Update Room</button>
        </div>
    </form>
    </div>
</div>
@endsection
