<?php

namespace App\Http\Controllers;

use App\Models\Content;
use App\Models\Publication;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PublicationController extends Controller
{
    public function index(): View
    {
        $publications = Publication::with(['author', 'category', 'comments.author'])
            ->withCount('comments')
            ->where('contents.status', 'visible')
            ->latest('contents.created_at')
            ->paginate(20);

        return view('feed.index', compact('publications'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'text' => 'required|string',
            'media_type' => 'nullable|string|in:image,video,audio,document',
            'category_id' => 'nullable|uuid|exists:categories,id',
        ]);

        $content = Content::create([
            'type' => 'publication',
            'status' => 'visible',
            'author_id' => $request->user()->id,
        ]);

        DB::table('publications')->insert(array_merge([
            'id' => $content->id,
            'created_at' => now(),
            'updated_at' => now(),
        ], $data));

        return redirect()->route('feed.index')->with('success', 'Publication posted.');
    }

    public function destroy(Request $request, Publication $publication): RedirectResponse
    {
        if ($request->user()->id !== $publication->author_id && ! $request->user()->isAdmin()) {
            abort(403);
        }

        Content::where('id', $publication->id)->update(['status' => 'deleted']);

        return redirect()->route('feed.index')->with('success', 'Publication deleted.');
    }
}
