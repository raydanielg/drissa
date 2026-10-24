@extends('layouts.dashboard')

@section('title', 'Edit Supplier - ' . config('app.name', 'Laravel'))
@section('page_title', 'Edit Supplier')

@section('content')
<div class="max-w-xl mx-auto space-y-5">
    <div class="bg-slate-800 rounded-md p-6 text-white">
        <p class="text-[10px] uppercase tracking-wider text-slate-400 mb-1">Logistics</p>
        <h1 class="text-xl font-bold">Edit Supplier</h1>
        <p class="text-xs text-slate-400 mt-1">Update details for {{ $supplier->name }}.</p>
    </div>

    <div class="bg-white rounded-md border border-gray-200 p-5">
    <form method="POST" action="{{ route('suppliers.update', $supplier) }}" class="space-y-4">
        @csrf
        @method('PUT')
        <div>
            <label class="block text-xs font-medium text-gray-700 mb-1">Supplier Name</label>
            <x-ui.input type="text" name="name" placeholder="e.g. MedSupply Tanzania Ltd" value="{{ $supplier->name }}" class="w-full border border-gray-200 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-slate-300 outline-none" required />
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-700 mb-1">Contact Person</label>
            <x-ui.input type="text" name="contact_person" placeholder="e.g. John Mwakalinga" value="{{ $supplier->contact_person }}" class="w-full border border-gray-200 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-slate-300 outline-none" />
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-700 mb-1">Email</label>
            <x-ui.input type="email" name="email" placeholder="e.g. sales@supplier.co.tz" value="{{ $supplier->email }}" class="w-full border border-gray-200 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-slate-300 outline-none" />
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-700 mb-1">Phone</label>
            <x-ui.input type="text" name="phone" placeholder="e.g. 0678 000 000" value="{{ $supplier->phone }}" class="w-full border border-gray-200 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-slate-300 outline-none" />
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-700 mb-1">Address</label>
            <x-ui.textarea name="address" placeholder="Supplier address or location..." class="w-full border border-gray-200 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-slate-300 outline-none" rows="3">{{ $supplier->address }}</x-ui.textarea>
        </div>
        <div class="flex items-center gap-2">
            <input type="checkbox" name="is_active" value="1" {{ $supplier->is_active ? 'checked' : '' }} class="rounded border-gray-300 text-slate-600 focus:ring-slate-300">
            <label class="text-sm text-gray-700">Active</label>
        </div>
        <div class="pt-4">
            <button type="submit" class="bg-slate-800 text-white text-sm font-medium px-5 py-2 rounded hover:bg-slate-700">Update Supplier</button>
        </div>
    </form>
    </div>
</div>
@endsection
