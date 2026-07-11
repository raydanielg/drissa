@extends('layouts.dashboard')

@section('title', 'Edit Lab Equipment - ' . config('app.name', 'Laravel'))
@section('page_title', 'Edit Lab Equipment')

@section('content')
<div class="max-w-2xl mx-auto bg-white rounded-xl border border-gray-100 p-6 shadow-sm">
    <form method="POST" action="{{ route('lab-equipment.update', $labEquipment) }}" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        @csrf
        @method('PUT')
        <div class="sm:col-span-2">
            <label class="block text-xs font-medium text-gray-700 mb-1">Name</label>
            <input type="text" name="name" value="{{ $labEquipment->name }}" class="w-full border rounded-lg px-3 py-2 text-sm" required>
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-700 mb-1">Model</label>
            <input type="text" name="model" value="{{ $labEquipment->model }}" class="w-full border rounded-lg px-3 py-2 text-sm">
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-700 mb-1">Serial Number</label>
            <input type="text" name="serial_number" value="{{ $labEquipment->serial_number }}" class="w-full border rounded-lg px-3 py-2 text-sm">
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-700 mb-1">Manufacturer</label>
            <input type="text" name="manufacturer" value="{{ $labEquipment->manufacturer }}" class="w-full border rounded-lg px-3 py-2 text-sm">
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-700 mb-1">Purchase Date</label>
            <input type="date" name="purchase_date" value="{{ $labEquipment->purchase_date?->format('Y-m-d') }}" class="w-full border rounded-lg px-3 py-2 text-sm">
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-700 mb-1">Last Service Date</label>
            <input type="date" name="last_service_date" value="{{ $labEquipment->last_service_date?->format('Y-m-d') }}" class="w-full border rounded-lg px-3 py-2 text-sm">
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-700 mb-1">Next Service Date</label>
            <input type="date" name="next_service_date" value="{{ $labEquipment->next_service_date?->format('Y-m-d') }}" class="w-full border rounded-lg px-3 py-2 text-sm">
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-700 mb-1">Status</label>
            <select name="status" class="w-full border rounded-lg px-3 py-2 text-sm">
                @foreach (['active', 'maintenance', 'retired'] as $status)
                    <option value="{{ $status }}" {{ $labEquipment->status === $status ? 'selected' : '' }}>{{ ucfirst($status) }}</option>
                @endforeach
            </select>
        </div>
        <div class="sm:col-span-2">
            <label class="block text-xs font-medium text-gray-700 mb-1">Notes</label>
            <textarea name="notes" class="w-full border rounded-lg px-3 py-2 text-sm" rows="3">{{ $labEquipment->notes }}</textarea>
        </div>
        <div class="sm:col-span-2 pt-4">
            <button type="submit" class="bg-emerald-600 text-white text-sm font-medium px-5 py-2 rounded-lg hover:bg-emerald-700">Update Equipment</button>
        </div>
    </form>
</div>
@endsection
