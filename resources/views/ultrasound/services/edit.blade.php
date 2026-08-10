@extends('layouts.dashboard')

@section('title', 'Edit Ultrasound Service - ' . config('app.name', 'Laravel'))
@section('page_title', 'Edit Ultrasound Service')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <h2 class="text-lg font-bold text-gray-900">Edit Ultrasound Service</h2>
        <a href="{{ route('ultrasound-services.index') }}" class="text-sm text-gray-600 hover:text-gray-900">Back</a>
    </div>

    <form method="POST" action="{{ route('ultrasound-services.update', $ultrasoundService) }}" class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 space-y-4">
        @csrf
        @method('PUT')
        <div>
            <label class="block text-xs font-medium text-gray-700 mb-1">Name <span class="text-red-500">*</span></label>
            <input type="text" name="name" value="{{ $ultrasoundService->name }}" required class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-700 mb-1">Code</label>
            <input type="text" name="code" value="{{ $ultrasoundService->code }}" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-700 mb-1">Price (TSh) <span class="text-red-500">*</span></label>
            <input type="number" name="price" value="{{ $ultrasoundService->price }}" required min="0" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-700 mb-1">Description</label>
            <textarea name="description" rows="3" class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">{{ $ultrasoundService->description }}</textarea>
        </div>
        <div>
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" name="is_active" value="1" {{ $ultrasoundService->is_active ? 'checked' : '' }} class="w-4 h-4 text-emerald-600 rounded focus:ring-emerald-500">
                <span class="text-sm text-gray-700">Active</span>
            </label>
        </div>
        <div class="flex justify-end gap-2 pt-2 border-t border-gray-100">
            <a href="{{ route('ultrasound-services.index') }}" class="px-4 py-2 text-sm text-gray-600 hover:bg-gray-100 rounded-lg">Cancel</a>
            <button type="submit" class="px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700">Update Service</button>
        </div>
    </form>
</div>
@endsection
