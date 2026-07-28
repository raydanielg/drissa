@extends('layouts.public')

@section('title', 'Blog - ' . config('app.name'))

@section('content')

<section class="bg-emerald-900 py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h1 class="text-4xl font-extrabold text-white">Blog</h1>
        <p class="mt-4 text-emerald-100/80 max-w-2xl mx-auto">Health tips, news, and articles on reproductive health and family planning.</p>
        <div class="mt-4 w-20 h-1 bg-gold-500 mx-auto rounded-full"></div>
    </div>
</section>

<section class="py-20 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        @if($posts->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($posts as $post)
                    <div class="card-hover bg-white rounded-2xl overflow-hidden border border-gray-100 shadow-sm">
                        <div class="h-48 bg-gradient-to-br from-emerald-500 to-emerald-700 flex items-center justify-center">
                            <svg class="w-16 h-16 text-white/30" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/></svg>
                        </div>
                        <div class="p-6">
                            <p class="text-xs text-gray-400 mb-2">{{ $post->published_at?->format('M d, Y') }}</p>
                            <h3 class="font-bold text-emerald-900 text-lg mb-2">{{ $post->title }}</h3>
                            <p class="text-sm text-gray-600 leading-relaxed mb-4">{{ \Illuminate\Support\Str::limit($post->excerpt ?? $post->body, 120) }}</p>
                            <a href="#" class="inline-flex items-center gap-1 text-sm font-semibold text-gold-600 hover:text-gold-700 transition-colors">
                                Read more
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="mt-8">{{ $posts->links() }}</div>
        @else
            <div class="text-center py-20">
                <svg class="w-20 h-20 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/></svg>
                <h3 class="text-xl font-bold text-gray-400">No articles yet</h3>
                <p class="text-sm text-gray-400 mt-2">Check back soon for health tips and articles.</p>
            </div>
        @endif
    </div>
</section>

@endsection
