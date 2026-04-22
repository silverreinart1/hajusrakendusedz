<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    public function store(Request $request, Post $post)
    {
        $request->validate(['body' => 'required|string|max:1000']);

        $post->comments()->create([
            'user_id' => Auth::id(),
            'body'    => $request->body,
        ]);

        return back()->with('success', 'Kommentaar lisatud!');
    }

    public function destroy(Comment $comment)
    {
        $user = Auth::user();
        if ($user->is_admin || $comment->user_id === $user->id) {
            $comment->delete();
        }
        return back()->with('success', 'Kommentaar kustutatud!');
    }
}
