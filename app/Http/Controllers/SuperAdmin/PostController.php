<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::latest()->paginate(15);
        return view('super-admin.posts.index', compact('posts'));
    }

    public function create()
    {
        return view('super-admin.posts.form');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:posts,slug',
            'content' => 'required|string',
            'excerpt' => 'nullable|string|max:500',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'is_published' => 'boolean',
        ]);

        $data['slug'] = $data['slug'] ?: Str::slug($data['title']);
        $data['author_id'] = auth()->id();
        if ($request->is_published) {
            $data['published_at'] = now();
        }

        Post::create($data);
        return redirect()->route('super-admin.posts.index')->with('success', 'Post created successfully.');
    }

    public function edit(Post $post)
    {
        return view('super-admin.posts.form', compact('post'));
    }

    public function update(Request $request, Post $post)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:posts,slug,' . $post->id,
            'content' => 'required|string',
            'excerpt' => 'nullable|string|max:500',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'is_published' => 'boolean',
        ]);

        if ($request->is_published && !$post->is_published) {
            $data['published_at'] = now();
        } elseif (!$request->is_published) {
            $data['published_at'] = null;
        }

        $post->update($data);
        return redirect()->route('super-admin.posts.index')->with('success', 'Post updated successfully.');
    }

    public function destroy(Post $post)
    {
        $post->delete();
        return redirect()->route('super-admin.posts.index')->with('success', 'Post deleted successfully.');
    }
}
