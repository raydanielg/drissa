@extends('layouts.dashboard')

@section('title', 'Patient Documents - ' . config('app.name', 'Laravel'))
@section('page_title', 'Documents: ' . $patient->fullName())

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
    <div class="bg-white rounded-xl border border-gray-100 p-6 shadow-sm">
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-full bg-gradient-to-br from-purple-500 to-purple-700 flex items-center justify-center text-white text-lg font-bold">
                    {{ strtoupper(substr($patient->first_name, 0, 1)) }}
                </div>
                <div>
                    <h2 class="text-xl font-bold text-gray-900">{{ $patient->fullName() }}</h2>
                    <p class="text-sm text-gray-500">MRN: {{ $patient->mrn }} | Files & Documents</p>
                </div>
            </div>
            <div class="flex items-center gap-1">
                <a href="{{ route('patients.show', $patient) }}" class="action-icon group/icon relative p-2.5 text-emerald-600 hover:bg-emerald-100 rounded-lg transition-colors" title="Profile">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    <span class="absolute -top-8 left-1/2 -translate-x-1/2 px-2 py-1 bg-gray-900 text-white text-[10px] rounded opacity-0 group-hover/icon:opacity-100 transition-opacity whitespace-nowrap pointer-events-none">Profile</span>
                </a>
                <a href="{{ route('patients.history', $patient) }}" class="action-icon group/icon relative p-2.5 text-blue-600 hover:bg-blue-100 rounded-lg transition-colors" title="History">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span class="absolute -top-8 left-1/2 -translate-x-1/2 px-2 py-1 bg-gray-900 text-white text-[10px] rounded opacity-0 group-hover/icon:opacity-100 transition-opacity whitespace-nowrap pointer-events-none">History</span>
                </a>
                <a href="{{ route('patients.edit', $patient) }}" class="action-icon group/icon relative p-2.5 text-emerald-600 hover:bg-emerald-100 rounded-lg transition-colors" title="Edit">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.43-9.525l-9.17 9.17a2 2 0 00-.586 1.414V17a1 1 0 001 1h2.828a2 2 0 001.414-.586l9.17-9.17a2 2 0 000-2.828l-1.414-1.414a2 2 0 00-2.828 0z"/></svg>
                    <span class="absolute -top-8 left-1/2 -translate-x-1/2 px-2 py-1 bg-gray-900 text-white text-[10px] rounded opacity-0 group-hover/icon:opacity-100 transition-opacity whitespace-nowrap pointer-events-none">Edit</span>
                </a>
            </div>
        </div>

        <h2 class="text-sm font-semibold text-gray-900 mb-4">Upload New Document</h2>
        <form method="POST" action="{{ route('patients.documents.store', $patient) }}" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-4 gap-4" data-ajax>
            @csrf
            <div class="md:col-span-2">
                <input type="text" name="title" placeholder="Document title" class="w-full border rounded-lg px-3 py-2 text-sm" required>
            </div>
            <div>
                <select name="category" class="w-full border rounded-lg px-3 py-2 text-sm" required>
                    <option value="">Category</option>
                    <option value="lab">Lab</option>
                    <option value="radiology">Radiology</option>
                    <option value="prescription">Prescription</option>
                    <option value="identification">Identification</option>
                    <option value="insurance">Insurance</option>
                    <option value="other">Other</option>
                </select>
            </div>
            <div>
                <input type="file" name="file" class="w-full text-sm" required>
            </div>
            <div class="md:col-span-3">
                <textarea name="description" placeholder="Description (optional)" class="w-full border rounded-lg px-3 py-2 text-sm" rows="2"></textarea>
            </div>
            <div class="md:col-span-1 flex items-end">
                <button type="submit" class="w-full bg-emerald-600 text-white text-sm font-medium px-4 py-2 rounded-lg hover:bg-emerald-700">Upload</button>
            </div>
        </form>
    </div>

    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <h2 class="text-sm font-semibold text-gray-900">Existing Documents</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                    <tr>
                        <th class="px-6 py-3">Title</th>
                        <th class="px-6 py-3">Category</th>
                        <th class="px-6 py-3">Size</th>
                        <th class="px-6 py-3">Uploaded</th>
                        <th class="px-6 py-3">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($documents as $doc)
                        <tr class="border-t border-gray-100 hover:bg-gray-50/50">
                            <td class="px-6 py-3">
                                <div class="font-medium">{{ $doc->title }}</div>
                                <div class="text-xs text-gray-500">{{ $doc->description }}</div>
                            </td>
                            <td class="px-6 py-3"><span class="capitalize">{{ $doc->category }}</span></td>
                            <td class="px-6 py-3">{{ $doc->formattedSize() }}</td>
                            <td class="px-6 py-3 text-xs text-gray-500">{{ $doc->created_at->format('M d, Y') }} by {{ $doc->uploader?->name }}</td>
                            <td class="px-6 py-3">
                                <div class="flex items-center gap-1">
                                    <a href="{{ route('patients.documents.download', $doc) }}" class="action-icon group/icon relative p-2 text-emerald-600 hover:bg-emerald-100 rounded-lg transition-colors" title="Download">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                        <span class="absolute -top-8 left-1/2 -translate-x-1/2 px-2 py-1 bg-gray-900 text-white text-[10px] rounded opacity-0 group-hover/icon:opacity-100 transition-opacity whitespace-nowrap pointer-events-none">Download</span>
                                    </a>
                                    <form method="POST" action="{{ route('patients.documents.destroy', $doc) }}" data-ajax data-confirm="Delete this document?" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="action-icon group/icon relative p-2 text-red-600 hover:bg-red-100 rounded-lg transition-colors" title="Delete">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            <span class="absolute -top-8 left-1/2 -translate-x-1/2 px-2 py-1 bg-gray-900 text-white text-[10px] rounded opacity-0 group-hover/icon:opacity-100 transition-opacity whitespace-nowrap pointer-events-none">Delete</span>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-6 py-6 text-center text-gray-400">No documents uploaded yet</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-100">{{ $documents->links() }}</div>
    </div>
</div>
@endsection
