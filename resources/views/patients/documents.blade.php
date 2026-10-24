@extends('layouts.dashboard')

@section('title', 'Patient Documents - ' . config('app.name', 'Laravel'))
@section('page_title', 'Documents: ' . $patient->fullName())

@section('content')
<div class="max-w-5xl mx-auto space-y-5">
    <div class="bg-slate-800 rounded-md p-6 text-white">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-full border-2 border-slate-600 bg-slate-700 flex items-center justify-center text-white text-lg font-bold">
                    {{ strtoupper(substr($patient->first_name, 0, 1)) }}
                </div>
                <div>
                    <p class="text-[10px] uppercase tracking-wider text-slate-400 mb-1">Documents</p>
                    <h1 class="text-xl font-bold">{{ $patient->fullName() }}</h1>
                    <p class="text-xs text-slate-400 mt-1">MRN: {{ $patient->mrn }} — Files & Documents</p>
                </div>
            </div>
            <div class="flex items-center gap-1">
                <a href="{{ route('patients.show', $patient) }}" class="p-2 text-slate-300 hover:bg-white/10 rounded transition-colors" title="Profile">
                    <i class="hgi-stroke hgi-user text-base"></i>
                </a>
                <a href="{{ route('patients.history', $patient) }}" class="p-2 text-slate-300 hover:bg-white/10 rounded transition-colors" title="History">
                    <i class="hgi-stroke hgi-clock-01 text-base"></i>
                </a>
                <a href="{{ route('patients.edit', $patient) }}" class="p-2 text-slate-300 hover:bg-white/10 rounded transition-colors" title="Edit">
                    <i class="hgi-stroke hgi-pencil-edit-01 text-base"></i>
                </a>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-md border border-gray-200 p-5">

        <h2 class="text-sm font-semibold text-gray-900 mb-4">Upload New Document</h2>
        <form method="POST" action="{{ route('patients.documents.store', $patient) }}" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-4 gap-4" data-ajax>
            @csrf
            <div class="md:col-span-2">
                <x-ui.input type="text" name="title" placeholder="Document title" class="w-full border border-gray-200 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-slate-300" required />
            </div>
            <div>
                <x-ui.select name="category" class="w-full border border-gray-200 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-slate-300" required>
                    <option value="">Category</option>
                    <option value="lab">Lab</option>
                    <option value="radiology">Radiology</option>
                    <option value="prescription">Prescription</option>
                    <option value="identification">Identification</option>
                    <option value="insurance">Insurance</option>
                    <option value="other">Other</option>
                </x-ui.select>
            </div>
            <div>
                <x-ui.input type="file" name="file" class="w-full text-sm" required />
            </div>
            <div class="md:col-span-3">
                <x-ui.textarea name="description" placeholder="Description (optional)" class="w-full border border-gray-200 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-slate-300" rows="2"></x-ui.textarea>
            </div>
            <div class="md:col-span-1 flex items-end">
                <button type="submit" class="w-full bg-slate-800 text-white text-sm font-medium px-4 py-2 rounded hover:bg-slate-700">Upload</button>
            </div>
        </form>
    </div>

    <div class="bg-white rounded-md border border-gray-200 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100">
            <h2 class="text-sm font-bold text-gray-900">Existing Documents</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-50 text-[10px] uppercase text-gray-400">
                    <tr>
                        <th class="px-5 py-3">Title</th>
                        <th class="px-5 py-3">Category</th>
                        <th class="px-5 py-3">Size</th>
                        <th class="px-5 py-3">Uploaded</th>
                        <th class="px-5 py-3">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse ($documents as $doc)
                        <tr class="hover:bg-gray-50/50">
                            <td class="px-5 py-3">
                                <div class="text-xs font-medium text-gray-900">{{ $doc->title }}</div>
                                <div class="text-[10px] text-gray-500">{{ $doc->description }}</div>
                            </td>
                            <td class="px-5 py-3 text-xs text-gray-600 capitalize">{{ $doc->category }}</td>
                            <td class="px-5 py-3 text-xs text-gray-600">{{ $doc->formattedSize() }}</td>
                            <td class="px-5 py-3 text-xs text-gray-500">{{ $doc->created_at->format('M d, Y') }} by {{ $doc->uploader?->name }}</td>
                            <td class="px-5 py-3">
                                <div class="flex items-center gap-1">
                                    <a href="{{ route('patients.documents.download', $doc) }}" class="p-1.5 text-slate-600 hover:bg-slate-100 rounded transition-colors" title="Download">
                                        <i class="hgi-stroke hgi-upload-01 text-base"></i>
                                    </a>
                                    <form method="POST" action="{{ route('patients.documents.destroy', $doc) }}" data-ajax data-confirm="Delete this document?" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 text-red-600 hover:bg-red-50 rounded transition-colors" title="Delete">
                                            <i class="hgi-stroke hgi-delete-02 text-base"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-5 py-8 text-center text-gray-400 text-xs">No documents uploaded yet</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-5 py-3 border-t border-gray-100">{{ $documents->links() }}</div>
    </div>
</div>
@endsection
