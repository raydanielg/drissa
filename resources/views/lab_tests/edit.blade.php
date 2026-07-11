@extends('layouts.dashboard')

@section('title', 'Edit Lab Test Type - ' . config('app.name', 'Laravel'))
@section('page_title', 'Edit Lab Test Type')

@section('content')
<div class="max-w-2xl mx-auto bg-white rounded-xl border border-gray-100 p-6 shadow-sm">
    <form method="POST" action="{{ route('lab-tests.update', $labTest) }}" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        @csrf
        @method('PUT')
        <div class="sm:col-span-2">
            <label class="block text-xs font-medium text-gray-700 mb-1">Name</label>
            <input type="text" name="name" value="{{ $labTest->name }}" class="w-full border rounded-lg px-3 py-2 text-sm" required>
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-700 mb-1">Code</label>
            <input type="text" name="code" value="{{ $labTest->code }}" class="w-full border rounded-lg px-3 py-2 text-sm">
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-700 mb-1">Unit</label>
            <input type="text" name="unit" value="{{ $labTest->unit }}" class="w-full border rounded-lg px-3 py-2 text-sm">
        </div>
        <div class="sm:col-span-2">
            <label class="block text-xs font-medium text-gray-700 mb-1">Reference Range</label>
            <input type="text" name="reference_range" value="{{ $labTest->reference_range }}" class="w-full border rounded-lg px-3 py-2 text-sm">
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-700 mb-1">Price</label>
            <input type="number" step="0.01" name="price" value="{{ $labTest->price }}" class="w-full border rounded-lg px-3 py-2 text-sm" required min="0">
        </div>
        <div class="sm:col-span-2">
            <label class="block text-xs font-medium text-gray-700 mb-1">Description</label>
            <textarea name="description" class="w-full border rounded-lg px-3 py-2 text-sm" rows="3">{{ $labTest->description }}</textarea>
        </div>
        <div class="sm:col-span-2 flex items-center gap-2">
            <input type="checkbox" name="is_active" value="1" {{ $labTest->is_active ? 'checked' : '' }} class="rounded border-gray-300">
            <label class="text-sm text-gray-700">Active</label>
        </div>
        <div class="sm:col-span-2 pt-4">
            <button type="submit" class="bg-emerald-600 text-white text-sm font-medium px-5 py-2 rounded-lg hover:bg-emerald-700">Update Test Type</button>
        </div>
    </form>
</div>
@endsection
