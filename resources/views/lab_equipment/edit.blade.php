@extends('layouts.dashboard')

@section('title', 'Edit Lab Equipment - ' . config('app.name', 'Laravel'))
@section('page_title', 'Edit Lab Equipment')

@section('content')
<div class="max-w-2xl mx-auto space-y-5">
    <div class="bg-slate-800 rounded-md p-6 text-white">
        <p class="text-[10px] uppercase tracking-wider text-slate-400 mb-1">Laboratory</p>
        <h1 class="text-xl font-bold">Edit Lab Equipment</h1>
        <p class="text-xs text-slate-400 mt-1">Update details for {{ $labEquipment->name }}.</p>
    </div>

    <div class="bg-white rounded-md border border-gray-200 p-5">
    <form method="POST" action="{{ route('lab-equipment.update', $labEquipment) }}" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        @csrf
        @method('PUT')
        <div class="sm:col-span-2">
            <label class="block text-xs font-medium text-gray-700 mb-1">Name</label>
            <x-ui.input type="text" name="name" value="{{ $labEquipment->name }}" class="w-full border border-gray-200 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-slate-300 outline-none bg-white" required />
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-700 mb-1">Model</label>
            <x-ui.input type="text" name="model" value="{{ $labEquipment->model }}" class="w-full border border-gray-200 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-slate-300 outline-none bg-white" />
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-700 mb-1">Serial Number</label>
            <x-ui.input type="text" name="serial_number" value="{{ $labEquipment->serial_number }}" class="w-full border border-gray-200 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-slate-300 outline-none bg-white" />
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-700 mb-1">Manufacturer</label>
            <x-ui.input type="text" name="manufacturer" value="{{ $labEquipment->manufacturer }}" class="w-full border border-gray-200 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-slate-300 outline-none bg-white" />
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-700 mb-1">Purchase Date</label>
            <x-ui.input type="text" data-fp-date name="purchase_date" value="{{ $labEquipment->purchase_date?->format('Y-m-d') }}" class="w-full border border-gray-200 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-slate-300 outline-none bg-white" />
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-700 mb-1">Last Service Date</label>
            <x-ui.input type="text" data-fp-date name="last_service_date" value="{{ $labEquipment->last_service_date?->format('Y-m-d') }}" class="w-full border border-gray-200 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-slate-300 outline-none bg-white" />
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-700 mb-1">Next Service Date</label>
            <x-ui.input type="text" data-fp-date name="next_service_date" value="{{ $labEquipment->next_service_date?->format('Y-m-d') }}" class="w-full border border-gray-200 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-slate-300 outline-none bg-white" />
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-700 mb-1">Status</label>
            <x-ui.select name="status" class="w-full border border-gray-200 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-slate-300 outline-none bg-white">
                @foreach (['active', 'maintenance', 'retired'] as $status)
                    <option value="{{ $status }}" {{ $labEquipment->status === $status ? 'selected' : '' }}>{{ ucfirst($status) }}</option>
                @endforeach
            </x-ui.select>
        </div>
        <div class="sm:col-span-2">
            <label class="block text-xs font-medium text-gray-700 mb-1">Notes</label>
            <x-ui.textarea name="notes" class="w-full border border-gray-200 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-slate-300 outline-none bg-white" rows="3">{{ $labEquipment->notes }}</x-ui.textarea>
        </div>
        <div class="sm:col-span-2 pt-4">
            <button type="submit" class="bg-slate-800 text-white text-sm font-medium px-5 py-2 rounded hover:bg-slate-700">Update Equipment</button>
        </div>
    </form>
    </div>
</div>
@endsection
