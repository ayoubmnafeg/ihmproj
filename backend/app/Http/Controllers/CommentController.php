<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Content;
use App\Models\Publication;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function index(Publication $publication): JsonResponse
    {
        $comments = Comment::with(['author.profile', 'replies.author.profile'])
            ->where('comments.publication_id', $publication->id)
            ->whereNull('comments.parent_id')
            ->where('contents.status', 'visible')
            ->latest('contents.created_at')
            ->paginate(30);

        return response()->json($comments);
    }

    public function store(Request $request, Publication $publication): JsonResponse
    {
        $data = $request->validate([
            'text'      => 'required|string',
            'parent_id' => 'nullable|uuid|exists:comments,id',
        ]);

        $content = Content::create([
            'type'      => 'comment',
            'status'    => 'visible',
            'author_id' => $request->user()->id,
        ]);

        $comment = Comment::create([
            'id'             => $content->id,
            'text'           => $data['text'],
            'publication_id' => $publication->id,
            'parent_id'      => $data['parent_id'] ?? null,
        ]);

        return response()->json($comment->load('author.profile'), 201);
    }

    public function destroy(Request $request, Comment $comment): JsonResponse
    {
        if ($request->user()->id !== $comment->author_id && ! $request->user()->isAdmin()) {
            return response()->json(['message' => 'Forbidden.'], 403);
        }

        Content::where('id', $comment->id)->update(['status' => 'deleted']);

        return response()->json(['message' => 'Comment deleted.']);
    }
}
