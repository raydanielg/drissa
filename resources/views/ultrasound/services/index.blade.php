@extends('layouts.dashboard')

@section('title', 'Ultrasound Services - ' . config('app.name', 'Laravel'))
@section('page_title', 'Ultrasound Services')

@section('content')
<div class="max-w-6xl mx-auto space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-lg font-bold text-gray-900">Ultrasound Services</h2>
            <p class="text-sm text-gray-500">Manage ultrasound scan types and pricing</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('ultrasound.queue') }}" class="px-4 py-2 text-sm text-gray-600 hover:bg-gray-100 rounded-lg">Queue</a>
            <a href="{{ route('ultrasound-services.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Add Service
            </a>
        </div>
    </div>

    @if (session('status'))
        <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm animate-fade">{{ session('status') }}</div>
    @endif

    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50/50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Code</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Price</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($services as $service)
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="px-6 py-3.5">
                            <div class="text-sm font-medium text-gray-900">{{ $service->name }}</div>
                            <div class="text-xs text-gray-500">{{ $service->description }}</div>
                        </td>
                        <td class="px-6 py-3.5 text-sm text-gray-700">{{ $service->code ?? '-' }}</td>
                        <td class="px-6 py-3.5 text-sm font-medium text-gray-900">{{ number_format($service->price) }} TSh</td>
                        <td class="px-6 py-3.5">
                            @if($service->is_active)
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700">Active</span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-700">Inactive</span>
                            @endif
                        </td>
                        <td class="px-6 py-3.5 text-right">
                            <a href="{{ route('ultrasound-services.edit', $service) }}" class="text-xs text-blue-600 hover:text-blue-700 font-medium">Edit</a>
                            <form method="POST" action="{{ route('ultrasound-services.destroy', $service) }}" class="inline" onsubmit="return confirm('Delete this service?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-xs text-red-600 hover:text-red-700 font-medium ml-2">Delete</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-6 py-3 border-t border-gray-100">
            {{ $services->links() }}
        </div>
    </div>
</div>
@endsection
