@extends('layouts.public')

@section('title', 'Blog - ' . config('app.name'))

@section('content')

<section class="bg-slate-800 py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h1 class="text-4xl font-extrabold text-white">Blog</h1>
        <p class="mt-4 text-slate-400 max-w-2xl mx-auto">Health tips, news, and articles on reproductive health and family planning.</p>
        <div class="mt-4 w-20 h-1 bg-white mx-auto rounded-full"></div>
    </div>
</section>

<section class="py-20 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        @if($posts->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($posts as $post)
                    <div class="card-hover bg-white rounded-2xl overflow-hidden border border-gray-100 shadow-sm">
                        <div class="h-48 bg-slate-700 flex items-center justify-center">
                            <i class="hgi-stroke hgi-news-01 text-base text-white/30"></i>
                        </div>
                        <div class="p-6">
                            <p class="text-xs text-gray-400 mb-2">{{ $post->published_at?->format('M d, Y') }}</p>
                            <h3 class="font-bold text-slate-900 text-lg mb-2">{{ $post->title }}</h3>
                            <p class="text-sm text-gray-600 leading-relaxed mb-4">{{ \Illuminate\Support\Str::limit($post->excerpt ?? $post->body, 120) }}</p>
                            <a href="#" class="inline-flex items-center gap-1 text-sm font-semibold text-slate-600 hover:text-slate-800 transition-colors">
                                Read more
                                <i class="hgi-stroke hgi-arrow-right-01 text-base"></i>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="mt-8">{{ $posts->links() }}</div>
        @else
            <div class="text-center py-20">
                <i class="hgi-stroke hgi-news-01 text-base mx-auto text-gray-300 mb-4"></i>
                <h3 class="text-xl font-bold text-gray-400">No articles yet</h3>
                <p class="text-sm text-gray-400 mt-2">Check back soon for health tips and articles.</p>
            </div>
        @endif
    </div>
</section>

@endsection
