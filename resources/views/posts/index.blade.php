@extends('layouts.dashboard')

@section('title', 'Blog Posts - ' . config('app.name', 'Laravel'))
@section('page_title', 'Blog Management')

@section('content')
<div class="space-y-5">
<div class="bg-slate-800 rounded-md p-6 text-white flex items-center justify-between">
    <div>
        <p class="text-[10px] uppercase tracking-wider text-slate-400 mb-1">Content</p>
        <h1 class="text-xl font-bold">Blog Posts</h1>
        <p class="text-xs text-slate-400 mt-1">Manage website articles and updates.</p>
    </div>
    <a href="{{ route('posts.create') }}" class="bg-white text-slate-800 text-xs font-medium px-3 py-1.5 rounded hover:bg-slate-100">+ New Post</a>
</div>
<div class="bg-white rounded-md border border-gray-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-50 text-[10px] uppercase text-gray-400 border-b border-gray-100">
                    <th class="px-5 py-3 font-medium text-left">Title</th>
                    <th class="px-5 py-3 font-medium text-left">Author</th>
                    <th class="px-5 py-3 font-medium text-left">Status</th>
                    <th class="px-5 py-3 font-medium text-left">Date</th>
                    <th class="px-5 py-3 font-medium text-left">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse ($posts as $post)
                    <tr class="hover:bg-gray-50/50">
                        <td class="px-5 py-3 text-xs font-medium text-gray-900">{{ Str::limit($post->title, 50) }}</td>
                        <td class="px-5 py-3 text-xs text-gray-600">{{ $post->author->name }}</td>
                        <td class="px-5 py-3">
                            <span class="inline-flex items-center gap-1.5 text-[10px] font-medium text-gray-600">
                                <span class="w-1.5 h-1.5 rounded-full {{ $post->is_published ? 'bg-emerald-500' : 'bg-amber-500' }}"></span>
                                {{ $post->is_published ? 'Published' : 'Draft' }}
                            </span>
                        </td>
                        <td class="px-5 py-3 text-xs text-gray-500">{{ $post->created_at->format('d M Y') }}</td>
                        <td class="px-5 py-3 flex items-center gap-2">
                            <a href="{{ route('posts.edit', $post) }}" class="text-slate-600 hover:text-slate-800 text-xs font-medium">Edit</a>
                            <form method="POST" action="{{ route('posts.destroy', $post) }}" data-ajax data-confirm="Delete this post?" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-700 text-xs font-medium">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-5 py-8 text-center text-gray-400 text-xs">No posts yet</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-5 py-3 border-t border-gray-100">
        {{ $posts->links() }}
    </div>
</div>
</div>
@endsection
