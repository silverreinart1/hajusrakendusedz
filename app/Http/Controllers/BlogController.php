<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class BlogController extends Controller
{
    public function index()
    {
        return Inertia::render('Blog/Index', [
            'posts' => Post::with('user')->latest()->get(),
        ]);
    }

    public function show(Post $post)
    {
        return Inertia::render('Blog/Show', [
            'post' => $post->load(['user', 'comments.user']),
        ]);
    }

    public function create()
    {
        return Inertia::render('Blog/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'required|string',
        ]);

        Post::create([...$validated, 'user_id' => Auth::id()]);

        return redirect()->route('blog.index')->with('success', 'Postitus loodud!');
    }

    public function edit(Post $post)
    {
        $this->authorize('update', $post);
        return Inertia::render('Blog/Edit', ['post' => $post]);
    }

    public function update(Request $request, Post $post)
    {
        $this->authorize('update', $post);

        $post->update($request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'required|string',
        ]));

        return redirect()->route('blog.show', $post)->with('success', 'Postitus uuendatud!');
    }

    public function destroy(Post $post)
    {
        $this->authorize('delete', $post);
        $post->delete();
        return redirect()->route('blog.index')->with('success', 'Postitus kustutatud!');
    }
}
