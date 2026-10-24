@extends('layouts.dashboard')

@section('title', 'Add Lab Test Type - ' . config('app.name', 'Laravel'))
@section('page_title', 'Add Lab Test Type')

@section('content')
<div class="max-w-2xl mx-auto space-y-5">
    <div class="bg-slate-800 rounded-md p-6 text-white">
        <p class="text-[10px] uppercase tracking-wider text-slate-400 mb-1">Laboratory</p>
        <h1 class="text-xl font-bold">Add Lab Test Type</h1>
        <p class="text-xs text-slate-400 mt-1">Create a new laboratory test with pricing and reference ranges.</p>
    </div>

    <div class="bg-white rounded-md border border-gray-200 p-5">
    <form method="POST" action="{{ route('lab-tests.store') }}" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        @csrf
        <div class="sm:col-span-2">
            <label class="block text-xs font-medium text-gray-700 mb-1">Name</label>
            <x-ui.input type="text" name="name" class="w-full border border-gray-200 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-slate-300 outline-none" required />
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-700 mb-1">Code</label>
            <x-ui.input type="text" name="code" class="w-full border border-gray-200 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-slate-300 outline-none" />
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-700 mb-1">Unit</label>
            <x-ui.input type="text" name="unit" class="w-full border border-gray-200 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-slate-300 outline-none" />
        </div>
        <div class="sm:col-span-2">
            <label class="block text-xs font-medium text-gray-700 mb-1">Reference Range (General)</label>
            <x-ui.input type="text" name="reference_range" class="w-full border border-gray-200 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-slate-300 outline-none" placeholder="e.g. 4.0-11.0 x10^3/uL" />
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-700 mb-1">Reference Range (Male)</label>
            <x-ui.input type="text" name="reference_range_male" class="w-full border border-gray-200 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-slate-300 outline-none" placeholder="e.g. 3.5-5.5" />
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-700 mb-1">Reference Range (Female)</label>
            <x-ui.input type="text" name="reference_range_female" class="w-full border border-gray-200 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-slate-300 outline-none" placeholder="e.g. 3.0-5.0" />
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-700 mb-1">Reference Range (Pregnant)</label>
            <x-ui.input type="text" name="reference_range_pregnant" class="w-full border border-gray-200 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-slate-300 outline-none" placeholder="e.g. 2.5-4.5" />
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-700 mb-1">Reference Range (Safe Days)</label>
            <x-ui.input type="text" name="reference_range_safe" class="w-full border border-gray-200 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-slate-300 outline-none" placeholder="e.g. 3.0-5.0" />
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-700 mb-1">Price</label>
            <x-ui.input type="number" step="0.01" name="price" class="w-full border border-gray-200 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-slate-300 outline-none" required min="0" />
        </div>
        <div class="sm:col-span-2">
            <label class="block text-xs font-medium text-gray-700 mb-1">Description</label>
            <x-ui.textarea name="description" class="w-full border border-gray-200 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-slate-300 outline-none" rows="3"></x-ui.textarea>
        </div>
        <div class="sm:col-span-2 flex items-center gap-2">
            <input type="checkbox" name="is_active" value="1" checked class="rounded border-gray-300 text-slate-600 focus:ring-slate-300">
            <label class="text-sm text-gray-700">Active</label>
        </div>
        <div class="sm:col-span-2 pt-4">
            <button type="submit" class="bg-slate-800 text-white text-sm font-medium px-5 py-2 rounded hover:bg-slate-700">Save Test Type</button>
        </div>
    </form>
    </div>
</div>
@endsection
