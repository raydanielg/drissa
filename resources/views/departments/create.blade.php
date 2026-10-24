@extends('layouts.dashboard')

@section('title', 'Add Department - ' . config('app.name', 'Laravel'))
@section('page_title', 'Add Department')

@section('content')
<div class="max-w-xl mx-auto space-y-5">
    <div class="bg-slate-800 rounded-md p-6 text-white">
        <p class="text-[10px] uppercase tracking-wider text-slate-400 mb-1">Human Resources</p>
        <h1 class="text-xl font-bold">Add Department</h1>
        <p class="text-xs text-slate-400 mt-1">Create a new hospital department or unit.</p>
    </div>

    <div class="bg-white rounded-md border border-gray-200 p-5">
    <form method="POST" action="{{ route('departments.store') }}" class="space-y-4">
        @csrf
        <div>
            <label class="block text-xs font-medium text-gray-700 mb-1">Name</label>
            <x-ui.input type="text" name="name" class="w-full border border-gray-200 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-slate-300 outline-none" required />
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-700 mb-1">Code</label>
            <x-ui.input type="text" name="code" class="w-full border border-gray-200 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-slate-300 outline-none" />
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-700 mb-1">Description</label>
            <x-ui.textarea name="description" class="w-full border border-gray-200 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-slate-300 outline-none" rows="3"></x-ui.textarea>
        </div>
        <div class="flex items-center gap-2">
            <input type="checkbox" name="is_active" value="1" checked class="rounded border-gray-300 text-slate-600 focus:ring-slate-300">
            <label class="text-sm text-gray-700">Active</label>
        </div>
        <div class="pt-4">
            <button type="submit" class="bg-slate-800 text-white text-sm font-medium px-5 py-2 rounded hover:bg-slate-700">Save Department</button>
        </div>
    </form>
    </div>
</div>
@endsection
