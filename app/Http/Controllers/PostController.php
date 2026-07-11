<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PostController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $posts = Post::with('author')->latest()->paginate(20);
        return view('posts.index', compact('posts'));
    }

    public function create()
    {
        return view('posts.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'excerpt' => 'nullable|string',
            'body' => 'required|string',
            'is_published' => 'boolean',
        ]);

        $data['slug'] = Str::slug($data['title']) . '-' . time();
        $data['author_id'] = auth()->id();
        $data['is_published'] = $request->boolean('is_published', false);
        if ($data['is_published']) {
            $data['published_at'] = now();
        }

        Post::create($data);

        return redirect()->route('posts.index')->with('status', 'Post created.');
    }

    public function edit(Post $post)
    {
        return view('posts.edit', compact('post'));
    }

    public function update(Request $request, Post $post)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'excerpt' => 'nullable|string',
            'body' => 'required|string',
            'is_published' => 'boolean',
        ]);

        $data['is_published'] = $request->boolean('is_published', false);
        if ($data['is_published'] && ! $post->published_at) {
            $data['published_at'] = now();
        } elseif (! $data['is_published']) {
            $data['published_at'] = null;
        }

        $post->update($data);

        return redirect()->route('posts.index')->with('status', 'Post updated.');
    }

    public function destroy(Post $post)
    {
        $post->delete();
        return back()->with('status', 'Post deleted.');
    }
}
