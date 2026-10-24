@extends('layouts.dashboard')

@section('title', 'Add Clinic Room - ' . config('app.name', 'Laravel'))
@section('page_title', 'Add Clinic Room')

@section('content')
<div class="max-w-2xl mx-auto space-y-5">
    <div class="bg-slate-800 rounded-md p-6 text-white">
        <p class="text-[10px] uppercase tracking-wider text-slate-400 mb-1">Clinic Management</p>
        <h1 class="text-xl font-bold">Add Clinic Room</h1>
        <p class="text-xs text-slate-400 mt-1">Create a new consultation room, ward or facility.</p>
    </div>

    <div class="bg-white rounded-md border border-gray-200 p-5">
    <form method="POST" action="{{ route('clinic-rooms.store') }}" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        @csrf
        <div class="sm:col-span-2">
            <label class="block text-xs font-medium text-gray-700 mb-1">Room Name</label>
            <x-ui.input type="text" name="name" class="w-full border border-gray-200 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-slate-300 outline-none bg-white" required />
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-700 mb-1">Code</label>
            <x-ui.input type="text" name="code" class="w-full border border-gray-200 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-slate-300 outline-none bg-white" />
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-700 mb-1">Department</label>
            <x-ui.select name="department_id" class="w-full border border-gray-200 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-slate-300 outline-none bg-white">
                <option value="">Select department</option>
                @foreach ($departments as $department)
                    <option value="{{ $department->id }}">{{ $department->name }}</option>
                @endforeach
            </x-ui.select>
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-700 mb-1">Type</label>
            <x-ui.select name="type" class="w-full border border-gray-200 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-slate-300 outline-none bg-white">
                <option value="consultation">Consultation</option>
                <option value="procedure">Procedure</option>
                <option value="ward">Ward</option>
                <option value="emergency">Emergency</option>
            </x-ui.select>
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-700 mb-1">Status</label>
            <x-ui.select name="status" class="w-full border border-gray-200 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-slate-300 outline-none bg-white">
                <option value="available">Available</option>
                <option value="occupied">Occupied</option>
                <option value="maintenance">Maintenance</option>
            </x-ui.select>
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-700 mb-1">Capacity</label>
            <x-ui.input type="number" name="capacity" value="1" min="1" class="w-full border border-gray-200 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-slate-300 outline-none bg-white" />
        </div>
        <div class="sm:col-span-2">
            <label class="block text-xs font-medium text-gray-700 mb-1">Description</label>
            <x-ui.textarea name="description" class="w-full border border-gray-200 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-slate-300 outline-none bg-white" rows="3"></x-ui.textarea>
        </div>
        <div class="sm:col-span-2 pt-4">
            <button type="submit" class="bg-slate-800 text-white text-sm font-medium px-5 py-2 rounded hover:bg-slate-700">Save Room</button>
        </div>
    </form>
    </div>
</div>
@endsection
