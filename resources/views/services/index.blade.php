@extends('layouts.dashboard')

@section('title', 'Services - ' . config('app.name', 'Laravel'))
@section('page_title', 'Services & Prices')

@section('content')
<div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
        <h2 class="text-sm font-semibold text-gray-900">Services Catalog</h2>
        <a href="{{ route('services.create') }}" class="bg-emerald-600 text-white text-xs font-medium px-3 py-1.5 rounded-lg hover:bg-emerald-700">+ Add Service</a>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left">
            <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                <tr>
                    <th class="px-6 py-3">Name</th>
                    <th class="px-6 py-3">Duration</th>
                    <th class="px-6 py-3">Price</th>
                    <th class="px-6 py-3">Color</th>
                    <th class="px-6 py-3">Status</th>
                    <th class="px-6 py-3">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($services as $service)
                    <tr class="border-t border-gray-100 hover:bg-gray-50/50">
                        <td class="px-6 py-3 font-medium">{{ $service->name }}</td>
                        <td class="px-6 py-3">{{ $service->duration_minutes }} min</td>
                        <td class="px-6 py-3">TSh {{ number_format($service->price, 2) }}</td>
                        <td class="px-6 py-3">
                            <span class="w-5 h-5 rounded-full inline-block border border-gray-200" style="background-color: {{ $service->color ?? '#10b981' }}"></span>
                        </td>
                        <td class="px-6 py-3">
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-medium {{ $service->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-700' }}">
                                {{ $service->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td class="px-6 py-3 flex items-center gap-2">
                            <a href="{{ route('services.edit', $service) }}" class="text-emerald-600 hover:text-emerald-700 text-xs font-medium">Edit</a>
                            <form method="POST" action="{{ route('services.destroy', $service) }}" data-ajax data-confirm="Delete this service?" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-700 text-xs font-medium">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-6 py-6 text-center text-gray-400">No services found</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-6 py-4 border-t border-gray-100">
        {{ $services->links() }}
    </div>
</div>
@endsection
