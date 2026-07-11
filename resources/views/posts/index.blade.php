@extends('layouts.dashboard')

@section('title', 'Blog Posts - ' . config('app.name', 'Laravel'))
@section('page_title', 'Blog Management')

@section('content')
<div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
        <h2 class="text-sm font-semibold text-gray-900">Posts</h2>
        <a href="{{ route('posts.create') }}" class="bg-emerald-600 text-white text-xs font-medium px-3 py-1.5 rounded-lg hover:bg-emerald-700">+ New Post</a>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left">
            <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                <tr>
                    <th class="px-6 py-3">Title</th>
                    <th class="px-6 py-3">Author</th>
                    <th class="px-6 py-3">Status</th>
                    <th class="px-6 py-3">Date</th>
                    <th class="px-6 py-3">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($posts as $post)
                    <tr class="border-t border-gray-100 hover:bg-gray-50/50">
                        <td class="px-6 py-3 font-medium">{{ Str::limit($post->title, 50) }}</td>
                        <td class="px-6 py-3">{{ $post->author->name }}</td>
                        <td class="px-6 py-3">
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-medium {{ $post->is_published ? 'bg-emerald-100 text-emerald-700' : 'bg-gold-100 text-gold-700' }}">
                                {{ $post->is_published ? 'Published' : 'Draft' }}
                            </span>
                        </td>
                        <td class="px-6 py-3 text-gray-500">{{ $post->created_at->format('d M Y') }}</td>
                        <td class="px-6 py-3 flex items-center gap-2">
                            <a href="{{ route('posts.edit', $post) }}" class="text-emerald-600 hover:text-emerald-700 text-xs font-medium">Edit</a>
                            <form method="POST" action="{{ route('posts.destroy', $post) }}" data-ajax data-confirm="Delete this post?" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-700 text-xs font-medium">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-6 py-6 text-center text-gray-400">No posts yet</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-6 py-4 border-t border-gray-100">
        {{ $posts->links() }}
    </div>
</div>
@endsection
