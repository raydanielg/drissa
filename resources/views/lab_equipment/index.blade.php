@extends('layouts.dashboard')

@section('title', 'Lab Equipment - ' . config('app.name', 'Laravel'))
@section('page_title', 'Lab Equipment')

@section('content')
<div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
        <h2 class="text-sm font-semibold text-gray-900">Equipment List</h2>
        <a href="{{ route('lab-equipment.create') }}" class="bg-emerald-600 text-white text-xs font-medium px-3 py-1.5 rounded-lg hover:bg-emerald-700">+ Add Equipment</a>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left">
            <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                <tr>
                    <th class="px-6 py-3">Name</th>
                    <th class="px-6 py-3">Model / S/N</th>
                    <th class="px-6 py-3">Manufacturer</th>
                    <th class="px-6 py-3">Next Service</th>
                    <th class="px-6 py-3">Status</th>
                    <th class="px-6 py-3">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($equipment as $item)
                    <tr class="border-t border-gray-100 hover:bg-gray-50/50">
                        <td class="px-6 py-3 font-medium">{{ $item->name }}</td>
                        <td class="px-6 py-3">{{ $item->model ?? '-' }} <br><span class="text-gray-400 text-xs">{{ $item->serial_number ?? '-' }}</span></td>
                        <td class="px-6 py-3">{{ $item->manufacturer ?? '-' }}</td>
                        <td class="px-6 py-3">{{ $item->next_service_date?->format('d M Y') ?? '-' }}</td>
                        <td class="px-6 py-3">
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-medium capitalize
                                @if($item->status === 'active') bg-emerald-100 text-emerald-700
                                @elseif($item->status === 'maintenance') bg-gold-100 text-gold-700
                                @else bg-red-100 text-red-700 @endif">
                                {{ $item->status }}
                            </span>
                        </td>
                        <td class="px-6 py-3 flex items-center gap-2">
                            <a href="{{ route('lab-equipment.edit', $item) }}" class="text-emerald-600 hover:text-emerald-700 text-xs font-medium">Edit</a>
                            <form method="POST" action="{{ route('lab-equipment.destroy', $item) }}" data-ajax data-confirm="Delete this equipment?" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-700 text-xs font-medium">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-6 py-6 text-center text-gray-400">No lab equipment found</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-6 py-4 border-t border-gray-100">
        {{ $equipment->links() }}
    </div>
</div>
@endsection
