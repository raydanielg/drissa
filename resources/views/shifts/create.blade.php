@extends('layouts.dashboard')

@section('title', 'Add Shift - ' . config('app.name', 'Laravel'))
@section('page_title', 'Add Shift')

@section('content')
<div class="max-w-xl mx-auto bg-white rounded-xl border border-gray-100 p-6 shadow-sm">
    <form method="POST" action="{{ route('shifts.store') }}" class="space-y-4">
        @csrf
        <div>
            <label class="block text-xs font-medium text-gray-700 mb-1">Shift Name</label>
            <x-ui.input type="text" name="name" placeholder="e.g. Morning Shift" class="w-full border rounded-lg px-3 py-2 text-sm" required />
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Start Time</label>
                <x-ui.input type="text" data-fp-time name="start_time" class="w-full border rounded-lg px-3 py-2 text-sm" required />
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">End Time</label>
                <x-ui.input type="text" data-fp-time name="end_time" class="w-full border rounded-lg px-3 py-2 text-sm" required />
            </div>
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-700 mb-1">Description</label>
            <x-ui.textarea name="description" class="w-full border rounded-lg px-3 py-2 text-sm" rows="3"></x-ui.textarea>
        </div>
        <div class="flex items-center gap-2">
            <input type="checkbox" name="is_active" value="1" checked class="rounded border-gray-300">
            <label class="text-sm text-gray-700">Active</label>
        </div>
        <div class="pt-4">
            <button type="submit" class="bg-slate-800 text-white text-sm font-medium px-5 py-2 rounded hover:bg-slate-700">Save Shift</button>
        </div>
    </form>
</div>
@endsection
