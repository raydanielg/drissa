@extends('layouts.dashboard')

@section('title', 'Clinic Rooms - ' . config('app.name', 'Laravel'))
@section('page_title', 'Clinic Management - Rooms')

@section('content')
<div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
        <h2 class="text-sm font-semibold text-gray-900">Clinic Rooms</h2>
        <a href="{{ route('clinic-rooms.create') }}" class="bg-emerald-600 text-white text-xs font-medium px-3 py-1.5 rounded-lg hover:bg-emerald-700">+ Add Room</a>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left">
            <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                <tr>
                    <th class="px-6 py-3">Name</th>
                    <th class="px-6 py-3">Code</th>
                    <th class="px-6 py-3">Department</th>
                    <th class="px-6 py-3">Type</th>
                    <th class="px-6 py-3">Capacity</th>
                    <th class="px-6 py-3">Status</th>
                    <th class="px-6 py-3">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($rooms as $room)
                    <tr class="border-t border-gray-100 hover:bg-gray-50/50">
                        <td class="px-6 py-3 font-medium">{{ $room->name }}</td>
                        <td class="px-6 py-3">{{ $room->code ?? '-' }}</td>
                        <td class="px-6 py-3">{{ $room->department?->name ?? '-' }}</td>
                        <td class="px-6 py-3 capitalize">{{ $room->type }}</td>
                        <td class="px-6 py-3">{{ $room->capacity }}</td>
                        <td class="px-6 py-3">
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-medium capitalize
                                @if($room->status === 'available') bg-emerald-100 text-emerald-700
                                @elseif($room->status === 'occupied') bg-gold-100 text-gold-700
                                @else bg-red-100 text-red-700 @endif">
                                {{ $room->status }}
                            </span>
                        </td>
                        <td class="px-6 py-3 flex items-center gap-2">
                            <a href="{{ route('clinic-rooms.edit', $room) }}" class="text-emerald-600 hover:text-emerald-700 text-xs font-medium">Edit</a>
                            <form method="POST" action="{{ route('clinic-rooms.destroy', $room) }}" data-ajax data-confirm="Delete this room?" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-700 text-xs font-medium">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="px-6 py-6 text-center text-gray-400">No rooms found</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-6 py-4 border-t border-gray-100">
        {{ $rooms->links() }}
    </div>
</div>
@endsection
