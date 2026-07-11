@extends('layouts.dashboard')

@section('title', 'New Post - ' . config('app.name', 'Laravel'))
@section('page_title', 'Create Blog Post')

@section('content')
<div class="max-w-3xl mx-auto bg-white rounded-xl border border-gray-100 p-6 shadow-sm">
    <form method="POST" action="{{ route('posts.store') }}" class="space-y-4">
        @csrf
        <div>
            <label class="block text-xs font-medium text-gray-700 mb-1">Title</label>
            <input type="text" name="title" class="w-full border rounded-lg px-3 py-2 text-sm" required>
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-700 mb-1">Excerpt</label>
            <textarea name="excerpt" class="w-full border rounded-lg px-3 py-2 text-sm" rows="3"></textarea>
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-700 mb-1">Body</label>
            <textarea name="body" class="w-full border rounded-lg px-3 py-2 text-sm" rows="10" required></textarea>
        </div>
        <div class="flex items-center gap-2">
            <input type="checkbox" name="is_published" value="1" class="rounded border-gray-300">
            <label class="text-sm text-gray-700">Publish now</label>
        </div>
        <div class="pt-4">
            <button type="submit" class="bg-emerald-600 text-white text-sm font-medium px-5 py-2 rounded-lg hover:bg-emerald-700">Save Post</button>
        </div>
    </form>
</div>
@endsection
