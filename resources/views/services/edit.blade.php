@extends('layouts.dashboard')

@section('title', 'Edit Service - ' . config('app.name', 'Laravel'))
@section('page_title', 'Edit Service')

@section('content')
<div class="max-w-2xl mx-auto space-y-5">
    {{-- Header --}}
    <div class="bg-slate-800 rounded-md p-6 text-white">
        <p class="text-[10px] uppercase tracking-wider text-slate-400 mb-1">Catalog</p>
        <h1 class="text-xl font-bold">Edit Service</h1>
        <p class="text-xs text-slate-400 mt-1">Update service details for {{ $service->name }}.</p>
    </div>

    <div class="bg-white rounded-md border border-gray-200 overflow-hidden">
        <form method="POST" action="{{ route('services.update', $service) }}" class="p-5 space-y-5" data-ajax>
            @csrf
            @method('PUT')
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Service Name</label>
                    <x-ui.input type="text" name="name" value="{{ $service->name }}" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-slate-300 outline-none" required />
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Price (TSh)</label>
                    <x-ui.input type="number" step="0.01" name="price" placeholder="0.00" value="{{ $service->price }}" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-slate-300 outline-none" required min="0" />
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Duration (minutes)</label>
                    <x-ui.input type="number" name="duration_minutes" placeholder="e.g. 30" value="{{ $service->duration_minutes }}" min="5" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-slate-300 outline-none" required />
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Color Code</label>
                    <input type="color" name="color" value="{{ $service->color ?? '#0f172a' }}" class="w-full h-10 border border-gray-200 rounded-lg px-2 py-1 text-sm cursor-pointer">
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Description</label>
                    <x-ui.textarea name="description" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-slate-300 outline-none" rows="3">{{ $service->description }}</x-ui.textarea>
                </div>
                <div class="sm:col-span-2 flex items-center gap-3 p-3 bg-gray-50 rounded-lg">
                    <input type="checkbox" name="is_active" value="1" {{ $service->is_active ? 'checked' : '' }} class="w-4 h-4 rounded border-gray-300 text-slate-600 focus:ring-slate-300">
                    <label class="text-sm text-gray-700 font-medium">Active</label>
                </div>
            </div>
            <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                <a href="{{ route('services.index') }}" class="px-4 py-2 text-sm text-gray-700 border border-gray-200 hover:bg-gray-100 rounded">Back</a>
                <button type="submit" class="bg-slate-800 hover:bg-slate-700 text-white text-sm font-medium px-5 py-2 rounded flex items-center gap-2">
                    <i class="hgi-stroke hgi-tick-02 text-base"></i>
                    Update Service
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
