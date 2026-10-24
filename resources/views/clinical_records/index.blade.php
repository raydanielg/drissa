@extends('layouts.dashboard')

@section('title', 'Clinical Records - ' . config('app.name', 'Laravel'))
@section('page_title', 'Clinical Records')

@section('content')
<div class="space-y-5">
    <div class="bg-slate-800 rounded-md p-6 text-white flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <p class="text-[10px] uppercase tracking-wider text-slate-400 mb-1">Clinical</p>
            <h1 class="text-xl font-bold">Clinical Records</h1>
            <p class="text-xs text-slate-400 mt-1">Manage patient diagnoses, treatments and medical notes.</p>
        </div>
        <a href="{{ route('clinical-records.create') }}" class="inline-flex items-center gap-2 bg-white text-slate-800 text-sm font-medium px-4 py-2 rounded hover:bg-slate-100 transition-colors self-start">
            <i class="hgi-stroke hgi-add-01 text-base"></i>
            Add Record
        </a>
    </div>

    <div class="bg-white rounded-md border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 text-[10px] uppercase text-gray-400 border-b border-gray-100">
                        <th class="px-5 py-3 font-medium text-left">Date</th>
                        <th class="px-5 py-3 font-medium text-left">Patient</th>
                        <th class="px-5 py-3 font-medium text-left">Doctor</th>
                        <th class="px-5 py-3 font-medium text-left">Diagnosis</th>
                        <th class="px-5 py-3 font-medium text-left">Visit</th>
                        <th class="px-5 py-3 font-medium text-left">Files</th>
                        <th class="px-5 py-3 font-medium text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse ($records as $record)
                        <tr class="hover:bg-gray-50/50">
                            <td class="px-5 py-3 text-xs text-gray-600">
                                <div class="flex items-center gap-1.5">
                                    <i class="hgi-stroke hgi-calendar-03 text-sm text-gray-400"></i>
                                    {{ $record->record_date->format('M d, Y') }}
                                </div>
                            </td>
                            <td class="px-5 py-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded bg-slate-100 text-slate-600 flex items-center justify-center text-[10px] font-bold">
                                        {{ strtoupper(substr($record->patient?->first_name ?: 'U', 0, 1)) }}
                                    </div>
                                    <span class="text-xs font-medium text-gray-900">{{ $record->patient?->fullName() ?? '-' }}</span>
                                </div>
                            </td>
                            <td class="px-5 py-3 text-xs text-gray-600">{{ $record->doctor?->name ?? '-' }}</td>
                            <td class="px-5 py-3 text-xs text-gray-600">{{ Str::limit($record->diagnosis, 40) }}</td>
                            <td class="px-5 py-3">
                                @if ($record->visit)
                                    <span class="text-[10px] font-medium text-slate-600">{{ $record->visit->visit_number }}</span>
                                @else
                                    <span class="text-gray-400 text-xs">-</span>
                                @endif
                            </td>
                            <td class="px-5 py-3">
                                @php $attCount = $record->attachments()->count(); @endphp
                                @if($attCount > 0)
                                    <span class="inline-flex items-center gap-1 text-xs text-gray-600">
                                        <i class="hgi-stroke hgi-attachment-01 text-sm text-gray-400"></i>
                                        {{ $attCount }}
                                    </span>
                                @else
                                    <span class="text-gray-400 text-xs">-</span>
                                @endif
                            </td>
                            <td class="px-5 py-3">
                                <div class="flex items-center justify-end gap-1">
                                    <a href="{{ route('clinical-records.show', $record) }}" class="action-icon group/icon relative p-1.5 text-slate-600 hover:bg-slate-100 rounded transition-colors" title="View">
                                        <i class="hgi-stroke hgi-view text-base"></i>
                                        <span class="absolute -top-8 left-1/2 -translate-x-1/2 px-2 py-1 bg-gray-900 text-white text-[10px] rounded opacity-0 group-hover/icon:opacity-100 transition-opacity whitespace-nowrap pointer-events-none">View</span>
                                    </a>
                                    <a href="{{ route('clinical-records.edit', $record) }}" class="action-icon group/icon relative p-1.5 text-slate-600 hover:bg-slate-100 rounded transition-colors" title="Edit">
                                        <i class="hgi-stroke hgi-pencil-edit-01 text-base"></i>
                                        <span class="absolute -top-8 left-1/2 -translate-x-1/2 px-2 py-1 bg-gray-900 text-white text-[10px] rounded opacity-0 group-hover/icon:opacity-100 transition-opacity whitespace-nowrap pointer-events-none">Edit</span>
                                    </a>
                                    <form method="POST" action="{{ route('clinical-records.destroy', $record) }}" data-ajax data-confirm="Delete this record?" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="action-icon group/icon relative p-1.5 text-red-600 hover:bg-red-50 rounded transition-colors" title="Delete">
                                            <i class="hgi-stroke hgi-delete-02 text-base"></i>
                                            <span class="absolute -top-8 left-1/2 -translate-x-1/2 px-2 py-1 bg-gray-900 text-white text-[10px] rounded opacity-0 group-hover/icon:opacity-100 transition-opacity whitespace-nowrap pointer-events-none">Delete</span>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="px-5 py-8 text-center text-gray-400">
                            <div class="flex flex-col items-center gap-2">
                                <i class="hgi-stroke hgi-file-02 text-4xl text-gray-300"></i>
                                <p class="text-xs">No clinical records found</p>
                            </div>
                        </td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-5 py-3 border-t border-gray-100">
            {{ $records->links() }}
        </div>
    </div>
</div>
@endsection
