<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Content;
use App\Models\Publication;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CommentController extends Controller
{
    public function store(Request $request, Publication $publication): RedirectResponse
    {
        $data = $request->validate([
            'text' => 'required|string',
            'parent_id' => 'nullable|uuid|exists:comments,id',
        ]);

        $content = Content::create([
            'type' => 'comment',
            'status' => 'visible',
            'author_id' => $request->user()->id,
        ]);

        DB::table('comments')->insert([
            'id' => $content->id,
            'text' => $data['text'],
            'publication_id' => $publication->id,
            'parent_id' => $data['parent_id'] ?? null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return back()->with('success', 'Comment posted.');
    }

    public function destroy(Request $request, Comment $comment): RedirectResponse
    {
        if ($request->user()->id !== $comment->author_id && ! $request->user()->isAdmin()) {
            abort(403);
        }

        Content::where('id', $comment->id)->update(['status' => 'deleted']);

        return back()->with('success', 'Comment deleted.');
    }
}
