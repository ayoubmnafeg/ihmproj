<?php

namespace App\Http\Controllers;

use App\Models\Content;
use App\Models\Publication;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PublicationController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $publications = Publication::with(['author.profile', 'category'])
            ->where('contents.status', 'visible')
            ->latest('contents.created_at')
            ->paginate(20);

        return response()->json($publications);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'title'       => 'required|string|max:255',
            'text'        => 'required|string',
            'media_type'  => 'nullable|string|in:image,video,audio,document',
            'category_id' => 'nullable|uuid|exists:categories,id',
        ]);

        $content = Content::create([
            'type'      => 'publication',
            'status'    => 'visible',
            'author_id' => $request->user()->id,
        ]);

        DB::table('publications')->insert(array_merge([
            'id' => $content->id,
            'created_at' => now(),
            'updated_at' => now(),
        ], $data));

        $publication = Publication::query()->where('publications.id', $content->id)->firstOrFail();

        return response()->json($publication->load(['author.profile', 'category']), 201);
    }

    public function show(Publication $publication): JsonResponse
    {
        return response()->json($publication->load(['author.profile', 'category', 'attachments']));
    }

    public function update(Request $request, Publication $publication): JsonResponse
    {
        if ($request->user()->id !== $publication->author_id) {
            return response()->json(['message' => 'Forbidden.'], 403);
        }

        $data = $request->validate([
            'title'       => 'sometimes|string|max:255',
            'text'        => 'sometimes|string',
            'media_type'  => 'nullable|string|in:image,video,audio,document',
            'category_id' => 'nullable|uuid|exists:categories,id',
        ]);

        $publication->update($data);

        return response()->json($publication->load(['author.profile', 'category']));
    }

    public function destroy(Request $request, Publication $publication): JsonResponse
    {
        if ($request->user()->id !== $publication->author_id && ! $request->user()->isAdmin()) {
            return response()->json(['message' => 'Forbidden.'], 403);
        }

        // Soft-delete: set status to deleted on the content record
        Content::where('id', $publication->id)->update(['status' => 'deleted']);

        return response()->json(['message' => 'Publication deleted.']);
    }
}
