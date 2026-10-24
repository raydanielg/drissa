@extends('layouts.dashboard')

@section('title', 'Edit Post - ' . config('app.name', 'Laravel'))
@section('page_title', 'Edit Blog Post')

@section('content')
<div class="max-w-3xl mx-auto space-y-5">
    <div class="bg-slate-800 rounded-md p-6 text-white">
        <p class="text-[10px] uppercase tracking-wider text-slate-400 mb-1">Content</p>
        <h1 class="text-xl font-bold">Edit Post</h1>
        <p class="text-xs text-slate-400 mt-1">Update the selected article.</p>
    </div>

    <div class="bg-white rounded-md border border-gray-200 p-5">
    <form method="POST" action="{{ route('posts.update', $post) }}" class="space-y-4">
        @csrf
        @method('PUT')
        <div>
            <label class="block text-xs font-medium text-gray-700 mb-1">Title</label>
            <x-ui.input type="text" name="title" value="{{ $post->title }}" class="w-full border border-gray-200 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-slate-300 outline-none" required />
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-700 mb-1">Excerpt</label>
            <x-ui.textarea name="excerpt" class="w-full border border-gray-200 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-slate-300 outline-none" rows="3">{{ $post->excerpt }}</x-ui.textarea>
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-700 mb-1">Body</label>
            <x-ui.textarea name="body" class="w-full border border-gray-200 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-slate-300 outline-none" rows="10" required>{{ $post->body }}</x-ui.textarea>
        </div>
        <div class="flex items-center gap-2">
            <input type="checkbox" name="is_published" value="1" {{ $post->is_published ? 'checked' : '' }} class="rounded border-gray-300 text-slate-600 focus:ring-slate-300">
            <label class="text-sm text-gray-700">Published</label>
        </div>
        <div class="pt-4">
            <button type="submit" class="bg-slate-800 text-white text-sm font-medium px-5 py-2 rounded hover:bg-slate-700">Update Post</button>
        </div>
    </form>
    </div>
</div>
@endsection
