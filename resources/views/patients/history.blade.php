@extends('layouts.dashboard')

@section('title', 'Patient History - ' . config('app.name', 'Laravel'))
@section('page_title', 'History: ' . $patient->fullName())

@section('content')
<div class="max-w-4xl mx-auto bg-white rounded-xl border border-gray-100 p-6 shadow-sm">
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 rounded-full bg-gradient-to-br from-blue-500 to-blue-700 flex items-center justify-center text-white text-lg font-bold">
                {{ strtoupper(substr($patient->first_name, 0, 1)) }}
            </div>
            <div>
                <h2 class="text-xl font-bold text-gray-900">{{ $patient->fullName() }}</h2>
                <p class="text-sm text-gray-500">MRN: {{ $patient->mrn }} | Born: {{ $patient->date_of_birth?->format('M d, Y') }}</p>
            </div>
        </div>
        <div class="flex items-center gap-1">
            <a href="{{ route('patients.show', $patient) }}" class="action-icon group/icon relative p-2.5 text-emerald-600 hover:bg-emerald-100 rounded-lg transition-colors" title="Profile">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                <span class="absolute -top-8 left-1/2 -translate-x-1/2 px-2 py-1 bg-gray-900 text-white text-[10px] rounded opacity-0 group-hover/icon:opacity-100 transition-opacity whitespace-nowrap pointer-events-none">Profile</span>
            </a>
            <a href="{{ route('patients.documents.index', $patient) }}" class="action-icon group/icon relative p-2.5 text-purple-600 hover:bg-purple-100 rounded-lg transition-colors" title="Files">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                <span class="absolute -top-8 left-1/2 -translate-x-1/2 px-2 py-1 bg-gray-900 text-white text-[10px] rounded opacity-0 group-hover/icon:opacity-100 transition-opacity whitespace-nowrap pointer-events-none">Files</span>
            </a>
            <a href="{{ route('patients.edit', $patient) }}" class="action-icon group/icon relative p-2.5 text-emerald-600 hover:bg-emerald-100 rounded-lg transition-colors" title="Edit">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.43-9.525l-9.17 9.17a2 2 0 00-.586 1.414V17a1 1 0 001 1h2.828a2 2 0 001.414-.586l9.17-9.17a2 2 0 000-2.828l-1.414-1.414a2 2 0 00-2.828 0z"/></svg>
                <span class="absolute -top-8 left-1/2 -translate-x-1/2 px-2 py-1 bg-gray-900 text-white text-[10px] rounded opacity-0 group-hover/icon:opacity-100 transition-opacity whitespace-nowrap pointer-events-none">Edit</span>
            </a>
        </div>
    </div>

    <div class="relative border-l-2 border-gray-200 ml-3 space-y-6">
        @forelse ($timeline as $item)
            <div class="relative pl-6">
                <div class="absolute -left-[9px] top-1 w-4 h-4 rounded-full {{ match($item['type']) { 'visit' => 'bg-blue-500', 'record' => 'bg-emerald-500', 'appointment' => 'bg-gold-500', 'document' => 'bg-purple-500', default => 'bg-gray-400' } }}"></div>
                <div class="text-xs text-gray-500 uppercase tracking-wide">{{ $item['date']?->format('M d, Y H:i') ?? '-' }}</div>
                <div class="text-sm font-semibold text-gray-900">
                    @if($item['link'])
                        <a href="{{ $item['link'] }}" class="hover:text-emerald-600">{{ $item['title'] }}</a>
                    @else
                        {{ $item['title'] }}
                    @endif
                </div>
                <div class="text-sm text-gray-600">{{ $item['subtitle'] }}</div>
            </div>
        @empty
            <div class="pl-6 text-sm text-gray-400">No history records found</div>
        @endforelse
    </div>
</div>
@endsection
