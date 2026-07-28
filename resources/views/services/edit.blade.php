@extends('layouts.dashboard')

@section('title', 'Edit Service - ' . config('app.name', 'Laravel'))
@section('page_title', 'Edit Service')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="bg-gradient-to-r from-emerald-600 to-emerald-700 px-6 py-4">
            <h2 class="text-white font-bold text-lg flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                Edit Service
            </h2>
            <p class="text-emerald-100 text-xs mt-1">Update service details for {{ $service->name }}.</p>
        </div>
        <form method="POST" action="{{ route('services.update', $service) }}" class="p-6 space-y-5" data-ajax>
            @csrf
            @method('PUT')
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Service Name</label>
                    <input type="text" name="name" value="{{ $service->name }}" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all" required>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Price (TSh)</label>
                    <input type="number" step="0.01" name="price" value="{{ $service->price }}" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all" required min="0">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Duration (minutes)</label>
                    <input type="number" name="duration_minutes" value="{{ $service->duration_minutes }}" min="5" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all" required>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Color Code</label>
                    <input type="color" name="color" value="{{ $service->color ?? '#10b981' }}" class="w-full h-10 border border-gray-200 rounded-lg px-2 py-1 text-sm cursor-pointer">
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Description</label>
                    <textarea name="description" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 outline-none transition-all" rows="3">{{ $service->description }}</textarea>
                </div>
                <div class="sm:col-span-2 flex items-center gap-3 p-3 bg-gray-50 rounded-lg">
                    <input type="checkbox" name="is_active" value="1" {{ $service->is_active ? 'checked' : '' }} class="w-4 h-4 rounded border-gray-300 text-emerald-600 focus:ring-emerald-500">
                    <label class="text-sm text-gray-700 font-medium">Active</label>
                </div>
            </div>
            <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                <a href="{{ route('services.index') }}" class="text-sm text-gray-500 hover:text-gray-700 font-medium">← Back to Services</a>
                <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-bold px-6 py-2.5 rounded-lg shadow-sm transition-all flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Update Service
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
