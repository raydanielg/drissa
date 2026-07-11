@extends('layouts.dashboard')

@section('title', 'Edit Supplier - ' . config('app.name', 'Laravel'))
@section('page_title', 'Edit Supplier')

@section('content')
<div class="max-w-xl mx-auto bg-white rounded-xl border border-gray-100 p-6 shadow-sm">
    <form method="POST" action="{{ route('suppliers.update', $supplier) }}" class="space-y-4">
        @csrf
        @method('PUT')
        <div>
            <label class="block text-xs font-medium text-gray-700 mb-1">Supplier Name</label>
            <input type="text" name="name" value="{{ $supplier->name }}" class="w-full border rounded-lg px-3 py-2 text-sm" required>
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-700 mb-1">Contact Person</label>
            <input type="text" name="contact_person" value="{{ $supplier->contact_person }}" class="w-full border rounded-lg px-3 py-2 text-sm">
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-700 mb-1">Email</label>
            <input type="email" name="email" value="{{ $supplier->email }}" class="w-full border rounded-lg px-3 py-2 text-sm">
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-700 mb-1">Phone</label>
            <input type="text" name="phone" value="{{ $supplier->phone }}" class="w-full border rounded-lg px-3 py-2 text-sm">
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-700 mb-1">Address</label>
            <textarea name="address" class="w-full border rounded-lg px-3 py-2 text-sm" rows="3">{{ $supplier->address }}</textarea>
        </div>
        <div class="flex items-center gap-2">
            <input type="checkbox" name="is_active" value="1" {{ $supplier->is_active ? 'checked' : '' }} class="rounded border-gray-300">
            <label class="text-sm text-gray-700">Active</label>
        </div>
        <div class="pt-4">
            <button type="submit" class="bg-emerald-600 text-white text-sm font-medium px-5 py-2 rounded-lg hover:bg-emerald-700">Update Supplier</button>
        </div>
    </form>
</div>
@endsection
