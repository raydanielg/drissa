@extends('layouts.dashboard')

@section('title', 'Services - ' . config('app.name', 'Laravel'))
@section('page_title', 'Services & Prices')

@section('content')
<div class="max-w-7xl mx-auto space-y-5">

    {{-- Services Table --}}
    <div class="bg-slate-800 rounded-md p-6 text-white flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <p class="text-[10px] uppercase tracking-wider text-slate-400 mb-1">Catalog</p>
            <h1 class="text-xl font-bold">Services & Prices</h1>
            <p class="text-xs text-slate-400 mt-1">Manage your clinic service catalog.</p>
        </div>
        <a href="{{ route('services.create') }}" class="inline-flex items-center gap-2 bg-white text-slate-800 text-sm font-medium px-4 py-2 rounded hover:bg-slate-100 self-start">
            <i class="hgi-stroke hgi-add-01 text-base"></i>
            Add Service
        </a>
    </div>

    {{-- Services Table --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
        <div class="bg-white rounded-md border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-medium text-gray-500 uppercase tracking-wide">Total</span>
                <i class="hgi-stroke hgi-building-03 text-base text-gray-400"></i>
            </div>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ $services->total() }}</p>
        </div>
        @php
            $activeCount = \App\Models\Service::where('is_active', true)->count();
            $avgPrice = \App\Models\Service::avg('price');
            $totalAppointments = \App\Models\Appointment::count();
        @endphp
        <div class="bg-white rounded-md border border-emerald-200 p-4">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-medium text-gray-500 uppercase tracking-wide">Active</span>
                <i class="hgi-stroke hgi-checkmark-circle-02 text-base text-gray-400"></i>
            </div>
            <p class="text-2xl font-bold text-emerald-600 mt-1">{{ $activeCount }}</p>
        </div>
        <div class="bg-white rounded-md border border-amber-200 p-4">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-medium text-gray-500 uppercase tracking-wide">Avg Price</span>
                <i class="hgi-stroke hgi-money-03 text-base text-gray-400"></i>
            </div>
            <p class="text-2xl font-bold text-amber-600 mt-1">TSh {{ number_format($avgPrice ?? 0, 0) }}</p>
        </div>
        <div class="bg-white rounded-md border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-medium text-gray-500 uppercase tracking-wide">Bookings</span>
                <i class="hgi-stroke hgi-calendar-03 text-base text-gray-400"></i>
            </div>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ $totalAppointments }}</p>
        </div>
    </div>

    {{-- Services Table --}}
    <div class="bg-white rounded-md border border-gray-200 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100">
            <h2 class="text-sm font-bold text-gray-900">Services Catalog</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 text-[10px] uppercase text-gray-400 border-b border-gray-100">
                        <th class="px-5 py-3 font-medium text-left">Name</th>
                        <th class="px-5 py-3 font-medium text-left">Duration</th>
                        <th class="px-5 py-3 font-medium text-left">Price</th>
                        <th class="px-5 py-3 font-medium text-left">Color</th>
                        <th class="px-5 py-3 font-medium text-left">Status</th>
                        <th class="px-5 py-3 font-medium text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse ($services as $service)
                        <tr class="hover:bg-gray-50/50">
                            <td class="px-5 py-3">
                                <div class="text-xs font-medium text-gray-900">{{ $service->name }}</div>
                                @if($service->description)
                                    <div class="text-[10px] text-gray-400 mt-0.5">{{ Str::limit($service->description, 50) }}</div>
                                @endif
                            </td>
                            <td class="px-5 py-3 text-xs text-gray-600">
                                <span class="inline-flex items-center gap-1">
                                    <i class="hgi-stroke hgi-clock-01 text-sm text-gray-400"></i>
                                    {{ $service->duration_minutes }} min
                                </span>
                            </td>
                            <td class="px-5 py-3 text-xs font-semibold text-gray-900">TSh {{ number_format($service->price, 0) }}</td>
                            <td class="px-5 py-3">
                                <span class="w-4 h-4 rounded-full inline-block border border-gray-200" style="background-color: {{ $service->color ?? '#0f172a' }}"></span>
                            </td>
                            <td class="px-5 py-3">
                                <span class="inline-flex items-center gap-1.5 text-[10px] font-medium text-gray-600">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $service->is_active ? 'bg-emerald-500' : 'bg-gray-400' }}"></span>
                                    {{ $service->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td class="px-5 py-3 text-right">
                                <div class="flex items-center justify-end gap-1">
                                    <a href="{{ route('services.edit', $service) }}" class="p-1.5 rounded text-slate-600 hover:bg-slate-100 transition-colors" title="Edit">
                                        <i class="hgi-stroke hgi-pencil-edit-01 text-base"></i>
                                    </a>
                                    <form method="POST" action="{{ route('services.destroy', $service) }}" data-ajax data-confirm="Delete this service?" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 rounded text-red-600 hover:bg-red-50 transition-colors" title="Delete">
                                            <i class="hgi-stroke hgi-delete-02 text-base"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-5 py-8 text-center text-gray-400 text-xs">
                            <i class="hgi-stroke hgi-building-03 text-4xl mx-auto mb-2 text-gray-300"></i>
                            No services found
                        </td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($services->hasPages())
        <div class="px-5 py-3 border-t border-gray-100">{{ $services->links() }}</div>
        @endif
    </div>
</div>
@endsection
