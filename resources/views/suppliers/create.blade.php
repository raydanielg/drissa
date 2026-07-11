@extends('layouts.dashboard')

@section('title', 'Add Supplier - ' . config('app.name', 'Laravel'))
@section('page_title', 'Add Supplier')

@section('content')
<div class="max-w-xl mx-auto bg-white rounded-xl border border-gray-100 p-6 shadow-sm">
    <form method="POST" action="{{ route('suppliers.store') }}" class="space-y-4">
        @csrf
        <div>
            <label class="block text-xs font-medium text-gray-700 mb-1">Supplier Name</label>
            <input type="text" name="name" class="w-full border rounded-lg px-3 py-2 text-sm" required>
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-700 mb-1">Contact Person</label>
            <input type="text" name="contact_person" class="w-full border rounded-lg px-3 py-2 text-sm">
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-700 mb-1">Email</label>
            <input type="email" name="email" class="w-full border rounded-lg px-3 py-2 text-sm">
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-700 mb-1">Phone</label>
            <input type="text" name="phone" class="w-full border rounded-lg px-3 py-2 text-sm">
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-700 mb-1">Address</label>
            <textarea name="address" class="w-full border rounded-lg px-3 py-2 text-sm" rows="3"></textarea>
        </div>
        <div class="flex items-center gap-2">
            <input type="checkbox" name="is_active" value="1" checked class="rounded border-gray-300">
            <label class="text-sm text-gray-700">Active</label>
        </div>
        <div class="pt-4">
            <button type="submit" class="bg-emerald-600 text-white text-sm font-medium px-5 py-2 rounded-lg hover:bg-emerald-700">Save Supplier</button>
        </div>
    </form>
</div>
@endsection
