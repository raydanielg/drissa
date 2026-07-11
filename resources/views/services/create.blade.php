@extends('layouts.dashboard')

@section('title', 'Add Service - ' . config('app.name', 'Laravel'))
@section('page_title', 'Add Service')

@section('content')
<div class="max-w-2xl mx-auto bg-white rounded-xl border border-gray-100 p-6 shadow-sm">
    <form method="POST" action="{{ route('services.store') }}" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        @csrf
        <div class="sm:col-span-2">
            <label class="block text-xs font-medium text-gray-700 mb-1">Service Name</label>
            <input type="text" name="name" class="w-full border rounded-lg px-3 py-2 text-sm" required>
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-700 mb-1">Price</label>
            <input type="number" step="0.01" name="price" class="w-full border rounded-lg px-3 py-2 text-sm" required min="0">
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-700 mb-1">Duration (minutes)</label>
            <input type="number" name="duration_minutes" value="30" min="5" class="w-full border rounded-lg px-3 py-2 text-sm" required>
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-700 mb-1">Color Code</label>
            <input type="color" name="color" value="#10b981" class="w-full h-10 border rounded-lg px-3 py-2 text-sm">
        </div>
        <div class="sm:col-span-2">
            <label class="block text-xs font-medium text-gray-700 mb-1">Description</label>
            <textarea name="description" class="w-full border rounded-lg px-3 py-2 text-sm" rows="3"></textarea>
        </div>
        <div class="sm:col-span-2 flex items-center gap-2">
            <input type="checkbox" name="is_active" value="1" checked class="rounded border-gray-300">
            <label class="text-sm text-gray-700">Active</label>
        </div>
        <div class="sm:col-span-2 pt-4">
            <button type="submit" class="bg-emerald-600 text-white text-sm font-medium px-5 py-2 rounded-lg hover:bg-emerald-700">Save Service</button>
        </div>
    </form>
</div>
@endsection
